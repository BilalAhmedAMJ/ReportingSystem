<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Form\Form;
use Zend\Form\Element;
use Dompdf\Dompdf;



use ZfcDatagrid\Column;

use Application\View\HighChart\DataTransform;


class ReportController extends AbstractActionController
{

    private $navigation_keys = array('sord','sidx','_search','page','rows','nd');
    
    public function getListForm($user,$post){

        $form = new Form();           

        $configSrv = $this->getServiceLocator()->get('ConfigService');
        
        $months = $configSrv->getConfig('months');
        //$month_value = $months_simple[];
        $monthYear = new Element\MonthSelect('monthyear');
        $monthYear->setMinYear(2017);
        $monthYear->setShouldRenderDelimiters(false);
        
        $monthYear->getMonthElement()->setValueOptions($months);

        if(key_exists('monthyear', $post)){
            $monthYear->getMonthElement()->setValue($post['monthyear']['month']);
            $monthYear->getYearElement()->setValue($post['monthyear']['year']);
        }      
        
        $monthYear->getMonthElement()->setEmptyOption('All');
        $monthYear->getYearElement()->setEmptyOption('All');
        $form->add($monthYear);
        
        $officeSrv = $this->getServiceLocator()->get('OfficeAssignmentService');
        $offices=$officeSrv->getBranchesWithOffices($user);
                
        $branchSrv = $this->getServiceLocator()->get('BranchManagementService');
        $branches = $branchSrv->listBranchNames(array_keys($offices));
        $branches = array_values($branches);
        $branches = array_combine($branches,$branches);
        
        
        $dept_ids=array();
        foreach($offices as $b=>$depts){
            $dept_ids=array_merge($dept_ids,$depts);
        }
        $dept_ids=(array_unique($dept_ids));
        $departments = $configSrv->listDepartmentNames($dept_ids,array('reportable'=>1));
        //$departments = array_values($configSrv->listDepartmentNames(array_values($offices)));

        $departments = array_combine($departments,$departments);

        $form->setOption('branches',$branches);
                
        $form->setOption('departments',$departments);
        
        if($this->getRequest()->isPost() ){
            $form->setData($post);
        }
        
        $form->prepare();

        return $form;
    }

    public function listAction(){
                
        $srv = $this->getServiceLocator()->get('ReportSubmissionService');
    
        $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
                  
        $req=$this->getRequest();
        $config = $this->getServiceLocator()->get('ConfigService');
        
        $grid_manager = $this->getServiceLocator()->get('ZfcDataGridFactory');
        $grid_name='ReportsList';
        $grid = $grid_manager->createGrid($grid_name);       
        
        $post = $grid->getSession()->postParameters;
        if($this->getRequest()->isPost()){
            $post=$this->params()->fromPost();
        }

        $initFilters = [];
        if( !isset($post['toolbarFilters']) || !isset($post['toolbarFilters']['branch_branch_level']) ){
            $office = $this->getServiceLocator()->get('OfficeAssignmentService')->getActiveOffices($user_id);
            $post['toolbarFilters']['branch_branch_level'] = ($office[0]->getBranch()->getBranchType() == 'Markaz' ? '!= Halqa' : '');
            $initFilters['branch_branch_level'] = true;
        }
        if( !isset($post['monthyear']) || !isset($post['monthyear']['year']) ){
            $lastMonth=date("m",strtotime("-1 MONTH"));
            if( $lastMonth < 7){
                $post['monthyear']['year'] = date('Y', strtotime('-13 MONTH'));
            }else{
                $post['monthyear']['year'] = date('Y', strtotime('-0 MONTH'));
            }
        }
        /*if( !isset($post['monthyear']) || !isset($post['monthyear']['month']) ){
            $post['monthyear']['month'] = date('m', strtotime('-1 MONTH'));
        }*/
        
        $form = $this->getListForm($user_id,$post);
        $data_source =$srv->createReportsDataSource($user_id,$post);        
        
        $grid->setTitle('Reports List');
        $grid->setDataSource($data_source);
        //$grid->setUserFilterDisabled(true);
        //$grid->setToolbarTemplate('partial/bootstrap_datagrid/reports_toolbar.phtml');
        $grid->setToolbarTemplate('application/report/reports_toolbar.phtml');

        $datagrid_helper = $this->getServiceLocator()->get('DataGridHelper');
		
		$col_options = array(
							'departments'=>$form->getOption('departments'),
							'branches'=>$form->getOption('branches')
						);
        $datagrid_helper->grid('ReportList',$col_options)->addColumns($grid);

        $office = $this->getServiceLocator()->get('OfficeAssignmentService')->getActiveOffices($user_id);

        if( isset($initFilters['branch_branch_level']) ){
            $grid->getColumnByUniqueId('branch_branch_level')->setFilterActive( ($office[0]->getBranch()->getBranchType() == 'Markaz' ? '!= Halqa' : '') );
        }

        // $grid->getColumnByUniqueId('branch_branch_level')->setFilterActive( array('Halqa','Jama\'at','Imarat') );

        $rowAction = new \ZfcDatagrid\Column\Action\Button();
        $rowId = $rowAction->getRowIdPlaceholder();

        $rowAction->setLink($this->url()->fromRoute('report/report').'?report_id='.$rowId);

        //var_dump(array($rowId,$rowAction->getLink()));
        //toolbarFilters[department_department_name]:Additional+Secretary+Mal

        $grid->setRowClickAction($rowAction);

        $datagrid_helper->filtersFromCache($grid,$this->getRequest());


        $office = $this->getServiceLocator()->get('OfficeAssignmentService')->getActiveOffices($user_id);
        if($this->params()->fromQuery('filter')=='own'){
            if($office[0]->getDepartment()->getReportable()){
                $grid->getColumnByUniqueId('department_department_name')->setFilterDefaultValue(
                                $office[0]->getDepartment()->getDepartmentName()
                                );
            }
        }
        
        // if(!$this->getRequest()->isPost()){
        //     if( $office[0]->getBranch()->getBranchType() == 'Markaz' ) {
        //         $grid->getColumnByUniqueId('branch_branch_level')->setFilterActive('!= Halqa');
        //     }
        // }
        $grid->getViewModel()->setVariable('form',$form);                

        $grid->render();

        $viewModel=new ViewModel(array('form'=>$form));
        
        $viewModel->setTemplate('application/report/list');

        $viewModel->addChild($grid->getResponse(),'reports_grid');
        
        return $viewModel;
    }

