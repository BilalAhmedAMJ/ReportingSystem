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
use Zend\Crypt\Password\Bcrypt as Bcrypt;
use Zend\Form\Form;
use Zend\View\Model\ModelInterface;

use Application\Entity\OfficeAssignmentRequest;

class OfficeAssignmentController extends AbstractActionController
{

    const REQ_ACTION_EDIT    =  'edit';
    const REQ_ACTION_UPDATE  =  'update';
    const REQ_ACTION_PROCESS =  'process';
    const REQ_ACTION_NEW     =  'new';
    
    public function indexAction()
    {
        $view =  new ViewModel(array('caller'=>' index' ));
		
		return $view;
    }

    public function requestAction()
    {
     
        $view =  new ViewModel();
        
    	$form= $this->getServiceLocator()->get('CreateFormService')->getform('office_bearer_request_form');
		 
		 if($this->getRequest()->isPost()){
		    
            $office_assignment_srv=$this->getServiceLocator()->get('OfficeAssignmentService');
             
		    $data=$this->getRequest()->getPost();
            
            $request = $office_assignment_srv->getOfficeAssignmentRequest($data['request_id']);
            
            $canProcesses = $this->canProcess($request);
            
            $view->setVariable('canProcess', $canProcesses);
            $view->setVariable('request', $request);
            
            if($request==null && $data['request_action'] != $this::REQ_ACTION_NEW){
                
                $this->flashMessenger()->addErrorMessage(sprintf(("Action %s is not supported"),$data['request_action']));           
                
            }

            if($request!=null){
                
                //request exists, get relevant information
                $existing_office = $office_assignment_srv->getBranchDepartmentOffice($request->getBranch(),$request->getDepartment()->getDepartmentName(),array('active','disabled'));
                if(!empty($existing_office))
                    $view->setVariable('existing_office', $existing_office);
                $usr_prf_srv=$this->getServiceLocator()->get('UserProfileService');
                $user = $usr_prf_srv->getUserByMembercode($request->getMemberCode());
                
                if($user){
                    $current_assignments = $office_assignment_srv->getCurrentOffices($user);
                    if(!empty($current_assignments)){
                        $view->setVariable('current_assignments', $current_assignments);
                    }
                }
            }            
            if(empty($data['request_action']) || $data['request_action']==$this::REQ_ACTION_EDIT){
            //if action is post we expect it to be either and update/save, edit/view or new request 
                //if no action is given or action is edit assume it to be edit/view, as we expect a request_id
                $req = $office_assignment_srv->getOfficeAssignmentRequest($data['request_id']);

                if($req){
                    $form=$this->fillFormFromRequest($form,$req,$view);
                }else{
                    $this->flashMessenger()->addErrorMessage(sprintf(("Unable to find request for [%s]"),$data['request_id']));
                }
                //next step is to save request
                $form->get('request_action')->setValue($this::REQ_ACTION_UPDATE);
                
            }elseif($data['request_action']==$this::REQ_ACTION_UPDATE 
                    || $data['request_action']==$this::REQ_ACTION_NEW){
                
                $form->setData($data);                
                $req = $office_assignment_srv->updateOfficeAssignmentRequest($data);

                if($req){
                    $form=$this->fillFormFromRequest($form,$req,$view);
                    $this->flashMessenger()->addSuccessMessage('Request is saved');
                }
                //next step is to save  request
                $form->get('request_action')->setValue($this::REQ_ACTION_UPDATE);

            }elseif($data['request_action']==$this::REQ_ACTION_PROCESS){
                try{
                    $req=$office_assignment_srv->processOfficeAssignmentRequest($data);
                    $form=$this->fillFormFromRequest($form,$req,$view);
                    $this->flashMessenger()->addSuccessMessage('Request satatus is updated!');
                    //Completed request processing, go to requests list
                    $this->redirect()->toRoute('office-assignment/requests');
                }catch(Error $error){
                    $this->flashMessenger()->addErrorMessage($error->getMessage());
                }
                //save after processing request
                $form->get('request_action')->setValue($this::REQ_ACTION_UPDATE);

            }else{
                $this->flashMessenger()->addErrorMessage(sprintf(("Action [%s] is not supported"),$data['request_action']));
                print_r($data['request_action']==$this::REQ_ACTION_EDIT);
                print_r($data);
                print_r('['.$this::REQ_ACTION_EDIT.']');
                exit;
                $form->setData($data);
            }
            
         }else{

             //create new office request
             $form->get('request_action')->setValue($this::REQ_ACTION_NEW);
         }

        
    	$view->setVariable('form',$form);
        return $view;
    }

