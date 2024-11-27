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

class ConfigController extends AbstractActionController
{

    public function indexAction()
    {
        $view =  new ViewModel(array('caller'=>' index' ));
		
		
		
		return $view;
    }

    public function userDepartmentsAction(){
        
        /**
         * @var \Application\Service\OfficeAssignmentService
         */        
        $office_srv = $this->getServiceLocator()->get('OfficeAssignmentService');
        
        $current_user = $this->current_user();
        
        $user_id = $current_user->getId();

        //TODO FIXME TEST
       //$user_id =  4420; //test as GS for Immarat Toronot
       
       $dept_ids=array();
       $active_offices=$office_srv->getBranchesWithOffices($user_id);
       foreach ($active_offices as $key => $value) {           
           $dept_ids=array_merge($dept_ids,$value);           
       }

        $cfgService=$this->getServiceLocator()->get('ConfigService');
        
        if($current_user->hasRole('sys-admin')){
            $dept_ids = null; 
        }

		$filters=$this->params()->fromQuery();
        $list = $cfgService->listDepartments($dept_ids,$filters);
        $serializer = $this->getServiceLocator()->get('jms_serializer.serializer');
        echo ($serializer->serialize(array('data'=>$list), 'json'));    
        exit;                
    }
    
    public function branchCodeAction(){
        $branch_srv = $this->getServiceLocator()->get('BranchManagementService');
        $filter=$this->params()->fromQuery('filter');
        $data=array();
        if(empty($filter)){
            error_log("Branch is not found $filter");
            $data=array('error'=>"Branch is not found $filter");    
        }else{
            $branch = $branch_srv->getBranchByCode($filter);
            if($branch && is_object($branch)){
                $data=array($filter=>$branch->getId());
            }else{
                $data=array('error'=>"Branch is not found $filter");
            }
        }
        echo ($serializer->serialize($data, 'json'));    
        exit;
    }
    public function userBranchesAction(){
        
        /**
         * @var \Application\Service\OfficeAssignmentService
         */        
        $office_srv = $this->getServiceLocator()->get('OfficeAssignmentService');
        
        $current_user = $this->current_user();
        
        $user_id = $current_user->getId();

        //TODO FIXME TEST
        //$user_id =  5017; //test as GS for Immarat Toronot

        $filter=$this->params()->fromQuery('filter');
        if(empty($filter)){
           $filter=$this->params()->fromPost('filter');          
        }   
        $include_child=true;     
        if($filter === 'own'){
            $include_child=false;
        }        
       
        $branch_ids=array_keys($office_srv->getBranchesWithOffices($user_id,$include_child));

        $branch_srv = $this->getServiceLocator()->get('BranchManagementService');
	
        $offce_srv = $this->getServiceLocator()->get('OfficeAssignmentService');
        $roles = ($offce_srv->getAllOfficeRoles($current_user) );

        $is_office_bearer=false;
        foreach ($roles as $role){
           if($role->getRoleId()=='office-bearer'){
               $is_office_bearer=true;
           }
        }

        if($current_user->hasRole('national-general-secretary')  || $current_user->hasRole('sys-admin') ||
            ( (!$is_office_bearer) && $current_user->hasRole('upload-documents') ) ||
            ( $current_user->hasRole('admin-upload-documents') )
           ){
            $branch_ids = null; 
	    error_log('Branch is set to null List all branches');
        }

        $list = $branch_srv->listBranches($branch_ids);
        $serializer = $this->getServiceLocator()->get('jms_serializer.serializer');
        echo ($serializer->serialize(array('data'=>$list), 'json'));    
        exit;                
    }
    
    
    private function branchJsonAction(){
            $srv = $this->getServiceLocator()->get('BranchManagementService');
            $list = $srv->listBranches();
            $serializer = $this->getServiceLocator()->get('jms_serializer.serializer');
            echo ($serializer->serialize(array('data'=>$list), 'json'));    
            exit;        
    }
    public function branchAction()
    {
        $uri = $this->getRequest()->getUri();         
        $isJson=substr($uri->getPath(),-4)==='json';

        if($isJson){
            $this->branchJsonAction();
        }
        $view =  new ViewModel( );
        return $view;
    }

    public function userRoleAction()
    {

         $profileService = $this->getServiceLocator()->get('UserProfileService');
                                        
        $view =  new ViewModel( );

        if($this->json()->uri()){
            $roles=$profileService->listUserRoles();
            
            return $this->json()->send(array('data'=>$roles));                   

        }else{
            return $view;        
        }
    }

