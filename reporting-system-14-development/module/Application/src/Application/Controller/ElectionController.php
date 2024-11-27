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
use Zend\Session\Container;

class ElectionController extends AbstractActionController
{

    private $labels=array(
                    'shura'=>'New Shura Election Result',
                    'office_bearer'=>'New Office Bearer Election Result',
                    'majlis_intikhab' => 'New Majlis Intikhab Election Result'
                    );
	
	public function indexAction(){
		return $this->redirect()->toRoute('election/list');
	}

	
	private function nextElectionReport($election_report,$election_reports){
			
		$last_election_report = null;
		
		foreach ($election_reports as $er) {
			if($last_election_report && $last_election_report->getId()==$election_report->getId()){
				return $er;
			}else{
				$last_election_report=$er;
			}
		}
		//we been through all no match go to first
		return $election_reports[0];
	}
	
	
	public function submitAction(){

		$isPost=$this->getRequest()->isPost();
		$user_data=null;
		$id=null;
		$service = $this->getServiceLocator()->get('ElectionService');

		$do_save=false;
		
		if($isPost){
			$user_data=$this->params()->fromPost();
			$data=$user_data;
			$do_save=true;
		}else{
			$user_data=$this->params()->fromQuery();			
		}
		
		$id = $user_data['id'];
		$election = $service->getElection($id);
						
        $user = $this->current_user();

        $officeSrv = $this->getServiceLocator()->get('OfficeAssignmentService');
        $offices=$officeSrv->getActiveOffices($user);
		$branch_label = $election->getBranch()->getBranchLevel();
		$read_only=false;
		$validation_errors = $service->validate($election);
		
		$read_only=$election->getElectionStatus()!='draft';
		
		$user_data['election_id']=$id;
		if($do_save && (empty($validation_errors) || $validation_errors[0]['error']==0) ){
			$user_data['election']['election_status']='submitted';
						
			$read_only=true;
		}
		if($do_save){
			//Save suer election 
			$service->saveElection($user_data);
		}
			
		
		$data=array('election'=>$service->getElection($id,true));				
		
		$form = $this->getElectionForm();
		
		$form->setData($data);

		//set terms
	        $election_term_fld = $form->get('election')->get('election_term');

	        $election_terms=array();
	        if( $data['election']['election_type'] == 'shura' ){
	            $election_terms = $form->get('election')->getOption('shura_terms');
	        }else{
	            $election_terms = $form->get('election')->getOption('office_bearer_terms');
	        }
                $form->get('election')->get('election_term')->setValueOptions($election_terms);
	
		
		$form->get('election')->get('branch_id')->setValueOptions(array($election->getBranch()->getId()=>$form->get('election')->get('branch_id')->getValue()));
		$form->get('election')->get('branch_id')->setValue($election->getBranch()->getId());
		$form->setLabel('Submit Election Result for Approval');
	
            //set terms
        $election_term_fld = $form->get('election')->get('election_term');
        
        $election_terms=array();
        if( $data['election']['election_type'] != 'office_bearer' ){
            $election_terms = $form->get('election')->getOption('shura_terms');    
        }else{
            $election_terms = $form->get('election')->getOption('office_bearer_terms');
        }        
        $form->get('election')->get('election_term')->setValueOptions($election_terms);
    	
		
		$view = new ViewModel(array('user_action'=>'submit','election'=>$election,'form'=>$form,'branch_label'=>$branch_label,'errors'=>$validation_errors,'read_only'=>$read_only ) );
		
		$view->setTemplate('application/election/form.phtml');
		return $view;

	}
	
    
    public function deleteAction(){
        $req = $this->getRequest();
        $id = null;
        $election_report_id=null;       
        $user_action='open';
        $cfgService=$this->getServiceLocator()->get('ConfigService');
        $config = $cfgService->getConfig('election_config');
        
        if($req->isPost()){
            $id = $this->params()->fromPost('id');
            $election_report_id = $this->params()->fromPost('election_report_id');
            $user_action=$this->params()->fromPost('user_action');  
        }else{
            $id = $this->params()->fromQuery('id');
        }
        
        if(empty($id)){
            $this->flashMessenger()->addErrorMessage(sprintf(("Unable to find election for [%s]"),$id ));
            return $this->redirect()->toRoute('election/list');
        }else{
            $service = $this->getServiceLocator()->get('ElectionService');
            $service->deleteElection($id,$this->current_user());
            return $this->redirect()->toRoute('election/list');
        }
    }
    
