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

class SummaryReportService implements FactoryInterface{
    
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

    private $reportingPeriodFrom = null;
    private $reportingPeriodTo = null;
    
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

    public function setReportingYear( $post_params )
    {
        $this->reportingPeriodFrom = "{$post_params['from_year']}-{$post_params['from_month']}-01";
        $this->reportingPeriodTo = "{$post_params['to_year']}-{$post_params['to_month']}-25";
        $this->reportingJamaat = $post_params['jamaat'];

        $branchSrv = $this->serviceLocator->get('BranchManagementService');
        $this->child_brances = $branchSrv->getChildBranches( $this->reportingJamaat, true );
        $this->child_brances[] = $this->reportingJamaat;

        return $this;
    }

    public function getCaption( $id )
    {
        $question = $this->serviceLocator->get('EntityService')->getObject('Question',$id);
        return $question->getCaption();
    }

    public function __sumOfLastAnswerOfEachData( &$aQuestion )
    {
        $data = $this->__listData( $aQuestion, true );

        $lasrAnswers = [];
        foreach($data as $item) {
            $item['value'] = trim($item['value']);
            if( !empty($item['value']) ) {
                $lasrAnswers[$item['branch']] = $item['value'];
            }
        }

        $sum = 0;
        foreach($lasrAnswers as $item) {
            $sum  += $item;
        }
        return $sum;
    }
    public function __listLastAnswerOfEachData( &$aQuestion )
    {
        $data = $this->__listData( $aQuestion, true );

        $lasrAnswers = [];
        foreach($data as $item) {
            $item['value'] = trim($item['value']);
            if( !empty($item['value']) ) {
                $lasrAnswers[$item['branch']] = $item;
            }
        }
        return $lasrAnswers;
    }

    public function __ifnotemptyData( &$aQuestion )
    {
        $data = $this->__listData( $aQuestion, true );
        $count = 0;
        foreach($data as $item) {
            $item['value'] = trim($item['value']);
            if( !empty($item['value']) ) {
                $count++;
            }
        }
        return $count;
    }

    public function __ifemptyData( &$aQuestion )
    {
        $data = $this->__listData( $aQuestion, true );
        $count = 0;
        foreach($data as $item) {
            $item['value'] = trim($item['value']);
            if( empty($item['value']) ) {
                $count++;
            }
        }
        return $count;
    }

    public function __countifData( &$aQuestion )
    {
        $data = $this->__listData( $aQuestion, true );
        $count = 0;
        $condition_met = false;
        $aQuestion['condition_value'] = trim($aQuestion['condition_value']);
        foreach($data as $item) {
            $item['value'] = trim($item['value']);
            switch( $aQuestion['condition'] ) {
                case 'gte': 
                        $condition_met = $item['value'] >= $aQuestion['condition_value']; 
                        break;
                case 'gt': 
                        $condition_met = $item['value'] > $aQuestion['condition_value']; 
                        break;
                case 'eq': 
                        $condition_met = $item['value'] == $aQuestion['condition_value']; 
                        break;
                case 'lte': 
                        $condition_met = $item['value'] <= $aQuestion['condition_value']; 
                        break;
                case 'lt': 
                        $condition_met = $item['value'] < $aQuestion['condition_value']; 
                        break;
                default: 
                        $condition_met = false;
            }
            if($condition_met)  $count++;
        }
        return $count;
    }

    public function __sumData( &$aQuestion )
    {
        $data = $this->__listData( $aQuestion );
        $sum = 0;
        foreach($data as $item) {
            $sum  += (float)$item['value'];
        }
        return $sum;
    }