    private function fillFormFromRequest(Form $form,OfficeAssignmentRequest $req,$view){
        

         $form->get('request_id')->setValue($req->getId());
         $form->get('full_name')->setValue($req->getFullName());
         $form->get('member_code')->setValue($req->getMemberCode());
         $form->get('email')->setValue($req->getEmail());
         $form->get('confirm_email')->setValue($req->getEmail());
         $form->get('primary_phone')->setValue($req->getPhonePrimary());
         $form->get('alt_phone')->setValue($req->getPhoneAlternate());
         $form->get('branch')->setValue($req->getBranch()->getId());
         $form->get('department')->setValue($req->getDepartment()->getId());
         $form->get('term')->setValue($req->getTerm());
         $form->get('expires_period')->setValue($req->getExpiresPeriod());       
         $form->get('request_reason')->setValue($req->getRequestReason());
         $form->get('processor_comments')->setValue($req->getProcessorComments());
         $form->get('approval_status')->setValue($req->getStatus());
         
         $usr_prf_srv=$this->getServiceLocator()->get('UserProfileService');
         $existing_user = $usr_prf_srv->getUserByMemberCode($req->getMemberCode());
         if(!$existing_user){
             $proposed=$usr_prf_srv->proposeUserName($req->getFullName());

             if(!$proposed) {
                 $form->get('user')->get('username')->setMessages(array('Check if given name is valid, unable to propose a username'));
             }else{
                 $form->get('user')->get('username')->setValue($proposed);
                 $form->get('user')->get('member_code')->setValue($req->getMemberCode());
             }
         }else{
             $view->setVariable('existing_user',$existing_user);
             $form->get('user')->get('username')->setValue($existing_user->getUsername());
             $form->get('user')->get('full_name')->setValue($existing_user->getDisplayName());
             $form->get('user')->get('member_code')->setValue($existing_user->getMemberCode());
             $form->get('user')->get('email')->setValue($existing_user->getEmail());
             $form->get('user')->get('primary_phone')->setValue($existing_user->getPhonePrimary());
             $form->get('user')->get('alt_phone')->setValue($existing_user->getPhoneAlternate());                          
         }
    
        return $form;
    }

    public function processAction()
    {

        $form= $this->getServiceLocator()->get('CreateFormService')->getform('office_bearer_request_form');
         
         if($this->getRequest()->isPost()){
            $form->setData($this->getRequest()->getPost());
         }
        
        $view =  new ViewModel(array('form'=>$form,'processing'=>true,'existing_user'=>false ));    
        $view->setTemplate('office-assignment/new-request');
        
        return $view;
    }