	/**
	 * user_action => (open,save,complete,next)
	 */	
    public function resultAction()
    {
		$req = $this->getRequest();
		$id = null;
		$election_report_id=null;		
		$user_action='open';
		$cfgService=$this->getServiceLocator()->get('ConfigService');
		$config = $cfgService->getConfig('election_config');
	    $is_new = $this->params()->fromPost('new');
		
		if($req->isPost()){
			$id = $this->params()->fromPost('id');
			$election_report_id = $this->params()->fromPost('election_report_id');
			$user_action=$this->params()->fromPost('user_action');	
		}else{
			$id = $this->params()->fromQuery('id');
		}
		
		if(empty($id)){
            $this->flashMessenger()->addErrorMessage(sprintf(("Unable to find election for [%s]"),$id ));
			return $this->redirect()->toRoute('election/list');
		}else{
			$service = $this->getServiceLocator()->get('ElectionService');
			
			$election = $service->getElection($id);
			if($election==NULL){
                $this->flashMessenger()->addErrorMessage(sprintf(("Unable to find election for [%s]"),$id ));
                return $this->redirect()->toRoute('election/list');   
			}
            
			$read_only = $election->getElectionStatus()!='draft';
			
			$result = array( 'election' => $election,'config'=>$config, 'election_report_list'=>$election->getElectionReports(),'read_only'=>$read_only );

			$election_reports=$election->getElectionReports();
			
			if(empty($election_report_id) ){
				//get first election report
				$election_report=$election_reports[0];
				
			}else{
				$election_report=$service->getElectionReport($election_report_id);				
			}

			$result['next_election_report']=$this->nextElectionReport($election_report, $election_reports);
			
			//TODO FIXME
			//process user action
			$post_data=$this->params()->fromPost();
			$do_save = ($user_action == 'save')||($user_action == 'complete');
			$move_to_next= ($user_action == 'complete');
			$failed_validation=false;
			
			if($do_save){
				//save election results
				$service->saveElection($post_data);				
			}
			if($user_action == 'complete'){
				//re-read election report and validate results
				$election_report=$service->getElectionReport($election_report_id);				
				$validations = $service->validate($election_report);

				if(is_array($validations)&&($validations[0]['type']=='summary') && $validations[0]['error']>0){
					$result['errors']=$validations;
					$move_to_next=false;
					$failed_validation=true;
					$move_to_next=false;
					if($validations[0]['warning']>0 && (! $election_report->getWarningsAcknowledged()) ){
						$result['acknowledged_warnings']=true;
					}
				}else{
					//validation passed let's save it
					$post_data['election_report']['report_status']='completed';
					$post_data['election_report']['warnings']=print_r($validations,true);
					//save updated status
					$service->saveElection($post_data);
					$move_to_next=true;
					
				}				
                
                //if election is not office_bearer and user action is complete forward to submit
                if($election->getElectionType()!='office_bearer'){
                    //forward to submit
                    $this->getRequest()->setQuery(new \Zend\Stdlib\Parameters(array('id'=>$election->getId())));
                    $submit_url = $this->url()->fromRoute('election/submit');
                    return $this->redirect()->toUrl($submit_url.'?id='.$election->getId());         
                } 
                
			}
			
            
			if($move_to_next){
					
				$election_report=$result['next_election_report'];
				
			}elseif( key_exists('next_election_report_id', $post_data) && !empty($post_data['next_election_report_id']) && !$failed_validation){
				// next_election_report_id is only set by client side in JS when user clicks on right side dept list otherwise it's empty
				// So, in control when we have a value here we go to that dept, always, otherwise we load current dept
				$election_report=$service->getElectionReport($post_data['next_election_report_id']);
			}
						
			//validate election report and show validation results
			//This is repeated step when we have a complete user action
			$validations = '';
			if(isset($is_new) && $is_new != 1){
			    $validations = '';
            }else{
                $validations = $service->validate($election_report);
            }

			if(is_array($validations)&&($validations[0]['type']=='summary') && ($validations[0]['error']>0||$validations[0]['warning']>0) ) {
				$result['errors']=$validations;
				if($validations[0]['warning']>0 && (! $election_report->getWarningsAcknowledged()) ){
					$result['acknowledged_warnings']=true;
				}
			}
			
			$result['election_report']=$election_report;
			//also get election proposals
			$result['election_proposals']=$election_report->getElectionProposals();

			
			$form=$this->getElectionForm($election,$election_report,$result['next_election_report']); 
			
			$result['form']=$form;
            
            $last_report=false;
            
            if(($election_report->getDepartment()->getId()==$config['last_department'])){
                $last_report=true;
            }elseif($election->getElectionType()!='office_bearer'){
                $last_report=true;
            }

            $result['last_report']=$last_report;
	        $view =  new ViewModel($result);
			
			return $view;
			
		}//election id exists
    }