    public function departmentAction()
    {
        $view =  new ViewModel( );
        return $view;
    }

    public function departmentJsonAction()
    {
        $sl=$this->getServiceLocator();
        
        
        $result=array('success'=>1,'error'=>'');        
        
        
        $cfgService=$this->getServiceLocator()->get('ConfigService');
        
        if($this->getRequest()->isPost()){ //for post requests let configService handle saving of department
            
            $postData=$this->getRequest()->getPost();
            $postData['id']=$postData['dept_id'];
            $success=$this->Transaction()->wrap($cfgService,'updateDepartment',$postData);
            
            $result['success']=$success;
            $result['error']=$this->Transaction()->getLastError();
         }
		$filters=$this->params()->fromQuery();
        $data = $cfgService->listDepartments(array(),$filters);
        
        foreach ($data as $key => $dept) {
            $dept['dept_id']=$dept['id'];
            $dept['rules']= (in_array('rules',$dept))?$this->crypt()->decrypt($dept['rules']):'';
            $dept['guide_lines']=(in_array('guide_lines',$dept))?$this->crypt()->decrypt($dept['guide_lines']):'';
            $data[$key]=$dept;
        }
        $result['recordCount']=count($data);
        $result['pageCount']=1;//for now let cleient manage paging
        $result['data']=$data;
        $result['filters']=$filters;
        
        return new JsonModel($result);
    }
  
  private function findRoleBasedConfig($config_values,$return_role_based_config=true){
      $result = array();


      //config is key value and we have a role base key
      if($return_role_based_config && is_array($config_values) 
          && array_key_exists('role_based_config',$config_values))
      {
          $result=$config_values['role_based_config'];

      }elseif( (! $return_role_based_config) && is_array($config_values) 
          && array_key_exists('role_based_config',$config_values))
      {

          $result=$config_values;
          unset($result['role_based_config']);
          
      }elseif (is_array($config_values)) {
      //config is array and one of the elements is nested array of role base config
          //index of role_based config
          $index=-1;
          foreach ($config_values as $key=>$value){
              if (is_array($value) && in_array('role_based_config', array_keys($value) )) {
                  $result = $value['role_based_config'];
                  $index=$key;
              }
          }
          if (! $return_role_based_config){
              $result = $config_values;
              if($index >= 0){
                unset($result[$index]);
              }
          }
      }

      return $result;
  }
  public function configValueForUserAction(){

      $config_item=$this->getInputParameter('config_item');
      $result_format=$this->getInputParameter('format');
      
      $sl=$this->getServiceLocator();
      $config=$sl->get('ConfigService')->getConfigValues($config_item);

      //check if this is a role based config
      $values=$this->findRoleBasedConfig($config);

      $result = array();
      if(is_array($values) && !empty($values) ){

          $current_user = $this->current_user();
          $roles = $current_user->getRoles();

          $role_keys = array_keys($values);

          foreach ($roles as $role){
            if( in_array($role->getRoleId(),$role_keys) ) {
               $result = array_merge($result,$values[$role->getRoleId()]);
            }
          }

          //user role is not restricted
          if(empty($result)){                          
              $result = $this->findRoleBasedConfig($config,false);
          }

      }

      if($config_item!=null){
          $result=array($config_item=>array_values($result));
      }
      return new JsonModel($result);
  }
  
  public function configValueAction(){
      $config_item=$this->getInputParameter('config_item');
      $result_format=$this->getInputParameter('format');

      $sl=$this->getServiceLocator();
      $values=$sl->get('ConfigService')->getConfigValues($config_item);
      
      $result = $this->transformArray($result_format,$values);
      if($config_item!=null){
          $result=array($config_item=>$result);
      }
      return new JsonModel($result);
  }