    public function updateAction(){

        if($this->params()->fromQuery('view')=='ajax'){
            //$this->layout('layout/ajax');
        }
		
        $currentUser = $this->zfcUserAuthentication()->getIdentity();
		$data = $this->params()->fromPost();
		$show_form=true;
		if($this->getRequest()->isPost() && key_exists('user_action', $data) && $data['user_action']=='update' ) {
			$usr_srv = $this->getServiceLocator()->get('UserProfileService');
			$user = $usr_srv->getUserById($data['user_id']);

			$office_srv = $this->getServiceLocator()->get('OfficeAssignmentService');
			$office = $office_srv->getOfficeAssignment($data['office_id']);
			$existing_status=$office->getStatus();
			$can_update = false;
			if(in_array($existing_status,array('active','approved','disabled')) ){
				$can_update = true;	
			}
			if(!$can_update){
				$this->flashMessenger()->addErrorMessage("Office Bearer can not be updated! ".ucfirst($existing_status)." status is locked!");
				$show_form=false;
				return new ViewModel(array('office'=>$office,'show_form'=>$show_form));
			}
			//check if office status is changed? if so change office and related user status
			if($office->getStatus() != trim($data['office_status'])){
				//update office status
				$status_update=$office_srv->updateOfficeAssignmentStatus($office,$data['office_status']);
				//$status_update
				$this->flashMessenger()->addSuccessMessage("Office Bearer status is updated");
			} 

			if ( key_exists('username', $data) && !empty(trim($data['username'])) && trim($data['username']) != $user->getUsername() ) {
				//update email
				try{
    				$usr_srv->updateUsername($user,$data);
    				$this->flashMessenger()->addSuccessMessage("Username is updated");
                }catch(\Exception $e){
                    $this->flashMessenger()->addErrorMessage("Error! ".$e->getMessage());
                }
			}
				
			if($office->getStatus()=='active'){
				//update user password is specified			
				if(! empty($data['newpassword']) ){
					$data['current_user'] = true;
					$password_updated = $usr_srv->updatePassword($user,$data);
					if($password_updated){
						$this->flashMessenger()->addSuccessMessage("User's password was updated");
					}else{
						$this->flashMessenger()->addErrorMessage("Unable to update user passowrd, please make sure new password and confirm password are same");
					}
				}
				//check if email is changed then update email			
				if ( trim($data['email']) != $user->getEmail() &&  !empty(trim($data['email'])) ) {
					//update email
					$usr_srv->updateEmail($user,$data);
					$this->flashMessenger()->addSuccessMessage("User Email is updated");
				}
				//check if membercode is changed then update membercode			

				if ( $currentUser->hasRole('national-general-secretary') && ( ! empty($data['membercode']) ) &&trim($data['membercode']) != $user->getMemberCode() ) {
					//update membercode
                   $usr_srv->updateMembercode($user,$data);
                   $this->flashMessenger()->addSuccessMessage("User Membercode is updated");
				}

				//check if email is changed then update email			
				if (trim($data['display_name']) != $user->getDisplayName() || trim($data['primaryphone']) != $user->getPhonePrimary()  || trim($data['alternatephone']) != $user->getPhoneAlternate()) {
					//update email
					$updated = $usr_srv->updatePhoneName($user,$data);
					if($updated)
						$this->flashMessenger()->addSuccessMessage("Name / Phone information is updated");
				}
                $currentUser = $this->zfcUserAuthentication()->getIdentity();
                
                //if user is admin and expiry period is modified, update it
                if( ($currentUser->hasRole('admin')||$currentUser->hasRole('sys-admin')) && key_exists('expires_period', $data)
                    && $data['expires_period'] != $office->getPeriodTo()->getPeriodCode()
                    ){
                    
                    $office = $office_srv->updateOfficeAssignmentPeriod($office,$data);
                    $office_srv->updateOfficeAssignment($office);
                    $this->flashMessenger()->addSuccessMessage("Expiry period is updated to ");
                }
			}
			if($existing_status!='active'&&$office->getStatus()!='active'){
				$this->flashMessenger()->addErrorMessage("Update is only allowed when office status is active");
			}
			
			$show_form=false;			
		}
		
        $office_id = $this->params()->fromQuery('id');
		if(!$office_id){
            $office_id=$this->params()->fromPost('office_id');
		}
        $srv = $this->getServiceLocator()->get('OfficeAssignmentService');
        $office = $srv->getOfficeAssignment($office_id);
        
        return new ViewModel(array('office'=>$office,'show_form'=>$show_form));
    }
	
	
	
    public function listEditAction(){
        
       //Create Grid for display only
       $grid = $this->createOfficeListDataGrid('edit');

       $title_filters=array('branch_list_type','export_list');       
       
       $grid->getViewModel()->setVariable('title_filters', $title_filters);


       $grid->render();
       
       $grid_response=$grid->getResponse();
       
       if($grid_response instanceof ModelInterface){
           $viewModel=new ViewModel();
           $viewModel->setTemplate('office-assignment/list');
           $viewModel->addChild($grid_response,'office_bearers_grid');
            
           return $viewModel;
       }
       else{
           return $grid_response;
       }

        
        
    }