    public function listAction()
    {

        $service = $this->getServiceLocator()->get('ElectionService');
        
        $current_user = $this->current_user();

		$data_source  = $service->electionsDataSource($current_user->getId());
    	
        $grid_manager = $this->getServiceLocator()->get('ZfcDataGridFactory');

        $grid_name='election_grid';
		$title = 'Elections List for Approvals';
		
        $grid = $grid_manager->createGrid($grid_name);
        $grid->setTitle($title);
        //$grid->setRendererName('jqGrid');
        $grid->setRendererName('bootstrapTable');
        $grid->setToolbarTemplate('application/election/election_toolbar.phtml');
        $grid->setId($grid_name);
		
        $grid->setDefaultItemsPerPage(100);

        $grid->setDataSource($data_source);
        //$grid->setUserFilterDisabled(true);
        
        $datagrid_helper = $this->getServiceLocator()->get('DataGridHelper');
		
		/* Do not display processed election resutls yet
		 * ,'processed_count'=>array('service'=>$service,'method'=>'getElectionReportsForElection','arg'=>'processed')*/
		$col_options=array( 'adapter' => $this->getServiceLocator()->get('DoctrineEncryptAdapter')
						   ,'complete_count'=>array('service'=>&$service,'method'=>'getElectionReportsForElection','arg'=>'completed')
						   ,'admin'=>($current_user->hasRole('admin')||$current_user->hasRole('sys-admin')
						              ||$current_user->hasRole('national-general-secretary') )
						  ); 
		
        $datagrid_helper->grid('ElectionList')->addColumns($grid,$col_options);


        if($this->getRequest()->isPost()){
           //a POST reuqest is a direct call to dataGrid and will be handled by grid renderer returning JSON data  
            return $grid->getResponse();
        }else{
            //For a non post reuqest we will setup datagrid page 
            $view = new ViewModel(array('list_type'=>'simple'));    
            $view->addChild($grid->getResponse(),'election_grid');    
            return $view;
        }
    }

    

	private function getElectionForm($election=null,$election_report=null,$next_election_report=null){
		
		$service = $this->getServiceLocator()->get('CreateFormService');
		
		$form_election = $service->getform('election');

        $form_elect_rep= $service->getform('election_report');

		$form = $this->getServiceLocator()->get('FormElementManager')->get('Form');		
		$form->setName('election_results_form');

		$form->add(array(
			'type'=>'Hidden',
			'name'=>'id'
		));
		$form->add(array(
			'type'=>'Hidden',
			'name'=>'user_action'
		));
		
		$form->add(array(
			'type'=>'Hidden',
			'name'=>'election_report_id'
		));

		$form->add(array(
			'type'=>'Hidden',
			'name'=>'next_election_report_id'
		));

		$form->add($form_election);
		$form->add($form_elect_rep);

		if($election){
		//populate election elements if election is provided
			$form->add(array(
	             'type' => 'Zend\Form\Element\Collection',
	             'name' => 'election_proposal',
	             'options' => array(
	                 'label' => 'Please choose categories for this product',
	                 'count' => count($election_report->getElectionProposals()),
	                 'should_create_template' => true,
	                 'allow_add' => true,
	                 'target_element' => array(
	                     'type' => 'ElectionProposalFieldset',
	                 ),
	             ),
	         ));
	
			$hydrator = new \Zend\Stdlib\Hydrator\ClassMethods(true);
			$saved_data = array('election_report'=>$hydrator->extract($election_report)
								,'election'=>$hydrator->extract($election)
							   );
			$func = function($value) use ($hydrator){ return $hydrator->extract($value);};
			$arr = $saved_data['election_report']['election_proposals']->toArray();
	
			$saved_data['election_proposal']=array_map($func, $arr);
		//print_r(['<pre>',$saved_data,'</pre>']);	
			$saved_data['next_election_report_id']=$next_election_report->getId();
			$form->setData($saved_data);			
		}
			
		$form->prepare();

		return $form;
	}	