  public function periodsAction(){
      $filter=$this->params()->fromQuery('filter');
      $from = new \DateTime('NOW');
      if($filter && strtolower($filter)=='all'){
          $from=new \DateTime(\Application\Entity\Period::START_PERIOD);
          $past='';
      }elseif($filter && strtolower($filter)=='last'){
          $from->modify('-1 month');
      }
      
      $config=$this->getServiceLocator()->get('ConfigService');

      $data=$config->getPeriods($from,$filter);      
      
      $years=$this->params()->fromQuery('years');
      if($years){
          $data = array_merge($config->getYears($from),$data);          
      }

      return $this->json()->send(array('data'=>$data));      
  }
  
  
  public function updateEncFieldAction(){
          
      if(!$this->getRequest()->isPost()){
          return new ViewModel();
      }
      
      $post = $this->params()->fromPost();

      $config = $this->getServiceLocator()->get('config');

      $adapter = $this->getServiceLocator()->get('DoctrineEncryptAdapter');
          
      
      $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
      $conn = $entityManager->getConnection();
      $stmt = $conn->executeQuery('select * from '.$post['table'].' where id =?',array($post['id']));
      
      //$stmt->setParam(1,$post['id']);
      
      $value=null;
      if($record = $stmt->fetch()){
          //update if
          if($post['oper']=='update'){
              
              $value = $adapter->encrypt($post['value']);
              $conn->executeQuery('begin');
              $success = $conn->executeUpdate('update '.$post['table'].' set '.$post['field'].' = ? where id = ?',
                                array($value,$post['id']),
                                array(\PDO::PARAM_STR,\PDO::PARAM_STR)
                                );
              $conn->executeQuery('commit');                                
              if($success>0){
                  $this->flashMessenger()->addSuccessMessage('Given record is saved');
                  //re-fetch
                  $stmt = $conn->executeQuery('select * from '.$post['table'].' where id=?',array($post['id']));
                  //$stmt->setParam(1,$post['id']);
                  $record = $stmt->fetch();
              }else{
                  //unable to find given record
                  $this->flashMessenger()->addErrorMessage("Unable to *save* row $post{id] for $post[table]");
              }              
          }
          
          $value = $record[$post['field']];
          //print_r($record);exit;
          $value = (!empty($value))?$adapter->decrypt($value):$value;
      }else{
          //unable to find given record
          $this->flashMessenger()->addErrorMessage("Unable to find row $post{id] from $post[table]");
      }
      
      return new ViewModel(array('value'=>$value,'data'=>$post));
    }
  
  
  
  public function reportQuestionsAction(){

        $grid_manager = $this->getServiceLocator()->get('ZfcDataGridFactory');
        $grid_name=$list_fields.'ReportQuestionsList';
        $grid = $grid_manager->createGrid($grid_name);       

        //$grid->setTitle('Documents List');
        $grid->setRendererName('jqgrid');
        $grid->setToolbarTemplate('');
        $grid->setDefaultItemsPerPage(15);
    
        $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
        $srv=$this->getServiceLocator()->get('ConfigService');
        $data_source =$srv->questionsDataSource($user_id);

        $grid->setDataSource($data_source);
        $grid->setUserFilterDisabled(false);
        
        $datagrid_helper = $this->getServiceLocator()->get('DataGridHelper');
        $datagrid_helper->grid('QuestionList')->addColumns($grid);

        
        if($this->getRequest()->isPost()){
           //a POST reuqest is a direct call to dataGrid and will be handled by grid renderer returning JSON data  
            return $grid->getResponse();
        }else{
            //For a non post reuqest we will setup datagrid page 
            $view = new ViewModel();    
            $view->addChild($grid->getResponse(),'question_grid');
    
            return $view;
        }  
  }

  private function addExpires(){
 
      $this->getResponse()->setHeader('Expires', '', true);
      $this->getResponse()->setHeader('Cache-Control', 'public', true);
      $this->getResponse()->setHeader('Cache-Control', 'max-age=604800');
      $this->getResponse()->setHeader('Pragma', '', true);

  }

  private function transformArray($result_format,$values){
      $result=array();

      if(is_array($values) && $result_format=='key-value'){
          foreach ($values as $key => $value) {
              $result[]=array('key'=>$key,'value'=>$value);
          }
      }else{
          $result=$values;
      }
      return $result;    
  }

  private function getInputParameter($param){
      $param_value=$this->params()->fromQuery($param);
      
      if(empty($param_value)){
          $param_value=$this->params()->fromPost($param);          
      }
      return $param_value;
  }

    public function __branchCodeAction(){
        $branch_srv = $this->getServiceLocator()->get('BranchManagementService');
        $serializer = $this->getServiceLocator()->get('jms_serializer.serializer');
        $filter=$this->params()->fromQuery('filter');
        $data=array();
        if(empty($filter)){
            error_log("Branch is not found $filter");
            $data=array('error'=>"Branch is not found $filter");    
        }else{
            $branch = $branch_srv->getBranchByCode($filter);
            if($branch && is_object($branch)){
                $data=array($filter=>$branch->getId());
            }else{
                error_log("Branch is not found $filter");
                $data=array('error'=>"Branch is not found $filter");
            }
        }
        echo ($serializer->serialize($data, 'json'));    
        exit;
    }

}
