<?php


namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;


use Doctrine\ORM\QueryBuilder as QueryBuilder;

use ZfcDatagrid\DataSource\Doctrine2 as GridDataSource;

use Application\Util\DocumentUtil;
use Application\Entity\Period;
use Application\Entity\Report;
use Application\Entity\QuestionGroupMapping;

class ReportSubmissionService implements FactoryInterface{
        
    private $serviceLocator;
    /**
     * @var  Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    private $entityFactory;
    
	/**
	 * @ Application\Service\OfficeAssignmentService
	 */
    private $officeService;
    
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
        $this->officeService = $this->serviceLocator->get('OfficeAssignmentService');

        return $this;
    }

    private function setAnswerFromOtherReport(\Application\Entity\Report $report, $answer)
    {
        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
        $entity_srv=$this->serviceLocator->get('EntityService');
        $question_group_mapping = $entity_srv->findOneBy('QuestionGroupMapping', ['question' => $answer->getQuestion()->getId()]);
        if( $question_group_mapping ) {
            $group_questions = $entity_srv->findBy('QuestionGroupMapping', ['group' => $question_group_mapping->getGroup()->getId()]);
            $question_ids = [];
            foreach($group_questions as $question) {
                $question_ids[] = $question->getQuestion()->getId();
            }

            $qb = $this->entityManager->createQueryBuilder();
            $qb->select('assignment')
                    ->from('\Application\Entity\OfficeAssignment','assignment')
                    ->where(' assignment.branch = :branch_id')->setParameter('branch_id', $report->getBranch())
                    ->andWhere(' assignment.department = :department_id')->setParameter('department_id', $report->getDepartment())
                    ->andWhere(' assignment.status = :status')->setParameter('status', 'active');   
            // $query = $qb->getQuery()->getSQL();
            $office_assignment = $qb->getQuery()->getResult();

            if( !empty($office_assignment) ) {
                $qb = $this->entityManager->createQueryBuilder();
                $qb->select('answer')
                        ->from('\Application\Entity\OfficeAssignment','assignment')
                        ->join('\Application\Entity\Report', 'report',\Doctrine\ORM\Query\Expr\Join::WITH, $qb->expr()->andx(
                            $qb->expr()->eq('assignment.branch', 'report.branch'),
                            $qb->expr()->eq('assignment.department', 'report.department'),
                            $qb->expr()->eq('report.period_from', ':period_from')
                        ))->setParameter('period_from',$report->getPeriodFrom())
                        ->join('\Application\Entity\Answer', 'answer', \Doctrine\ORM\Query\Expr\Join::WITH, $qb->expr()->andx(
                            $qb->expr()->eq('report.id', 'answer.report'),
                            $qb->expr()->in('answer.question', ':group_questions')
                        ))->setParameter('group_questions',$question_ids)
                        ->where('assignment.user = :user')->setParameter('user', $office_assignment[0]->getUser())
                        ->andWhere(' assignment.status = :status')->setParameter('status', 'active')
                        ->andWhere(' report.status not in (:rep_status)')->setParameter('rep_status', ['deleted'])
                        ->orderBy('report.date_completed', 'desc')
                        ;   
                // $query = $qb->getQuery()->getSQL();
                $default_answer = $qb->getQuery()->getResult();
                if( is_array($default_answer) && count($default_answer) ) {
                    $answer->setValue($default_answer[0]->getValue());   
                }
            }
        }          
    }
            
   /**
    * @param Application\Entity\User
    * @param array
    */
   public function createReport($current_user, $data){
       //check access
       $user_roles = $current_user->getRoles();
       /**
        * @var Application\Entity\ReportConfig
        */
       $report_config = $this->entityManager->find('Application\Entity\ReportConfig',$data['report_config']);
       
       //roles allowed to create report as per report_config
       $allowed_roles = $report_config->getRoleCreate();

       $have_role=true;
       //if any of user roles contains any of the requires roles user can create report
       array_map(function ($role) use(&$have_role,$allowed_roles){
           $have_role=$have_role || in_array($role->getRoleId(), $allowed_roles);
       }, $user_roles);       
       
       //fix it we don't need it for now TODO FIXME so disabled       
       $have_role=true; 
       
       $errors = array();
       if(!$have_role){
          $errors[]="User ".$current_user->getDisplayName()." can't create \"".$report_config->getReportName()."\"";
       }
       
       $have_office=false;
       /**
        * @var \Doctrine\Common\Collections\Collection */
       $offices=$current_user->getOfficeAssignments();
       $offices=$offices->toArray();
       //if user have any active office in given branch with given dept or with dept in supervise list
       
       $data['branch_level'] = null;
       array_map(
           function ($office) use(&$data,&$have_office,&$offices){
                $have_office=$have_office ||
                        (
                           $office->getStatus()=="active"
                        && $office->getBranch()->getId() == $data['branch'] 
                        && (   $office->getDepartment()->getId() == $data['department'] //current office is for given dept
                            || in_array($data['department'],$office->getSuperviseDepartments()) //or given dept is in supervise list
                          )
                        );
                $data['branch_level'] = $office->getBranch()->getId() == $data['branch'] ? $office->getBranch()->getBranchLevel() : $data['branch_level'];
            }
            , $offices
       );//array_map       

       error_log('CanCreate AdminUser'.$current_user->isAdmin());

       if(!$have_office && ! $current_user->isAdmin() ){
           $errors[]="User ".$current_user->getDisplayName()." can't create report for selected Branch & Depratment";
       }
       //access check is done so let's thorw exception here and abort if user is not allowed to create report   
       if(!empty($errors)){
           throw new \InvalidArgumentException(join(". ",$errors));
       }       

       //get all qustions for the given report config that are active
       $questions = $this->getQuestions($report_config,$data);
       if(count($questions)<1){
           throw new \InvalidArgumentException('Report of type "'.$report_config->getReportName().'" can\'t be created for selected department');
       }

       //let's create report now
       $entity_srv = $this->serviceLocator->get('EntityService');
              
       /** 
        * @var Application\Entity\Report */
       $report = null;
       
       //first check if report for said branch, period_from and department exists we will not create new report 
       $report = $this->getReport($data['branch'], $data['period_from'], $data['report_config'], $data['department']);
       
       if($report && is_object($report) || (is_array($report) && count($report)>0 ) ){
           //throw new \Exception('report exists '. ($report->getId()));
           return is_array($report)?$report[0]:$report;   
       }else{
           //now that no such report exists, let's create one
           $report=$this->entityFactory->getReport();           
       }
       
       //populate report with data 
       $report->setBranch($entity_srv->getObject('Branch',$data['branch']));
       $report->setDepartment($entity_srv->getObject('Department',$data['department']));
       $report->setDateCreated(new \DateTime());
       $report->setDateModified(new \DateTime());
       $report->setPeriodFrom($entity_srv->getObject('Period',$data['period_from']));
       $report->setPeriodTo($report_config->getReportEndingPeriod($report->getPeriodFrom()));
       $report->setReportConfig($report_config);
       $report->setUserCreated($current_user);
       $report->setUserModified($current_user); 
       $submittedBy = $current_user->getDisplayName();
       if(empty($submittedBy)){
           $submittedBy = $current_user->getUsername();
       }
       $report->setSubmittedByName($submittedBy);
       
       //now that we have a report we will initialize it with empty answers for all questions
       $answers = array();
       foreach ($questions as $question) {
          $num_answers = 1;//each quesiton will have at least one answer
          //echo $question->getId()."\n";
          $constrt = $question->getConstraintsArray(); 
          if($constrt && is_array($constrt)&& key_exists('answers_count', $constrt)){
              $num_answers=$constrt['answers_count'];
          }
          
          for ($i=1; $i <= $num_answers ; $i++) { 
              $answer = $this->createAnswer($question,$report,$i);
              $this->setAnswerFromOtherReport($report, $answer);       
              $answers[] = $answer;     
          }
       }
       
       $this->entityManager->transactional(function($em) use(&$report,&$answers){
           $em->persist($report);
           foreach ($answers as $ans) {               
               $em->persist($ans);
           }
       });
           
           
       return ($report);
       
   }


   public function getAnswer($report,$question,$filter=array()){

        if(key_exists('checkForRollUp',$filter) && $filter['checkForRollUp']){
            $branchSrv = $this->serviceLocator->get('BranchManagementService');
            $response = $this->updateRollUpValueIfApplied($report, $question);

            if( $response['status'] && ($response['function']=='grid-to-grid-list' || $response['function']=='grid-to-grid-summary' || $response['function']=='grid-to-grid-group') ) {
                return $response['value'];
            }
        }

        $qb = $this->entityManager->createQueryBuilder()
                   ->select('answer')
                   ->from('\Application\Entity\Answer','answer')
                   ->join('answer.question','question')
                   ->join('answer.report','report')
                   ->leftjoin('question.parent','parent')
                   ->where('answer.report = :report')
                   ->setParameter('report', $report)
                   ->orderBy('answer.answer_number,question.sort_order')
                   ;

        if(key_exists('children',$filter) && $filter['children']){
            $qb->andWhere(' ( answer.question = :question  OR parent = :question) ')
               ->setParameter('question',$question);
        } 
        else {
            $qb->andWhere('answer.question = :question')
               ->setParameter('question',$question);
        }

       if(key_exists('answer_number',$filter) && $filter['answer_number']>0){
            $qb->andWhere('answer.answer_number = :answer_number')
               ->setParameter('answer_number', $filter['answer_number']);           
       }

       $result = $qb->getQuery()->execute();
       
       return $result;        
   }
   public function addEmptyAnswers($report_id,$question_id,$answer_number){
       
        $report = $this->getReportById($report_id);
        //if no answer number is given add next answer_number
        if( ! ($answer_number) || $answer_number < 1){
            $entity_srv=$this->serviceLocator->get('EntityService');
            $main_question = $entity_srv->getObject('Question',$question_id);
            $answers = $this->getAnswer($repor, $question, $answer_number);
            $answer_number=count($answers)+1;
        }

        $all_quesitons=$this->getQuestionWithChildren($question_id);
        $answers = array();
        
		foreach ($all_quesitons as $question) {		    
            $answer = $this->getAnswer($repor, $question, $answer_number);
            //we will create an answer if we don't already have an answer for this particular quesiton,report,answer_number combination
            if(count($answer)<1){
                $answers[]=$this->createAnswer($question, $report, $answer_number);
            }
		}

// $dump=array();
// foreach ($all_quesitons as $q) {
	// $dump[]="Q".$q->getId();
// }
// foreach ($answers as $a) {
    // $dump[]="A_Q".$a->getQuestion()->getId()." _ ".$a->getAnswerNumber();
// }
// var_dump($dump);
// exit;

       //now that we have every thing that needs to be persisted, let's wrap it inside a transaction
       $this->entityManager->transactional(function($em) use($answers){
           foreach ($answers as $answer) {
               $em->persist($answer);
           }
       });		
   }
   
   public function createAnswer($question,$report,$answerNumber){
          /**
           * @var Application\Entity\Answer
           */           
          $answer = $this->entityFactory->getAnswer();
          $answer->setCaption($report->replaceTokens($question->getCaption()));
          $answer->setQuestion($question);
          $answer->setReport($report);
          $answer->setAnswerNumber($answerNumber);
          
          return $answer;
   }
    
   public function getReportById($report_id){
       $report = $this->entityManager->find('\Application\Entity\Report', $report_id);
	   if($report && $this->checkAccess($report)){
	   	return $report;	
	   }else{
		return null;
	   }
   }


  private function fetchAnswers($report){

        /**
         * @var QueryBuilder */
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('answer')
           ->from('\Application\Entity\Answer','answer')
           ->join('answer.question','question')
           ->join('answer.report','report')
           ->where('answer.report = :report')
           ->setParameter('report', $report)
           ->orderBy(' question.sort_order , answer.question' , 'asc')
        ;
        
       return $qb->getQuery()->execute();
      
  }

   private function addChildren($question,array &$result){
       
   }
   
   public function getQuestionWithChildren($question_id){
       $entity_srv=$this->serviceLocator->get('EntityService');
       $question = $entity_srv->getObject('Question',$question_id);

       $result = array($question->getId()=>$question);

       $to_check_stack = array($question->getId());

       while(sizeof($to_check_stack)>0){
           
           $this_question = $entity_srv->getObject('Question',array_pop($to_check_stack));
           
           $children = $entity_srv->findBy('Question',array('parent'=>$this_question));
		   foreach ($children as $child) {
	           if($child->getId()!=$this_question->getId()){
	               array_push($to_check_stack,$child->getId());
			   }
			   if(! key_exists($child->getId(), $result)){
			   		$result[$child->getId()]=$child;
			   } 
		   } //for each child 
                       
       } //until we have questions to check on stack

		return $result;
		       
   }

   public function getAnswers($report){

       $all_answers = $this->fetchAnswers($report);

       foreach ($all_answers as $answer) {

           $child_answers_qb = $this->entityManager->createQueryBuilder();
           $child_answers_qb->select('answer')
               ->from('\Application\Entity\Answer','answer')
               ->join('answer.question','question')
               ->join('answer.report','report')
               ->where('question.parent != question and answer.report = :report')->setParameter('report', $report)
               ->andWhere(' question.parent = :parentQuestion')->setParameter('parentQuestion', $answer->getQuestion())
               ->andWhere('answer.answer_number = :answer_number')->setParameter('answer_number', $answer->getAnswerNumber())
               ->orderBy(' question.sort_order , answer.question' , 'asc')
               ;
           //var_dump($child_answers_qb->getQuery()->getSQL());
           $all_child_answers = $child_answers_qb->getQuery()->execute();
           foreach ($all_child_answers as $child_answer) {
                $response = $this->updateRollUpValueIfApplied($report, $child_answer->getQuestion(), ['ignore-grid-question'=>true]);
                if( $response['status'] ) {
                    $child_answer->setValue( $response['value'] );
                }  
           }
           $answer->setAnswersForChildQuestions($all_child_answers);         
           //include this answer in resultset;
           //$answers[]=$answer;
       }       

       //re-organize answers, 
       // only include top level answers (i.e. an answer that does not have a parent or when it is it's own parent
       $answers = array();
       foreach ($all_answers as $answer) {

           if($answer->getQuestion()->getParent() !=null && $answer->getQuestion()->getId()!==$answer->getQuestion()->getParent()->getId()){
               //if($answer->getId()==115770)
               // error_log('Why Child? '.$answer->getId());
               //var_dump($answer->getQuestion()->getId());           
               continue;//it is a child question, ignore it
           }else{
                $response = $this->updateRollUpValueIfApplied($report, $answer->getQuestion(), ['ignore-grid-question'=>true]);
                if( $response['status'] ) {
                    $answer->setValue( $response['value'] );
                }                
                $answers[]=$answer;
                //error_log('ID is '.$answer->getId());
           }
       }
       /*
      usort($answers,function($ans1,$ans2){
          $q1=$ans1->getQuestion();
          $q2=$ans2->getQuestion();
           if($q1->getSortOrder()==$q2->getSortOrder()){
               return $q1->getId()<$q2->getId()?$ans1:$ans2;
           }else{
               return $q1->getSortOrder()<$q2->getSortOrder()?$ans1:$ans2;
           }
        });
        */
      return $answers;
   }

   
   private function processEligiblity($value, $params, $operation)
   {
       $result_overall = null;
       foreach($params as $paramKey => $paramVal) {
           $paramKey = preg_replace('/(_([0-9])+)$/','',$paramKey);
           switch( $paramKey ) {
               case 'greater_than':         $result =  $value > $paramVal;          break;
               case 'greater_than_equal':   $result =  $value >= $paramVal;         break;
               case 'less_than':            $result =  $value < $paramVal;          break;
               case 'less_than_equal':      $result =  $value <= $paramVal;         break;
               case 'equal_to':             $result =  $value == $paramVal;         break;
               case 'abs_equal_to':         $result =  $value === $paramVal;        break;
               case 'not_equal_to':         $result =  $value != $paramVal;         break;
               case 'empty':                $result =  empty($value) == $paramVal;  break;
               case in_array($paramKey,['and','or']): $result = $this->processEligiblity($value, $paramVal, $paramKey);      break;
               default:  throw new \InvalidArgumentException("invalid Opertation");
           }
           if( $result_overall === null ) {
               $result_overall = $result;
           }
           if( $operation == 'and' ) {
               $result_overall = $result_overall && $result;
               if( $result_overall == false ) return false;
           }
           else if( $operation == 'or' ) {
               $result_overall = $result_overall || $result;
               if( $result_overall == true ) return true;
           }
       }
       return $result_overall;

   }

   private function checkEligiblity($value, $conditions)
   {
       if( count($conditions) !== 1  ) {
        throw new \InvalidArgumentException("conditions error: root level can have only one operation");
       }
        $operation = array_keys($conditions)[0];
        if( in_array($operation, ['and', 'or']) ) {
            return $this->processEligiblity($value, $conditions, $operation);
        }
        else {
            return $this->processEligiblity($value, $conditions, 'and');
        }
       
   }

   public function processConstraints($constraints, $branchLevel)
   {
       if( isset($constraints['rollup_settings']) ) {
            $rollup_settings_of_level = $constraints['rollup_settings'][$branchLevel];
            if( isset($rollup_settings_of_level['copy_from']) ) {

                # if copy_from is defined than roll up settings for copy_from level must be defined, if not throw exception
                if( isset($constraints['rollup_settings'][$rollup_settings_of_level['copy_from']]) ) {
                    $rollup_settings_of_level_actual = $rollup_settings_of_level;
                    $rollup_settings_of_level = $constraints['rollup_settings'][$rollup_settings_of_level['copy_from']];
                    unset($rollup_settings_of_level_actual['copy_from']);
                    # restore custom settings, if any
                    foreach($rollup_settings_of_level as $key => $val) {
                        if( !isset($rollup_settings_of_level_actual[$key]) ) {
                            $rollup_settings_of_level_actual[$key] = $val;
                        }
                    }
                    $constraints['rollup_settings'][$branchLevel] = $rollup_settings_of_level_actual;
                }
                else {
                    throw new \Exception("'copy_from' exception: constraints -> rollup_settings -> {$rollup_settings_of_level['copy_from']} is not defnied");
                }
            }
        }
        return $constraints;
   }

   private function updateRollUpValueIfApplied($report, $question, $additional_options=[])
   {
        $response = ['status'=>false];

        $branchSrv = $this->serviceLocator->get('BranchManagementService');
        $child_brances = $branchSrv->getChildBranches( $report->getBranch(), true );

        # Get constraints of this question
        if( !is_object($question) ) {
            $cild_questions = $this->getQuestionWithChildren($question);
            $question = $cild_questions[$question];
        }
        else{
            $cild_questions = $this->getQuestionWithChildren($question);
        }

        if($question->getId()==1934) {
            $a = 1;
        }

        $constraints = $question->getConstraintsArray();

        # check if roll up settings have been defined
        if( isset($constraints['rollup_settings']) ) {

            # get branch level of this report
            $branchLevel = $report->getBranch()->getBranchLevel();

            # check if we have any roll up settings for this branch level. if not, roll is not required
            if( isset($constraints['rollup_settings'][$branchLevel]) ) {

                $constraints = $this->processConstraints($constraints, $branchLevel);

                # check for required options for roll up setting
                if( !isset($constraints['rollup_settings'][$branchLevel]['function']) )  {
                        throw new \Exception("question:{$question->getId()}, for rollup_settings, function is mandatory");
                }
                if( !isset($constraints['rollup_settings'][$branchLevel]['questions'])  ) {
                        throw new \Exception("question:{$question->getId()}, for rollup_settings, questions is mandatory");
                }
                if( !isset($constraints['rollup_settings'][$branchLevel]['levels']) ) {
                        throw new \Exception("question:{$question->getId()}, for rollup_settings, levels is mandatory");
                }
                
                # if we need to aggregate data of my own branch as well
                if( isset($constraints['rollup_settings'][$branchLevel]['inclusive_aggregation']) && $constraints['rollup_settings'][$branchLevel]['inclusive_aggregation']===true )  {
                    $child_brances = array_merge($child_brances, [$report->getBranch()]);
                }

                #format settings as we need
                if( !is_array($constraints['rollup_settings'][$branchLevel]['questions']) ) $constraints['rollup_settings'][$branchLevel]['questions'] = [$constraints['rollup_settings'][$branchLevel]['questions']];
                if( !is_array($constraints['rollup_settings'][$branchLevel]['levels']) ) $constraints['rollup_settings'][$branchLevel]['levels'] = [$constraints['rollup_settings'][$branchLevel]['levels']];
                if( !isset($constraints['rollup_settings'][$branchLevel]['ignore_report_status']) ) $constraints['rollup_settings'][$branchLevel]['ignore_report_status'] = false;
                if( !isset($constraints['rollup_settings'][$branchLevel]['ignore_child_report_status']) ) $constraints['rollup_settings'][$branchLevel]['ignore_child_report_status'] = false;

                # validate levels
                if(count(array_unique(array_merge(['Jama`at','Imarat','Halqa','Markaz'],$constraints['rollup_settings'][$branchLevel]['levels']))) != 4) {
                    throw new \Exception("question:{$question->getId()}, for rollup_settings, in valid level value found");
                }

                # validate ignore_report_status
                if( !($constraints['rollup_settings'][$branchLevel]['ignore_report_status'] === true || $constraints['rollup_settings'][$branchLevel]['ignore_report_status'] ===false) ){
                    throw new \Exception("question:{$question->getId()}, for rollup_settings, in valid ignore_report_status value found");
                }

                # validate ignore_report_status
                if( !($constraints['rollup_settings'][$branchLevel]['ignore_child_report_status'] === true || $constraints['rollup_settings'][$branchLevel]['ignore_child_report_status'] ===false) ){
                    throw new \Exception("question:{$question->getId()}, for rollup_settings, in valid ignore_report_status value found");
                }

                if( $constraints['rollup_settings'][$branchLevel]['ignore_report_status'] === false ) {
                    if( $report->getStatus() != 'draft') {
                        $response = [   'status' => false, 
                                        'message' => 'no change', 
                                        'function' => $constraints['rollup_settings'][$branchLevel]['function']  ];
                    }
                }

                # get answers from child brances
                $rollUpAnswers = $this->entityManager->createQueryBuilder();
                $rollUpAnswers->select('answer')
                    ->from('\Application\Entity\Answer','answer')
                    ->join('answer.question','question')
                    ->join('answer.report','report')
                    ->join('report.branch','branch')
                    ->where('report.period_from = (:period_from)')->setParameter('period_from', $report->getPeriodFrom())
                    ->andWhere('report.branch IN (:branches)')->setParameter('branches', $child_brances)
                    ->andWhere('answer.question IN (:questions)')->setParameter('questions', $constraints['rollup_settings'][$branchLevel]['questions'])
                    ->andWhere('branch.branch_level IN (:level)')->setParameter('level', $constraints['rollup_settings'][$branchLevel]['levels'])
                    ;
                $rollUpAnswers ->andWhere('report.status != :status')->setParameter('status', 'deleted');
                if( $constraints['rollup_settings'][$branchLevel]['ignore_child_report_status'] !== true ) {
                    $rollUpAnswers ->andWhere('report.status != :status')->setParameter('status', 'draft');
                }
                //var_dump($rollUpAnswers->getQuery()->getSQL()); die;
                

                if( in_array($constraints['rollup_settings'][$branchLevel]['function'], ['sum', 'avg', 'count','count_branches']) ) {

                    $child_answers = $rollUpAnswers->getQuery()->execute();
                    $eligible_child_values = [];
                    $eligible_child_values_branch = [];
                    foreach($child_answers as $ans) {
                        $thisConstraints = $ans->getQuestion()->getConstraintsArray();
                        if( isset($thisConstraints['rollup_settings']) && isset($thisConstraints['rollup_settings'][$ans->getReport()->getBranch()->getBranchLevel()]) ) {
                            continue;
                        }
                        $value = trim($ans->getValue());
                        if( isset($constraints['rollup_settings'][$branchLevel]['conditions']) ) {
                            if($this->checkEligiblity($value, $constraints['rollup_settings'][$branchLevel]['conditions'])) {
                                $eligible_child_values[] = $value;
                                $eligible_child_values_branch [$ans->getReport()->getBranch()->getId()] = 1;
                            }
                        }
                        else {
                            $eligible_child_values[] = $value;
                            $eligible_child_values_branch [$ans->getReport()->getBranch()->getId()] = 1;
                        }
                    }     
                    
                    if( $constraints['rollup_settings'][$branchLevel]['function'] == 'sum' ) {
                        //$answer->setValue( array_sum($eligible_child_values) );
                        $response = [   'status' => true, 
                                        'value' => array_sum($eligible_child_values), 
                                        'function' => $constraints['rollup_settings'][$branchLevel]['function']  ];
                    }
                    else if( $constraints['rollup_settings'][$branchLevel]['function'] == 'avg' ) {
                        $response = [   'status' => true, 
                                        'value'=> (count($eligible_child_values)==0 ? 0 : round(array_sum($eligible_child_values) / count($eligible_child_values))), 
                                        'function' => $constraints['rollup_settings'][$branchLevel]['function']  ];
                    }
                    else if( $constraints['rollup_settings'][$branchLevel]['function'] == 'count' ) {
                        $response = [   'status' => true, 
                                        'value' => count($eligible_child_values), 
                                        'function' => $constraints['rollup_settings'][$branchLevel]['function']  ];
                    }
                    else if( $constraints['rollup_settings'][$branchLevel]['function'] == 'count_branches' ) {
                        $response = [   'status' => true, 
                                        'value' => count($eligible_child_values_branch), 
                                        'function' => $constraints['rollup_settings'][$branchLevel]['function']  ];
                    }
                }
                else if( $constraints['rollup_settings'][$branchLevel]['function'] == 'grid-to-grid-list' ) {
                    // if( !isset($cild_questions) ) {
                    //     $cild_questions = $this->getQuestionWithChildren($question);
                    // }
                    //$rollUpAnswers->setParameter('questions', $cild_questions);
                    $response = [   'status' => true, 
                                    'value' => (isset($additional_options['ignore-grid-question']) && $additional_options['ignore-grid-question'] ? [] : $rollUpAnswers->getQuery()->execute()), 
                                    'function' => $constraints['rollup_settings'][$branchLevel]['function']  ];
                }
                else if( $constraints['rollup_settings'][$branchLevel]['function'] == 'grid-to-grid-summary' || $constraints['rollup_settings'][$branchLevel]['function'] == 'grid-to-grid-group' ) {
                    // if( !isset($cild_questions) ) {
                    //     $cild_questions = $this->getQuestionWithChildren($question);
                    // }
                    //$rollUpAnswers->setParameter('questions', $cild_questions);
                    $response = [   'status' => true, 
                                    'value' => (isset($additional_options['ignore-grid-question']) && $additional_options['ignore-grid-question'] ? [] : $rollUpAnswers->getQuery()->execute()), 
                                    'function' => $constraints['rollup_settings'][$branchLevel]['function']  ];
                }
                else {
                    throw new \Exception("{$constraints['rollup_settings'][$branchLevel]['function']} is not a valid function");
                }

            }

        }
        return $response;
   }


   public function getReport($branch_id,$period_from,$report_config,$department_id,$include_deleted=false){
        /**
         * @var QueryBuilder */
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('report')
           ->from('\Application\Entity\Report','report')
           ->where('report.branch = :branch and report.report_config = :reportConfig and report.period_from = :periodFrom ')
           ->setParameter('branch', $branch_id)
           ->setParameter('reportConfig', $report_config)
           ->setParameter('periodFrom', $period_from)
           ;
           
       if (!$include_deleted){
           $qb->andWhere(' report.status != :deleted_status')
              ->setParameter('deleted_status', 'deleted');
       }
       
       if($department_id && $department_id>0){
           $qb->andWhere(' report.department = :department')
              ->setParameter('department', $department_id);
       }
       
       $reports = $qb->getQuery()->execute();
	   $have_access=$this->checkAccess($reports);
	   if(!$have_access){
	       return null;
	   }

       if(count($reports)==1){
            return $reports[0];
       }else {
           return $reports;
       }
   }
    
   private function checkAccess($reports){
   		
		$have_access=true;
	#	return $have_access;		
        //getcurrent user
        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
		$branches_with_offices = $this->officeService->getBranchesWithOffices($current_user);
		
		if(!is_array($reports)){
			$reports=array($reports);	
		}
		

		if(!empty($branches_with_offices) && is_array($branches_with_offices)){
			foreach ($reports as $report) {
				$branch = $report->getBranch();
				$dept = $report->getDepartment();
				//user hold a office in given branch and dept or have access to that office
				if(key_exists($branch->getId(), $branches_with_offices) && in_array($dept->getId(), $branches_with_offices[$branch->getId()])){
					$have_access= ($have_access && true);//if there is access to previously checkked reports add access for this report
				}else{
					$have_access=false;//stop access to all reports
				}
			}
		}else{
			error_log('$branches_with_offices is empty or not array');
		}
		if($current_user->isAdmin()){
            $have_access=true;
        }
		return $have_access;		
			
   }
	
   public function getQuestions($report_config,$report_params, $parent_question=null){
        $question_repo = $this->entityManager->getRepository('\Application\Entity\Question');
        /**
         * @var QueryBuilder */
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('question')
           ->from('\Application\Entity\Question', 'question')
           //->join('question.report_config', 'report_config')
           ->where('question.active_question = 1  and  question.report_config = :reportConfig');           
        $qb->setParameter('reportConfig', $report_config);
        
        if( !empty($parent_question) ) {
            $qb->andWhere( $qb->expr()->eq('question.parent', $parent_question) );
        }

        if(!empty($report_params['department'])){
            $qb->andWhere( $qb->expr()->eq('question.department', $report_params['department']) );
        }

        $qb->orderBy('question.sort_order');

        $questions = $qb->getQuery()->execute();

        $questionsFiltered = [];
        foreach($questions as $question) {
            $questionConstraints = $question->getConstraintsArray();
            if( 
                (/*!isset($questionConstraints['display_levels']) ||*/ (is_array($questionConstraints['display_levels']) && in_array($report_params['branch_level'], $questionConstraints['display_levels']))) &&
                (!isset($questionConstraints['display_months']) || (is_array($questionConstraints['display_months']) && in_array(date('m', strtotime($report_params['period_from'])), $questionConstraints['display_months']))) &&
                (!isset($questionConstraints['effective_from']) || (!empty($questionConstraints['effective_from']) && strtotime($report_params['period_from'])>=strtotime($questionConstraints['effective_from']) ) ) &&
                (!isset($questionConstraints['effective_to']) || (!empty($questionConstraints['effective_to']) && strtotime($report_params['period_from'])<=strtotime($questionConstraints['effective_to']) ) )
             ) {
                $questionsFiltered[] = $question; 
            }
        }
        return $questionsFiltered;
    }
    
    public function addYearMonthFilter($qb,$filter_criteria){

       //filter by year/month criteria if provided
       if($filter_criteria && isset($filter_criteria['monthyear'])){
           //All months for the year are requsted
           $monthyear = $filter_criteria['monthyear'];
           $all_months=empty($monthyear['month']);
           
            if(!$all_months && !empty($monthyear['year'])){
                $date = new \DateTime();
                $date->setDate($monthyear['year'],$monthyear['month'],1);
                $period_code=$date->format('M-Y');
                $qb->andWhere("period_from.period_code = '$period_code'");
            }elseif(!$all_months && empty($monthyear['year'])){
                $date = new \DateTime();
                //use dummy year we just need to convert to month name
                $date->setDate(2000,$monthyear['month'],1);
                $period_code = $date->format('M-%');
                $qb->andWhere("period_from.period_code like '$period_code'");
            }elseif(!empty($monthyear['year'])){
                
                $start=Period::getStartOfYear($monthyear['year'])->format('Y-m-d');
                $end=Period::getEndOfYear($monthyear['year'])->format('Y-m-d');
                $qb->andWhere("date(period_from.period_start) >= date('$start')");
                $qb->andWhere("date(period_from.period_end) <= date('$end')");
                
                error_log("date(period_from.period_start) >= date('$start')");
                error_log("date(period_from.period_end) <= date('$end')");
            }
                      
       }

       return $qb;           
        
    }
    
    public function createReportsDataSource($user_id,$filter_criteria=null){
       
        $office_srv = $this->serviceLocator->get('OfficeAssignmentService');
        
        $branches_with_offices = $office_srv->getBranchesWithOffices($user_id);

        $branchSrv = $this->serviceLocator->get('BranchManagementService');

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('report')
        
           ->from('\Application\Entity\Report','report')
           
           ->join('report.department','department')                      
           ->join('report.branch','branch')
           ->join('report.period_from','period_from')
           ->join('report.period_to','period_to')           
           ;

       // we don't want ORs as first where clause expr
       // otherwise add on expr might cause issues TODO confirm if we really need this
       $qb->where(' report.status != :deleted_status ')
          ->setParameter('deleted_status', 'deleted');
           ;

       //if branch_type filter is not  given we assume exclude Halqa
       if( isset($filter_criteria['toolbarFilters']['branch_branch_level']) && $filter_criteria['toolbarFilters']['branch_branch_level'] == '!= Halqa' ){
           error_log('Excluding Halqa');
           $qb->andWhere(' branch.branch_level != :branch_level ')
              ->setParameter('branch_level', 'Halqa');
            ;
        }
error_log(print_r(['InCreateDataSource',$filter_criteria],true));      


 
       $qb = $this->addYearMonthFilter($qb,$filter_criteria);
             
       $expr_builder=$qb->expr();
       $orX = $expr_builder->orX(); 
       
       foreach ($branches_with_offices as $branch => $dept_list) {
           $branches = $branchSrv->getChildBranches( $branch);
           array_push($branches,$branch);
           $orX->add(
                $expr_builder->andX(
                    $expr_builder->in('branch',$branches),
                    $expr_builder->in('department', $dept_list)
                )
            );    
       }
              
       $qb->andWhere($orX); //–  Lou Terrailloune Oct 15 '12 at 13:45  from StackOverflow

      // error_log(print_r($qb->__toString(),true));
        // echo $qb->getQuery()->getSQL(); die;
       $data_source = new GridDataSource($qb);
        
       return $data_source;
   }        



  private function updateReportStatus($report,$current_user,$data,$answers_keyed){
           //ReportConfig have a config array that have report status flow config
           $curr_status = (isset($data['report_status']) && !empty($data['report_status']) ? $data['report_status'] : $report->getStatus());
           $reportConfig = $report->getReportConfig();
           $configs=$reportConfig->getConfigArray();
          //default config applies to a report unless we have a special config for branch level of owning branch
          $branch_level=$report->getBranch()->getBranchLevel();
          $config_level='Default';
          if(key_exists($branch_level,$configs['report_flow'])){
              $config_level=$branch_level;
          }
           
           $status_config=$configs['report_flow'][$config_level][$curr_status];
           
           //TODO restrict submission based on current role
           //var_dump($status_config);exit;
           
           //get next status based on status flow
           $next_status=$status_config['next_status'];
           $prev_status = key_exists('prev_status', $status_config)?$status_config['prev_status']:false;           

           //update status
           if($data['user_action']=='submit'){
                $report->setStatus($next_status);
           }elseif($data['user_action']=='back'){
               $report->setStatus($prev_status);
               $next_status=$prev_status;               
           }elseif($data['user_action']=='delete'){
               $report->setStatus('deleted');
               $next_status='deleted';
           }else{
               throw new \Exception("Undefined action ".$data['user_action']);
           }
           //we also need to update date and user for each status for the user that submitted from that status
           $date_method='setDate'.ucfirst($configs['status_map'][$next_status]);
           $user_method='setUser'.ucfirst($configs['status_map'][$next_status]);
           if(method_exists($report,$date_method)){
                $report->$date_method(new \DateTime);
           }
           if(method_exists($report,$user_method)){
               $report->$user_method($current_user);           
           }
           
           //send email for report status change if appropriate
           $this->reportStatusChangeNotification($report,$status_config,$current_user,$answers_keyed,$next_status, $data);
            
           return $report;
  }
   

   public function saveReport($report,$current_user,$data,$files){

       if(!is_object($report))
            throw new \InvalidArgumentException('Report can\'t be null');

       /** 
        * @var Application\Entity\Report */
       $report=$report;
       
       $report->setDateModified(new \DateTime());
       $report->setUserModified($current_user);
       
       //first we need to update/save file attachments
       $attachments = $this->saveAttachments($report,$files);
       foreach ($attachments as $key => $attachment) {
           $data[$key]=json_encode($attachment);
       }
       
       //get saved answers, i.e. alnswers already in DB and linked to this report
       $answers = $this->fetchAnswers($report);
       $answers_keyed=array();
       //let's update these known answsers
       foreach ($answers as $answer) {
           $akey=$answer->getAnswerKey();
           if(key_exists($akey, $data)){
               $answer->setValue($data[$akey]);
               $answers_keyed[$akey]=$answer;
           }else{
               //error_log('Unable to find saved answer for '.$akey);
           }
       }        
       //Save answers that are "new" i.e. not created yet  
       $new_answers = array_diff_key($data, $answers_keyed);
       foreach ($new_answers as $key=>$value){
           $key_parts=explode('_', $key);
           if($key_parts[0]!=='Q'){               
                // if($key_parts[0]!=='Q' || trim($value)==''){               
               continue;//this posted value is not an answer to a question
           }
           $question = $this->serviceLocator->get('EntityService')->getObject('Question',$key_parts[1]);
           //since this is new answer (i.e. not in db already create one)
           $answer = $this->createAnswer($question, $report, $key_parts[2]);
           $answer->setValue($data[$key]);
           $answers_keyed[$key]=$answer;
       }

       //delete answer if needed
       if($data['user_action']=='del'){           
           $answers = $this->getAnswer($report, $data['del']['question_id'],array('children'=>true,'answer_number'=>$data['del']['answer_number']) );
           
           $this->entityManager->transactional(function($em) use(&$answers){
               //delete all answers
               foreach ($answers as $answer) {
                $em->remove($answer);                   
               }
           });
       }

       //update report status
       if(in_array($data['user_action'],array('submit','back','delete') ) ){
           $report = $this->updateReportStatus($report, $current_user, $data,$answers_keyed);
       }
       
       //now that we have every thing that needs to be persisted, let's wrap it inside a transaction
       $this->entityManager->transactional(function($em) use(&$report,&$queries,&$answers_keyed){
           $em->persist($report);
           foreach ($answers_keyed as $key=>$answer) {
               $em->persist($answer);
           }
       });
   }//saveReport

   
   public function saveAttachments($report,$files){
       $files_to_save=array();
       $doc_util=$this->serviceLocator->get('DocUtil');
       $saved_files=array();
       foreach ($files as $question => $file) {
          //Process only if file is uploaded
          if($file['name']!=''&&$file['tmp_name']!=''&&$file['error']==0){
              //Save file to attachments area
              $this_saved_files = $doc_util->saveUploadedFiles(array($file),DocumentUtil::UPLOAD_TYPE_ATTACHMENT);
              $saved_files[$question]=$this_saved_files;
              //data to be saved
          }//if file is attached
          else{//if file is already saved earlier keep that
              $question_keys =explode('_', $question);
              $answers = $this->getAnswer($report,$question_keys[1]);
			  if($answers){
	              $saved_files[$question]=json_decode($answers[0]->getValue());
			  }
          }
       }//foreach attachment question 
    
       //updated  $saved_files with file information
       //return it;
       return $saved_files;
   }

    private function replaceTokens($report,$caption){
        $parent_office_bearer_designation = $report->getBranch()->getParent()?$report->getBranch()->getParent()->getOfficeBearerDesignation():'N/A'; 
        $tokens = array(
                'branch_head_title' => $report->getBranch()->getBranchHeadTitle(),
                'office_bearer_designation' => $report->getBranch()->getOfficeBearerDesignation(),
                'parent_office_bearer_designation' => $parent_office_bearer_designation,
                'office_bearer_title' => $report->getDepartment()->getOfficeBearer(),
            );
        
        foreach ($tokens as $key => $value) {
            $caption = preg_replace("/##$key##/", $value, $caption);
        }
        return $caption;
    }


  public function reportStatusChangeNotification($report,$next_status_config,$current_user,$answers_keyed,$status_override, $data){
      
      $next_status=$next_status_config['next_status'];
      //if an override status is given override default configured next status
      if($next_status !== $status_override){
          $next_status = $status_override;
      }
      
      $office_srv = $this->officeService;
      $report_office = $office_srv->getBranchDepartmentOffice($report->getBranch(),$report->getDepartment()->getDepartmentName());
      if($report_office==null){
          //first office of user who created report
          $offices = $office_srv->getActiveOffices($report->getUserCreated());
          $report_office=$offices[0];
      }
      $sender_office = $office_srv->getActiveOffices($current_user);
      $sender_office=$sender_office&&count($sender_office)>0?$sender_office[0]:null;
      $report_parent_office = $office_srv->getBranchDepartmentOffice($report->getBranch()->getParent(),$report->getDepartment()->getDepartmentName());
      if($report_parent_office==null){
          //get office of "GS" of parent
         $report_parent_office = $office_srv->getBranchDepartmentOffice($report->getBranch()->getParent(),'General Secretary');
         if(is_array($report_parent_office))
            $report_parent_office=$offices[0];
      }
      //last attempt, check if branch is Markaz, send to GS
      if($report_parent_office==null && $report->getBranch()->getBranchLevel()=='Markaz'){
         $report_parent_office = $office_srv->getBranchDepartmentOffice($report->getBranch(),'General Secretary');
      }

      $user_to_notify=null;
      $office_of_next_user=null;
      $subject='';
      $report_messages=array();
      $all_report_messages=$this->getReportMessagesData($report,$answers_keyed);
      $report_messages=key_exists($next_status, $all_report_messages)?$all_report_messages[$next_status]:array();
      $feedback_message=array();
      $link_type=null;
      $email_president=false;
      switch ($next_status) {
          case 'draft':
              $link_type='feedback';
              $office_of_next_user = $report_office;              
              $subject=('ACTION REQUIRED: Report is returned for you to complete');
              array_map(function($e) use(&$feedback_message){$feedback_message[$e['from_office']->getTitle(true)]=$e['message'];}, $report_messages);
              break;
              
          case 'completed':
              $office_of_next_user = $office_srv->getBranchDepartmentOffice($report->getBranch(),'President');              
              $subject=('ACTION REQUIRED: Report is submitted for you to be verified');
              break;
          
          case 'verified':
              $office_of_next_user = $report_parent_office;              
              $subject=('ACTION REQUIRED: Report is submitted for you to be received');
              array_map(function($e) use(&$feedback_message){$feedback_message[$e['from_office']->getTitle(true)]=$e['message'];}, $report_messages);
              break;

          case 'received':
              $office_of_next_user = $report_office;
              $subject='Report has been received ';
              $link_type='feedback';
              //combine all messages to feed back
              array_map(function($e) use(&$feedback_message){$feedback_message[$e['from_office']->getTitle()]=$e['message'];}, $report_messages);
              //we will consider notification message as feed back message and not include feedback message              
              $report_messages=array();
              if(isset($data['email_president']) && $data['email_president']!=''){
                $email_president=true;
              }
              break;

          default:
              //DO NOTHING
              //for all otehr state transitions we don't send any emails              
              break;
      }

      if( isset($data['user_action']) && $data['user_action']=='back' && isset($data['feedback']) && !empty($data['feedback']) ) {
        $feedback_message[] = $data['feedback'];
      }

      if($office_of_next_user && $office_of_next_user->getUser()){
          $to_email=$office_of_next_user->getUser()->getEmail();
          $template='report_status_notification';
          $data=array('report'=>$report,'next_status_config'=>$next_status_config,
                      'report_office'=>$report_office,'to_office'=>$office_of_next_user,
                      'from_office'=>$sender_office,'report_parent_office'=>$report_parent_office,
                      'template'=>$template,'to_email'=>$to_email,'subject'=>$subject,
                      'current_user'=>$current_user,
                      'message'=>$feedback_message,
                      'report_messages'=>$report_messages,
                      'link_type'=>$link_type,
                      'next_status'=>$next_status
                      );
          //Send email to president of reporting Jamaat
          if($email_president){
            $presidentDept = $this->serviceLocator->get('ConfigService')->getConfig('president_department');
            $presidentOffice = $this->officeService->getBranchDepartmentOffice($report->getBranch(),$presidentDept); 
            $presidentEmail = $presidentOffice->getUser()->getEmail();
            if(strlen($presidentEmail)>1){
                $data['cc_email']=$presidentEmail;
            }
          }//send email to preseident
                      
          $msg_srv = $this->serviceLocator->get('MessagesService');
          
          $msg_srv->createReportNotification($data);
          
      }

    }//reportStatusChangeNotification

  
  public function getReportMessagesData($report,$answers_keyed){
      $report_messages_data=array();
      foreach ($answers_keyed as $key => $answer) {

          $value=$answer->getValue();
          if(empty($value)){
              //ignore questions where there is no answer provided
              continue;
          }
          $question = $answer->getQuestion();
          $report_msg_config = $question->getConstraint('report_messages');
          if(! $report_msg_config ){
              //ignore questions where report_message constraint is not reuqired 
              continue;
          }

          if(! key_exists($report_msg_config['report_status'], $report_messages_data) ){
              $report_messages_data[$report_msg_config['report_status']]=array(); 
          }
          
          //generate data
          $report_office=$this->officeService->getBranchDepartmentOffice($report->getBranch(),$report->getDepartment()->getDepartmentName());          
          $to_office = $report_office;
          if($to_office==null){
              //first office of user who created report
              $offices = $this->officeService->getActiveOffices($report->getUserCreated());
              $report_office=$offices[0];
          }
          if($report_msg_config['to_office'] == 'parent'){
              $to_office = $this->officeService->getBranchDepartmentOffice($report->getBranch()->getParent(),$report->getDepartment()->getDepartmentName());
              if($to_office==null){
                  //get office of "GS" of parent
                 $office = $this->officeService->getBranchDepartmentOffice($report->getBranch()->getParent(),'General Secretary');
                 $report_parent_office=$office;
                 //if not found find office President of current branch
                 if($to_office==null){
                     $office = $this->officeService->getBranchDepartmentOffice($report->getBranch(),'President');
                     $report_parent_office=$office;
                 }
                 $to_office = $report_parent_office;
              }
          }
          
          $from_office = $this->officeService->getBranchDepartmentOffice($report->getBranch(),$report->getDepartment()->getDepartmentName());
          if($from_office==null){
              //first office of user who created report
              $offices = $this->officeService->getActiveOffices($report->getUserCreated());
              $from_office=$offices[0];              
          }
          if($report_msg_config['from_office'] == 'parent'){
              $from_office = $this->officeService->getBranchDepartmentOffice($report->getBranch()->getParent(),$report->getDepartment()->getDepartmentName());
              
              if($from_office==null){
                  //get office of "GS" of parent
                 $offices = $this->officeService->getBranchDepartmentOffice($report->getBranch()->getParent(),'General Secretary');
                 $report_parent_office=$office[0];
              }
          }
          if($report_msg_config['from_office'] == 'president'){
              $from_office = $this->officeService->getBranchDepartmentOffice($report->getBranch(),'President');
          }

          $office_title = $from_office?$from_office->getTitle(true):'';

          $this_report_msg_data = array('from_office'=>$from_office,'to_office'=>$to_office,'report_office'=>$report_office,
                                        'report'=>$report,'link_type'=>$report_msg_config['link_type'],
                                        'message'=>$answer->getValue(),'status'=>$report_msg_config['report_status'],
                                        'message_title'=>$report_msg_config['title'],
                                        'subject'=>sprintf("ACTION REQUIRED: %s %s",$report_msg_config['title'],$office_title),
                                        'template'=>'report_message'
                                        );
                                                  
          //print_r("Message is ".$this_report_msg_data['message']);exit;
          //$this->serviceLocator->get('Logger')->err('Adding report message for status '.$report_msg_config['report_status'].' Question '.$key.$answer->getValue());
          
          //append this report_message
          $report_messages_data[$report_msg_config['report_status']][]=$this_report_msg_data;
          
          //$this->serviceLocator->get('Logger')->err('Count for '.$report_msg_config['report_status'].' is now '.count($report_messages_data[$report_msg_config['report_status']]));
      }

      
    //$this->printMsgData($report_messages_data);  
    return $report_messages_data;
  }//getReportMessagesData

  private function printMsgData($report_messages_data){
      $data=$report_messages_data;
      
      print_r('<pre>');
      print_r(array_keys($data));
      foreach ($data as $s=>$msgs) {
          print_r(($s));
          if(is_array($msgs))      
          foreach ($msgs as $m) {
              print_r(array_keys($m));
          }
      }
      //print_r($pdata);
      print_r('</pre>');
  }
  
  public function canView($user,$report){
      //TODO FIXME
      return true;
  }
  
  public function canEdit($user,$report){
      
      //if report is in a status that is readonly no one can edit it
      //@OfficeAssignmentService
      $office_srv = $this->serviceLocator->get('OfficeAssignmentService');
      $status =$report->getStatus();

      //no one can edit in received      
      if($status == 'received'){
         return false;
      }

      $president_dept = $this->serviceLocator->get('ConfigService')->getConfig('president_department');

     //only user, President or GS can edit
      if($status == 'draft'){
        $active_report_office = $office_srv->getActiveOffices($user,$report->getBranch(),$report->getDepartment()); 
        
        $gs_dept = $this->serviceLocator->get('ConfigService')->getConfig('gs_department');
        $active_gs_office = $office_srv->getActiveOffices($user,$report->getBranch(),$gs_dept);

        $active_president_office = $office_srv->getActiveOffices($user,$report->getBranch(),$president_dept,true);
        
        $same_office = $active_report_office!=null && count($active_report_office)==1;
        $gs_same_branch = $active_gs_office !=null && count($active_gs_office)==1;

        $assistant_of_same_as_report_dept = false;
        foreach($user->getOfficeAssignments() as $office) {
            if(
                $office->getDepartment()->getAssistantOf() &&
                $report->getDepartment()->getId() == $office->getDepartment()->getAssistantOf()->getId() &&
                $office->getBranch()->getId() == $report->getBranch()->getId()
            ) {
                $assistant_of_same_as_report_dept = true;
                break;
            }
        }
        
        $president_same_branch = $active_president_office !=null && count($active_president_office)==1;
error_log('Can Edit Adminuser '.$user->isAdmin());       
        return  $same_office || $gs_same_branch||$president_same_branch||$user->isAdmin() || $assistant_of_same_as_report_dept;
        
      }
            
     //only President can edit
      if($status == 'completed'){

        $active_report_office = $office_srv->getActiveOffices($user,$report->getBranch(),$president_dept,true); 
        //user have office        
        return $active_report_office!=null && count($active_report_office)==1;
      }


     //only superior counter part can edit
      if($status == 'verified'){
        $allow_supervise=true;
        $active_report_office = $this->officeService->getActiveOffices($user,$report->getBranch()->getParent(),$report->getDepartment(),$allow_supervise);

        //user have office        
        return $active_report_office!=null && count($active_report_office)==1;
      }

      return false;
      
  }//canEdit


}//ReportSubmissionService