	private function getUserBranches($election_type='office_bearer'){
		//if no election is given add accessable branches list 
        $user = $this->current_user();
        $officeSrv = $this->getServiceLocator()->get('OfficeAssignmentService');
        $offices=$officeSrv->getActiveOffices($user);
		$include_child=true;
        $branch_ids=array_keys($officeSrv->getBranchesWithOffices($user,$include_child));

        $branch_srv = $this->getServiceLocator()->get('BranchManagementService');

        if($user->hasRole('national-general-secretary')  || $user->hasRole('sys-admin') || $user->hasRole('all-election-data-entry')){
            $branch_ids = null; 
        }

        $filters=array();
        if($election_type == 'majlis_intikhab'){
            $filters['branch_level']='Imarat';
        }
        $branches = $branch_srv->listBranchNames($branch_ids,$filters);
		
		//add first element to be empty, to force selection  
		array_unshift($branches,array('value'=>'','label'=>''));
		
		return 	$branches;
	}

	public function printAction(){
		$req = $this->getRequest();
		$id = null;
		
		if($req->isPost()){
			$id = $this->params()->fromPost('id');
		}else{
			$id = $this->params()->fromQuery('id');
		}
		
		if(empty($id)){
            $this->flashMessenger()->addErrorMessage(sprintf(("Unable to find election for [%s]"),$id ));
			return $this->redirect()->toRoute('election/list');
		}else{

			$service = $this->getServiceLocator()->get('ElectionService');
			$election = $service->getElection($id);
			$config= $service->getElectionConfig();
			
			$this->layout('layout/ajax');
			
			return new ViewModel(array('election'=>$election,'config'=>$config,'validations'=>$service->validate($election)));
		}
	}

	private function addExpires(){
 
		$this->getResponse()->setHeader('Expires', '', true);
		$this->getResponse()->setHeader('Cache-Control', 'public', true);
		$this->getResponse()->setHeader('Cache-Control', 'max-age=604800');
		$this->getResponse()->setHeader('Pragma', '', true);

	}

	public function createAction(){
		
		$isPost=$this->getRequest()->isPost();
        $data = $this->params()->fromPost();
        
        if(! key_exists('election', $data)){
            $data['election'] = array();
        }
        if(!key_exists('election_type', $data['election']) ){
            $data['election']['election_type']='';
        }

        $user = $this->current_user();
        $officeSrv = $this->getServiceLocator()->get('OfficeAssignmentService');
        $offices=$officeSrv->getActiveOffices($user);
		
		$form = $this->getElectionForm();
		$branches = $this->getUserBranches($data['election']['election_type']);
		$form->get('election')->get('branch_id')->setValueOptions($branches);
								
		$branch_label = $offices[0]->getBranch()->getBranchLevel();
		if($branch_label!='Halqa' && $branch_label!='Jama`at'){
			$branch_label='Branch';
		}
		
		
		if($isPost && ($data['user_action'] !== 'election_type' ) && !empty($data['election']['election_type']) ){
			$data = $this->params()->fromPost();
			$isValid = $this->isValidElectionData($data['election']); 
			if($isValid){
				//valid election data proceed with creating new election record
				$service = $this->getServiceLocator()->get('ElectionService');
				$election = $service->saveElection($data);
                
                //check if it's an existing election
                if($election->getDateCreated()->getTimestamp() != $election->getDateModified()->getTimestamp()){
                    //election already existed, let user know as we will not be updating some fields
                    $this->flashMessenger()->addWarningMessage(sprintf("A result for this election was already started on %s, please compelte existing result or delete it for new results"
                                                                       ,$election->getDateCreated()->format('Y-m-d H:i:s')
                                                                       )
                                                               );
                }
                
				$data['election']['election_id']=$election->getId();
				$data['election_id']=$election->getId();			
				$data['id']=$election->getId();
                $data['new']=1;
				$this->getRequest()->setPost(new \Zend\Stdlib\Parameters($data));
				$view = $this->resultAction();
				$view->setTemplate('application/election/result.phtml');
				return $view;
			}
		}elseif(!$isPost || ! key_exists('election_type', $data['election']) || empty($data['election']['election_type'])){
            $view = new ViewModel(array('form'=>$form,'branch_label'=>$branch_label));
            $view->setTemplate('application/election/election_type.phtml');
            return $view;
		}
        

        //set terms
        $election_term_fld = $form->get('election')->get('election_term');
        
        $election_terms=array();
        if( $data['election']['election_type'] != 'office_bearer' ){
            $election_terms = $form->get('election')->getOption('shura_terms');    
        }else{
            $election_terms = $form->get('election')->getOption('office_bearer_terms');
        }        
		$form->get('election')->get('election_term')->setValueOptions($election_terms);
        
		$form->setData($data);
		$form->setLabel($this->labels[$data['election']['election_type']]);
				
		$view = new ViewModel(array('form'=>$form,'branch_label'=>$branch_label));
		$view->setTemplate('application/election/form.phtml');
		return $view;
	}
	