    public function __listData( $aQuestion, $returnEmpty=false )
    {
        /*
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('a')
                ->from('\Application\Entity\Period','p')
                ->join('\Application\Entity\Report', 'r', \Doctrine\ORM\Query\Expr\Join::WITH, $qb->expr()->eq('p.period_code', 'r.period_from'))
                ->join('\Application\Entity\Answer', 'a', \Doctrine\ORM\Query\Expr\Join::WITH, $qb->expr()->andx(
                    $qb->expr()->eq('r.id', 'a.report'),
                    $qb->expr()->in('a.question', ':question')
                )) ->setParameter('question', array_keys($aQuestion['question']))
                ->where('p.period_start >= :period_from')->setParameter('period_from', $this->reportingPeriodFrom)
                ->andWhere('p.period_start <= :period_to')->setParameter('period_to', $this->reportingPeriodTo)
                ->orderBy('p.period_start', 'asc')
                ;   
        if( isset($aQuestion['level']) ) {
            $qb->join('\Application\Entity\Branch', 'b', \Doctrine\ORM\Query\Expr\Join::WITH, $qb->expr()->andx(
                $qb->expr()->eq('r.branch', 'b.id'),
                $qb->expr()->in('b.branch_level', ':branch_level')
            )) ->setParameter('branch_level', $aQuestion['level']);
        }

        $qb->andWhere('r.branch IN (:branches)')->setParameter('branches', $this->child_brances);
        echo $query = $qb->getQuery()->getSQL(); die;
        $answers = $qb->getQuery()->execute();    
        */




        $sql = "SELECT a0_.id, a0_.caption, a0_.value, a0_.answer_number, a0_.report_id, a0_.question_id
                FROM periods p1_ 
                INNER JOIN reports r2_ ON (p1_.period_code = r2_.period_from) 
                INNER JOIN answers a0_ ON (r2_.id = a0_.report_id AND a0_.question_id IN (:question)) 
                INNER JOIN branches b3_ ON (r2_.branch_id = b3_.id AND b3_.branch_level IN (:branch_level)) 
                WHERE p1_.period_start >= :period_from AND p1_.period_start <= :period_to AND r2_.branch_id IN (:branches) 
                ORDER BY p1_.period_start ASC";
        $query = $this->entityManager->createQuery($sql);

        $rsm = new ResultSetMapping();            
        $columns = ['id', 'caption', 'value', 'answer_number', 'report_id', 'question_id'];
        foreach( $columns as $col ) {
            $rsm->addScalarResult($col, $col);
        }
        $qb = $this->entityManager->createNativeQuery($sql, $rsm);  
        $qb->setParameter('question', array_keys($aQuestion['question']));    
        $qb->setParameter('branch_level', $aQuestion['level']);    
        $qb->setParameter('branches', $this->child_brances);    
        $qb->setParameter('period_from', $this->reportingPeriodFrom);    
        $qb->setParameter('period_to', $this->reportingPeriodTo);    

        $answers = $qb->getResult();
        $adapter = $this->serviceLocator->get('DoctrineEncryptAdapter');

        $data = [];
        foreach($answers as $answer) {
            $value = $adapter->decrypt($answer['value']);
            if( $returnEmpty || trim($value) !== '' ) {
                $data[] = [
                    'month' => $answer['period_from'],
                    'branch' => $answer['branch_name'],
                    'value' => $value
                ];
            }
        } 
        unset($answers);
        $this->entityManager->clear();
        return $data;
    } 