    public function createAction(){
        /*
        if( $this->params()->fromQuery('test') == '3375' ) {

                $usr_srv = $this->getServiceLocator()->get('UserProfileService');
                $usr_srv->createUser([
                    'email' => 'naila.mahmood@ahmadiyya.ca',
                    'full_name' => 'Naila Mahmood',
                    'member_code' => '7140',
                    'phone_primary' => '',
                    'phone_alternate' => '',
                    'username' => 'naila.mahmood',
                    'status' => 'active',
                    'password' => '4sE#bl3)!Kc$',
                ], false);
            
            
                $entity_mgr  = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $qb = $entity_mgr->createQueryBuilder();
                $qb->select('user')
                        ->from('\Application\Entity\User', 'user');
                $users = $qb->getQuery()->execute();
                foreach($users as $user) {
                    echo $user->getId() . ' - ' .  $user->getMemberCode() . ' - ' . $user->getUsername() . ' - ' .   $user->getEmail() . ' - ' . $user->getPhonePrimary() .  '<br />';
                }
                die;
            }
            /**/
        
        /**
         * @var Zend\Form\Form
         */
        $form= $this->getServiceLocator()->get('CreateFormService')->getform('create_report');

        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
    
        if($this->getRequest()->isPost()){
            $post_data=$this->params()->fromPost();
            $form->setData($post_data);
            /** 
             * @var Application\Service\ReportSubmissionService  */
            $report_srv = $this->getServiceLocator()->get('ReportSubmissionService');
            try{
                
                $report = $report_srv->createReport($current_user, $post_data);
                
                $url=($this->url()->fromRoute('report/report').'?report_id='.$report->getId());
                $this->redirect()->toUrl($url);
                //$this->redirect()->toRoute('report/report',array('report_id' => $report->getId()), array('report_id' => $report->getId()));
                
            }catch(\Exception $exp){
                $this->flashMessenger()->addErrorMessage('Unable to create report : '.$exp->getMessage());
            }            
        }else{
            $offices = $this->getServiceLocator()->get('OfficeAssignmentService')->getActiveOffices($current_user);
            $period=\Application\Entity\Period::createCurrent();
            $period = $period->addMonths(-1);//default period is last month
            $data = array('period_from'=>$period);
            if(count($offices)==1){
                $data = array_merge($data,array('branch'=>$offices[0]->getBranch()->getId(),
                                            'branch_type'=>$offices[0]->getBranch()->getBranchType(),
                                            'department'=>$offices[0]->getDepartment()->getId(),
                                            )
                                );
            }else{
                $data['branch_type']='Branch';
            }
            
            $form->setData($data);
        }
        
        return new ViewModel(array('form'=>$form,'branch_type'=>$data['branch_type']));
    }      