	private function isValidElectionData($election_data){
		
		$cfgService=$this->getServiceLocator()->get('ConfigService');	
		$config = $cfgService->getConfig('election_config');
        $result = true;
        
        //validate quorum
		$election_call=$election_data['election_call'];
		$quorum_percentage = $config['quorum'][$election_call];
		$quorum_needed = $quorum_percentage * ($election_data['chanda_payers']+$election_data['defaulters']); 
		$quorum = $election_data['eligible_voters_present'];
		
		$met = ($quorum >= $quorum_needed);
        		
		if(!$met){
            $this->flashMessenger()->addErrorMessage(sprintf("Eligible Voters Present don't meet quorum requirements (%s vs %s)",round($quorum_needed),$quorum));							
            $result = false;
		}

        
		//validate allowed delegates
		if($election_data['election_type'] == 'shura'){
			$total_payers = $election_data['chanda_payers'] + $election_data['lajna_payers'] + $election_data['under_eighteen_wassayat'];
	        $allowed_calculated = 0;
	        if($total_payers < $config['shura']['min_payers_for_election']){
	            $allowed_calculated = 0;
	        }else{
	            $allowed_calculated =ceil($total_payers /  $config['shura']['max_payers_per_delegate']);
	        }
	        $allowed_user_input = $election_data['allowed_delegates'];
	        
	        if ($allowed_calculated != $allowed_user_input){
	            $this->flashMessenger()->addErrorMessage(sprintf("Number of delegates provided don't meet required delegates (%s vs %s)",($allowed_calculated),$allowed_user_input));
	            $result = false;
	        }
		}
        
		//validate allowed electors
		if($election_data['election_type'] == 'majlis_intikhab'){
			$total_payers = $election_data['chanda_payers'] + $election_data['lajna_payers'] + $election_data['under_eighteen_wassayat'];
	        $allowed_calculated = 0;
            $min_payers_req=$config['majlis_intikhab']['min_payers_for_election'];
	        if($total_payers < $config['majlis_intikhab']['min_payers_for_election']){
	            $allowed_calculated = 0;
	        }else{
	            $allowed_calculated = $config['majlis_intikhab']['min_allowed_electors'] + 
	            					  ceil( ($total_payers - $min_payers_req) /  $config['majlis_intikhab']['max_payers_per_elector']);
	        }
	        $allowed_user_input = $election_data['allowed_electors'];
	        
	        if ($allowed_calculated != $allowed_user_input){
	            $this->flashMessenger()->addErrorMessage(sprintf("Number of electors provided don't meet required eletors (%s vs %s)",($allowed_calculated),$allowed_user_input));
	            $result = false;
	        }
            
            //print_r(array($allowed_calculated ,$allowed_user_input, ceil( ($total_payers - $min_payers_req) /  $config['majlis_intikhab']['max_payers_per_elector'])));exit;

		}
		return $result;
	}
	
