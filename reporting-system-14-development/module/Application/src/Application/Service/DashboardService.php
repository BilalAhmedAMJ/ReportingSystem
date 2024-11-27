<?php


namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

use Application\Entity\Period;


class DashboardService implements FactoryInterface{
    
    private $serviceLocator;
    private $entityManager;
    
    /**
     * @var ReportAnalysisService
     */
    private $reportAnalysisService;
    
    /**
     * @var ConfigService
     */
    private $configService;
    
    /**
     * @var OfficeAssignmentService
     */
    private $officeAssignmentService;

    /**
     * @var BranchManagementService
     */
    private $branchService;

    private $reportStatuses;
    
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){
               
        $this->serviceLocator = $serviceLocator;  
        $this->entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $this->reportAnalysisService = $serviceLocator->get('ReportAnalysisService');
        $this->configService = $serviceLocator->get('ConfigService');
        $this->officeAssignmentService = $serviceLocator->get('OfficeAssignmentService');
        
        $this->reportStatuses = $this->configService->getConfig('report_status_display');
        
        $this->branchService = $serviceLocator->get('BranchManagementService');
        
        //add outstanding
        $this->reportStatuses['outstanding']='Outstanding';
        
        return $this;
    }
         
    
    /**
     * Step 1: Owen Reports and SubOrdinate Reports for first office assignment
     * 
     */
    public function reportsSubmission($user,$year,$period_code=null,$include_outstanding=true){
        
        if(isset($period_code)){
            if(strpos($period_code,$year)<0){
                throw new \InvalidArgumentException("Year given [$year] does not patch with period given [$period_code]");
            }
            //normalize year based on Jama`at year setup
            $year = Period::getYearFromPeriod($period_code);
        }elseif(!isset($year)){
        	    throw new \InvalidArgumentException("Year is required if no Reporting period is specified!!");
        }else{//there is a year given 
            
            //get last period of the year (assuming that if year is not past, we will have current period as last)            
            $periods = $this->reportAnalysisService->getPeriodsFromYear($year);
			if(count($periods)<1){
				//$periods = $this->reportAnalysisService->getPeriodsFromYear($year-1);
				throw new \Exception('No periods found');
			}
            $period_code = array_pop($periods)->getPeriodCode();   
                         
        }
                        
        $has_own_reports=false;
        $offices = $this->officeAssignmentService->getActiveOffices($user);
        $departments_own=array();
        $departments_supervise=array();
        foreach ($offices as $office) {
            if( (!$has_own_reports) && $office->getDepartment()->getReportable()){
                $has_own_reports = true;
                $departments_own[]=$office->getDepartment();
                $departments_supervise = $office->getSuperviseDepartments(); 
            }
        }
        
        /** 
         * 
         * TODO FIXME For multiple office for this
         * 
         */
        $all_branches = array_keys($this->officeAssignmentService->getBranchesWithOffices($user,true));
        $own_branches = array_keys($this->officeAssignmentService->getBranchesWithOffices($user,false));
        
        $subordinate_branches = array_diff($all_branches, $own_branches);
        $has_subordinates=count($subordinate_branches)>0;
        
        $offices = $this->officeAssignmentService->getActiveOffices($user);
        $supervised_departments=array();
        $same_branch=array();
        $has_samebranch=false;
        foreach ($offices as $office) {
            if(count($office->getSuperviseDepartments())>0){
                $supervised_departments=$office->getSuperviseDepartments();
                $same_branch[]=$office->getBranch()->getId();
                $has_samebranch=true;
                if($office->getDepartment()->getReportable()){
                    $supervised_departments[]=$office->getDepartment()->getId();    
                }
                
            }
        }
        
        $report_statuses = $this->reportStatuses;

        if(! $include_outstanding){
            //add outstanding
            unset($report_statuses['outstanding']);
        }

        
        $result = array('year'=>$year,'period_code'=>$period_code,'has_own'=>$has_own_reports,'has_subordinates'=>$has_subordinates,'has_samebranch'=>$has_samebranch);
        
        if($has_own_reports){
            $result['own']['branches']=$this->branchService->listBranches($own_branches);
            $result['own']['all_periods']=$this->reportAnalysisService->getPeriodsFromYear($year);
            $result['own']['data'] = $this->reportAnalysisService->getReportSubmissionStats($own_branches, $year, array_keys($report_statuses),$departments_own);
            $result['own']['status_ytd'] = $this->byStatus($result['own']['data']);         
        }
        
        if($has_subordinates){
            $result['subordinate']['branches']=$this->branchService->listBranches($subordinate_branches);            
            $result['subordinate']['all_periods']=$this->reportAnalysisService->getPeriodsFromYear($year);
            $result['subordinate']['data'] = $this->reportAnalysisService->getReportSubmissionStats($subordinate_branches, $year, array_keys($report_statuses),$departments_own);            
            $result['subordinate']['status_ytd'] = $this->byStatus($result['subordinate']['data']);            
            $result['subordinate']['last'] = $this->filterPeriod($result['subordinate']['data'],$period_code);  
                                             
            $result['subordinate']['status_last'] = $this->byStatus($result['subordinate']['last']);

        }
        
        if($has_samebranch){
            $result['samebranch']['branches']=$this->branchService->listBranches($same_branch);
            $result['samebranch']['all_periods']=$this->reportAnalysisService->getPeriodsFromYear($year);
            
            $result['samebranch']['data'] = $this->reportAnalysisService->getReportSubmissionStats($same_branch, $year, array_keys($report_statuses),$supervised_departments);            
            $result['samebranch']['status_ytd'] = $this->byStatus($result['samebranch']['data']);            
            $result['samebranch']['last'] = $this->filterPeriod($result['samebranch']['data'],$period_code);                                   
            $result['samebranch']['status_last'] = $this->byStatus($result['samebranch']['last']);
                        
        }
        
        return $result;
        
    }

    private function getColor($status){
        $colors=array(
            'draft'=>'#333333',
            'completed'=>'#E8B110',
            'verified'=>'#3A87AD',
            'received'=>'#82AF6F',
            'outstanding'=>'#CC3300',
        );
        return $colors[$status];
    }

    private function byStatus($data){
        $result =array();
        
        foreach ($data as $rep) {
            $status=$rep['report_status'];
            if(key_exists($status, $result)){
                $result[$status]['y']++;
            }
            else{
                $result[$status]=array('name'=>$this->reportStatuses[$status],'y'=>1);
                $result[$status]['color']=$this->getColor($status);
            } 
        }
        
        return $result;
    }
    
    private function filterPeriod($data,$period_code){

        $results=array();

        foreach ($data as $i=>$rep) {
            if($rep['period_code'] == $period_code){
                $results[]=$rep;
            } 
        }

        return $results;        
    }
    
    private function lastPeriod($data){
        $results_by_period=array();
        $last_period_date=new \DateTime('1900-01-01');//initialize last period to be an old date
        $last_period='';
        
        foreach ($data as $i=>$rep) {
            
            if(!key_exists($rep['period_code'],$results_by_period)){
                $results_by_period[$rep['period_code']]=array();
            }
            $results_by_period[$rep['period_code']][]=$rep;
            $date = new \DateTime($rep['period_start']);
            if($last_period_date<$date){
                $last_period=$rep['period_code'];
                $last_period_date=$date;
            }
        }
        
        return $results_by_period[$last_period];        
    }
}