    public function getSummaryRportQuestions($report_id, $postData = null, $add_index = false)
    {
        // $report = $this->entityManager->find('\Application\Entity\SummaryReport', 9);
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('r')
                ->from('\Application\Entity\SummaryReport','r')
                ->where('r.report_id = :report_id')->setParameter('report_id',$report_id)
                ->orderBy('r.sort_order', 'asc')
                ;   
        if( isset($postData['qid']) ) {
            $qb->andWhere('r.id = :qid')->setParameter('qid',$postData['qid']);
        }
        // echo $query = $qb->getQuery()->getSQL(); die;
        $rep_questions = $qb->getQuery()->execute();    
        $data = [];
        $serial_no = 1;
        foreach($rep_questions as $rep_question) {
            $question_ids = $rep_question->getQuestion();
            $question_ids = explode(',', $question_ids);

            $qb2 = $this->entityManager->createQueryBuilder();
            $qb2->select('q')
                    ->from('\Application\Entity\Question','q')
                    ->where('q.id IN (:question_ids)')->setParameter('question_ids',$question_ids)
                    ->orderBy('q.sort_order', 'asc')
            ;   
            // echo $query = $qb->getQuery()->getSQL(); die;
            $questions = $qb2->getQuery()->execute();   
            $questions_arr = [];
            $department = '';
            foreach($questions as $question) {

                $levels_str = '';
                if( $department == '' ) {
                    $department = $question->getDepartment()->getDepartmentName();
                }
                $constraints = $question->getConstraints();
                if( !empty($constraints) ) {
                    $constraints = json_decode($constraints, true);
                    if( isset($constraints['display_levels']) ) {
                        $levels_str .= ' (L:'. implode(',', array_map(function($v){ return $v[0]; }, $constraints['display_levels'])) .')';
                    }
                    if( isset($constraints['rollup_settings']) ) {
                        if( !empty($levels_str) ) $levels_str .= ', ';
                        $levels_str .= ' (R:'. implode(',', array_map(function($v){ return $v[0]; }, array_keys($constraints['rollup_settings']))) .')';
                    }
                }

                $questions_arr[ $question->getId() ] = $question->getId() . ': ' .
                                                        '('.$question->getDepartment()->getDepartmentName() .') ' .
                                                        ($question->getParent() ? ($question->getParent()->getCaption() . ' => ') : '') .
                                                        $question->getCaption() . $levels_str;
            }

            if( isset($postData['qid']) ) {
                return [
                    'id' => $rep_question->getId(),
                    'report_id' => $rep_question->getReport()->getId(),
                    'question' => $questions_arr,
                    'department' => $department,
                    'caption' => $rep_question->getCaption(),
                    'action' => $rep_question->getAction(),
                    'level' => explode(',', $rep_question->getLevel()),
                    'condition' => $rep_question->getCondition(),
                    'condition_value' => $rep_question->getConditionValue(),
                    'sort_order' => $rep_question->getSortOrder()
                ];
            }
            else {
                if( $add_index ) {
                    $data[] = [
                        'id' => $rep_question->getId(),
                        'question' => $questions_arr,
                        'department' => $department,
                        'caption' => $rep_question->getCaption(),
                        'action' => $rep_question->getAction(),
                        'level' => explode(',', $rep_question->getLevel()),
                        'condition' => $rep_question->getCondition(),
                        'condition_value' => $rep_question->getConditionValue(),
                        'sort_order' => $rep_question->getSortOrder()
                    ];
                }
                else {
                    $data[] = [
                        $serial_no++,
                        implode(',', $questions_arr),
                        $department,
                        $rep_question->getCaption(),
                        $rep_question->getAction(),
                        $rep_question->getLevel(),
                        trim($rep_question->getCondition() . ' ' . $rep_question->getConditionValue()),
                        $rep_question->getId(),
                        $rep_question->getSortOrder()
                    ];
                }
            }

        } 
        return ['data'=>$data];

    }

    public function getSummaryReportList()
    {
        $repo=$this->entityManager->getRepository('Application\Entity\SummaryListReport');
        return $repo->findAll();
    }          

    public function updateSummaryReport($post)
    {
        if( isset($post['report_id']) && !empty($post['report_id']) ) {
            $repo = $this->entityManager->getRepository('Application\Entity\SummaryListReport');
            $summary_report = $repo->find($post['report_id']);
        }
        else {
            $summary_report = $this->entityFactory->getSummaryListReport();
        }
        $summary_report->setTitle($post['report_title']);

        $this->entityManager->transactional(function($em) use( &$summary_report ){
            $em->persist($summary_report);   
            $em->flush();
         });         
    }         

    public function deleteSummaryReport($report_id)
    {
        $repo = $this->entityManager->getRepository('Application\Entity\SummaryListReport');
        $summary_report = $repo->find($report_id);

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('summary_report_questions')
           ->from('\Application\Entity\SummaryReport','summary_report_questions')
           ->where('summary_report_questions.report_id = :report')
           ->setParameter('report', $report_id)
        ;
        $questions = $qb->getQuery()->execute();

        $this->entityManager->transactional(function($em) use( &$summary_report, $questions ){
            $em->remove($summary_report);   
            foreach ($questions as $question) {
                $em->remove($question);                   
            }
            $em->flush();
        });         
    }          