    public function attachmentAction(){

        //if request is to download attachment
        $action = $this->params()->fromQuery('a');
        $response=null;
        if($action === 'download'){

            $file_name=$this->params()->fromQuery('f');
            $question=split('_',$this->params()->fromQuery('q'));
            $report_id=$this->params()->fromQuery('r');

            
            $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
            $report_srv = $this->getServiceLocator()->get('ReportSubmissionService');
            $report = $report_srv->getReportById($report_id);
            
            $can_view = $report_srv->canView($current_user,$report);
            if($can_view){
                $answer = $report_srv->getAnswer($report,$question[1]);

                $file_info = json_decode($answer[0]->getValue(),true);
                
                $response = $this->getServiceLocator()->get('DocUtil')->attchmentToResponse($file_info[0]);
                if(!$response){
                    $this->flashMessenger()->addErrorMessage("Attachment requested, does not exist!");
                    return $this->redirect()->toUrl($this->url()->fromRoute('report/report').'?report_id='.$report->getId());                    
                }
            }else{
                $this->flashMessenger()->addErrorMessage("Not authorized to view this report!");
                $response=null;            
            }
        }else{
            $this->flashMessenger()->addErrorMessage("No attachement to download. $path");
            $response=null;            
        }                
        return $response;
    }
    public function answersDataAction(){
        
        $report_id = $this->params()->fromPost('report_id');
        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
        $report_srv = $this->getServiceLocator()->get('ReportSubmissionService');
        $report = $report_srv->getReportById($report_id);
        $result=array();
        if($report_srv->canView($current_user,$report)){
            
            $question_id = $this->params()->fromPost('question_id');
            $answers = $report_srv->getAnswer($report,$question_id,array('children'=>TRUE, 'checkForRollUp'=>TRUE));

            $child_questions = $report_srv->getQuestions(
                $report->getReportConfig(),
                ['department'=>$report->getDepartment()->getId(), 'branch_level'=>$report->getBranch()->getBranchLevel(), 'period_from'=>$report->getPeriodFrom()->getPeriodCode()], 
                $question_id
            );
                        
            //$child_question = $report_srv->getQuestionWithChildren($question_id);

            $parent_question=$this->serviceLocator->get('EntityService')->getObject('Question',$question_id);
            $parent_question_constraints = $parent_question->getConstraintsArray();
            $branchLevel = $report->getBranch()->getBranchLevel();
            $parent_question_constraints     = $report_srv->processConstraints($parent_question_constraints, $branchLevel);
            
            $grid_data=array();
            
            //$$parent_question_constraints['rollup_settings'][$branchLevel]['questions']
            foreach ($answers as $answer) {
                
                $availabl_questions[$answer->getQuestion()->getId()] = 1;
                $key='ans_'.$answer->getReport()->getBranch()->getId().'_'.$answer->getAnswerNumber();
                if(!key_exists($key,$grid_data)){
                    $grid_data[$key]=array('answer_number'=>$answer->getAnswerNumber() );
                }
                if( isset($parent_question_constraints['rollup_settings'][$branchLevel]['branch_indetifier_child']) ) {
                    $grid_data[$key][$parent_question_constraints['rollup_settings'][$branchLevel]['branch_indetifier_child']] = $answer->getReport()->getBranch()->getBranchName();
                }
                $grid_data[$key][$answer->getQuestion()->getId()]=$answer->getValue();
                $grid_data[$key]['report_id']=$report_id;
                $grid_data[$key]['question_id']=$question_id;
                $grid_data[$key]['answer_id']=$answer->getId();
            }

            $first_empty_row = true;
            if( isset($parent_question_constraints['rollup_settings'][$branchLevel]['branch_indetifier_child']) ) {
                unset( $availabl_questions[$parent_question_constraints['rollup_settings'][$branchLevel]['branch_indetifier_child']] );
            }            
            $availabl_questions = array_keys($availabl_questions);

            if( isset($parent_question_constraints['rollup_settings'][$branchLevel]['function']) ) {

                if( $parent_question_constraints['rollup_settings'][$branchLevel]['function'] == 'grid-to-grid-list' ) {

                    $questions_mapping = array_flip($parent_question_constraints['rollup_settings'][$branchLevel]['questions']);
                    # in case of rollup, remove empty rows except first one
                    foreach ($grid_data as $key => &$val) {

                        # if rollung up from differnt grid, map questions as defined in settings
                        foreach( $questions_mapping as $mKey => $mVal ) {
                            if( isset($val[$mKey]) ) {
                                $val[$mVal] = $val[$mKey];
                                unset($val[$mKey]);
                            }
                        }

                        $all_empty = true;
                        foreach ($questions_mapping as $question) {
                            if( !empty($val[$question]) ) {
                                $all_empty = false;
                            }

                        }
                        if( $all_empty ) {
                            if( $first_empty_row==true ) {
                                $first_empty_row = false;
                                # if everything is empty remove branch as well
                                if( isset($parent_question_constraints['rollup_settings'][$branchLevel]['branch_indetifier_child']) ) {
                                    $grid_data[$key][$parent_question_constraints['rollup_settings'][$branchLevel]['branch_indetifier_child']] = null;
                                } 
                            }
                            else {
                                unset($grid_data[$key]);
                            }
                        }
                    }

                    $result['data']=array_values($grid_data);

                }
                else if( $parent_question_constraints['rollup_settings'][$branchLevel]['function'] == 'grid-to-grid-summary' ) {

                    $data_for_sumrization = [];
                    foreach ($grid_data as $key => $val) {
                        $all_empty = true;
                        foreach ($availabl_questions as $question) {
                            if( !empty($val[$question]) ) {
                                $all_empty = false;
                            }

                        }
                        if( !$all_empty ) {
                            foreach ($val as $qid => $ans) {
                                $data_for_sumrization[$qid][] = $ans;
                            }
                        
                        }
                    }

                    $key_count = 1;
                    $sumrized_data = [];
                    foreach($child_questions as $question ) {
                        $q_2_sum = $parent_question_constraints['rollup_settings'][$branchLevel]['questions'][$question->getId()]['question'];

                        if( isset($parent_question_constraints['rollup_settings'][$branchLevel]['questions'][$question->getId()]) ) {
                            $function = $parent_question_constraints['rollup_settings'][$branchLevel]['questions'][$question->getId()]['function'];
                        }
                        else {
                            $function = 'count';
                        }
                        
                        if( $function == 'sum' ) {
                            $sumrized_data[$question->getId()] = array_sum($data_for_sumrization[$q_2_sum]);
                        }
                        else if( $function == 'avg' ) {
                            $sumrized_data[$question->getId()] = (count($data_for_sumrization[$q_2_sum])==0 ? 0 : round(array_sum($data_for_sumrization[$q_2_sum]) / count($data_for_sumrization[$q_2_sum])));
                        }
                        else if( $function == 'count' ) {
                            $sumrized_data[$question->getId()] = count($data_for_sumrization[$q_2_sum]);
                        }

                    }

                    if( !empty($sumrized_data) ) {
                        $sumrized_data['answer_number'] = $key_count;
                        $sumrized_data['report_id'] = $report_id;
                        $sumrized_data['question_id'] = $question_id;
                        $sumrized_data['answer_id'] = 0;
                    }

                    $result['data'] = [$sumrized_data];

                }
                else if( $parent_question_constraints['rollup_settings'][$branchLevel]['function'] == 'grid-to-grid-group' ) {

                    $group_by = $parent_question_constraints['rollup_settings'][$branchLevel]['group_by'];
                    $group_indetifier_child = $parent_question_constraints['rollup_settings'][$branchLevel]['group_indetifier_child'];

                    $data_for_sumrization = [];
                    foreach ($grid_data as $key => $val) {
                        $all_empty = true;
                        foreach ($availabl_questions as $question) {
                            if( !empty($val[$question]) ) {
                                $all_empty = false;
                            }

                        }
                        if( !$all_empty ) {
                            foreach ($val as $qid => $ans) {
                                $data_for_sumrization[$val[$group_by]][$qid][] = $ans;
                            }
                        
                        }
                    }

                    $key_count = 1; 
                    foreach($data_for_sumrization as  $groupKey=>$groupVal) {
                        $sumrized_data = [];
                        foreach($child_questions as $question ) {
                            $q_2_sum = $parent_question_constraints['rollup_settings'][$branchLevel]['questions'][$question->getId()]['question'];

                            if( isset($parent_question_constraints['rollup_settings'][$branchLevel]['questions'][$question->getId()]) ) {
                                $function = $parent_question_constraints['rollup_settings'][$branchLevel]['questions'][$question->getId()]['function'];
                            }
                            else {
                                $function = 'count';
                            }
                            
                            $sumrized_data[$group_indetifier_child] = $groupKey;
                            if( $function == 'sum' ) {
                                $sumrized_data[$question->getId()] = array_sum($data_for_sumrization[$groupKey][$q_2_sum]);
                            }
                            else if( $function == 'avg' ) {
                                $sumrized_data[$question->getId()] = (count($data_for_sumrization[$groupKey][$q_2_sum])==0 ? 0 : round(array_sum($data_for_sumrization[$groupKey][$q_2_sum]) / count($data_for_sumrization[$groupKey][$q_2_sum])));
                            }
                            else if( $function == 'count' ) {
                                $sumrized_data[$question->getId()] = count($data_for_sumrization[$groupKey][$q_2_sum]);
                            }

                        }


                        if( !empty($sumrized_data) ) {
                            $sumrized_data['answer_number'] = $key_count;
                            $sumrized_data['report_id'] = $report_id;
                            $sumrized_data['question_id'] = $question_id;
                            $sumrized_data['answer_id'] = $key_count++;
                        }
    
                        $result['data'][] = $sumrized_data;                        
                    }

                }
                
            }
            else {
                $result['data']=array_values($grid_data);
            }
            
        }else{
           $result['error']='Not Authorized to view report!'; 
        }
   
        return $this->json()->send($result);
      
    }
    

    public function answersSaveAction(){
        
        $result = array();        
        $report_srv = $this->getServiceLocator()->get('ReportSubmissionService');
        
        if($this->getRequest()->isPost()){
            
            
            $post_data=$this->params()->fromPost();
            $report = $report_srv->getReportById($this->params()->fromQuery('report_id'));
            $question_data=array();
            $answer_number = $post_data['answer_number'];
            $user_action=$post_data['oper'];
            $question_id=$this->params()->fromQuery('question_id');

            if($user_action == 'edit' && ($answer_number=='' || $answer_number==null)){
                $result['error']="Invalid arguments edit operation without answer_number is not allowed" ;
                $result['data']=array('error'=>$result['error']);
            }else if($user_action == 'add'){                
                $answer_number = $this->getNextAnswerNumber($report_srv,$report,$question_id);
                $post_data['answer_number']=$answer_number;
                $post_data['question_id']=$question_id;
                $post_data[$question_id]=null;
                $post_data['report_id']=$report->getId();
            }else if($user_action == 'del'){
                $question_data['user_action']=$user_action;
                $question_data['del']=array('user_action'=>$user_action,'question_id'=>$question_id,'answer_number'=>$this->params()->fromPost('id'));                                               
            }else if(! in_array($user_action, array('add','edit','del') ) ){
                $result['error']="Invalid action $user_action" ;
                $result['data']=array('error'=>$result['error']);
            }
            
            if($answer_number>0 || $user_action=='del'){
                //transform user data
                foreach ($post_data as $key => $value) {
                    if(is_numeric($key)){
                        $question_data['Q_'.$key.'_'.$answer_number]=$value;
                    }
                }
                //save report
                $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
                $report_srv->saveReport($report,$current_user,$question_data,$this->params()->fromFiles());
                
                $result['data']=$post_data;
                $result['status']='success';
                $result['post_data']=$post_data;
            }
			//$report_srv->addEmptyAnswers($post_data['report_id'],$post_data['question_id'],$post_data['answer_number']);			
        }else{
            $result['error']="Invalid access!!This page can not be accessed this way.";
            $result['data']=array('error'=>$result['error']);
            
        }
      return $this->json()->send($result);  
      //return new JsonModel($result);
    }

