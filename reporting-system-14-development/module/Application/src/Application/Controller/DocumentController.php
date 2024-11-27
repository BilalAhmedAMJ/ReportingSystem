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
use Zend\Http\Headers;
use Zend\Http\Response\Stream;
use Zend\View\Model\JsonModel;

use Application\View\DataGrid\IconButton;

use ZfcDatagrid\Column;

class DocumentController extends AbstractActionController
{
    
    private $uploaded_types=array('election_lists','tajnid_lists','mal_report','system_data');

    public function indexAction()
    {
		
		return $this->listAction();
    }

    private function getDocumentId($action){
         //expect URI for this request to have a document id hash as last art of URI path
         $uri = $this->getRequest()->getUri();
         $path = preg_split('%/%',$uri->getPath());
         $doc_id=array_pop($path);
         //if "download" i.e. action name is last part of URI then try to get docid from post or query params 
        if(preg_match("/${action}/", $doc_id)){
                 
             $doc_id = $this->params()->fromPost('document');
             if(!$doc_id){
                 $doc_id = $this->params()->fromQuery('document');
             }
         }
        return $doc_id;        
    }

    public function editAction(){
 
        $user = $this->zfcUserAuthentication()->getIdentity();
        
        $doc_id=$this->getDocumentId('edit');
         
        if($user->hasRole('admin')||$user->hasRole('sys-admin')){

            $docSrv = $this->getServiceLocator()->get('DocumentService');
            
            $document = $docSrv->setDocumentToExpire($doc_id);
            
        }else{
            $this->flashMessenger()->addErrorMessage(sprintf(("Unable to find document [%s]"),$doc->getTitle()));
        }
        
        $redirectUrl = $this->url()->fromRoute('document/list');
        if(!empty($this->getRequest()->getQuery('filter'))){
            $redirectUrl=$redirectUrl.'?filter='.$this->getRequest()->getQuery('filter');
        }
        return $this->redirect()->toUrl($redirectUrl);
    }

    public function downloadAction()
    {

        $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        //expect URI for this request to have a document id hash as last art of URI path
        $uri = $this->getRequest()->getUri();
        $path = preg_split('%/%',$uri->getPath());
        $doc_id=array_pop($path);
        //if "download" i.e. action name is last part of URI then try to get docid from post or query params 
        if(preg_match('/download/', $doc_id)){
                
            $doc_id = $this->params()->fromPost('document');
            if(!$doc_id){
                $doc_id = $this->params()->fromQuery('document');
            }
        }

        if($doc_id && $doc_id != ''){
            $srv = $this->getServiceLocator()->get('DocumentService');
            /**
             * @var \Application\Entity\Document
             */
            $doc = $srv->getDocument($doc_id);
            
    
            //$response = $this->sendFile($doc->getFileName(),$doc->getTitle(),$doc->getDocumentExt());
            $response = $this->getServiceLocator()->get('DocUtil')->documentToResponse($doc);
            if(!$response){
                $this->flashMessenger()->addErrorMessage(sprintf(("Unable to find document [%s]"),$doc->getTitle()));
            }
            
            
        }else{
            $this->flashMessenger()->addErrorMessage("No document to download. ");
            $response=null;
        }        

        if(!$response){
            $response =  new ViewModel();
            $response->setTemplate('application/document/list');                
        }
        
        return $response;
    }

    public function uploadAction()
    {
        $user = $this->current_user();
        $offce_srv = $this->getServiceLocator()->get('OfficeAssignmentService');
        $roles = ($offce_srv->getAllOfficeRoles($user) );
        
        $conf_srv = $this->getServiceLocator()->get('ConfigService');
        $document_upload_types = $conf_srv->getConfigValues('document_upload_types');

    	$is_office_bearer=false;
    	foreach ($roles as $role){
    	   if($role->getRoleId()=='office-bearer'){
                   $is_office_bearer=true;
               }
    	}

        if($this->params()->fromPost('response_type')=='json'){
            
            $sl = $this->getServiceLocator();
            $data = $this->params()->fromPost();
            //first save uploaded documents to disk
            
            $allowed_jamaat_halqa;
            
            if(key_exists('jamaat_halqa', $data)){
                $jamaat_with_parents = $this->getJamaatWithParents($data['jamaat_halqa']);
                $allowed_jamaat_halqa = 'branch_id='.join(',branch_id=',$jamaat_with_parents);
                
                $data['allowed_jamaat_halqa']=$allowed_jamaat_halqa.',';
                
            }
            
            $files = $this->params()->fromFiles('documents');
            $doc_util=$sl->get('DocUtil');
            $saved_files = $doc_util->saveUploadedFiles($files);
            
            //create documents for saved files
            $doc_srv = $sl->get('DocumentService'); 
            
            $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
            
            
            
            $docs = $doc_srv->saveFiles($data,$saved_files,$current_user,array('user_type','jamaat_halqa','allowed_jamaat_halqa'));
            
            return  $this->json()->send(array('fields'=>$this->params()->fromPost(),
                                              'files'=>$docs
                                              )
                                       );            
        }else{
            return $view =  new ViewModel(array('caller'=>'document index','document_upload_types'=>$document_upload_types ));            
        }
    }