    public function getSummaryReport( $post )
    {
        try {
            $aQuestions = $this->getSummaryRportQuestions( $post['report_id'], null, true);
            if( isset($post['by_month']) ) {
                $reportingPeriodFrom = "{$post['from_year']}-{$post['from_month']}-01";
                $reportingPeriodTo = "{$post['to_year']}-{$post['to_month']}-01";
                do {
                    $post['from_year'] = date('Y', strtotime($reportingPeriodFrom));
                    $post['from_month'] = date('m', strtotime($reportingPeriodFrom));
                    $post['to_year'] = date('Y', strtotime($reportingPeriodFrom));
                    $post['to_month'] = date('m', strtotime($reportingPeriodFrom));
                    $this->setReportingYear( $post ); 
                    foreach($aQuestions['data'] as &$aQuestion) {
                        $aQuestion['data'][ "{$post['from_year']}-{$post['from_month']}" ] = $this->{'__'.$aQuestion['action'].'Data'}( $aQuestion );
                        gc_collect_cycles();
                    }  
                    $reportingPeriodFrom = date('Y-m-d', strtotime($reportingPeriodFrom.' +1 MONTH'));
                } while( strtotime($reportingPeriodFrom) <= strtotime($reportingPeriodTo) );
            }
            else {
                $this->setReportingYear( $post ); 
                foreach($aQuestions['data'] as &$aQuestion) {
                    $aQuestion['data'] = $this->{'__'.$aQuestion['action'].'Data'}( $aQuestion );
                    gc_collect_cycles();
                }   
            }


            // print_r($aQuestions);
        }
        catch(Exception $e) {
            print_r( $question );
            die;
        }

        
        return $aQuestions;
    }          
    
    public function getQuestionsList()
    {
        $entity_srv=$this->serviceLocator->get('EntityService');

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('question')
                ->from('\Application\Entity\Question','question')
                ->where('question.active_question = 1')
                ->andWhere('question.id = question.parent OR question.parent = \'\' OR question.parent is NULL')
                ->orderBy('question.department, question.sort_order')
                ;   
        // $query = $qb->getQuery()->getSQL();
        $list = [];
        $questions = $qb->getQuery()->execute();    
        foreach($questions as $question) {

            if( $question->getId() == 150 ) {
                $a = 11;
            }

            $children = $entity_srv->findBy('Question',array('parent'=>$question->getId() ));
            $haveChildren = false;
            
            foreach ($children as $child) {
                if( $question->getId() != $child->getId() ) {

                    $grand_children = $entity_srv->findBy('Question',array('parent'=>$child->getId() ));
                    $haveGrandChildren = false;

                    foreach ($grand_children as $grand_child) {
                        if( $child->getId() != $grand_child->getId() ) {

                            $levels_str = '';
                            $constraints = $grand_child->getConstraints();
                            if( !empty($constraints) ) {
                                $constraints = json_decode($constraints, true);
                                if( isset($constraints['display_levels']) ) {
                                    $levels_str .= ' (L:'. implode(',', array_map(function($v){ return $v[0]; }, $constraints['display_levels'])) .')';
                                }
                                if( isset($constraints['rollup_settings']) ) {
                                    if( !empty($levels_str) ) $levels_str .= ', ';
                                    $levels_str .= ' (R:'. implode(',', array_map(function($v){ return $v[0]; }, array_keys($constraints['rollup_settings']))) .')';
                                }
                            }

                            $haveChildren = true;
                            $haveGrandChildren = true;
                            $list[] = [
                                'value' =>  $grand_child->getId(),
                                'text' =>   str_replace(['\'','`','\\u2018', '‘'], '', $grand_child->getId() . ': ' .
                                            '('.$grand_child->getDepartment()->getDepartmentName() .') ' .
                                            $question->getCaption() . ' => ' .
                                            $child->getCaption() . ' => ' .
                                            $grand_child->getCaption()). $levels_str
                            ];
                        }
                    }

                    if(!$haveGrandChildren) {
                        $levels_str = '';
                        $constraints = $child->getConstraints();
                        if( !empty($constraints) ) {
                            $constraints = json_decode($constraints, true);
                            if( isset($constraints['display_levels']) ) {
                                $levels_str .= ' (L:'. implode(',', array_map(function($v){ return $v[0]; }, $constraints['display_levels'])) .')';
                            }
                            if( isset($constraints['rollup_settings']) ) {
                                if( !empty($levels_str) ) $levels_str .= ', ';
                                $levels_str .= ' (R:'. implode(',', array_map(function($v){ return $v[0]; }, array_keys($constraints['rollup_settings']))) .')';
                            }
                        }

                        $haveChildren = true;
                        $list[] = [
                            'value' =>  $child->getId(),
                            'text' =>   str_replace(['\'','`','\\u2018', '‘'], '', $child->getId() . ': ' .
                                        '('.$child->getDepartment()->getDepartmentName() .') ' .
                                        $question->getCaption() . ' => ' .
                                        $child->getCaption()). $levels_str
                        ];
                    }
                } 
            }

            if(!$haveChildren) {

                $levels_str = '';
                $constraints = $question->getConstraints();
                if( !empty($constraints) ) {
                    $constraints = json_decode($constraints, true);
                    if( isset($constraints['display_levels']) ) {
                        $levels_str .= ' (L:'. implode(',', array_map(function($v){ return $v[0]; }, $constraints['display_levels'])) .')';
                    }
                    if( isset($constraints['rollup_settings']) ) {
                        if( !empty($levels_str) ) $levels_str .= ', ';
                        $levels_str .= ' (R:'. implode(',', array_map(function($v){ return $v[0]; }, array_keys($constraints['rollup_settings']))) .')';
                    }
                }

                $list[] = [
                    'value' =>  $question->getId(),
                    'text' =>   str_replace(['\'','`','\\u2018', '‘'], '', $question->getId() . ': ' .
                                '('.$question->getDepartment()->getDepartmentName() .') ' .
                                $question->getCaption()). $levels_str
                ];                
            }

        }         
        
        return $list;
    }