    private function getNextAnswerNumber($report_srv,$report,$question_id){
        $saved_answers = $report_srv->getAnswer($report,$question_id);
        $max_answer_number=1;
        foreach ($saved_answers as $answer) {
            if($max_answer_number < $answer->getAnswerNumber()){
                $max_answer_number=$answer->getAnswerNumber();
            }
        }   
        
        return $max_answer_number+1;//add one to max_answer_number for next 
    }

    public function historyAction()
    {
        $this->layout('layout/ajax');

        $report_id=$this->params()->fromQuery('report_id');
        $question_id=$this->params()->fromQuery('question_id');

        $repAnalysisSrv = $this->getServiceLocator()->get('ReportAnalysisService');      
        $history = $repAnalysisSrv->getQuestionHistory($report_id, $question_id);

        $vm = new ViewModel(array(
            'history' => $history
        ));
        return $vm;         
    }

    public function reportAction(){
        
        $report_id=$this->params()->fromQuery('report_id');

        if ( $this->params()->fromPost('report_id') )
            $report_id=$this->params()->fromPost('report_id');

        
        /** 
         * @var Application\Service\ReportSubmissionService  */
        $report_srv = $this->getServiceLocator()->get('ReportSubmissionService');
        $report = ($report_id)?$report_srv->getReportById($report_id):NULL;

        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();

        if(!is_object($report)){
            $data = $this->params()->fromPost();
//error_log(print_r($data,true));
            $this->flashMessenger()->setNamespace('error')->addMessage('Unable to find report '.$report_id);            
            //TODO
            //redirect to list
            $url=($this->url()->fromRoute('report'));
            return $this->redirect()->toUrl($url);
            
        }
           
        //
        // Save report if it was a post
        //
        if($this->getRequest()->isPost() && is_object($report)){
            
            $data = $this->params()->fromPost();
            $files = $this->params()->fromFiles();

            # report cannot be submitted before last day of the repoting month
            if($data['user_action']=='submit' && is_object($report)){
                if( strtotime('now') < strtotime(date('Y-m-d', $report->getPeriodFrom()->getPeriodEnd()->getTimestamp()))  ) {

                    $this->flashMessenger()->setNamespace('warning')->addMessage('Report cannot be submitted before '.date('Y-m-d', $report->getPeriodFrom()->getPeriodEnd()->getTimestamp()));   

                    ### DO NOT REDIRECT, this causes user data to be lost
                    //$url=($this->url()->fromRoute('report/report').'?report_id='.$report->getId());
                    //return $this->redirect()->toUrl($url);
                    ###  SAVE USER DATA as DRAFT
                    $data['user_action']='save';
                }
            }
            
            //print_r($data);exit;            
            // $form->setData($data);
            // $form->isValid();

            $report_srv->saveReport($report,$current_user,$data,$files);

            if($data['user_action']=='delete' && is_object($report)){
                $this->flashMessenger()->setNamespace('warning')->addMessage('Report is deleted '.$report->getReportTitle());            
                $url=($this->url()->fromRoute('report'));
                return $this->redirect()->toUrl($url);
            }

            if($data['user_action']=='back' && is_object($report)){
                $this->flashMessenger()->setNamespace('warning')->addMessage('Report is returned to '.$report->getStatus().' state, for corrections');            
            }

            if($data['next_status']=='received' && is_object($report)){
                $this->flashMessenger()->setNamespace('warning')->addMessage('Report is received  '.$report->getReportTitle());            
                $url=($this->url()->fromRoute('report'));
                return $this->redirect()->toUrl($url);
            }

        }

        /** 
         *@var Zend\Form\Form */
        $form = $this->getServiceLocator()->get('CreateFormService')->getForm('report_details');
        $answers = $report_srv->getAnswers($report);
        //
        // Set-up report form
        //
        $this->getServiceLocator()->get('CreateFormService')->addAnswers($form,$answers);
            
        $form->setData(array('report_id'=>$report->getId(),'report_status'=>$report->getStatus()));
        
        $layout='normal';
        if($this->params()->fromQuery('view') && $this->params()->fromQuery('view')=='print'){
            $layout='print';
        }
        
        $can_edit = $report_srv->canEdit($current_user, $report);
        
	$has_force_update_role=false;
	if($current_user->hasRole('update_reports')){
#	   $has_force_update_role=true;
	};
        
	//can delete report
        $can_delete=false;
        if($current_user->isAdmin() && $report->getStatus()=='draft'){
            $can_delete=true;
        }

        //can return report
        $can_return=$this->canReturnReport($report,$current_user);
        
        $child_branches = [];
        if( in_array($report->getBranch()->getBranchLevel(), array('Imarat', 'Markaz') ) ) {
            $branchSrv = $this->getServiceLocator()->get('BranchManagementService');
            $branchSrv->getChildBranches($report->getBranch()->getId());
            if( $report->getBranch()->getBranchLevel() == 'Imarat' ) {
                $child_branches['title'] = 'No. of Halqajaat';
            }
            else {
                $child_branches['title'] = 'No. of Imaraat / Jama`at(s)';
            }
            $child_branches['count'] = count($branchSrv->getChildBranches($report->getBranch()->getId()));
        }

        error_log('canEdit['.$can_edit.'] canDelete ['.$can_delete.
                 '] canReturn['.$can_return.'] canForceUpdateReports ['.$has_force_update_role.
                 '] sysadmin ['.$current_user->hasRole('sys-admin').'] child_branches ['.$child_branches.']');
        return new ViewModel(array('layout'=>$layout,'report'=>$report,'form'=>$form,
                                   'canEdit'=>$can_edit,'canDelete'=>$can_delete,'canReturn'=>$can_return,
				                    'canForceUpdateReports'=>$has_force_update_role,
                                   'sysadmin' => $current_user->hasRole('sys-admin'),
                                   'child_branches' => $child_branches
                                   ));
    }