	public function exportAction(){
		
		$isPost=$this->getRequest()->isPost();
        $form = $this->exportForm();
		if($isPost){
		    
			$post_data = $this->params()->fromPost();
            $form->setData($post_data);
            $filters = array();
			$status=array('submitted');
            if($post_data['export_filter']=='all'){
                $status[]='processed';    
            }
            $filters['election_status']=$status;
            
            if(key_exists('branches',$post_data) && is_array($post_data['branches'])){
                $filters['branch']=$post_data['branches'];
            }
            
            if(key_exists('election_type',$post_data) && !empty($post_data['election_type'])){
                $filters['election_type']=$post_data['election_type'];
            }            
            
            $service = $this->getServiceLocator()->get('ElectionService');            
			$export_list = $service->getElections($filters); 
            
            if(count($export_list)<1){
                $this->flashMessenger()->addErrorMessage(("Unable to find election results matching given criteria"));                
            }else{
                $exported_response = $this->electionResultsToResponse($service,$export_list);
                //now mark all elections as processed
                $service->markExported($export_list,'processed',$this->current_user());
                
                return $exported_response;
            }//
		}//not post
		return new ViewModel( array('form'=>$form) );		
	}

    private function electionResultsToResponse($service,$export_list){
        $file_h = fopen( 'php://output', 'w' );
        $first=true;
        ob_start(); // buffer the output ...                        
        foreach ($export_list as $election) {
            $election_proposals = $service->exportElection($election);
            foreach ($election_proposals as $election_proposal) {
                if($first){
                    fputcsv($file_h,array_keys($election_proposal), ',','"');
                    $first=false;
                }
                fputcsv($file_h,array_values($election_proposal), ',','"');
                error_log('exporting ');
            }            
        }
        fclose($file_h);
        $content = ob_get_clean(); // ... then return it as a string!

        $headers = new \Zend\Http\Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="election_results_' . (date('Ymd_His') ) .'.csv"',
            'Content-Type' => 'text/csv',
            'Content-Length' =>  strlen($content)
        ));
        $this->getResponse()->getHeaders()->addHeaders($headers);
        $this->getResponse()->setContent($content);
        return $this->getResponse();
    }

    public function mlistAction(){

    	$param=$this->params()->fromQuery('id');
    	$target=$this->params()->fromQuery('target');
    	$result=array('param'=>$param, 'target'=>$target);
		$session = new Container('mlist_count');
		$tooManyRequest=false;

       if (!isset($session->codes_counter)) {
           $session->codes_counter = array();
       }


		if (!isset($session->counter)) {
		    $session->counter = 0;
		}else if( sizeof($session->codes_counter)  > 200){
    		$result['status']='error';
    		$result['message']='Unauthorized access! Your session is reported to Admin!!';
			$tooManyRequest=true;
    	}
    	if(!$this->current_user()->hasRole('election-data-entry')){
    		$result['status']='error';
    		$result['message']='Unauthorized request!';
    	}else if(empty($param)){
    		$result['status']='error';
    		$result['message']='ID Parameter is required';
    	}else if(!$tooManyRequest){ //user is authorized and id was provided
	        $member_srv = $this->getServiceLocator()->get('MemberService');
			$list = $member_srv->getMemberByHash(($param));            		
			$found = ($list!=null&&is_array($list)&&sizeof($list)>0);
			$result['status']=($found)?'success':'error';
			$result['data']=$list;
			$result['mhash']=sha3($param);
			if($found){
				//Add to search to session count
			    $session->counter++;
			    $result['mhash_count']=$session->counter;
                            $session->codes_counter[$param]=1;
                            $result['mhash_codes_count']=sizeof($session->codes_counter);


			}
    	}

    	return $this->json()->send($result);
    }

	private function exportForm(){

		$form = $this->getServiceLocator()->get('FormElementManager')->get('Form');
		$form->setName('export_form');
        
        		
		$branches = $this->getUserBranches();		
		$form->add(array(
			'type'=>'Select',
			'name'=>'branches',
			'options'=>array(
					'value_options'=>$branches,
				),
			'attributes'=>array(
					'multiple'=>true					
				)
			)
		);

		$form->add(array(
			'type'=>'Select',
			'name'=>'export_filter',
			'options'=>array(
					'value_options'=>array('new'=>'Since Last Export','all'=>'All Results')
				),
			'attributes'=>array('data-multi'=>'multi'),
			'class'=>'select2 select2multi'
			)
		);
		
		$form->add(array(
			'type'=>'Hidden',
			'name'=>'user_action'
		));
		
        //add election type        
        $service = $this->getServiceLocator()->get('CreateFormService');
        $form_election = $service->getform('election');
        $form->add($form_election->get('election_type'));        
		return $form;
	}
}