    public function deleteQuestions( $data )
    {
        if( isset($data['id']) && !empty($data['id']) ) {
            $report = $this->entityManager->find('\Application\Entity\SummaryReport', $data['id']);
            $this->entityManager->remove($report);
            $this->entityManager->flush();
            return  ['status'=>true, 'message'=>'saved successfully'] ;   
        }
        return  ['status'=>false, 'message'=>'required data missing'] ;   
    }

    public function updateQuestions( $data )
    {
        $data = $data['data'];
        if( isset($data['id']) && !empty($data['id']) ) {
            $report = $this->entityManager->find('\Application\Entity\SummaryReport', $data['id']);
        }
        else {
            $sqlWithTotal = " select max(sort_order) max_sort_order from summary_report_questions where report_id = {$data['report_id']} ";
            $query = $this->entityManager->createQuery($sqlWithTotal);
            $rsm = new ResultSetMapping();            
            $rsm->addScalarResult('max_sort_order', 'max_sort_order');
            $query = $this->entityManager->createNativeQuery($sqlWithTotal, $rsm);  
            $max_sort_order = $query->getResult();
            $max_sort_order = $max_sort_order[0]['max_sort_order'] + 1;       

            $report = $this->entityFactory->getSummaryReport();
            $report->setSortOrder( $max_sort_order );
        }

        $repo=$this->entityManager->getRepository('Application\Entity\SummaryListReport');
        $report_list = $repo->find( $data['report_id'] );

        $report->setQuestion( $data['question_ids'] );
        $report->setReport( $report_list );
        $report->setCaption( $data['question_caption'] );
        $report->setAction( $data['question_action'] );
        $report->setLevel( $data['question_level'] );
        if( $data['question_action'] != 'countif' ) {
            $data['question_cond'] = $data['question_cond_val'] = '';
        }
        $report->setCondition( $data['question_cond'] );
        $report->setConditionValue( $data['question_cond_val'] );
        $this->entityManager->transactional(function($em) use( &$report ){
           $em->persist($report);   
           $em->flush();
        }); 

        return  ['status'=>true, 'message'=>'saved successfully'] ;   
    }

    public function orderQuestions( $data )
    {
        foreach($data['data'] as $qKey => $qVal) {
            $report = $this->entityManager->find('\Application\Entity\SummaryReport', $qKey);
            $report->setSortOrder( $qVal );
            $this->entityManager->persist($report); 
            $this->entityManager->flush();
        }
        return  ['status'=>true, 'message'=>'saved successfully'] ;   
    }
}
?>