    private function canReturnReport($report,$current_user){
        $report_config_details = $report->getReportConfig()->getConfigArray();
          
        //default config applies to a report unless we have a special config for branch level of owning branch
        $branch_level=$report->getBranch()->getBranchLevel();
        $config_level='Default';
        if(key_exists($branch_level,$report_config_details['report_flow'])){
              $config_level=$branch_level;
        }
          
        $current_status_config=$report_config_details['report_flow'][$config_level][$report->getStatus()];
        $prev_status = key_exists('prev_status', $current_status_config)?$current_status_config['prev_status']:false;
        //an empty key def is same as no def
        if(empty($prev_status)){
            $prev_status=false;
        }
        if(!$prev_status){
            return false;//if previous status is not defined we can't return report
        }
        //next we need to confirm user has role to modify        
        $has_role=false;
        
        if($current_status_config['roles_prev'] && is_array($current_status_config['roles_prev'])){
            foreach ($current_status_config['roles_prev'] as $role) {
                $has_role = $has_role || ($current_user->hasRole($role));    
            }
        }elseif($current_status_config['roles_prev'] && is_string($current_status_config['roles_prev'])){
            $has_role = $has_role || ($current_user->hasRole($current_status_config['roles_prev']));    
        }else{
           error_log("Prev status riles are not defined "); //.print_r($current_status_config)
        }
        
        if($current_user->hasRole('sys-admin') || $current_user->hasRole('national-secretary') ){
            $has_role=true;
        }
        
        // print_r(['<pre>','prev_status'=>$prev_status,'has_role'=>$current_user->getRoles(),$current_user->getId(),
                 // 'roles'=>$current_status_config['roles'],'config'=>$current_status_config]);exit;
        // we can only return if previous status is configured
        // user has a role defined in config roles
        $can_return=$prev_status && $has_role;
            
        return $can_return;
    }

    /**
     * @param report_confg a valid report_config id parameter as GET or POST, if both are present POST value is used
     * @return JSON response containing list of departments that are allowed for given report config
     */
    public function reportConfigDepartmentsAction(){
        
        $report_config_param = $this->params()->fromQuery('report_config');
         $report_config_param_post = $this->params()->fromPost('report_config');
        if(isset($report_config_param_post)){
            $report_config_param = $report_config_param_post;
        }

        if(! isset($report_config_param)){
            return new JsonModel(array('error'=>'Invalid call : report_config is required parameter'));
        }
        
        $config_srv = $this->getServiceLocator()->get('ConfigService');
        /**
         * @var \Application\Entity\ReportConfig
         */
        $report_config = $this->getServiceLocator()->get('EntityService')->getObject('ReportConfig',$report_config_param);

        if(! isset($report_config)){
            return new JsonModel(array('error'=>'Invalid call : Report type is required ['.$report_config_param.'] was given'));
        }
        
        $dept_ids = $report_config->getDepartments();
        if(isset($dept_ids) && ! empty($dept_ids) && ! is_array($dept_ids)){
            $dept_ids = array($dept_ids);    
        }

        $dept_list = $config_srv->listDepartments($dept_ids);
        
        $result = array('status'=>'success','data'=>$dept_list);
        
        return new JsonModel($result);
    }      

    /**
     * @return Returns JSON list of report config items 
     */
    public function reportConfigAction(){
        
        $config_srv = $this->getServiceLocator()->get('ConfigService');
        /**
         * @var \Application\Entity\ReportConfig
         */
        $report_config = $this->getServiceLocator()->get('EntityService')->findAll('ReportConfig');
        
        $result = array('status'=>'success','data'=>$report_config);
        
        return $this->json()->send($result);
    }
    
    public function tarbiyatAction()
    {
        $form=$this->getServiceLocator()->get('CreateFormService')->getform('report_tarbiyat');

        $vm = new ViewModel(array('form'=>$form));
        return $vm;        
    }

    public function gsReportAction()
    {
        $form=$this->getServiceLocator()->get('CreateFormService')->getform('gs_report');
        $vm = new ViewModel(array('form'=>$form));
        return $vm;        
    }
        