    public function listAction()
    {
    
       //Create Grid for display only
       $grid = $this->createOfficeListDataGrid('view');

       $title_filters=array('branch_list_type','export_list');  
            
       $grid->getViewModel()->setVariable('title_filters', $title_filters);

       $grid->render();
       
       $grid_response=$grid->getResponse();
       
       if($this->params()->fromQuery('view')=='print2'){
           return $grid_response;
       }elseif($grid_response instanceof ModelInterface){
           $viewModel=new ViewModel();
            
            //$viewModel->setTemplate('application/report/list');
            //$grid->setToolbarTemplate(null);
    
           $viewModel->addChild($grid_response,'office_bearers_grid');
            
           return $viewModel;
       }
       else{
           return $grid_response;
       }

    }

    /**
     * 
     * Returns OfficeAssignment list dataGrid for given parameters
     * 
     * @param $list_fields: can be one of [view,edit].
     *   when value is "view" returns data grid with office contact info only.
     *   when value is "edit" returns data grid with user info along with edit actions.
     * 
     * @param $list_type: can be one of [all,samebranch, otherbranch]
     *   returned list will be filtered based on provided value if it is other than default (all)
     *  
     */
    private function createOfficeListDataGrid($list_fields){

       $list_type='all';

       if( $this->params()->fromQuery('list_type') ){
          $list_type=$this->params()->fromQuery('list_type'); 
       }
       if( $this->params()->fromPost('list_type') ){
          $list_type=$this->params()->fromPost('list_type'); 
       }

       $srv = $this->getServiceLocator()->get('OfficeAssignmentService');
       
       $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();

        
       $grid_manager = $this->getServiceLocator()->get('ZfcDataGridFactory');
       $grid_name=$list_fields.'OfficeBearerList';
       $grid = $grid_manager->createGrid($grid_name);       
       $grid->setTitle('Office Bearer List');
       $grid->setDefaultItemsPerPage(30);

       $post = $grid->getSession()->postParameters;
       if($this->getRequest()->isPost()){
           $post=$this->params()->fromPost();
       }
       $status = 'active';
       if(key_exists('toolbarFilters', $post) && key_exists('office_status', $post['toolbarFilters']) ){ //&& !empty($post['toolbarFilters']['office_status'])
           $status = $post['toolbarFilters']['office_status'];           
       }
       $data_source =$srv->createOfficeAssignmentDataSource($user_id,$list_type,$status);

       $grid->setDataSource($data_source);
       $grid->getViewModel()->setVariable('list_type', $list_type);

       $user_srv = $this->getServiceLocator()->get('UserProfileService');
       
       $branch_ids=array_keys($srv->getBranchesWithOffices($user_id,true));
       
       $canEditOffice = $user_srv->canEditOffice($branch_ids);

       $datagrid_helper = $this->getServiceLocator()->get('DataGridHelper');

       $col_optins = array('edit'=>$canEditOffice,'fields'=>$list_fields);
              
       
       $config = $this->getServiceLocator()->get('config');
       $col_optins['adapter'] = $this->getServiceLocator()->get('DoctrineEncryptAdapter'); //$config['doctrine']['encryption']['orm_default']['adapter'];
       
       $col_source = $datagrid_helper->grid('OfficeBearerList',$col_optins);
       $col_source->addColumns($grid);

        /**
         * Default filtering is for ones own department
         */
       if($list_type=='other_branches' || ($list_type!='same_branch' && $this->params()->fromQuery('filter')=='own') ){
                        
            $office = $srv->getActiveOffices($user_id);
            if($office[0]&&$office[0]->getDepartment()->getReportable()){
                $grid->getColumnByUniqueId('department_department_name')->setFilterDefaultValue(
                                $office[0]->getDepartment()->getDepartmentName()
                            );
            }
            if(count($office)>0&&$office[0]){
                $this->setDepartmentFilter($office[0]->getDepartment()->getDepartmentName());
            }
       }
       if($list_type=='same_branch'){
            //remove department filter
            $this->setDepartmentFilter(null);
            $grid->getColumnByUniqueId('department_department_name')->unsetFilterDefaultValue();
       }else{
           $datagrid_helper->filtersFromCache($grid,$this->getRequest());
       }

       //set filter list form
       $grid->getViewModel()->setVariable('form',$this->list_filter_form());     
       
        return $grid;        
        
    }


