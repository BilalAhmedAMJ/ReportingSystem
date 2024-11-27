<?php


namespace Application\Service;

use Application\Entity\UserToken;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManager;

use Doctrine\ORM\Query\ResultSetMapping;


use Application\Entity\Report;
use Application\Entity\Question;
use Application\Entity\Branch;
use Application\Entity\OfficeAssignment;
use Application\View\HighChart\DataTransform;
use Application\Entity\Period;
use Davaxi\Sparkline;

class ReportAnalysisService implements FactoryInterface{
    
    private $serviceLocator;
    
    /**
    @var \Doctrine\ORM\EntityManager
     * 
     * **/
    private $entityManager;

    /**
    @var \Application\Service\CreateEntityFactory
     * 
     * **/
    private $entityFactory;
    
    /**
    @var \Application\Service\EntityService
     * 
     * **/
    private $entityService;
    
    private $special_offices = array('President','General Secretary');

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       
        $this->serviceLocator = $serviceLocator;  
        $this->entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $this->entityFactory = $this->serviceLocator->get('entityFactory');
        $this->entityService = $this->serviceLocator->get('entityService');
        
        return $this;
    }


    /**
     * Return's office to be used for dash board from users assigned offices
     * Hirarchy is as follows:
     * if user is assigned an office of President/Local Amir select that office.
     * If not check if user is assigned an office of General Secretary return that
     * otherwise return office at the highest branch level based on @ref{Bracnh::BranchHirarchy} 
     */
    public function getDashBoardOffice($user){
        
        $offices = $this->serviceLocator->get('OfficeAssignmentService')->getActiveOffices($user); if(count($offices)==1){
            return $offices[0];
        } $dashBoardOffice=$offices[0];
        $dashBoardOfficeDept = $dashBoardOffice->getDepartment()->getDepartmentName();
        $dashBoardBranchType   = $dashBoardOffice->getBranch()->getBranchType();
        
        foreach ($offices as $office) {
            /**
             * @var OfficeAssignment
            */ $office=$office;
            $this_office_dept = $office->getDepartment()->getDepartmentName();
            $this_branch_type = $office->getBranch()->getBranchType();
            /**
             * @TODO This is a very poor implementation of req will fail in many cases
             * but we will fix once we get those cases, for now it works :)
             */ if($dashBoardOfficeDept!='President'){
                if(in_array($this_office_dept,$this->special_offices) || ($dashBoardBranchType=='Halqa' && $this_branch_type!='Halqa') ){ $dashBoardOffice=$office; $dashBoardBranchType=$this_branch_type;
                    $dashBoardOfficeDept = $this_office_dept;
                }
            }              
        }
        
        return $dashBoardOffice;
    }

    public function getBasicDashBoard($user){ $target_office=$this->getTargetOffice($user);
        
        $data = array();
        
        if($target_office->getBranch()->getBranchType()!=='Halqa'){
            $data['subbranch_report'] = $this->getBasicDashBoardSubBranch();
        }
        
        
        if(in_array($target_office->getDepartment()->getDepartmentName(),$this->special_offices) ){
            $data['samebranch_report'] = $this->getBasicDashBoardSameBranch();    
        }
        
        return $data;
    }
    
    /**
     * 
     * @TODO change "period_end" and timeframe "6 months" to be parameters to function 
     * 
     */
    public function getBasicDashBoardSubBranch($branch){
        
        $period_end = Period::create('Dec-2014');
        $period_start = $period_end->addMonths(-6);
        $data = array();
         
        $dql = "select branch.branch_name,period.period_code, count(report.id) as report_cont 
                      from \Application\Entity\Report report 
                      join report.branch branch
                      join report.period_from period
                      where branch.parent = :parent
                      and period.period_start >= :period_start
                      and period.period_start<= :period_end
                      group by branch,period.period_code
                      ";

        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('parent',$branch);
        $query->setParameter('period_start',$period_start->getPeriodStart());
        $query->setParameter('period_end',$period_end->getPeriodEnd());
        
        $data['last_six_month'] =  $query->getResult();

        $period_start = $period_end->addMonths(-1);

        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('parent',$branch);
        $query->setParameter('period_start',$period_start->getPeriodStart());
        $query->setParameter('period_end',$period_start->getPeriodEnd());

        $data['last_month'] =  $query->getResult();
        
        return $data;        
    }
    
    public function getBasicDashBoardSameBranch($branch){
        //$period_end = Period::create('Dec-2014');
        //$period_start = $period_end->addMonths(-6);
        $date = getdate();
        $year = $date['year'];
        $data = array();
         
        $dql = "select branch.branch_name, period.period_code, count(report.id) as report_cont 
                      from \Application\Entity\Report report 
                      join report.branch branch
                      join report.period_from period
                      where branch = :branch
                      and period.year_code like :year
                      group by branch, period
                      ";

        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('branch',$branch);
        //$query->setParameter('period_start',$period_start->getPeriodStart());
        //$query->setParameter('period_end',$period_end->getPeriodEnd());
        
        $query->setParameter('year',$year.'%');
        
        $data['last_six_month'] =  $query->getResult();

        $period_start = $period_end->addMonths(-1);

        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('branch',$branch);
        $query->setParameter('period_start',$period_start->getPeriodStart());
        $query->setParameter('period_end',$period_start->getPeriodEnd());

        $data['last_month'] =  $query->getResult();
        
        return $data;          
    }

    public function getGSReportData($params, $sort_by_level=false)
    {
        $srvOffice = $this->serviceLocator->get('OfficeAssignmentService');
        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
        $user_branches = array_keys($srvOffice->getBranchesWithOffices($current_user,true, null, 'force-grandchildren'));   
        $reporting_month_from = strtotime("{$params['year_from']}-{$params['month_from']}-01");     


        $response = ['data'=>[], 'summary'=>[], 'reporting_month'=>[],  'imarat_summary'=>[]];

        while( $reporting_month_from <= strtotime("{$params['year_to']}-{$params['month_to']}-01") ) {
            $reporting_month = date('M-Y', $reporting_month_from);     



            if(empty($report_statuses)){
                $report_statuses = array('completed','verified','received'); 
            }      
            else { $report_statuses= explode(',',$params['report_status']);
            }  
            $filters = " where b.status = 'active' ";
            $filters .= isset($params['branch']) && !empty($params['branch']) ? " and b.id in ( {$params['branch']} ) " : " and b.id in ( ".implode(',', $user_branches)." ) ";
            $sql = "
            SELECT r.id as 'report_id', b.id as 'branch_id', b.parent_branch_id, b.branch_level, if(b.branch_level='Imarat',b.branch_level,bp.branch_level) 'processed_branch_level',  if(b.branch_level='Imarat',b.branch_name,bp.branch_name) as 'parent_branch_name', b.branch_name, b.branch_level,  a_amila.`value` as 'amila_meeting_held' , a_ijlas.`value` as 'ijlas_held'
            FROM branches b     
            LEFT JOIN branches bp on b.parent_branch_id = bp.id
            LEFT JOIN reports r on b.id = r.branch_id and r.period_from = '${reporting_month}' and department_id = 5 and r.status in ('".implode("','", $report_statuses)."')
            LEFT JOIN answers a_amila on r.id = a_amila.report_id and a_amila.question_id = 152
            LEFT JOIN answers a_ijlas on r.id = a_ijlas.report_id and a_ijlas.question_id = 158
            {$filters}
            order by FIELD(processed_branch_level, 'Imarat', 'Markaz', 'Jama`at', 'Halqa'), parent_branch_name, FIELD(bp.branch_level, 'Imarat',  'Halqa'), b.branch_name
            ";
            // echo '<pre>';  echo $sql; die;
            $query = $this->entityManager->createQuery($sql);
            
            $rsm = new ResultSetMapping();            
            $columns = ['branch_id', 'branch_name', 'processed_branch_level', 'branch_level', 'parent_branch_id', 'parent_branch_name', 'amila_meeting_held', 'ijlas_held'];
            foreach( $columns as $col ) {
                $rsm->addScalarResult($col, $col);
            }
        
            $query = $this->entityManager->createNativeQuery($sql, $rsm);  
            $data = $query->getResult();
            $adapter = $this->serviceLocator->get('DoctrineEncryptAdapter');
            $processedData = array();
            $imarat_id = [];
            foreach($data as $val) {       
                $val['amila_meeting_held'] = isset($processedData[$val['branch_id']]) && $processedData[$val['branch_id']]['amila_meeting_held'] == 1 ? 1 : trim($adapter->decrypt( $val['amila_meeting_held'] ));
                $val['ijlas_held'] = isset($processedData[$val['branch_id']]) && $processedData[$val['branch_id']]['ijlas_held'] == 1 ? 1 : trim($adapter->decrypt( $val['ijlas_held'] ));
                $processedData[ $val['branch_id'] ] = $val;
                if( $val['branch_level'] == 'Imarat' ) {
                    $imarat_id[$val['branch_id']] = 1;
                }
            }   
            $levels = array('Markaz', 'Imarat', 'Jama`at', 'Halqa');
            $summary = $imarat_summary = array();
            foreach($levels as $val) {  
                $summary[$val]['count'] = 0;
                $summary[$val]['amila'] = 0;
                $summary[$val]['ijlas'] = 0;
            }
            $imarat_id = array_keys($imarat_id);
            foreach($processedData as $val) {      
                
                if( in_array($val['parent_branch_id'], $imarat_id) ) {
                    $imarat_summary[$val['parent_branch_id']]['count'] = (isset($imarat_summary[$val['parent_branch_id']]['count']) ? ($imarat_summary[$val['parent_branch_id']]['count']+1) : 1);
                    if( $val['amila_meeting_held'] == 1 ) {
                        $imarat_summary[$val['parent_branch_id']]['amila_meeting_held'] = (isset($imarat_summary[$val['parent_branch_id']]['amila_meeting_held']) ? ($imarat_summary[$val['parent_branch_id']]['amila_meeting_held']+1) : 1);
                    }
                    if( $val['ijlas_held'] == 1 ) {
                        $imarat_summary[$val['parent_branch_id']]['ijlas_held'] = (isset($imarat_summary[$val['parent_branch_id']]['ijlas_held']) ? ($imarat_summary[$val['parent_branch_id']]['ijlas_held']+1) : 1);
                    }
                }
                
                $summary[$val['branch_level']]['count']++;
                if( $val['amila_meeting_held'] == 1 ) $summary[$val['branch_level']]['amila']++;
                if( $val['ijlas_held'] == 1 ) $summary[$val['branch_level']]['ijlas']++;
            }    

            
            foreach($processedData as $key => $val) {
                $response['data'][$key]['branch_id'] = $val['branch_id'];
                $response['data'][$key]['parent_branch_id'] = $val['parent_branch_id'];
                $response['data'][$key]['branch_level'] = $val['branch_level'];
                $response['data'][$key]['processed_branch_level'] = $val['processed_branch_level'];
                $response['data'][$key]['parent_branch_name'] = $val['parent_branch_name'];
                $response['data'][$key]['branch_name'] = $val['branch_name'];
                $response['data'][$key]['amila_meeting_held'][$reporting_month] = $val['amila_meeting_held'];
                $response['data'][$key]['ijlas_held'][$reporting_month] = $val['ijlas_held'];
            }
            $response['summary'][$reporting_month] = $summary;
            $response['reporting_month'][$reporting_month] = $reporting_month;
            $response['imarat_summary'][$reporting_month] = $imarat_summary;


            $reporting_month_from = strtotime( date('Y-m-d', $reporting_month_from)." +1 MONTH"); 
        }


        // echo '<pre>';
        // print_r($response);
        // die;
        return $response;
        
    }

    private function allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, $department_id, $question_id, $levels, $action)
    {
        $reporting_month = date('M-Y', strtotime("{$m['Y']}-{$m['M']}"));   
        $filters = " where b.status = 'active' ";
        $filters .= " and b.id in ( ".implode(',', $user_branches)." ) ";
        $sql = "
        SELECT r.id as 'report_id', b.id as 'branch_id', b.parent_branch_id, b.branch_level, if(b.branch_level='Imarat',b.branch_level,bp.branch_level) 'processed_branch_level',  if(b.branch_level='Imarat',b.branch_name,bp.branch_name) as 'parent_branch_name', b.branch_name, b.branch_level,  a.`value`
        FROM branches b 
        LEFT JOIN branches bp on b.parent_branch_id = bp.id
        LEFT JOIN reports r on b.id = r.branch_id and r.period_from = '${reporting_month}' and department_id = {$department_id} and r.status in ('".implode("','", $report_statuses)."')
        LEFT JOIN answers a on r.id = a.report_id and a.question_id = {$question_id}
        {$filters}
        order by FIELD(processed_branch_level, 'Imarat', 'Markaz', 'Jama`at', 'Halqa'), parent_branch_name, FIELD(bp.branch_level, 'Imarat',  'Halqa'), b.branch_name
        ";
        // echo '<pre>';  echo $sql; die;
        $query = $this->entityManager->createQuery($sql);
        
        $rsm = new ResultSetMapping();            
        $columns = ['branch_id', 'branch_name', 'processed_branch_level', 'branch_level', 'parent_branch_id', 'parent_branch_name', 'value'];
        foreach( $columns as $col ) {
            $rsm->addScalarResult($col, $col);
        }
    
        $query = $this->entityManager->createNativeQuery($sql, $rsm);  
        $data = $query->getResult();
        $adapter = $this->serviceLocator->get('DoctrineEncryptAdapter');

        $processedData = array();
        foreach($data as $val) {    
            if( empty($levels) || in_array($val['branch_level'], $levels) )  {
                if( $action == 'YES_NO_COUNT'  ){ 
                    $processedData[ $val['branch_id'] ] = isset($processedData[$val['branch_id']]) && $processedData[$val['branch_id']] == 1 ? 1 : trim($adapter->decrypt( $val['value'] ));
                }
                else if( $action == 'SUM'  ){ 
                    $processedData[ $val['branch_id'] ] = isset($processedData[$val['branch_id']]) ? ($processedData[$val['branch_id']]+trim($adapter->decrypt( $val['value'] ))) : trim($adapter->decrypt( $val['value'] ));
                }
                else if( $action == 'SUM_ALL'  ){ 
                    $__val = trim($adapter->decrypt($val['value']));
                    if (!empty( $__val ) ){
                        $processedData[] = $__val;
                    }
                }
                else if( $action == 'COUNT_NON_EMPTY'  ){ 
                    $__val = trim($adapter->decrypt($val['value']));
                    if (!empty( $__val ) ){
                        $processedData[] = 1;
                    }
                }
            }
        }   
        return array_sum($processedData);

    }

    private function allDepartmentSummaryReport_reports_required($department_id, $user_branches)
    {
        $branch_filter = ' and b.id in ('. implode(',', $user_branches) . ') ';
        $sql = "SELECT sum(required) as reports_required 
        FROM required_reports  q
        inner join branches b on q.branch_id = b.id and b.branch_level in ('Jama`at', 'Imarat')
        where q.department_id = {$department_id} and b.status = 'active'  $branch_filter 
        ;";
        // echo '<pre>';  echo $sql; die;
        $query = $this->entityManager->createQuery($sql);
        
        $rsm = new ResultSetMapping();            
        $rsm->addScalarResult('reports_required', 'reports_required');

        $query = $this->entityManager->createNativeQuery($sql, $rsm);  
        $data = $query->getResult();
        return $data[0]['reports_required'];
    }

    private function allDepartmentSummaryReport_reports_received($m, $department_id, $report_statuses, $user_branches)
    {
        $branch_filter = ' and b.id in ('. implode(',', $user_branches) . ') ';
        $reporting_month = date('M-Y', strtotime("{$m['Y']}-{$m['M']}"));   
        $sql = "SELECT count(distinct r.id) as reports_received
        FROM branches b 
        INNER JOIN reports r on b.id = r.branch_id and r.period_from = '${reporting_month}' and department_id = {$department_id} and r.status in ('".implode("','", $report_statuses)."')
        where b.status = 'active'  
        and b.branch_level in ('Jama`at', 'Imarat') ${branch_filter}
        ;";
        // echo '<pre>';  echo $sql; die;
        $query = $this->entityManager->createQuery($sql);
        
        $rsm = new ResultSetMapping();            
        $rsm->addScalarResult('reports_received', 'reports_received');

        $query = $this->entityManager->createNativeQuery($sql, $rsm);  
        $data = $query->getResult();
        return $data[0]['reports_received'];
    }

    private function allDepartmentSummaryReport_branches_summary($user_branches)
    {
        $branch_filter = ' and b.id in ('. implode(',', $user_branches) . ') ';
        $sql = "SELECT sum(if(branch_level='Imarat',1,0)) as 'no_of_imarat', sum(if(branch_level='Jama`at',1,0)) as 'no_of_jamats', sum(if(branch_level='Halqa',1,0)) as 'no_of_halqajaat'
        FROM branches b
        where b.status = 'active'  ${branch_filter}
        ;";
        // echo '<pre>';  echo $sql; die;
        $query = $this->entityManager->createQuery($sql);
        
        $rsm = new ResultSetMapping();            
        $rsm->addScalarResult('no_of_imarat', 'no_of_imarat');
        $rsm->addScalarResult('no_of_jamats', 'no_of_jamats');
        $rsm->addScalarResult('no_of_halqajaat', 'no_of_halqajaat');

        $query = $this->entityManager->createNativeQuery($sql, $rsm);  
        $data = $query->getResult();
        return $data[0];
    }

    public function allDepartmentSummaryReport($data, $sort_by_level=false)
    {
        $srvOffice = $this->serviceLocator->get('OfficeAssignmentService');
        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
        $user_branches = array_keys($srvOffice->getBranchesWithOffices($current_user,true, null, 'force-grandchildren')); 

        if( !empty($data['branch']) ) {
            $user_branches = array_intersect($user_branches, explode(',', $data['branch']));
        }
        
        $branches_summary = $this->allDepartmentSummaryReport_branches_summary($user_branches);
          
        if(empty($report_statuses)){
            $report_statuses = array('completed','verified','received'); 
        }      
        else { $report_statuses= explode(',',$data['report_status']);
        }  
        // $start_date= (array)Period::getStartOfYear($data['period_from']); 
        // $end_date= (array)Period::getEndOfYear($data['year']);
        $months = [];

        $temp_start_date = strtotime($data['period_from'].'-01');
        $temp_end_date = strtotime($data['period_to'].'-01');
        do {
            $months[] = ['Y'=>date('Y', $temp_start_date), 'M'=>date('m', $temp_start_date)];
            $temp_start_date = strtotime( date('Y-m-d',$temp_start_date) . ' +1 MONTH');

            if( count($months) >= 12 ) break;
        } while($temp_start_date <= $temp_end_date);

        $questions = [];

        # General Secretary
        $did = 5;
        $questions[$did]['department'] = 'General Secretary';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q152i']['caption'] = 'No. of Majlis ‘Āmila Meetings held in Jamā‘ats';
        $questions[$did]['questions']['Q152ii']['caption'] = 'No. of Majlis ‘Āmila Meetings held in Halqas';
        $questions[$did]['questions']['Q158']['caption'] = 'No. of Ijlas ‘Ām held';

        # Tablīgh
        $did = 13;
        $questions[$did]['department'] = 'Tablīgh';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q3099']['caption'] = 'Tablīgh sub-committee meeting held';
        $questions[$did]['questions']['Q1648']['caption'] = 'No. of Jamā‘at members involved in One Day a Week Tablīgh programs';
        $questions[$did]['questions']['Q1649']['caption'] = 'No. of Jamā‘at members motivated for Tablīgh activities';
        $questions[$did]['questions']['Q1652']['caption'] = 'No. of Dā‘iān Ilallāh';
        $questions[$did]['questions']['Q1654']['caption'] = 'No. of Dā‘iān Ilallāh classes held';
        $questions[$did]['questions']['Q1661']['caption'] = 'No. of active Tablīgh contacts';
        $questions[$did]['questions']['Q1662']['caption'] = 'No. of Exhibitions / Open Houses held';
        $questions[$did]['questions']['Q1669']['caption'] = 'Number of copies of the Holy Qur’ān gifted';
        $questions[$did]['questions']['Q1677']['caption'] = 'No. of Books distributed';
        $questions[$did]['questions']['Q1684']['caption'] = 'No. of Flyers / leaflets distributed';
        $questions[$did]['questions']['Q1691']['caption'] = 'No. of people to whom Ahmadiyyat’s message was conveyed';
        $questions[$did]['questions']['Q1701']['caption'] = 'No. of Tablīgh Bookstalls held';
        $questions[$did]['questions']['Q1716']['caption'] = 'No. of Audio/Video media distributed';
        $questions[$did]['questions']['Q1749']['caption'] = 'No. of New Bai‘ats achieved';

        # Tarbiyat
        $did = 15;
        $questions[$did]['department'] = 'Tarbiyat';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q3189']['caption'] = 'Drugs | No. of new cases (this month)';
        $questions[$did]['questions']['Q3194']['caption'] = 'Alcohol | No. of new cases (this month)';
        $questions[$did]['questions']['Q3199']['caption'] = 'Illicit relationships | No. of new cases (this month)';
        $questions[$did]['questions']['Q3204']['caption'] = 'Un-Islamic Ways of Earning | No. of new cases (this month)';
        $questions[$did]['questions']['Q3209']['caption'] = 'Financial disputes | No. of new cases (this month)';
        $questions[$did]['questions']['Q3214']['caption'] = 'Business and Jobs consisting of Alcohol and Pork | No. of new cases (this month)';
        $questions[$did]['questions']['Q3219']['caption'] = 'Purdah | No. of new cases (this month)';
        $questions[$did]['questions']['Q3224']['caption'] = 'Matrimonial issues | No. of new cases (this month)';
        $questions[$did]['questions']['Q3229']['caption'] = 'Family Issues other than Matrimonial Issues | No. of new cases (this month)';
        $questions[$did]['questions']['Q3234']['caption'] = 'In-active members | No. of new cases (this month)';
        $questions[$did]['questions']['Q3239']['caption'] = 'Other | No. of new cases (this month)';

        # Ta‘līm
        $did = 12;
        $questions[$did]['department'] = 'Ta‘līm';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q2994']['caption'] = 'Educational events held to promote religious, secular and academic education';
        $questions[$did]['questions']['Q2995']['caption'] = 'Educational events held to promote religious, secular and academic education (Attendance)';
        $questions[$did]['questions']['Q1830']['caption'] = 'No. of members who demonstrated outstanding academic performance';
        $questions[$did]['questions']['Q1831']['caption'] = 'No. of members who did not complete high school';
        $questions[$did]['questions']['Q1832']['caption'] = 'No. of members helped in continuing their education';
        $questions[$did]['questions']['Q1835']['caption'] = 'No. of events held for the promotion of higher education and science';

        # Isha‘at
        $did = 6;
        $questions[$did]['department'] = 'Isha‘at';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q1300']['caption'] = 'No. of active bookstalls';
        $questions[$did]['questions']['Q1306']['caption'] = 'No. of books provided to other departments - Tablīgh, Lajna, MKAC etc.';
        $questions[$did]['questions']['Q1366']['caption'] = 'No. of Exhibitions of the Holy Qur’ān held';
        $questions[$did]['questions']['Q1373']['caption'] = 'No. of copies of the Holy Qur’ān sold';
        $questions[$did]['questions']['Q1380']['caption'] = 'No. of copies of the Holy Qur’ān gifted';

        # Sam‘i wa Basari
        $did = 10;
        $questions[$did]['department'] = 'Sam‘i wa Basari';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q1536']['caption'] = 'No. of members who watch MTA on digital media (mta.tv, YouTube etc.)';        
        $questions[$did]['questions']['Q1544']['caption'] = 'No. of events conducted using Audio or Video conferencing';        

        # Rishta Nata
        $did = 9;
        $questions[$did]['department'] = 'Rishta Nata';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q1443']['caption'] = 'No. of marriageable male members';        
        $questions[$did]['questions']['Q1444']['caption'] = 'No. of marriageable female members';        

        # Umūr Khārijiyya
        $did = 17;
        $questions[$did]['department'] = 'Umūr Khārijiyya';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q2055']['caption'] = 'No. of Religious organizations your department is in regular contact';        
        $questions[$did]['questions']['Q2111']['caption'] = 'No. of non-Jamā‘at programs your department attended';        

        # Umūr ‘Āmma
        $did = 16;
        $questions[$did]['department'] = 'Umūr ‘Āmma';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q1904']['caption'] = 'No. of opportunities communicated to members regarding jobs, trade, new business etc.';        

        # Ḍiyāfat
        $did = 3;
        $questions[$did]['department'] = 'Ḍiyāfat';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q1280']['caption'] = 'No. of accommodation request(s) fulfilled';        
        $questions[$did]['questions']['Q1282']['caption'] = 'No. of transportation request(s) fulfilled';        
        $questions[$did]['questions']['Q1284']['caption'] = 'No. of new immigrants arrived in your area';        

        # Mal
        $did = 4;
        $questions[$did]['department'] = 'Mal';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q1188']['caption'] = 'No. of earning members';
        $questions[$did]['questions']['Q1195']['caption'] = 'No. of members who gave budget based on \'TRUE\' Income';
        $questions[$did]['questions']['Q1196']['caption'] = 'No. of earning members contacted for declaring true Income';

        # Add. Secretary Māl
        $did = 1;
        $questions[$did]['department'] = 'Add. Secretary Māl';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q1250']['caption'] = 'No. of members who started paying ba-sharah chanda (based on true net income)';

        # Waṣāyā
        $did = 21;
        $questions[$did]['department'] = 'Waṣāyā';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q2559']['caption'] = 'No. of Mūṣī';
        $questions[$did]['questions']['Q2566']['caption'] = 'No. of Wasiyyat applications submitted';
        $questions[$did]['questions']['Q2573']['caption'] = 'No. of completed Jeem forms (Schedule C) submitted';

        # WaṣāTa‘līmul Qur’ān Waqf ‘Ārḍiyā
        $did = 23;
        $questions[$did]['department'] = 'Ta‘līmul Qur’ān Waqf ‘Ārḍi';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q1850']['caption'] = 'No. of members who know correct recitation of the Holy Qur’ān';
        $questions[$did]['questions']['Q1883']['caption'] = 'No. of certified Holy Qur’ān teachers';
        $questions[$did]['questions']['Q1885']['caption'] = 'No. of members participated in Waqf ‘Ārḍi';

        # Taḥrīk Jadīd
        $did = 14;
        $questions[$did]['department'] = 'Taḥrīk Jadīd';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q3008']['caption'] = 'No. of members informed regarding Taḥrīk Jadīd Scheme, its demands, worldwide activities and achievements';

        # Waqf Jadīd
        $did = 18;
        $questions[$did]['department'] = 'Waqf Jadīd';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q2140']['caption'] = 'No. of members informed regarding objectives of Waqf Jadīd scheme';
        $questions[$did]['questions']['Q2142']['caption'] = 'No. of members informed about the directives of Huzoor Anwar (aa) / instructions of Markaz regarding Waqf Jadīd scheme';

        # Add. Sec. Tarbiyat and Waqf Jadīd Nau Muba’i’in
        $did = 19;
        $questions[$did]['department'] = 'Add. Sec. Tarbiyat and Waqf Jadīd Nau Muba’i’in';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q1111']['caption'] = 'No. of Nau Muba’i‘in';
        $questions[$did]['questions']['Q1112']['caption'] = 'No. of Nau Muba’i‘in who learned the aims, objects of Waqf Jadīd and system of the Jama‘at';
        $questions[$did]['questions']['Q1113']['caption'] = 'No. of Nau Muba’i‘in who received proper Tarbiyat and they are informed of their obligations and responsibilities as Ahmadis';
        $questions[$did]['questions']['Q1115']['caption'] = 'No. of meeting(s) / class(es) held with Nau Muba’i‘in';
        $questions[$did]['questions']['Q1136']['caption'] = 'No. of sessions (individual or combined) arranged for learning the recitation and meanings of the Holy Qur’ān';
        $questions[$did]['questions']['Q1137']['caption'] = 'No. of Nau Muba’i‘in participating in financial sacrifices towards Chanda Waqf Jadīd';
        $questions[$did]['questions']['Q1138']['caption'] = 'No. of lost Nau Muba’i‘in contacted';

        # Waqf Nau
        $did = 20;
        $questions[$did]['department'] = 'Waqf Nau';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q2170']['caption'] = 'No. of Wāqifīn Nau';
        $questions[$did]['questions']['Q2178']['caption'] = 'No. who are regular in 5 daily prayers';
        $questions[$did]['questions']['Q2193']['caption'] = 'No. who recite the Holy Qur’ān daily';
        $questions[$did]['questions']['Q2208']['caption'] = 'No. who write at least one letter monthly to Huzoor Anwar (aa)';
        $questions[$did]['questions']['Q2223']['caption'] = 'No. who listen to Friday sermon';
        $questions[$did]['questions']['Q2238']['caption'] = 'No. who have updated portfolio/file';
        $questions[$did]['questions']['Q2253']['caption'] = 'No. who are learning a new language';
        $questions[$did]['questions']['Q2283']['caption'] = 'No. who read Waqf Nau Magazine';
        $questions[$did]['questions']['Q2328']['caption'] = 'No. of Counseling / Career Guidance Session held';

        # Zirā‘at
        $did = 22;
        $questions[$did]['department'] = 'Zirā‘at';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q2537']['caption'] = 'No. of members involved in agricultural pursuits';

        # Ṣan‘at-o-Tijārat
        $did = 11;
        $questions[$did]['department'] = 'Ṣan‘at-o-Tijārat';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q1576']['caption'] = 'No. of unemployed members looking for work';
        $questions[$did]['questions']['Q1583']['caption'] = 'No. of members in managerial jobs';
        $questions[$did]['questions']['Q1590']['caption'] = 'No. of members in the manufacturing business';
        $questions[$did]['questions']['Q1597']['caption'] = 'No. of members in skilled trades';
        $questions[$did]['questions']['Q1604']['caption'] = 'No. of members owning businesses';
        $questions[$did]['questions']['Q1611']['caption'] = 'No. of members provided help in establishing businesses';
        $questions[$did]['questions']['Q1618']['caption'] = 'No. of members assisted / guided in getting employment (in coordination with Umūr ‘Āmma Department)';
        $questions[$did]['questions']['Q1625']['caption'] = 'No. of members informed about opportunities in trade and industry across Canada';

        # Muḥāsib
        $did = 8;
        $questions[$did]['department'] = 'Muḥāsib';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q2877']['caption'] = 'No. of receipt books in your custody';

        # Amīn
        $did = 2;
        $questions[$did]['department'] = 'Amīn';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q2757']['caption'] = 'Total amount of payments (petty cash) made to Muḥāsib/ Secretary Māl towards expenses?';
        $questions[$did]['questions']['Q2776']['caption'] = 'Value of non-cash items (no assets should be kept in custody)';

        # Internal Auditor
        $did = 24;
        $questions[$did]['department'] = 'Internal Auditor';
        $questions[$did]['reports_required'] = $this->allDepartmentSummaryReport_reports_required($did, $user_branches);
        foreach($months as $m) {
            $questions[$did]['reports_received'][ "{$m['Y']}-{$m['M']}"] = $this->allDepartmentSummaryReport_reports_received($m, $did, $report_statuses, $user_branches);
        }
        $questions[$did]['questions']['Q2820']['caption'] = 'No. of department / organization whose audit is done';

        foreach($months as $m) {

            $questions[5]['questions']['Q152i']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 5, 152, ['Jama`at','Imarat'], 'YES_NO_COUNT');
            $questions[5]['questions']['Q152ii']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 5, 152, ['Halqa'], 'YES_NO_COUNT');
            $questions[5]['questions']['Q158']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 5, 158, [], 'YES_NO_COUNT');
            
            $questions[13]['questions']['Q3099']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 3099, [], 'YES_NO_COUNT');
            $questions[13]['questions']['Q1648']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1648, [], 'SUM');
            $questions[13]['questions']['Q1649']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1649, [], 'SUM');
            $questions[13]['questions']['Q1652']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1652, [], 'SUM');
            $questions[13]['questions']['Q1654']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1654, [], 'SUM');
            $questions[13]['questions']['Q1654']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1657, [], 'SUM');
            $questions[13]['questions']['Q1661']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1661, [], 'SUM');
            $questions[13]['questions']['Q1662']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1662, [], 'SUM');
            $questions[13]['questions']['Q1662']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1665, [], 'SUM');
            $questions[13]['questions']['Q1669']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1669, [], 'SUM');
            $questions[13]['questions']['Q1669']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1672, [], 'SUM');
            $questions[13]['questions']['Q1677']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1677, [], 'SUM');
            $questions[13]['questions']['Q1677']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1680, [], 'SUM');
            $questions[13]['questions']['Q1684']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1684, [], 'SUM');
            $questions[13]['questions']['Q1684']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1688, [], 'SUM');
            $questions[13]['questions']['Q1691']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1691, [], 'SUM');
            $questions[13]['questions']['Q1691']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1700, [], 'SUM');
            $questions[13]['questions']['Q1701']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1701, [], 'SUM');
            $questions[13]['questions']['Q1701']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1705, [], 'SUM');
            $questions[13]['questions']['Q1716']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1716, [], 'SUM');
            $questions[13]['questions']['Q1716']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1719, [], 'SUM');
            $questions[13]['questions']['Q1749']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1749, [], 'SUM');
            $questions[13]['questions']['Q1749']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 13, 1752, [], 'SUM');
            
            $questions[15]['questions']['Q3189']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 15, 3189, [], 'SUM');
            $questions[15]['questions']['Q3194']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 15, 3194, [], 'SUM');
            $questions[15]['questions']['Q3199']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 15, 3199, [], 'SUM');
            $questions[15]['questions']['Q3204']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 15, 3204, [], 'SUM');
            $questions[15]['questions']['Q3209']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 15, 3209, [], 'SUM');
            $questions[15]['questions']['Q3214']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 15, 3214, [], 'SUM');
            $questions[15]['questions']['Q3219']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 15, 3219, [], 'SUM');
            $questions[15]['questions']['Q3224']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 15, 3224, [], 'SUM');
            $questions[15]['questions']['Q3229']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 15, 3229, [], 'SUM');
            $questions[15]['questions']['Q3234']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 15, 3234, [], 'SUM');
            $questions[15]['questions']['Q3239']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 15, 3239, [], 'SUM');

            $questions[12]['questions']['Q2994']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 12, 2994, [], 'COUNT_NON_EMPTY');
            $questions[12]['questions']['Q2995']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 12, 2995, [], 'SUM_ALL');
            $questions[12]['questions']['Q1830']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 12, 1830, [], 'SUM');
            $questions[12]['questions']['Q1831']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 12, 1831, [], 'SUM');
            $questions[12]['questions']['Q1832']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 12, 1832, [], 'SUM');
            $questions[12]['questions']['Q1835']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 12, 1835, [], 'COUNT_NON_EMPTY');
            
            $questions[6]['questions']['Q1300']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 6, 1300, [], 'SUM');
            $questions[6]['questions']['Q1306']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 6, 1306, [], 'SUM');
            $questions[6]['questions']['Q1366']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 6, 1366, [], 'SUM');
            $questions[6]['questions']['Q1366']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 6, 1369, [], 'SUM');
            $questions[6]['questions']['Q1373']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 6, 1373, [], 'SUM');
            $questions[6]['questions']['Q1373']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 6, 1376, [], 'SUM');
            $questions[6]['questions']['Q1380']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 6, 1380, [], 'SUM');
            $questions[6]['questions']['Q1380']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 6, 1383, [], 'SUM');
            
            $questions[10]['questions']['Q1536']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 10, 1536, [], 'SUM');
            $questions[10]['questions']['Q1544']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 10, 1544, [], 'SUM');
            
            $questions[9]['questions']['Q1443']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 9, 1443, [], 'SUM');
            $questions[9]['questions']['Q1444']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 9, 1444, [], 'SUM');
            
            $questions[17]['questions']['Q2055']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 17, 2055, [], 'COUNT_NON_EMPTY');
            $questions[17]['questions']['Q2111']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 17, 2111, [], 'SUM');
            
            $questions[16]['questions']['Q1904']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 16, 1904, [], 'SUM');
            $questions[16]['questions']['Q1904']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 16, 1907, [], 'SUM');
            
            $questions[3]['questions']['Q1280']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 3, 1280, [], 'SUM');
            $questions[3]['questions']['Q1282']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 3, 1282, [], 'SUM');
            $questions[3]['questions']['Q1284']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 3, 1284, [], 'SUM');
            
            $questions[4]['questions']['Q1188']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 4, 1188, [], 'SUM');
            $questions[4]['questions']['Q1188']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 4, 1191, [], 'SUM');
            $questions[4]['questions']['Q1195']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 4, 1195, [], 'SUM');
            $questions[4]['questions']['Q1196']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 4, 1196, [], 'SUM');
            
            $questions[1]['questions']['Q1250']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 1, 1250, [], 'SUM');
            
            $questions[21]['questions']['Q2559']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 21, 2559, [], 'SUM');
            $questions[21]['questions']['Q2559']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 21, 2562, [], 'SUM');
            $questions[21]['questions']['Q2566']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 21, 2566, [], 'SUM');
            $questions[21]['questions']['Q2566']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 21, 2569, [], 'SUM');
            $questions[21]['questions']['Q2573']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 21, 2573, [], 'SUM');
            $questions[21]['questions']['Q2573']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 21, 2576, [], 'SUM');
            
            $questions[23]['questions']['Q1850']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 23, 1850, [], 'SUM');
            $questions[23]['questions']['Q1850']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 23, 1853, [], 'SUM');
            $questions[23]['questions']['Q1883']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 23, 1883, [], 'SUM');
            $questions[23]['questions']['Q1885']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 23, 1885, [], 'SUM');
            
            $questions[14]['questions']['Q3008']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 14, 3008, [], 'SUM');
            $questions[14]['questions']['Q3008']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 14, 3011, [], 'SUM');
            
            $questions[18]['questions']['Q2140']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 18, 2140, [], 'SUM');
            $questions[18]['questions']['Q2142']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 18, 2142, [], 'SUM');
            
            $questions[19]['questions']['Q1111']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 19, 1111, [], 'SUM');
            $questions[19]['questions']['Q1111']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 19, 1145, [], 'SUM');
            $questions[19]['questions']['Q1112']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 19, 1112, [], 'SUM');
            $questions[19]['questions']['Q1112']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 19, 1151, [], 'SUM');
            $questions[19]['questions']['Q1113']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 19, 1113, [], 'SUM');
            $questions[19]['questions']['Q1113']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 19, 1157, [], 'SUM');
            $questions[19]['questions']['Q1115']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 19, 1115, [], 'COUNT_NON_EMPTY');
            $questions[19]['questions']['Q1136']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 19, 1136, [], 'SUM');
            $questions[19]['questions']['Q1136']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 19, 1169, [], 'SUM');
            $questions[19]['questions']['Q1137']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 19, 1137, [], 'SUM');
            $questions[19]['questions']['Q1137']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 19, 1175, [], 'SUM');
            $questions[19]['questions']['Q1138']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 19, 1138, [], 'SUM');
            $questions[19]['questions']['Q1138']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 19, 1181, [], 'SUM');
            
            $questions[20]['questions']['Q2170']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2170, [], 'SUM');
            $questions[20]['questions']['Q2170']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2171, [], 'SUM');
            $questions[20]['questions']['Q2170']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2172, [], 'SUM');
            $questions[20]['questions']['Q2170']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2173, [], 'SUM');
            $questions[20]['questions']['Q2170']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2174, [], 'SUM');
            $questions[20]['questions']['Q2170']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2175, [], 'SUM');
            $questions[20]['questions']['Q2170']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2176, [], 'SUM');
            $questions[20]['questions']['Q2178']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2178, [], 'SUM');
            $questions[20]['questions']['Q2178']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2179, [], 'SUM');
            $questions[20]['questions']['Q2178']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2180, [], 'SUM');
            $questions[20]['questions']['Q2178']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2181, [], 'SUM');
            $questions[20]['questions']['Q2178']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2182, [], 'SUM');
            $questions[20]['questions']['Q2178']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2183, [], 'SUM');
            $questions[20]['questions']['Q2178']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2184, [], 'SUM');
            $questions[20]['questions']['Q2193']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2193, [], 'SUM');
            $questions[20]['questions']['Q2193']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2194, [], 'SUM');
            $questions[20]['questions']['Q2193']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2195, [], 'SUM');
            $questions[20]['questions']['Q2193']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2196, [], 'SUM');
            $questions[20]['questions']['Q2193']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2197, [], 'SUM');
            $questions[20]['questions']['Q2193']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2198, [], 'SUM');
            $questions[20]['questions']['Q2193']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2199, [], 'SUM');
            $questions[20]['questions']['Q2208']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2208, [], 'SUM');
            $questions[20]['questions']['Q2208']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2209, [], 'SUM');
            $questions[20]['questions']['Q2208']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2210, [], 'SUM');
            $questions[20]['questions']['Q2208']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2211, [], 'SUM');
            $questions[20]['questions']['Q2208']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2212, [], 'SUM');
            $questions[20]['questions']['Q2208']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2213, [], 'SUM');
            $questions[20]['questions']['Q2208']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2214, [], 'SUM');
            $questions[20]['questions']['Q2223']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2223, [], 'SUM');
            $questions[20]['questions']['Q2223']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2224, [], 'SUM');
            $questions[20]['questions']['Q2223']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2225, [], 'SUM');
            $questions[20]['questions']['Q2223']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2226, [], 'SUM');
            $questions[20]['questions']['Q2223']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2227, [], 'SUM');
            $questions[20]['questions']['Q2223']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2228, [], 'SUM');
            $questions[20]['questions']['Q2223']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2229, [], 'SUM');
            $questions[20]['questions']['Q2238']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2238, [], 'SUM');
            $questions[20]['questions']['Q2238']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2239, [], 'SUM');
            $questions[20]['questions']['Q2238']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2240, [], 'SUM');
            $questions[20]['questions']['Q2238']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2241, [], 'SUM');
            $questions[20]['questions']['Q2238']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2242, [], 'SUM');
            $questions[20]['questions']['Q2238']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2243, [], 'SUM');
            $questions[20]['questions']['Q2238']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2244, [], 'SUM');
            $questions[20]['questions']['Q2253']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2253, [], 'SUM');
            $questions[20]['questions']['Q2253']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2254, [], 'SUM');
            $questions[20]['questions']['Q2253']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2255, [], 'SUM');
            $questions[20]['questions']['Q2253']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2256, [], 'SUM');
            $questions[20]['questions']['Q2253']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2257, [], 'SUM');
            $questions[20]['questions']['Q2253']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2258, [], 'SUM');
            $questions[20]['questions']['Q2253']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2259, [], 'SUM');
            $questions[20]['questions']['Q2283']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2283, [], 'SUM');
            $questions[20]['questions']['Q2283']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2284, [], 'SUM');
            $questions[20]['questions']['Q2283']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2285, [], 'SUM');
            $questions[20]['questions']['Q2283']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2286, [], 'SUM');
            $questions[20]['questions']['Q2283']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2287, [], 'SUM');
            $questions[20]['questions']['Q2283']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2288, [], 'SUM');
            $questions[20]['questions']['Q2283']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2289, [], 'SUM');
            $questions[20]['questions']['Q2328']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2328, [], 'SUM');
            $questions[20]['questions']['Q2328']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2329, [], 'SUM');
            $questions[20]['questions']['Q2328']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2330, [], 'SUM');
            $questions[20]['questions']['Q2328']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2331, [], 'SUM');
            $questions[20]['questions']['Q2328']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2332, [], 'SUM');
            $questions[20]['questions']['Q2328']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2333, [], 'SUM');
            $questions[20]['questions']['Q2328']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2334, [], 'SUM');
            $questions[20]['questions']['Q2328']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2352, [], 'SUM');
            $questions[20]['questions']['Q2328']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2353, [], 'SUM');
            $questions[20]['questions']['Q2328']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2354, [], 'SUM');
            $questions[20]['questions']['Q2328']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2355, [], 'SUM');
            $questions[20]['questions']['Q2328']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2356, [], 'SUM');
            $questions[20]['questions']['Q2328']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2357, [], 'SUM');
            $questions[20]['questions']['Q2328']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 20, 2358, [], 'SUM');
            
            $questions[22]['questions']['Q2537']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 22, 2537, [], 'SUM');
            $questions[22]['questions']['Q2537']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 22, 2540, [], 'SUM');
            
            $questions[11]['questions']['Q1576']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1576, [], 'SUM');
            $questions[11]['questions']['Q1576']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1579, [], 'SUM');
            $questions[11]['questions']['Q1583']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1583, [], 'SUM');
            $questions[11]['questions']['Q1583']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1586, [], 'SUM');
            $questions[11]['questions']['Q1590']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1590, [], 'SUM');
            $questions[11]['questions']['Q1590']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1593, [], 'SUM');
            $questions[11]['questions']['Q1597']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1597, [], 'SUM');
            $questions[11]['questions']['Q1597']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1600, [], 'SUM');
            $questions[11]['questions']['Q1604']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1604, [], 'SUM');
            $questions[11]['questions']['Q1604']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1607, [], 'SUM');
            $questions[11]['questions']['Q1611']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1611, [], 'SUM');
            $questions[11]['questions']['Q1611']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1614, [], 'SUM');
            $questions[11]['questions']['Q1618']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1618, [], 'SUM');
            $questions[11]['questions']['Q1618']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1621, [], 'SUM');
            $questions[11]['questions']['Q1625']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1625, [], 'SUM');
            $questions[11]['questions']['Q1625']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 11, 1628, [], 'SUM');
            
            $questions[8]['questions']['Q2877']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 8, 2877, [], 'SUM');
            $questions[8]['questions']['Q2877']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 8, 2880, [], 'SUM');
            
            $questions[2]['questions']['Q2757']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 2, 2757, [], 'SUM');
            $questions[2]['questions']['Q2757']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 2, 2760, [], 'SUM');
            $questions[2]['questions']['Q2776']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 2, 2776, [], 'SUM');
            $questions[2]['questions']['Q2776']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 2, 2777, [], 'SUM');
            $questions[2]['questions']['Q2776']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 2, 2778, [], 'SUM');
            $questions[2]['questions']['Q2776']['data'][ "{$m['Y']}-{$m['M']}" ] += $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 2, 2779, [], 'SUM');
         
            $questions[24]['questions']['Q2820']['data'][ "{$m['Y']}-{$m['M']}" ] = $this->allDepartmentSummaryReport_data($m, $data, $user_branches, $report_statuses, 24, 2820, [], 'COUNT_NON_EMPTY');


           

        }
        // print_r($questions); die;
        
        
        $sparkline = new Sparkline();
        return ['data'=>$questions, 'months'=>$months, 'branches_summary'=>$branches_summary, 'sparkline'=>$sparkline, 'year'=>$data['year']];
        
    }


    public function getTarbiyatReportData($data, $sort_by_level=false)
    {
        $srvOffice = $this->serviceLocator->get('OfficeAssignmentService');
        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
        $user_branches = array_keys($srvOffice->getBranchesWithOffices($current_user,true, [5,15,26,1001,1005], 'force-grandchildren'));   
        $reporting_month = date('M-Y', strtotime("{$data['year']}-{$data['month']}-01"));     
        if(empty($report_statuses)){
            $report_statuses = array('completed','verified','received'); 
        }      
        else { $report_statuses= explode(',',$data['report_status']);
        }  

        $filters = ' where 1 ';
        $filters .= isset($data['office']) && !empty($data['office']) ? " and a.department_id in ( {$data['office']} )" : '';
        $filters .= isset($data['branch']) && !empty($data['branch']) ? " and a.ar_branch_id in ( {$data['branch']} )" : " and a.ar_branch_id in ( ".implode(',', $user_branches)." ) ";
        //$filters .= " and a.status in ('".implode("','", $report_statuses)."') ";

        $sql = "
        select ar_branch_id,   ar_department_id, a.display_name, a.user_id, a.department_name, a.branch_id, a.branch_name, a.parent_branch_id, a.parent_branch_name, a.branch_level, a.department_id, a.department_name, a.user_id, a.report_id, a.status, a.prayers, a.fajr, a.maghrib_isha, a.quran, a.friday_prayer, a.friday_sermon, a.nawafil
        from  (
            select ar.*, r.*, us.id 'user_id', us.display_name, d.department_name, b.branch_name, b.branch_level, b.parent_branch_id, bp.branch_name as 'parent_branch_name', a_prayers.value 'prayers', a_fajr.value 'fajr', a_mag_isha.value 'maghrib_isha', a_quran.value 'quran', a_fri_pr.value 'friday_prayer', a_fri_sr.value 'friday_sermon', a_nawafil.value 'nawafil'
            from (
                select d.id as 'ar_department_id', b.id as 'ar_branch_id' from departments d, branches b where d.reportable = 1 and b.status = 'active'
            ) ar
            left join (
                select * from (select * from office_assignments where status=  'active'  order by id desc limit 10000000)t group by branch_id, department_id
            ) u on ar.ar_branch_id = u.branch_id and ar.ar_department_id = u.department_id
            left join (
                select * from (
                    SELECT r.id as report_id, r.period_from, r.branch_id, r.department_id, r.status FROM reports r where r.period_from = '{$reporting_month}' and r.status in ('completed','verified','received') order by r.date_modified desc limit 10000
                ) r group by r.branch_id, r.department_id
            ) r on ar.ar_branch_id = r.branch_id and ar.ar_department_id = r.department_id
            LEFT JOIN branches b on ar.ar_branch_id = b.id
            LEFT JOIN branches bp on b.parent_branch_id = bp.id
            LEFT JOIN departments d on ar.ar_department_id = d.id
            LEFT JOIN answers a_prayers on r.report_id = a_prayers.report_id and a_prayers.question_id in (627,805,806,807,808,809,810,811,812,813,814,815,816,817,818,819,820,821,822,823,824,825,826,827)
            LEFT JOIN answers a_fajr on r.report_id = a_fajr.report_id and a_fajr.question_id in (3296,3297,3298,3299,3300,3301,3302,3303,3304,3305,3306,3307,3308,3309,3310,3311,3312,3313,3314,3315,3316,3317,3318,3319)
            LEFT JOIN answers a_mag_isha on r.report_id = a_mag_isha.report_id and a_mag_isha.question_id in (3327,3328,3329,3330,3331,3332,3333,3334,3335,3336,3337,3338,3339,3340,3341,3342,3343,3344,3345,3346,3347,3348,3349,3350)
            LEFT JOIN answers a_quran on r.report_id = a_quran.report_id and a_quran.question_id in (3358,3359,3360,3361,3362,3363,3364,3365,3366,3367,3368,3369,3370,3371,3372,3373,3374,3375,3376,3377,3378,3379,3380,3381)
            LEFT JOIN answers a_fri_pr on r.report_id = a_fri_pr.report_id and a_fri_pr.question_id in (579,580,581,582,583,584,585,586,587,588,589,590,591,592,593,594,595,596,597,598,599,600,601,602)    
            LEFT JOIN answers a_fri_sr on r.report_id = a_fri_sr.report_id and a_fri_sr.question_id in (603,604,605,606,607,608,609,610,611,612,613,614,615,616,617,618,619,620,621,622,623,624,625,626)
            LEFT JOIN answers a_nawafil on r.report_id = a_nawafil.report_id and a_nawafil.question_id in (3644,3645,3646,3647,3648,3649,3650,3651,3652,3653,3654,3655,3656,3657,3658,3659,3660,3661,3662,3663,3664,3665,3666,3667)
            LEFT JOIN users us on u.user_id = us.id
        ) a 
        {$filters}
        
        ";

        if( $sort_by_level )    $sql .= " order by FIELD(a.branch_level, 'Markaz', 'Imarat', 'Jama`at', 'Halqa'); ";
        else                    $sql .= " order by a.parent_branch_name, a.branch_name ;";

        // echo '<pre>';  echo $sql; die;
        $query = $this->entityManager->createQuery($sql);
        
        $rsm = new ResultSetMapping();            
        $columns = ['ar_branch_id', 'ar_department_id', 'display_name', 'user_id', 'department_name', 'branch_id', 'branch_name', 'parent_branch_id', 'parent_branch_name', 'branch_level', 'department_id', 'department_name', 'user_id', 'report_id', 'status', 'prayers', 'fajr', 'maghrib_isha', 'quran', 'friday_prayer', 'friday_sermon', 'nawafil'];
        foreach( $columns as $col ) {
            $rsm->addScalarResult($col, $col);
        }
      
        $query = $this->entityManager->createNativeQuery($sql, $rsm);  
        $data = $query->getResult();
        $adapter = $this->serviceLocator->get('DoctrineEncryptAdapter');

        $processedData = array();
        foreach($data as $val) {
            
            $bAddResults = false;
            $val['display_name'] = trim($adapter->decrypt( $val['display_name'] ));
            $val['prayers'] = trim($adapter->decrypt( $val['prayers'] ));
            $val['fajr'] = trim($adapter->decrypt( $val['fajr'] ));
            $val['maghrib_isha'] = trim($adapter->decrypt( $val['maghrib_isha'] ));
            $val['quran'] = trim($adapter->decrypt( $val['quran'] ));
            $val['friday_prayer'] = trim($adapter->decrypt( $val['friday_prayer'] ));
            $val['friday_sermon'] = trim($adapter->decrypt( $val['friday_sermon'] ));
            $val['nawafil'] = trim($adapter->decrypt( $val['nawafil'] ));

            $processedData['info']['branches'][$val['ar_branch_id']] = 1;
            if(!empty($val['report_id'])) {
                $processedData['info']['branches_submitted'][$val['branch_id']] = 1;
            }

            if(empty($val['user_id'])){
                if(empty($val['report_id'])) {
                    $processedData['info']['amila_members_not_assigned'] = (isset($processedData['info']['amila_members_not_assigned']) ? ($processedData['info']['amila_members_not_assigned']+1) : 1);
                }
                else {
                    $processedData['info']['amila_members_not_assigned_but_submitted'] = (isset($processedData['info']['amila_members_not_assigned_but_submitted']) ? ($processedData['info']['amila_members_not_assigned_but_submitted']+1) : 1);
                    $bAddResults = true;
                }                
            }
            else {
                if(empty($val['report_id'])) {
                    $processedData['info']['amila_members_not_submitted'][$val['user_id']] = 1;
                }
                else {
                    $processedData['info']['amila_members_submitted'][$val['user_id']] = 1;
                    $bAddResults = true;
                }
            }            

            if( $bAddResults ) {
                // if we hvae user_id, it means secretary assigned and we can uniquely identify. pick higest number.
                $key = (!empty($val['user_id']) ? $val['user_id'] : 'rep-'.$val['report_id']);
                if( isset($processedData['data'][$key]) ) {
                    foreach(['fajr', 'maghrib_isha', 'quran', 'friday_prayer', 'friday_sermon', 'nawafil'] as $q) {
                        if( !($val[$q]==='' || is_null($val[$q])) ) {
                            $processedData['data'][$key][$q] = max(array($processedData['data'][$key][$q], $val[$q]));
                        }
                    }
                }
                else {
                    $processedData['data'][$key] = $val;
                }
            }
        }   
        $processedData['info']['branches'] = count($processedData['info']['branches']);
        $processedData['info']['branches_submitted'] = count($processedData['info']['branches_submitted']);
        $processedData['info']['amila_members_submitted'] = count($processedData['info']['amila_members_submitted']);
        $processedData['info']['amila_members_not_submitted'] = count($processedData['info']['amila_members_not_submitted']);
        return $processedData;
        
    }

    public function getTarbiyatReportOverAll($data)
    {
        $data = $this->getTarbiyatReportData($data, true);
        // echo '<pre>'; print_r( $data); echo '</pre>'; die;

        $srvOffice = $this->serviceLocator->get('OfficeAssignmentService');
        $branchSrv = $this->serviceLocator->get('BranchManagementService');

        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
        $user_branches = array_keys($srvOffice->getBranchesWithOffices($current_user,true, [5,15,26,1001,1005], 'force-grandchildren'));   

        $user_branches_detail = [];
        foreach($user_branches as $branch) {
            $branch_detail = $branchSrv->getBranch($branch);
            $user_branches_detail[$branch] = ['id'=>$branch, 'parent'=>($parent = empty($branch_detail->getParent()) ? 27 : $branch_detail->getParent()->getId()), 'name'=>$branch_detail->getBranchName(), 'level'=>$branch_detail->getBranchLevel()];
        }

        $sorted_brances = array();
        $first_parent_id = null;
        # 1: Add Markaz Data
        foreach($user_branches_detail as $key => $branch) {
            if($branch['level'] == 'Markaz') {
                $sorted_brances[$branch['id']] = $branch;
                $first_parent_id = $branch['id'];
                break;
            }
        }
        # 2: Add Imarat Data
        foreach($user_branches_detail as $key => $branch) {
            if($branch['level'] == 'Imarat' ) {
                if( isset($sorted_brances[$branch['parent']]) ) {
                    $sorted_brances[$branch['parent']]['child'][$branch['id']] = $branch;
                    if( empty($first_parent_id) ) $first_parent_id = $branch['parent'];
                }
                else {
                    $sorted_brances[$branch['id']] = $branch;
                    if( empty($first_parent_id) ) $first_parent_id = $branch['id'];
                }
            }
        }
        # 3: Add Jama`at Data
        foreach($user_branches_detail as $key => $branch) {
            if($branch['level'] == 'Jama`at') {
                if( isset($sorted_brances[$branch['parent']]) ) {
                    $sorted_brances[$branch['parent']]['child'][$branch['id']] = $branch;
                    if( empty($first_parent_id) ) $first_parent_id = $branch['parent'];
                }
                else {
                    $sorted_brances[$branch['id']] = $branch;
                    if( empty($first_parent_id) ) $first_parent_id = $branch['id'];
                }
            }
        }
        # 4: Add Halqa Data
        foreach($user_branches_detail as $key => $branch) {
            if($branch['level'] == 'Halqa') {
                if( isset($sorted_brances[$branch['parent']]) ) {
                    $sorted_brances[$branch['parent']]['child'][$branch['id']] = $branch;
                    if( empty($first_parent_id) ) $first_parent_id = $branch['parent'];
                }
                else if( !empty($first_parent_id) && isset($sorted_brances[$first_parent_id]['child'][$branch['parent']]) ) {
                    $sorted_brances[$first_parent_id]['child'][$branch['parent']]['child'][$branch['id']] = $branch;
                }
                else {
                    $sorted_brances[$branch['id']] = $branch;
                }
            }
        }

        function increment(&$processedData, $question, $index, &$val=null){
            if( !isset($processedData[$question][$index]) ) {
                $processedData[$question][$index] = 1;
            }
            else {
                $processedData[$question][$index]++;
            }

            if( !empty($val) ) {
                $val[$question] = $index;
            }
        }

        function summary(&$processedData, &$val, $ummary_idx='summary') 
        {
            if(!isset($processedData[$ummary_idx])) $processedData[$ummary_idx] = [];
            foreach(['prayers'] as $question) {
                /*if( $val[$question] !== '' )*/ increment($processedData[$ummary_idx], $question, 'data-for');
                if( $val[$question] === '' ) increment($processedData[$ummary_idx], $question, 'blank', $val);
                if( $val[$question] === 0 || $val[$question] === '0' ) increment($processedData[$ummary_idx], $question, 'zero', $val);
                if( $val[$question] == 1 ) increment($processedData[$ummary_idx], $question, '1', $val);
                if( $val[$question] == 2 ) increment($processedData[$ummary_idx], $question, '2', $val);
                if( $val[$question] == 3 ) increment($processedData[$ummary_idx], $question, '3', $val);
                if( $val[$question] == 4 ) increment($processedData[$ummary_idx], $question, '4', $val);
                if( $val[$question] >= 5 ) increment($processedData[$ummary_idx], $question, '5', $val);
            }            

            foreach(['fajr', 'maghrib_isha', 'quran', 'nawafil'] as $question) {
                /*if( $val[$question] !== '' )*/ increment($processedData[$ummary_idx], $question, 'data-for');
                if( $val[$question] === '' ) increment($processedData[$ummary_idx], $question, 'blank', $val);
                if( $val[$question] === 0 || $val[$question] === '0' ) increment($processedData[$ummary_idx], $question, 'zero', $val);
                if( $val[$question] == '1-10' ) increment($processedData[$ummary_idx], $question, '1-10', $val);
                if( $val[$question] == '11-20' ) increment($processedData[$ummary_idx], $question, '11-20', $val);
                if( $val[$question] == '21+' ) increment($processedData[$ummary_idx], $question, '21+', $val);
            }

            foreach(['friday_prayer', 'friday_sermon'] as $question) {
                /*if( $val[$question] !== '' )*/ increment($processedData[$ummary_idx], $question, 'data-for');
                if( $val[$question] === '' ) increment($processedData[$ummary_idx], $question, 'blank', $val);
                if( $val[$question] === 0 || $val[$question] === '0' ) increment($processedData[$ummary_idx], $question, 'zero', $val);
                if( $val[$question] == 1 ) increment($processedData[$ummary_idx], $question, '1', $val);
                if( $val[$question] == 2 ) increment($processedData[$ummary_idx], $question, '2', $val);
                if( $val[$question] == 3 ) increment($processedData[$ummary_idx], $question, '3', $val);
                //if( $val[$question] >= 1 && $val[$question] <= 3 ) increment($processedData[$ummary_idx], $question, '1-3');
                if( $val[$question] >= 4 ) increment($processedData[$ummary_idx], $question, 'All', $val);
            }
            
        }
        foreach($data['data'] as $member) {
            if( isset($sorted_brances[$member['branch_id']]) ) {
                summary($sorted_brances[$member['branch_id']], $_=$member, 'summary-markaz');
                summary($sorted_brances[$member['branch_id']], $member, 'summary');
                $sorted_brances[$member['branch_id']]['members'][] = $member;
            }
            else if( isset($sorted_brances[$first_parent_id]['child'][$member['branch_id']]) ) {
                summary($sorted_brances[$first_parent_id], $_=$member, 'summary-markaz');
                summary($sorted_brances[$first_parent_id]['child'][$member['branch_id']], $_=$member, 'summary-imarat');
                summary($sorted_brances[$first_parent_id]['child'][$member['branch_id']], $member, 'summary');
                $sorted_brances[$first_parent_id]['child'][$member['branch_id']]['members'][] = $member;
            }
            else if( isset($sorted_brances[$first_parent_id]['child'][$member['parent_branch_id']]['child'][$member['branch_id']]) ) {
                summary($sorted_brances[$first_parent_id], $_=$member, 'summary-markaz');
                summary($sorted_brances[$first_parent_id]['child'][$member['parent_branch_id']], $_=$member, 'summary-imarat');
                summary($sorted_brances[$first_parent_id]['child'][$member['parent_branch_id']]['child'][$member['branch_id']], $member, 'summary');
                $sorted_brances[$first_parent_id]['child'][$member['parent_branch_id']]['child'][$member['branch_id']]['members'][] = $member;
            }
        }

        //echo '<pre>'; print_r( $sorted_brances); echo '</pre>'; die;
        return $sorted_brances;
    }
    public function getTarbiyatReport($data)
    {
        $data = $this->getTarbiyatReportData($data);
        // echo '<pre>'; print_r($data); die;

        function increment(&$processedData, $k1, $k2){
            if( !isset($processedData['data'][$k1][$k2]) ) {
                $processedData['data'][$k1][$k2] = 1;
            }
            else {
                $processedData['data'][$k1][$k2]++;
            }
        }

        $processedData = [];
        foreach($data['data'] as &$val) {

            foreach(['prayers'] as $question) {
                if( $val[$question] !== '' ) increment($processedData, $question, 'data-for');
                if( $val[$question] === '' ) increment($processedData, $question, 'blank');
                if( $val[$question] === 0 || $val[$question] === '0' ) increment($processedData, $question, 'zero');
                if( $val[$question] == 1 ) increment($processedData, $question, '1');
                if( $val[$question] == 2 ) increment($processedData, $question, '2');
                if( $val[$question] == 3 ) increment($processedData, $question, '3');
                if( $val[$question] == 4 ) increment($processedData, $question, '4');
                if( $val[$question] >= 5 ) increment($processedData, $question, '5');
            }

            foreach(['fajr', 'maghrib_isha', 'quran', 'nawafil'] as $question) {
                if( $val[$question] !== '' ) increment($processedData, $question, 'data-for');
                if( $val[$question] === '' ) increment($processedData, $question, 'blank');
                if( $val[$question] === 0 || $val[$question] === '0' ) increment($processedData, $question, 'zero');
                if( $val[$question] == '1-10' ) increment($processedData, $question, '1-10');
                if( $val[$question] == '11-20' ) increment($processedData, $question, '11-20');
                if( $val[$question] == '21+'  ) increment($processedData, $question, '21+');
            }

            foreach(['friday_prayer', 'friday_sermon'] as $question) {
                if( $val[$question] !== '' ) increment($processedData, $question, 'data-for');
                if( $val[$question] === '' ) increment($processedData, $question, 'blank');
                if( $val[$question] === 0 || $val[$question] === '0' ) increment($processedData, $question, 'zero');
                if( $val[$question] == 1 ) increment($processedData, $question, '1');
                if( $val[$question] == 2 ) increment($processedData, $question, '2');
                if( $val[$question] == 3 ) increment($processedData, $question, '3');
                //if( $val[$question] >= 1 && $val[$question] <= 3 ) increment($processedData, $question, '1-3');
                if( $val[$question] >= 4 ) increment($processedData, $question, 'All');
            }
        }

        $processedData['info'] = $data['info'];
        return $processedData;                
    }

    public function getMonthlyReportStatus($data)
    {
        $year_s = $data['year'];
        $year_e = $data['year'] + 1;

        $columns = [];
        $starting_month = "{$year_s}-07-01";
        for( $i=0; $i<12; $i++ ) {
            $month = date('M-Y', strtotime($starting_month));

            $columns[] = " group_concat(if(t.period_code='{$month}',t.draft,NULL)) '{$month}-draft' ";
            $columns[] = " group_concat(if(t.period_code='{$month}',t.completed,NULL)) '{$month}-completed' ";
            $columns[] = " group_concat(if(t.period_code='{$month}',t.verified,NULL)) '{$month}-verified' ";
            $columns[] = " group_concat(if(t.period_code='{$month}',t.received,NULL)) '{$month}-received' ";

            $totalColumns[] = " sum(`{$month}-draft`) ";
            $totalColumns[] = " sum(`{$month}-completed`) ";
            $totalColumns[] = " sum(`{$month}-verified`) ";
            $totalColumns[] = " sum(`{$month}-received`) ";

            $starting_month = date('Y-m-d', strtotime($starting_month.' +1 MONTH'));
        }

        


        $sql = "
                select d.department_name as department, " . implode(',', $columns) . ", r.expected
            from (
                SELECT p.period_code, r.department_id, sum(if(r.status='verified',1,0)) 'verified', sum(if(r.status='received',1,0)) 'received',
                        sum(if(r.status='draft',1,0)) 'draft', sum(if(r.status='completed',1,0)) 'completed'
                from periods p
                inner join reports r on p.period_code = r.period_from
                inner join branches b on r.branch_id = b.id and b.branch_type = 'Jama`at' and b.status = 'active'
                where p.year_code = '{$year_s}-{$year_e}'
                group by p.period_code, r.department_id
            ) t
            left join departments d on t.department_id = d.id
            left join (
                SELECT department_id, sum(required) 'expected' 
                FROM required_reports r
                inner join branches b on r.branch_id = b.id and b.status = 'active' and b.branch_type = 'Jama`at'
                group by department_id
            ) r on t.department_id = r.department_id
            group by t.department_id
            order by sort_order asc
            limit 100000000
        ";

        $sqlWithTotal  = " select * from ({$sql}) t_1 ";
        $sqlWithTotal .= " union all ";
        $sqlWithTotal .= " select 'Total' as department, " . implode(',', $totalColumns) . ", sum(`expected`) as expected ";
        $sqlWithTotal .= " from ({$sql}) t_2 ";

        // echo $sql; die;
        $query = $this->entityManager->createQuery($sqlWithTotal);
        
        $rsm = new ResultSetMapping();            
        $rsm->addScalarResult('department', 'department');
        $starting_month = "{$year_s}-07-01";
        for( $i=0; $i<12; $i++ ) {
            $col_formated = date('M-Y-', strtotime($starting_month));
            $rsm->addScalarResult("{$col_formated}draft", "{$col_formated}draft");
            $rsm->addScalarResult("{$col_formated}completed", "{$col_formated}completed");
            $rsm->addScalarResult("{$col_formated}verified", "{$col_formated}verified");
            $rsm->addScalarResult("{$col_formated}received", "{$col_formated}received");
            $starting_month = date('Y-m-d', strtotime($starting_month.' +1 MONTH'));
        }
        $rsm->addScalarResult('expected', 'expected');

        $query = $this->entityManager->createNativeQuery($sqlWithTotal, $rsm);  
        $data = $query->getResult();
        return $data;
    }

    public function getMarkazMonthlyReportStatus($data)
    {
        $year_s = $data['year'];
        $year_e = $data['year'] + 1;

        $columns = $totalColumns = [];
        $starting_month = "{$year_s}-07-01";
        for( $i=0; $i<12; $i++ ) {
            $month = date('M-Y', strtotime($starting_month));

            if( strtotime('now - 1 month') < strtotime(date('y-m-7', strtotime($starting_month))) ) {
                $columns[] = " '' as '{$month}-achieved' ";
                $columns[] = " '' as '{$month}-required' ";
                $columns[] = " '' as '{$month}-received' ";
                $columns[] = " '' as '{$month}-percent' ";
            }
            else {
                $columns[] = " if(b.create_date < '".date('Y-m-01', strtotime($starting_month))."', if((".str_replace('-','',$month)."Received/t1.required*100)>=80,'Yes','No'), '') '{$month}-achieved' ";
                $columns[] = " t1.required '{$month}-required' ";
                $columns[] = " ".str_replace('-','',$month)."Received '{$month}-received' ";
                $columns[] = " round(".str_replace('-','',$month)."Received/t1.required*100) '{$month}-percent' ";
            }
            $sub_columns[] = " sum(if(p.period_code='{$month}',1,0)) '".str_replace('-','',$month)."Received' ";
            $total_achieved[] = " if((".str_replace('-','',$month)."Received/t1.required*100)>=80 AND b.create_date < '".date('Y-m-01', strtotime($starting_month))."',1,0) ";
            if( strtotime('7-'.$month) < strtotime(date('Y-m-07')) && (in_array('all', $data['month']) || in_array( date('m', strtotime($month)), $data['month'])) ) {
                $total_nos[] = " if((".str_replace('-','',$month)."Received/t1.required*100)<80 AND b.create_date < '".date('Y-m-01', strtotime($starting_month))."',1,0) ";
            }
            $totalColumns[] = " concat( 'Y (', sum(if(`{$month}-achieved`='Yes',1,0)), ')<br />N (', sum(if(`{$month}-achieved`='No',1,0)),')' ) '{$month}-achieved'";
            $totalColumns[] = " '' as '{$month}-required' ";
            $totalColumns[] = " '' as '{$month}-received' ";
            $totalColumns[] = " '' as '{$month}-percent' ";

            $starting_month = date('Y-m-d', strtotime($starting_month.' +1 MONTH'));            
        }

        $sql = "
            select if(b.branch_level='Imarat','Large Jama`at','Jama`at') 'level', b.branch_name, t1.required, " . implode(',', $columns) . ", 
            (" .  implode('+', $total_achieved) . ") 'total_achieved', (" .  implode('+', $total_nos) . ") 'total_nos' 
            from branches b
            left join (
                select branch_id, sum(required) 'required' from required_reports group by  branch_id
            ) t1 on b.id = t1.branch_id
            left join (
                SELECT r.branch_id,	" . implode(',', $sub_columns) . "
                from periods p
                inner join reports r on p.period_code = r.period_from
                inner join branches b on r.branch_id = b.id and b.branch_type = 'Jama`at' and b.status = 'active'
                inner join required_reports rr on r.branch_id = rr.branch_id and r.department_id = rr.department_id and rr.required = 1
                where p.year_code = '{$year_s}-{$year_e}' and r.status in ('".implode("','", $data['status'])."')
                group by r.branch_id                
            ) t2 on b.id = t2.branch_id
            where b.branch_type = 'Jama`at' and status = 'active' 
            and branch_level in ('".implode("','", $data['jamaat_types'])."')
            ".(isset($data['no_only']) && $data['no_only']=='yes'?' and ('.implode('+', $total_nos).') >= 1 ':'')."
            order by b.branch_level, b.branch_name
            limit 1000000000
        ";
        $sqlWithTotals  = " select * from ($sql) t_1 ";
        $sqlWithTotals .= " union all ";
        $sqlWithTotals .= " select '' as 'level', 'Total' as 'branch_name', '' as 'required', " . implode(',', $totalColumns) . ", '' as 'total_achieved', '' as 'total_nos' 
                            from ($sql) t_2 ";
        // echo $sqlWithTotals; die;
        $query = $this->entityManager->createQuery($sqlWithTotals);
        
        $rsm = new ResultSetMapping();            
        $rsm->addScalarResult('level', 'level');
        $rsm->addScalarResult('branch_name', 'branch_name');
        $rsm->addScalarResult('period_code', 'period_code');
        $starting_month = "{$year_s}-07-01";
        for( $i=0; $i<12; $i++ ) {
            $col_formated = date('M-Y', strtotime($starting_month));
            $rsm->addScalarResult("{$col_formated}-achieved", "{$col_formated}-achieved");
            $rsm->addScalarResult("{$col_formated}-required", "{$col_formated}-required");
            $rsm->addScalarResult("{$col_formated}-received", "{$col_formated}-received");
            $rsm->addScalarResult("{$col_formated}-percent", "{$col_formated}-percent");
            $starting_month = date('Y-m-d', strtotime($starting_month.' +1 MONTH'));
        }
        $rsm->addScalarResult('total_achieved', 'total_achieved');

        $query = $this->entityManager->createNativeQuery($sqlWithTotals, $rsm);  
        $data = $query->getResult();
        // print_r($data); die;
        return $data;
    }

    public function getTarbiyatReportByJamaat($data)
    {
        $data = $this->getTarbiyatReportData($data);

        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
        $offices = $this->serviceLocator->get('OfficeAssignmentService')->getActiveOffices($current_user);
        foreach($offices as $ind=>$office) {
            if( !in_array($office->getDepartment()->getId(), [5,15,26,1001]) ) {
                unset($offices[$ind]);
            }
            else {
                $brnach = $office->getBranch()->getBranchType();
            }
        }
    

        function increment(&$processedData, $data, $question, $category){

            $jamaat_id = ($data['branch_level']=='Halqa' && $data['active_user_branch_type'] != 'Halqa' ? $data['parent_branch_id'] : $data['branch_id']); 
            $jamaat_name = ($data['branch_level']=='Halqa' && $data['active_user_branch_type'] != 'Halqa' ? $data['parent_branch_name'] : $data['branch_name']); 
            if( !isset($processedData[$jamaat_id]) ) {

                $processedData[$jamaat_id]['branch_name'] = $jamaat_name;
                //$processedData[$jamaat_id]['parent_branch_name'] = $data['parent_branch_name'];
                foreach(['fajr', 'maghrib_isha', 'quran', 'nawafil'] as $__question) {
                    foreach(['20+', '11-20', '1-10', 'zero' ] as $__category) {
                        $processedData[$jamaat_id][$__question][$__category] = 0;
                    }
                }
                foreach(['friday_prayer', 'friday_sermon'] as $__question) {
                    foreach([ '4+', '3', '2', '1', 'zero' ] as $__category) {
                        $processedData[$jamaat_id][$__question][$__category] = 0;
                    }
                }
                $processedData[$jamaat_id][$question][$category] = 1;
            }
            else {
                $processedData[$jamaat_id][$question][$category]++;
            }
        }

        $processedData = [];
        $additionalData = [];

        foreach($data['data'] as &$val) {

            $val['active_user_branch_type'] = $brnach;

            foreach(['prayers'] as $question) {
                if( $val[$question] !== '' ) increment($processedData, $val, $question, 'data-for');
                if( $val[$question] === '' ) increment($processedData, $val, $question, 'blank');
                if( $val[$question] === 0 || $val[$question] === '0' ) increment($processedData, $val, $question, 'zero');
                if( $val[$question] == 1 ) increment($processedData, $val, $question, '1');
                if( $val[$question] == 2 ) increment($processedData, $val, $question, '2');
                if( $val[$question] == 3 ) increment($processedData, $val, $question, '3');
                if( $val[$question] == 4 ) increment($processedData, $val, $question, '4');
                if( $val[$question] >= 5 ) increment($processedData, $val, $question, '5');
            }            

            foreach(['fajr', 'maghrib_isha', 'quran', 'nawafil'] as $question) {
                if( $val[$question] !== '' ) increment($processedData, $val, $question, 'data-for');
                if( $val[$question] === '' ) increment($processedData, $val, $question, 'blank');
                if( $val[$question] === 0 || $val[$question] === '0' ) increment($processedData, $val, $question, 'zero');
                if( $val[$question] == '1-10' ) increment($processedData, $val, $question, '1-10');
                if( $val[$question] == '11-20') increment($processedData, $val, $question, '11-20');
                if( $val[$question] == '21+'  ) increment($processedData, $val, $question, '21+');
            }

            foreach(['friday_prayer', 'friday_sermon'] as $question) {
                if( $val[$question] !== '' ) increment($processedData, $val, $question, 'data-for');
                if( $val[$question] === '' ) increment($processedData, $val, $question, 'blank');
                if( $val[$question] === 0 || $val[$question] === '0' ) increment($processedData, $val, $question, 'zero');
                if( $val[$question] == 1 ) increment($processedData, $val, $question, '1');
                if( $val[$question] == 2 ) increment($processedData, $val, $question, '2');
                if( $val[$question] == 3 ) increment($processedData, $val, $question, '3');
                //if( $val[$question] >= 1 && $val[$question] <= 3 ) increment($processedData, $val, $question, '1-3');
                if( $val[$question] >= 4 ) increment($processedData, $val, $question, 'All');
            }
        }

        return ['data'=>$processedData, 'info'=>$data['info']];
    }

    public function getTarbiyatReportByJamaatMembers($postData)
    {
        $data = $this->getTarbiyatReportData($postData);
        //echo '<pre>'; print_r($postData);print_r($data); die;
        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
        $offices = $this->serviceLocator->get('OfficeAssignmentService')->getActiveOffices($current_user);
        foreach($offices as $ind=>$office) {
            if( !in_array($office->getDepartment()->getId(), [5,15,26,1001]) ) {
                unset($offices[$ind]);
            }
            else {
                $brnach = $office->getBranch()->getBranchType();
            }
        }        

        $processedData = [];
        $additionalData = [];
        $additionalData['question'] = $postData['question'];
        foreach($data['data'] as &$val) {

            $jamaat_id = ($val['branch_level']=='Halqa' && $brnach != 'Halqa' ? $val['parent_branch_id'] : $val['branch_id']); 
            $jamaat_name = ($val['branch_level']=='Halqa' && $brnach != 'Halqa' ? $val['parent_branch_name'] : $val['branch_name']); 
            if( $postData['jamaat'] == $jamaat_id ) {
                
                $questions = explode('|', $postData['question']);
                foreach($questions as $question) {
                    if( 
                        $postData['category'] == 'blank' && ($val[$question] === '') ||
                        $postData['category'] == 'zero' && ($val[$question] === 0 || $val[$question] === '0') ||
                        $postData['category'] == '1-10' && ($val[$question] == '1-10') ||
                        $postData['category'] == '11-20' && ($val[$question] == '11-20' ) ||
                        $postData['category'] == '21+' && ($val[$question] == '21+') ||
                        $postData['category'] == '1-3' && ($val[$question] >= 1 && $val[$question] <= 3) ||
                        $postData['category'] == 'All' && ($val[$question] >= 4) ||
                        $postData['category'] == '1' && ($val[$question] == 1) ||
                        $postData['category'] == '2' && ($val[$question] == 2) ||
                        $postData['category'] == '3' && ($val[$question] == 3) ||
                        $postData['category'] == '4' && ($val[$question] == 4) ||
                        $postData['category'] == '5' && ($val[$question] >= 5)
                    ) {
                        $processedData[] = $val;
                        $data['info']['branch_name'] = $jamaat_name;
                        $data['info']['category'] = $postData['category'];
                        break;
                    }
                }
    
            }
        }

        return ['data'=>$processedData, 'info'=>$data['info']];
    }
    
    public function getAnalysisReport(array $branch_ids,$year,array $report_statuses,$department=null,$by_office=false){
        
        $stats = $this->getReportSubmissionStats( $branch_ids,$year,$report_statuses,$department);

        //filter for status
        if(empty($report_statuses)){
            $report_statuses = array('draft', 'completed','verified','received','deleted','outstanding'); 
        } $filtered_stats=array();
        foreach($stats as $report){ 
            if(in_array($report['report_status'],$report_statuses)){ 
                $filtered_stats[]=$report;
            }
        }
        
        $branchSrv = $this->serviceLocator->get('BranchManagementService');

        $branches_list = $branchSrv->getBranchList($branch_ids);

        $periods = $this->getPeriodsFromYear($year);
        
        $summary_result = DataTransform::summarizeReportsByBranchPeriod($filtered_stats,$branches_list,$periods);
        
        return array(
                        'periods'=>$periods, 
                        'branches'=>$branches_list,//$this->serviceLocator->get('BranchManagementService')->listBranches($branches), 
                        'departments'=>$this->serviceLocator->get('ConfigService')->listDepartments($department), 
                        'summary'=>$summary_result
                     );
        
    }

        
    
    /**
     * Retuns all periods of the given year in accending order
     * 
     * If future is not specified, it will not return future periodss 
     *
     * TODO FIXME refactor to a different service, separation of concern
     */
    public function getPeriodsFromYear($year,$future=false){
        
        $dql = "SELECT period 
                FROM \Application\Entity\Period period 
                WHERE date(period.period_start) >= :start_date
                and date(period.period_end) <= :end_date
                
                ORDER BY period.period_start asc"; $start_date=Period::getStartOfYear($year); $end_date=new \DateTime();
        $period_end = Period::getEndOfYear($year);
                
        if($future){
            //we want all periods for this year even if interval is in future
            //use criteria that interval end should be less than today from next year
            //but year be same is given year to get these periods
            //$period_end->modify('+1 year');
            $end_date = $period_end ;
        }

        $end_date = $period_end ;
         
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('start_date',$start_date);
        $query->setParameter('end_date',$end_date);
        //$query->setParameter('year',$year.'%');
        //$query->setParameter('period_end',$period_end);

        return $query->getResult();
           
    }

        
    /**
     * TODO FIXME: Need to be fixed for offices that are just assigned, 
     * does not take care of the case when an office is assigned now but was not assigned 
     * for the reporting period for which we are generatoing stats
     * 
     */          
    public function getReportSubmissionStats(array $branches,$year,array $report_statuses,$department=null){
        /*
        $sql =  ' SELECT  ' 
                .'
               `owd`.`branch_id` AS `office_branch_id`,
               `owd`.`branch_name` AS `branch_name`,
               `owd`.`branch_code` AS `branch_code`,
               `owd`.`department_id` AS `office_department_id`,
               `owd`.`department_name` AS `department_name`,
               `owd`.`department_code` AS `department_code`,
               `owd`.`display_name` AS `office_bearer`,
               `owd`.`email` AS `email`,
               `owd`.`phone_primary` AS `phone_primary`,
               `owd`.`phone_alternate` AS `phone_alternate`,
               `owd`.`username` AS `username`,
               `owd`.`period_to` AS `office_expiry`,
               `p`.`period_code` AS `period_code`,
               `p`.`period_start` AS `period_start`,
               `p`.`year_code` AS `year_code`,
               `p`.`period_end` AS `period_end`,
               `rs`.`report_id`,
               `rs`.`period_from` AS `period_from`,
               `rs`.`period_to` AS `period_to`,
               `rs`.`created_by_user_id` AS `created_by_user_id`,
               `rs`.`completed_by_user_id` AS `completed_by_user_id`,
               `rs`.`modified_by_user_id` AS `modified_by_user_id`,
               `rs`.`verified_by_user_id` AS `verified_by_user_id`,
               `rs`.`received_by_user_id` AS `received_by_user_id`,
               `rs`.`date_created` AS `date_created`,
               `rs`.`date_completed` AS `date_completed`,
               `rs`.`date_modified` AS `date_modified`,
               `rs`.`date_verified` AS `date_verified`,
               `rs`.`date_received` AS `date_received`,
               ifnull(`rs`.`report_status`,\'outstanding\') AS `report_status`
            
            FROM 
            periods p 
            JOIN offices_with_details owd on  owd.branch_id in (:branches) '.( ($department)?' AND owd.department_id in (:department) ':'')
         .' LEFT OUTER JOIN reports_submission_reports rs ON rs.period_code=p.period_code AND rs.office_branch_id = owd.branch_id AND rs.office_department_id=owd.department_id
            WHERE owd.status=\'active\' AND owd.reportable=1
          '
         .' AND date(p.period_start) >= :start_date '
         .' AND date(p.period_end) <= :end_date '
         .' AND (report_status is null or report_status IN (:report_statuses) )'
                        
         ;  */ 
         
         $sql = "
            SELECT	db.branch_id as office_branch_id, b.branch_name, b.branch_code, db.department_id as office_department_id, d.department_name, d.department_code, o.display_name as office_bearer,
                o.email, o.phone_primary, o.phone_alternate, o.username, o.period_to as office_expiry, db.period_code, r.period_start, r.year_code, r.period_end, r.report_id, r.period_from,
                r.period_to, r.created_by_user_id, r.completed_by_user_id, r.modified_by_user_id, r.verified_by_user_id, r.received_by_user_id, r.date_created, r.date_completed, r.date_modified, r.date_verified, 
                r.date_received, ifnull(r.status,'outstanding') AS report_status
            FROM(
                SELECT t.* FROM (
                    SELECT 	d.`id` as department_id,`department_name`,`department_code`,`rules`,`guide_lines`,d.`status` as department_status,`reportable`,`office_bearer`,`sort_order`,`elected`,
                            b.`id` as branch_id,`parent_branch_id`,`branch_name`,`branch_code`,b.`status` as branch_status,`branch_type`,`branch_head_title`,`office_bearer_designation`,`branch_level`, p.period_code
                    FROM `departments` `d`, `branches` `b`, `periods` `p`
                    where d.status = 'active' and b.status = 'active' and d.reportable=1 and b.id in (:branches) ".( ($department)?' AND d.id in (:department) ':'')."
                        AND date(p.period_start) >= :start_date and date(p.period_end) <= :end_date
                ) t
                INNER JOIN required_reports r ON r.department_id = t.department_id AND r.branch_id = t.branch_id AND required = 1
            ) db
            LEFT JOIN (
                select r.id as report_id, r.department_id, r.branch_id, r.status as report_status, p.period_code, p.period_start, p.year_code, p.period_end, r.period_from, r.period_to, r.created_by_user_id,
                    r.completed_by_user_id, r.modified_by_user_id, r.verified_by_user_id, r.received_by_user_id, r.date_created, r.date_completed, r.date_modified, r.date_verified, r.date_received, r.status
                from periods p
                inner join reports r on p.period_code = r.period_from
                where date(p.period_start) >= :start_date and date(p.period_end) <= :end_date 
            ) r ON db.department_id = r.department_id AND db.branch_id = r.branch_id and db.period_code = r.period_code
            LEFT JOIN (
                select o.*, u.email, u.phone_primary, u.phone_alternate, u.display_name, u.username
                from (
                    select * from (
                        select o.id as office_id, o.branch_id as office_branch_id, o.department_id as office_department_id, o.user_id as office_user_id, o.period_to
                        from office_assignments o
                        where  o.status = 'active' 
                        order by id desc limit 10000000
                    ) t
                    group by office_id, office_branch_id, office_department_id
                    limit 10000000
                ) o
                inner join users u on o.office_user_id = u.id
            ) o ON db.department_id = o.office_department_id AND db.branch_id = o.office_branch_id
            LEFT JOIN branches b on db.branch_id = b.id
            LEFT JOIN departments d on db.department_id = d.id       
            WHERE ifnull(r.status,'outstanding') IN (:report_statuses)
         ";
    
        $query = $this->entityManager->createNativeQuery($sql,$this->getReportSubmissionResultSetMapping()); $start_date=Period::getStartOfYear($year); $end_date=Period::getEndOfYear($year);
        
        $query->setParameter('branches',$branches);
        
        $query->setParameter('report_statuses',$report_statuses);
        
        $now = new \DateTime();
        if($now<$end_date){ $end_date=$now;
        }
        //$query->setParameter('period_end',$period_end);
        $query->setParameter('start_date',$start_date);
        $query->setParameter('end_date',$end_date);
        
        if($department){
            $department = is_array($department)?$department:array($department);
            $query->setParameter('department',$department);    
        }
                 
        $result = $query->getResult();

        $comp = function ($a, $b){
            if($a['period_start'] == $b['period_start'])
                return 0;
            else{
                $a_date = new \DateTime($a['period_start']);
                $b_date = new \DateTime($b['period_start']);
                return $a_date > $b_date;
            }
        };
        usort($result,$comp);
        return $result;                
    }
    
    private function getReportSubmissionResultSetMapping(){


        $rsm = new ResultSetMapping();            
        
        $rsm->addScalarResult('office_branch_id','office_branch_id');
        $rsm->addScalarResult('branch_name','branch_name');
        $rsm->addScalarResult('branch_code','branch_code');
        $rsm->addScalarResult('department_name','department_name');
        $rsm->addScalarResult('department_code','department_code');
        $rsm->addScalarResult('office_department_id','office_department_id');
        $rsm->addScalarResult('office_bearer','office_bearer');
        $rsm->addScalarResult('email','email');
        $rsm->addScalarResult('phone_primary','phone_primary');
        $rsm->addScalarResult('phone_alternate','phone_alternate');
        $rsm->addScalarResult('username','username');
        $rsm->addScalarResult('office_expiry','office_expiry');
        $rsm->addScalarResult('period_code','period_code');
        $rsm->addScalarResult('period_start','period_start');
        $rsm->addScalarResult('report_id','report_id');
        $rsm->addScalarResult('report_config','report_config');
        $rsm->addScalarResult('period_from','period_from');
        $rsm->addScalarResult('period_to','period_to');
        $rsm->addScalarResult('branch_id','branch_id');
        $rsm->addScalarResult('department_id','department_id');
        $rsm->addScalarResult('created_by_user_id','created_by_user_id');
        $rsm->addScalarResult('completed_by_user_id','completed_by_user_id');
        $rsm->addScalarResult('modified_by_user_id','modified_by_user_id');
        $rsm->addScalarResult('verified_by_user_id','verified_by_user_id');
        $rsm->addScalarResult('received_by_user_id','received_by_user_id');
        $rsm->addScalarResult('date_created','date_created');
        $rsm->addScalarResult('date_completed','date_completed');
        $rsm->addScalarResult('date_modified','date_modified');
        $rsm->addScalarResult('date_verified','date_verified');
        $rsm->addScalarResult('date_received','date_received');
        $rsm->addScalarResult('report_status','report_status');
        
        return $rsm;
        
    }  

    public function getQuestionHistory($report_id, $question_id)
    {
        $entity_srv=$this->serviceLocator->get('EntityService');
        $report = $entity_srv->findOneBy('Report', ['id'=>$report_id]);
        $question = $entity_srv->findOneBy('Question', ['id'=>$question_id]);


        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('a')
                ->from('\Application\Entity\Period','p')
                ->leftjoin('\Application\Entity\Report', 'r',\Doctrine\ORM\Query\Expr\Join::WITH, $qb->expr()->andx(
                    $qb->expr()->eq('p.period_code', 'r.period_from'),
                    $qb->expr()->eq('r.branch', ':branch'),
                    $qb->expr()->eq('r.department', ':department'),
                    $qb->expr()->neq('r.status', ':rep_status')
                ))
                    ->setParameter('branch',$report->getBranch()->getId())
                    ->setParameter('department',$report->getDepartment()->getId())
                    ->setParameter('rep_status','deleted')

                ->join('\Application\Entity\Answer', 'a', \Doctrine\ORM\Query\Expr\Join::WITH, $qb->expr()->andx(
                    $qb->expr()->eq('r.id', 'a.report'),
                    $qb->expr()->eq('a.question', ':questions')
                ))
                    ->setParameter('questions',$question->getId())

                ->where('p.period_start <= :period_start')->setParameter('period_start', $report->getPeriodFrom()->addMonths(-1)->getPeriodStart())
                ->andWhere('p.period_start >= :period_start_2')->setParameter('period_start_2', $report->getPeriodFrom()->addMonths(-6)->getPeriodStart())
                ->orderBy('p.period_start', 'desc')
                ;   
        // $query = $qb->getQuery()->getSQL();
        return $qb->getQuery()->execute();        
    }
        
}
?>