    private function getJamaatWithParents($jamaat_halqa){
        
        $jamaat_with_parents=array();

        if(empty($jamaat_halqa)){
          return $jamaat_with_parents;
        } 
        $branch_srv = $this->getServiceLocator()->get('BranchManagementService');
        
        if( ! is_object($jamaat_halqa) ){
            $jamaat_halqa = $branch_srv->getBranch($jamaat_halqa);
        }
        
        $jamaat_with_parents[] = $jamaat_halqa->getId();
        
        $parent = $jamaat_halqa->getParent();
        
        while( $parent != null && $parent->getId() != $jamaat_halqa->getId() ){
            $jamaat_with_parents[] = $parent->getId();
            $parent = $parent->getParent();
        }
        
        return $jamaat_with_parents;
    }

    private function createDocumentGrid($list_type,$title){
        /** @var $grid \ZfcDatagrid\Datagrid */
        $grid_manager = $this->getServiceLocator()->get('ZfcDataGridFactory');
        
        $grid_name="${list_type}_grid";
        $grid = $grid_manager->createGrid($grid_name);
        $grid->setTitle($title);
        $grid->setRendererName('jqGrid');
        $grid->setRendererName('bootstrapTable');        
        $grid->setToolbarTemplate('');
        $grid->setId($grid_name);
        
        $grid->setDefaultItemsPerPage(0);
    
        $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
        $current_user = $this->zfcUserAuthentication()->getIdentity();
        $srv=$this->getServiceLocator()->get('DocumentService');
                
        $params = array();
        if($list_type=='uploaded'){
            $params = array('types'=>$this->uploaded_types );
            $user = $this->zfcUserAuthentication()->getIdentity();     
            $officeSrv = $this->getServiceLocator()->get('OfficeAssignmentService');
            $offices=$officeSrv->getBranchesWithOffices($user);
            $allowed_uploaded_docs = $this->getServiceLocator()->get('ConfigService')->getConfigValuesForUser($current_user,'dept_allowed_uploaded_docs');
            $branches='';
            $all_depts = array();
            foreach ($offices as $branch => $depts) {
                $has_one = array_intersect(array_values($depts),$allowed_uploaded_docs);
                if(count($has_one)>0){ 
                    $branches=$branches.','.$branch;
                    $all_depts=array_merge($depts,$all_depts);
                }
            }

            if( $user->hasRole('admin-upload-documents')){
                $branches='27';
            }           
            $params['branch_id']=$branches;
            
            if( !$current_user->hasRole('admin') && !$current_user->hasRole('sys-admin') && count($all_depts)<1 ){
              //types based on roles
              $docTypes = $this->getServiceLocator()->get('ConfigService')->getConfigValuesForUser($current_user,'document_type');
              $params['types']=$docTypes;
            }
        }
       
        $data_source =$srv->documentsDataSource($user_id,$params);

        $grid->setDataSource($data_source);
        
        $col_optins = array(
		'adapter'=> $this->getServiceLocator()->get('DoctrineEncryptAdapter')
	        );
 
        $datagrid_helper = $this->getServiceLocator()->get('DataGridHelper',$col_optins);
        
        $editable=false;
        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
        if($current_user->hasRole('admin')||$current_user->hasRole('sys-admin')){
            $editable=true;
        }
        //$editable=;

        
        $datagrid_helper->grid('DocumentList',$col_optins)->addColumns($grid,$editable,$list_type=='uploaded');        
        return $grid;
    }
    
    public function listAction()
    {
        $list_type='generic';
        $list_title='Documents List';
        $toolbarFilters = $this->params()->fromPost('toolbarFilters');
         
        if( ($this->params()->fromQuery('filter') && $this->params()->fromQuery('filter')=='uploaded') ||
            ($toolbarFilters && in_array($toolbarFilters['document_document_type'],$this->uploaded_types) )
          ){
            $list_type='uploaded';
            $list_title='Uploaded Documents';

            $post = $this->getRequest()->getPost();
            $toolbarFilters = $post->get('toolbarFilters');
            //$toolbarFilters['document_document_type']=$this->uploaded_types[0];
            $post->set('toolbarFilters',$toolbarFilters);
            $this->getRequest()->setPost($post);
        }    
            
        $documents_grid = $this->createDocumentGrid($list_type,$list_title);

        if($list_type=='uploaded'){
            //$documents_grid->setUserFilterDisabled(true);                
            $documents_grid->getColumnByUniqueId('document_document_type')->setFilterDefaultValue('');                        
        }        
                
        if($this->getRequest()->isPost()){
            
           //a POST reuqest is a direct call to dataGrid and will be handled by grid renderer returning JSON data  
            return $documents_grid->getResponse();
        }else{
            //For a non post reuqest we will setup datagrid page 
            $view = new ViewModel(array('list_type'=>$list_type));    
            $view->addChild($documents_grid->getResponse(),'documents_grid');
            $view->getTemplate('application/document/index');
    
            return $view;
        }
    }


}