    public function requestsAction(){
        
        $officeAssignmentService = $this->getServiceLocator()->get('OfficeAssignmentService');    
        $status='requested';
        // if($this->getRequest()->isPost()){
            // $status=$this->getRequest()->getPost('request_status');
        // }

        $currentUser = $this->zfcUserAuthentication()->getIdentity();
        $data_source = $officeAssignmentService->getRequestsDataSource($status,$currentUser);
        $datagrid_helper = $this->getServiceLocator()->get('DataGridHelper');
        
       $grid_manager = $this->getServiceLocator()->get('ZfcDataGridFactory');
       $grid_name='Office_Proposal_Requests';
       $grid = $grid_manager->createGrid($grid_name);       
       
       $grid->setTitle('Office Proposal Requests');
       $grid->setDefaultItemsPerPage(30);
       $grid->setDataSource($data_source);

       $form = $this->list_filter_form();
       
       $grid->getViewModel()->setVariable('form',$form);
       
       $grid->getViewModel()->setVariable('header_part','partial/bootstrap_datagrid/requests_header.phtml');

       $col_source = $datagrid_helper->grid('OfficeBearerRequestList',array('adapter' => $this->getServiceLocator()->get('DoctrineEncryptAdapter')));
       $col_source->addColumns($grid);

       $grid->render();
       
        $viewModel=new ViewModel(array('form'=>$form));
        
        $viewModel->setTemplate('application/office-assignment/requests_list');

        $viewModel->addChild($grid->getResponse(),'requests_grid');
        
        return $viewModel;
       
    }
  


    public function requestJQGridAction(){
        
        $officeAssignmentService = $this->getServiceLocator()->get('OfficeAssignmentService');    
        $status='requested';
        if($this->getRequest()->isPost()){
            $status=$this->getRequest()->getPost('request_status');
        }

        if($this->json()->uri()){
            $currentUser = $this->zfcUserAuthentication()->getIdentity();
            $requests = $officeAssignmentService->listRequestsForStatus($status,$currentUser);
            return $this->json()->send(array('data'=>$requests));
        }

        $vars=array('office_assignment_req_status'=>json_encode($this->getServiceLocator()->get('ConfigService')->getConfigValues('office_assignment_req_status')));
        
        return new ViewModel($vars);
    }
  
  private function setDepartmentFilter($dept=null){
    
    $post = $this->getRequest()->getPost();
    $toolbarFilters = $post->get('toolbarFilters');
    if($dept==null){
        unset($toolbarFilters['department_department_name']);
    }else{
        $toolbarFilters['department_department_name']=$dept;
    }
    $post->set('toolbarFilters',$toolbarFilters);
    $this->getRequest()->setPost($post);
    
  }
  
  private function canProcess($request){
      
      $navPlus = $this->getServiceLocator()->get('viewhelpermanager')->get('navPlus');
      
      $role_can_process = $navPlus->controllerAccess('office-assignment','process');
      
      $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();        
      
      $branch = $request?$request->getBranch():null;
      
      $can_process_branch = false;
      
      if($role_can_process && $branch !=null ){
          
          $offSrv = $this->getServiceLocator()->get('OfficeAssignmentService');
          $active_offices = $offSrv->getActiveOffices($current_user,$branch->getParent());
          //a valid role can process branch if they have at least one office in parent branch
          $can_process_branch = !empty($active_offices);
          if(!($can_process_branch) && !empty($branch->getParent())){
          //if current user don't have access to request's branch
          //try parent branch
              $active_offices = $offSrv->getActiveOffices($current_user,$branch->getParent()->getParent());
              $can_process_branch = !empty($active_offices);
          }
      }
      
      return $role_can_process && $can_process_branch;
  }

}