    public function gsReportProcessAction()
    {
        $repAnalysisSrv = $this->getServiceLocator()->get('ReportAnalysisService');
        $this->layout('layout/ajax');
        
        $data=array();
        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            //$data = $repAnalysisSrv->getTarbiyatReport($data);
            $data = $repAnalysisSrv->getGSReportData($data);
            // echo '<pre>'; print_r($data); // die;
            $vm = new ViewModel($data);
            //$vm->setTerminal(true);
            return $vm; 
        }
        else {
            return [];
        }
    }

    public function allDepartmentSummaryAction()
    {
        $form=$this->getServiceLocator()->get('CreateFormService')->getform('report_tarbiyat');
        $vm = new ViewModel(array('form'=>$form));
        return $vm;        
    }
        
    public function allDepartmentSummaryProcessAction()
    {
        $repAnalysisSrv = $this->getServiceLocator()->get('ReportAnalysisService');
        $this->layout('layout/ajax');
        
        $data=array();
        if($this->getRequest()->isPost()) {
            $post_data = $this->params()->fromPost();
            //$data = $repAnalysisSrv->getTarbiyatReport($data);
            $data = $repAnalysisSrv->allDepartmentSummaryReport($post_data);

            if( isset($post_data['report_pdf']) && $post_data['report_pdf']=='yes' ) {
                $vm = new ViewModel($data);
                $vm->setTemplate('application/report/all-department-summary-process.phtml');

                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        
                $layout = new ViewModel();
                $layout->setTemplate("layout/ajax");
                $layout->setVariable("content", $viewRender->render($vm));
            
                $html = $viewRender->render($layout);
                $html = str_replace('src="/img/', 'src="' . ROOT_PATH . '/public/img/', $html); 
        
                $dompdf = new Dompdf();
                $dompdf->loadHtml($html,'UTF-8');
                $dompdf->setPaper($post_data['page_size'], $post_data['page_orientation']);
                $dompdf->render();
                $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
                die;
            }


            $vm = new ViewModel($data);
            //$vm->setTerminal(true);
            return $vm; 
        }
        else {
            return [];
        }
    }
    
    public function monthlyReportAction()
    {
        $form=$this->getServiceLocator()->get('CreateFormService')->getform('monthly_report_status');

        $vm = new ViewModel(array(
            'form'=>$form,
            'default_year'=>\Application\Entity\Period::getTermStartCurrent()
        ));
        return $vm;        
    }
    
    public function monthlyReportStatusAction()
    {
        $repAnalysisSrv = $this->getServiceLocator()->get('ReportAnalysisService');      
        $this->layout('layout/ajax');
        
        $data=array();
        if($this->getRequest()->isPost()) {

            $post_data = $this->params()->fromPost();
            $data = $repAnalysisSrv->getMonthlyReportStatus($post_data);

            if( isset($post_data['report_pdf']) && $post_data['report_pdf']=='yes' ) {
                $vm = new ViewModel(array('data'=>$data, 'post_data'=>$post_data));
                $vm->setTemplate('application/report/monthly-report-status.phtml');

                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        
                $layout = new ViewModel();
                $layout->setTemplate("layout/ajax");
                $layout->setVariable("content", $viewRender->render($vm));
            
                $html = $viewRender->render($layout);
                $html = str_replace('src="/img/', 'src="' . ROOT_PATH . '/public/img/', $html); 
        
                $dompdf = new Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper($post_data['page_size'], $post_data['page_orientation']);
                $dompdf->render();
                $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
                die;
            }
        }
        $vm = new ViewModel(array('data'=>$data, 'post_data'=>$post_data));
        return $vm;
    }
    
    public function markazMonthlyReportAction()
    {
        $form=$this->getServiceLocator()->get('CreateFormService')->getform('markaz_monthly_report_status');

        $vm = new ViewModel(array(
            'form'=>$form, 
            'default_year'=>\Application\Entity\Period::getTermStartCurrent()
        ));
        return $vm;    
    }

    public function markazMonthlyReportStatusAction()
    {
        $repAnalysisSrv = $this->getServiceLocator()->get('ReportAnalysisService');      
        $this->layout('layout/ajax');
        
        $data=array();
        if($this->getRequest()->isPost()) {

            $post_data = $this->params()->fromPost();
            $data = $repAnalysisSrv->getMarkazMonthlyReportStatus($post_data);

            if( isset($post_data['report_pdf']) && $post_data['report_pdf']=='yes' ) {
                $vm = new ViewModel(array('data'=>$data, 'post_data'=>$post_data));
                $vm->setTemplate('application/report/markaz-monthly-report-status.phtml');

                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        
                $layout = new ViewModel();
                $layout->setTemplate("layout/ajax");
                $layout->setVariable("content", $viewRender->render($vm));
            
                $html = $viewRender->render($layout);
                $html = str_replace('src="/img/', 'src="' . ROOT_PATH . '/public/img/', $html); 
        
                $dompdf = new Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper($post_data['page_size'], $post_data['page_orientation']);
                $dompdf->render();
                $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
                die;
            }
            // print_r($data); die;
        }
        $vm = new ViewModel(array('data'=>$data, 'post_data'=>$post_data));
        return $vm;
    }
    
    public function tarbiyatReportAction()
    {
        $repAnalysisSrv = $this->getServiceLocator()->get('ReportAnalysisService');
        $this->layout('layout/ajax');
        
        $data=array();
        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            //$data = $repAnalysisSrv->getTarbiyatReport($data);
            $data = $repAnalysisSrv->getTarbiyatReportOverAll($data);
            // echo '<pre>'; print_r($data); // die;


            $current_user = $this->getServiceLocator()->get('UserProfileService')->getCurrentUser();
            $office_list = $this->getServiceLocator()->get('OfficeAssignmentService')->getActiveOffices($current_user);
            $heigest_branch=null;
            
            foreach($office_list as $ind=>$office) {
                if( in_array($office->getDepartment()->getId(), [5,15,26,1005]) ) {
                    $office_branch = $office->getBranch()->getBranchLevel();
                    if( 
                        $heigest_branch == null ||
                        ($heigest_branch == 'Halqa' && in_array($office_branch, ['Imarat', 'Markaz'])) ||
                        ($heigest_branch == 'Imarat' && in_array($office_branch, ['Markaz'])) ||
                        ($heigest_branch == 'Jama`at' && in_array($office_branch, ['Markaz']))
                    ) {
                        $heigest_branch= $office_branch;
                    }
                }
            }

            $vm = new ViewModel(array('data'=>$data, 'user_branch'=>$heigest_branch));

            //$vm->setTerminal(true);
            return $vm; 
        }
        else {
            return [];
        }
    }

    /*public function tarbiyatAction()
    {

        $form=$this->getServiceLocator()->get('CreateFormService')->getform('report_tarbiyat');
        $table=null;
        $periods=null;
        $repAnalysisSrv = $this->getServiceLocator()->get('ReportAnalysisService');
    
        $data=array();
        $__repdata = [];
        if($this->getRequest()->isPost()){
            $this->layout('layout/ajax');
            $data = $this->params()->fromPost();
            $form->setData($data);

            $data = $repAnalysisSrv->getTarbiyatReport($data);

            $sections = [
                [
                    'header' => [ "category"=>'Days', 'fajr'=>'Fajr', 'maghrib_isha'=>'Maghrib / Isha', 'quran'=>'Qur`an' ],
                    'rows' => [ '26+', '21-25', '16-20', '11-15', '6-10', '1-5', 'zero' ]
                ],
                [
                    'header' => [ "category"=>'Days', 'friday_prayer'=>'Friday Prayer', 'friday_sermon'=>'Friday Sermon' ],
                    'rows' => [ '4+', '3', '2', '1', 'zero' ]
                ]
            ];
            $sectionProcessedData = [];
            $sectionProcessedDataSum = [];
            foreach($sections as $skey => $section) {
                $header = $section['header'];
                $rows = $section['rows'];
                $processedData = [];
                $processedDataSum = [];
                foreach($rows as $row_k => $row_v) {
                    foreach($header as $head_k => $head_v) {
                        if( $head_k == "category" ) $processedData[$row_k]["category"] = $row_v;
                        else $processedData[$row_k][$head_v] = (isset($data[$head_k][$row_v]) ? $data[$head_k][$row_v] : 0);

                        if( $head_k == "category" ) $processedDataSum["category"] = '';
                        else $processedDataSum[$head_v] = (isset($processedDataSum[$head_v]) ? $processedDataSum[$head_v] + $data[$head_k][$row_v] : $data[$head_k][$row_v]);
        
                    }
                }

                $sectionProcessedData[$skey] = $processedData;
                $sectionProcessedDataSum[$skey] = $processedDataSum;
            }
            $vm = new ViewModel(array('sections'=>($sections), 'processedData'=>$sectionProcessedData, 'processedDataSum'=>$sectionProcessedDataSum, 'amilaMembersSubmittedReport'=>$data['amila-members-submitted']));
            $vm->setTemplate('report/tarbiyat-report');
            $vm->setTerminal(true);
            unset($data);
            return $vm;
        }
        if(!isset($data['user_action'])){
            $data['user_action']='form';
        }
		$department=null;
        if(isset($data['user_action']) && $data['user_action']=='report'){
            
        }
        
        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();        
        $offices = $this->getServiceLocator()->get('OfficeAssignmentService')->getActiveOffices($current_user);
        
        $repAnalysisSrv = $this->getServiceLocator()->get('ReportAnalysisService');
        
        $processedData = [];


        $vm = new ViewModel(array('form'=>$form,'table'=>$table,'periods'=>$periods,'user'=>$current_user,'selected_department'=>$department,'processedData'=>$processedData));
        return $vm;         
    }

    public function tarbiyatReportAction()
    {
        //echo "here"; die;
        $repAnalysisSrv = $this->getServiceLocator()->get('ReportAnalysisService');
        $this->layout('layout/ajax');
        
        $data=array();
        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            
            // $data = $repAnalysisSrv->getTarbiyatReportByJamaat($data);
            // echo '<pre>'; print_r($data); die;

            $data = $repAnalysisSrv->getTarbiyatReport($data);
            //echo '<pre>'; print_r($data); // die;
            $sections = [
                [
                    'header' => [ "category"=>'Times', 'prayers'=>'Daily Prayers' ],
                    'rows' => [ '5', '4', '3', '2', '1', 'zero', 'blank' ]
                ],
                [
                    'header' => [ "category"=>'Days', 'fajr'=>'Fajr', 'maghrib_isha'=>'Maghrib / Isha', 'quran'=>'Qur`an' ],
                    'rows' => [ '21+', '11-20', '1-10', 'zero', 'blank' ]
                ],
                [
                    'header' => [ "category"=>'Days', 'friday_prayer'=>'Friday Prayer', 'friday_sermon'=>'Friday Sermon' ],
                    'rows' => [ 'All', '3', '2', '1', 'zero', 'blank' ]
                ]
            ];
            $sectionProcessedData = [];
            $sectionProcessedDataSum = [];
            foreach($sections as $skey => $section) {
                $header = $section['header'];
                $rows = $section['rows'];
                $processedData = [];
                $processedDataSum = [];
                foreach($rows as $row_k => $row_v) {
                    foreach($header as $head_k => $head_v) {
                        if( $head_k == "category" ) $processedData[$row_k]["category"] = $row_v;
                        else $processedData[$row_k][$head_v] = (isset($data['data'][$head_k][$row_v]) ? $data['data'][$head_k][$row_v] : 0);

                        if( $head_k == "category" ) $processedDataSum["category"] = '';
                        else {
                            if( $row_v != 'blank' ) {
                                $processedDataSum[$head_v] = (isset($processedDataSum[$head_v]) ? $processedDataSum[$head_v] + $data['data'][$head_k][$row_v] : $data['data'][$head_k][$row_v]);
                            }
                        }
        
                    }
                }

                $sectionProcessedData[$skey] = $processedData;
                $sectionProcessedDataSum[$skey] = $processedDataSum;
            }
            // echo '<pre>'; print_r($sectionProcessedData); 
            // echo '<pre>'; print_r($sectionProcessedDataSum); die;
            $vm = new ViewModel(array('sections'=>($sections), 'processedData'=>$sectionProcessedData, 'data'=>$data, 'processedDataSum'=>$sectionProcessedDataSum, 'additionalInfo'=>$data['info']));
        }
        else {
            $vm = new ViewModel();
        }
        $vm->setTerminal(true);
        unset($data);
        return $vm;
    }

    public function tarbiyatReportDetailsAction()
    {
        $repAnalysisSrv = $this->getServiceLocator()->get('ReportAnalysisService');
        $this->layout('layout/ajax');

        $data=array();
        if($this->getRequest()->isPost()) {
            $post_data = $this->params()->fromPost();
            $data = $repAnalysisSrv->getTarbiyatReportByJamaat($post_data);
            //echo '<pre>'; print_r($data); die;

            $headers_template = [ "category"=>'Jama`at', 'prayers'=>'Daily Prayers', 'fajr'=>'Fajr', 'maghrib_isha'=>'Maghrib / Isha', 'quran'=>'Qur`an', 'friday_prayer'=>'Friday Prayer', 'friday_sermon'=>'Friday Sermon' ];
            $headers['branch_name'] = 'Jama`at';
            $questions = explode('|', $post_data['question']);
            foreach($questions as $question) {
                $headers[$question] =  $headers_template[$question];
            }        
            $processedData = [];
            $processedDataSum = [];
            foreach($data['data'] as $key => $row ) {
                $this_row = [];
                $this_row['branch_name'] = $row['branch_name'];
                $this_row['branch_id'] = $key;
                foreach($questions as $question) {
                    $this_row[$question] =  $row[$question][$post_data['category']];
                    $processedDataSum[$question] =  $processedDataSum[$question] + $row[$question][$post_data['category']];
                }
                $processedData[] = $this_row;
            }

            $vm = new ViewModel(array('headers'=>$headers, 'category'=>$post_data['category'], 'data'=>$data, 'processedData'=>$processedData, 'processedDataSum'=>$processedDataSum, 'additionalInfo'=>$data['info']));
            $vm->setTerminal(true);
            unset($data);
            return $vm;   

        }
        else {
            $vm = new ViewModel();
        }
        $vm->setTerminal(true);
        unset($data);
        return $vm;        
    }

    public function tarbiyatReportBymembersAction()
    {
        $repAnalysisSrv = $this->getServiceLocator()->get('ReportAnalysisService');
        $this->layout('layout/ajax');

        $data=array();
        if($this->getRequest()->isPost()) {
            $post_data = $this->params()->fromPost();
            $data = $repAnalysisSrv->getTarbiyatReportByJamaatMembers($post_data);

            $headers_template = [ "category"=>'Jama`at', 'prayers'=>'Daily Prayers', 'fajr'=>'Fajr', 'maghrib_isha'=>'Maghrib / Isha', 'quran'=>'Qur`an', 'friday_prayer'=>'Friday Prayer', 'friday_sermon'=>'Friday Sermon' ];
            $headers['branch_name'] = 'Jama`at / Halqa';
            $headers['department_name'] = 'Department';
            $headers['display_name'] = 'Member';
            $questions = explode('|', $post_data['question']);
            foreach($questions as $question) {
                $headers[$question] =  $headers_template[$question];
            }        
            $processedData = [];
            $processedDataSum = [];
            foreach($data['data'] as $row ) {
                $this_row = [];
                $this_row['branch_name'] = $row['branch_name'];
                $this_row['department_name'] = $row['department_name'];
                $this_row['display_name'] = $row['display_name'];
                foreach($questions as $question) {
                    $this_row[$question] =  $row[$question];
                }
                $processedData[] = $this_row;
            }

            $vm = new ViewModel(array('headers'=>$headers, 'processedData'=>$processedData, 'additional_info'=>$data['info']));
            $vm->setTerminal(true);
            unset($data);
            return $vm;   

        }
        else {
            $vm = new ViewModel();
        }
        $vm->setTerminal(true);
        unset($data);
        return $vm;        

    }*/
    
    public function analysisAction(){
        /**
         * @var Zend\Form\Form
         */
        $form=$this->getServiceLocator()->get('CreateFormService')->getform('report_analysis');
        $table=null;
        $periods=null;
        $repAnalysisSrv = $this->getServiceLocator()->get('ReportAnalysisService');
        
        $data=array();
        if($this->getRequest()->isPost()){
            $data = $this->params()->fromPost();
            $form->setData($data);
        }
        if(!isset($data['user_action'])){
            $data['user_action']='form';
        }
        $template = 'report/analysis_form';
		$department=null;
        if(isset($data['user_action']) && $data['user_action']=='report'){
            $this->layout('layout/ajax');
            $template = 'report/analysis_report';    
            $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
            
            $srvOffice = $this->getServiceLocator()->get('OfficeAssignmentService');
            $include_halqa = $data['include_halqa']?$data['include_halqa']:false;

            if( $srvOffice->getUserLevel($current_user) == 'national' ) {
                $include_halqa_for_branches = true;
            }
            else {
                $include_halqa_for_branches = $include_halqa;
            }
            $force_no_grandchildren = $include_halqa ? 'force-grandchildren' : 'force-no-grandchildren';


            $branches_with_offices = $srvOffice->getBranchesWithOffices($current_user,$include_halqa_for_branches, null, $force_no_grandchildren);
            $user_departments = array_unique(array_values($branches_with_offices)[0],SORT_NUMERIC);
            $user_branches = array_keys($branches_with_offices);


            $branches=$data['branch']?explode(',',$data['branch']):$user_branches;
            $department=($data['office']?explode(',',$data['office']):null);

            $department = array_intersect($department,$user_departments);
            if( empty($department) ) {
                $department = $user_departments;
            }

            $report_statuses= explode(',',$data['report_status']);
            $branchSrv = $this->getServiceLocator()->get('BranchManagementService');
            
            //Tag filter overrides branch and department selection
            if($data['tag_filter']){    
                
                $filtered_branches=[];
                if( in_array('all_imarat', $data['tag_filter']) ) {
                    $filtered_branches = array_merge($filtered_branches, $branchSrv->getBranchIdsBySize('all_imarat'));
                }
                if( in_array('large_jamaats', $data['tag_filter']) ) {
                    $filtered_branches = array_merge($filtered_branches, $branchSrv->getBranchIdsBySize('large'));
                }
                if( in_array('medium_jamaats', $data['tag_filter']) ) {
                    $filtered_branches = array_merge($filtered_branches, $branchSrv->getBranchIdsBySize('medium'));
                }
                if( in_array('small_jamaats', $data['tag_filter']) ) {
                    $filtered_branches = array_merge($filtered_branches, $branchSrv->getBranchIdsBySize('small'));
                }
                if ( !empty($filtered_branches) ) {
                    $branches = $filtered_branches;
                }

                // $configSrv = $this->getServiceLocator()->get('ConfigService');

                // $filters = $configSrv->getConfig('tag_filter');
                // foreach($filters as $tag){
                //     if($tag['id'] == $data['tag_filter']){

                //         $branches=$tag['branch'];
                //         //$branches=array_intersect($tag['branch'],$user_branches;
                //         $department=array_intersect($tag['department'],$user_departments); 
                //         //print_r([$tag['department'],$user_departments,$department]);exit();
                //         break;
                //     }
                // }                
            }
            if($include_halqa){
                
                $branches = array_merge($branchSrv->getChildBranches($branches),$branches);
            }
            
            $table = $repAnalysisSrv->getAnalysisReport($branches,$data['year'],$report_statuses,$department);

            // print_r($table);
            // exit;
            $periods = array();
            //foreach ($repAnalysisSrv->getPeriodsFromYear($data['year']) as $p) {
            foreach ($table['periods'] as $p) {    
                $periods[$p->getPeriodCode()]=$p->getPeriodEnd();
            }
            //print_r(($periods));exit;


            if( isset($data['report_pdf']) && $data['report_pdf']=='yes' ) {
                $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();        
                $offices = $this->getServiceLocator()->get('OfficeAssignmentService')->getActiveOffices($current_user);
                $vm = new ViewModel(array('form'=>$form,'table'=>$table,'periods'=>$periods,'user'=>$current_user,'selected_department'=>$department));
                $vm->setTemplate('application/report/analysis_report.phtml');

                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        
                $layout = new ViewModel();
                $layout->setTemplate("layout/ajax");
                $layout->setVariable("content", $viewRender->render($vm));
            
                $html = $viewRender->render($layout);
                $html = str_replace('src="/img/', 'src="' . ROOT_PATH . '/public/img/', $html);
        
                $dompdf = new Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper($data['page_size'], $data['page_orientation']);
                $dompdf->render();
                $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
                die;
            }
            
        }
        
        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();        
        $offices = $this->getServiceLocator()->get('OfficeAssignmentService')->getActiveOffices($current_user);
        // $data =  $repAnalysisSrv->getBasicDashBoardSubBranch($offices[0]->getBranch());
        // $data = DataTransform::convertToColumn($data['last_six_month'],'period_code','branch_name','report_cont');
        //'data'=>$data,
        $vm = new ViewModel(array('form'=>$form,'table'=>$table,'periods'=>$periods,'user'=>$current_user,'selected_department'=>$department));
        $vm->setTemplate($template);
        
        return $vm; 
    }
    public function decryptAnswersAction()
    {
        $this->getServiceLocator()->get('ReportSubmissionService')->decryptAnswers();
        echo die;
    }

    public function summaryReportDataAction()
    {
        $post = null;
        if($this->getRequest()->isPost()){
            $post=$this->params()->fromPost();
        }
        $repAnalysisSrv = $this->getServiceLocator()->get('SummaryReportService');

        $report_id = $this->params()->fromQuery('id');

        $aQuestions = $repAnalysisSrv->getSummaryRportQuestions( $report_id, $post );
        return $this->json()->send($aQuestions);  
    }

    public function summaryReporQuestionsAction()
    {
        $repAnalysisSrv = $this->getServiceLocator()->get('SummaryReportService');
        $aQuestions = $repAnalysisSrv->getQuestionsList();
        return $this->json()->send($aQuestions);  
    }

    public function summaryListAction()
    {
        $modelData = [];
        
        $repAnalysisSrv = $this->getServiceLocator()->get('SummaryReportService');

        if($this->getRequest()->isPost()){
            $post=$this->params()->fromPost();
            if( isset($post['report_id_delete']) && !empty($post['report_id_delete']) ) {
                $repAnalysisSrv->deleteSummaryReport( $post['report_id_delete'] );
            }
            else {
                $repAnalysisSrv->updateSummaryReport( $post );    
            }
        }

        $aReportData = $repAnalysisSrv->getSummaryReportList();   
        $modelData['reportData'] =   $aReportData;  

        $vm = new ViewModel($modelData);
        $vm->setTemplate( 'report/summary_reports' );
        return $vm; 
    }

    public function summaryAction()
    {
        ini_set('max_execution_time', 120);
        $modelData = [];
        if($this->getRequest()->isPost()){
            $post=$this->params()->fromPost();
            $repAnalysisSrv = $this->getServiceLocator()->get('SummaryReportService');
            $aReportData = $repAnalysisSrv->getSummaryReport( $post );    
            $modelData['reportData'] =   $aReportData;  
            $template = 'report/summary' . (isset($post['by_month']) ? '_monthly' : '') ;
        }
        else {
            $template = 'report/summary_report_questions';
        }

        $modelData['report_id'] = $this->params()->fromQuery('id');
        if( empty($modelData['report_id']) ) {
            $modelData['report_id'] = 1;
        }

        $branchSrv = $this->getServiceLocator()->get('BranchManagementService');
        $modelData['branches'] = $branchSrv->listBranchNames(null, ['status'=>'active', 'branch_type'=>'Jama`at']);

        $vm = new ViewModel($modelData);
        $vm->setTemplate($template);
        return $vm; 
    }

    public function updateSummaryReporAction()
    {
        if($this->getRequest()->isPost()){
            $post=$this->params()->fromPost();
            
            $reportSrv = $this->getServiceLocator()->get('SummaryReportService');
            if( $post['action']=='delete' ) {
                $response = $reportSrv->deleteQuestions($post);
            }
            else if( $post['action']=='edit' || $post['action']=='add' ) {
                $response = $reportSrv->updateQuestions($post);
            }      
            else if( $post['action']=='order' ) {
                $response = $reportSrv->orderQuestions($post);
            }
            return $this->json()->send( $response );    
        }
        return $this->json()->send( ['status'=>false, 'message'=>'required data missing'] );          
    }    
}
