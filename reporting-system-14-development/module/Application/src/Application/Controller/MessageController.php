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

use Application\View\Helper\SmartDateTimeFormatHelper as SmartFormat;
use Application\Form\RemindersForm;

use Application\Util\DocumentUtil;

use ZfcDatagrid\DataSource\Doctrine2 as GridDataSource;
use Zend\View\Model\JsonModel;

class MessageController extends AbstractActionController
{

    public function indexAction()
    {
        
        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();        

        $action = $this->getEvent()->getRouteMatch()->getParam('action');
        if(!$action || $action=='index' || $action==''){
            $action='inbox';
        }

        $srv = $this->serviceLocator->get('MessagesService');
        $messages = $srv->getUserMessages($current_user,$action);
       
        $view =  new ViewModel(array('messages'=>$messages,'mailbox'=>$action));
        
        if($this->params()->fromPost('operation')=='open'){
            $view->setVariable('user_message', $this->getUserMessage());
        }

        $view->setTemplate('message/index');
        
        return $view;
        
    }

    public function composeAction()
    {
        $view =  new ViewModel(array('caller'=>' index' ));
        
        
        
        return $view;
    }


	public function bulkActionAction(){
		
		$data = array();
		$ids=array();
		$result=array();
        if($this->getRequest()->isPost()){
        	            
            $data = $this->params()->fromPost();
			$ids=$data['message_ids'];
			$user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
			
            $messagesSrv  = $this->getServiceLocator()->get('MessagesService');
			$r = $messagesSrv->updateUserMessages($data['bulk_action'],$user_id,$ids);
			$result['status']='looks good';
            $result['message_ids']=$ids;
			$result['result']=$r;
		}
		return new JsonModel($result);		
	}

    public function systemreminderAction(){

        sleep(rand(60, 3600));
        $data = [];

        $data['month'] = date('M', strtotime('previous month'));
        if($data['month']){
            $monthDate = new \DateTime($data['month']);
            $nowDate = new \DateTime();
            //if period is in future decrement year to get last year period
            if($nowDate<$monthDate){
                $monthDate->modify('-1 year');
            }
            $period = $monthDate->format('M-Y');
            $data['period']=$period; 
        }

        $userSrv = $this->serviceLocator->get('UserProfileService');
        // $current_user = $userSrv->getUserById(2146); // dev
        $current_user = $userSrv->getUserById(6034);

        $officeSrv = $this->serviceLocator->get('OfficeAssignmentService');         
        $branches_with_office=($officeSrv->getBranchesWithOffices($current_user));
        $dept_ids=array();
        foreach ($branches_with_office as $key => $value) {
        	foreach($value as $dept){
        		$dept_ids[$dept]=$dept;
        	}	
        }
        $dept_ids = array_keys($dept_ids);
        $data['office'] = $dept_ids;

        $branch_ids=array_keys($branches_with_office);
        $data['branch'] = $branch_ids;

        $data['send_to_rule'] =  $this->getRequest()->getParam('send_to'); //'not_completed';
        $data['level_rule'] =  $this->getRequest()->getParam('level'); //'local';

        $reciepient = $officeSrv->getReminderReciepientList($current_user,$data);
        if(count($reciepient) > 0 ){ 
            $this->logMailData($current_user,$data,$reciepient);

            $messagesSrv  = $this->getServiceLocator()->get('MessagesService');
            $sender_offices=$officeSrv->getActiveOffices($current_user);
            $result = $messagesSrv->generateReminders($sender_offices[0],$reciepient,$data);
        }
 
        echo "system reminder";
    }
	
    public function reminderAction(){

        // file_put_contents($file, $email);
        
        $form = $this->serviceLocator->get('FormElementManager')->get('Application\Form\RemindersForm');
        $reciepient=null;
        $result=array('','');
        if($this->getRequest()->isPost()){
            
            $data = $this->params()->fromPost();
            $invalidData=false; 
            if ( (!array_key_exists('branch',$data)) || (!array_key_exists('month',$data)) || (!array_key_exists('office',$data)) ){
               $invalidData=true; 
               $this->flashMessenger()->setNamespace('error')->addMessage('Invalid Data!!  No mail was sent!');
            }
            if($data['branch']==0){
                unset($data['branch']);
            }            
            if($data['office']==0){
                $data['office'] = array_keys($form->getOffices());
            }            
            //convert month to period
            if($data['month']){
                 
                $monthDate = new \DateTime($data['month']);
                $nowDate = new \DateTime();
                //if period is in future decrement year to get last year period
                if($nowDate<$monthDate){
                    $monthDate->modify('-1 year');
                }
                $period = $monthDate->format('M-Y');
                $data['period']=$period; 
            }
            
            $form->setData($data);

            //save attachments            
            /**
             * @var DocumentUtil
             */
            if (! $invalidData ){
               #error_log(print_r(['OFFICES',$data['office']],true) );
               #error_log(print_r(['DATA',$data],true) );
               $doc_util=$this->getServiceLocator()->get('DocUtil');
               $files = $this->params()->fromFiles('file_attachments');
               $saved_files = $doc_util->saveUploadedFiles($files,DocumentUtil::UPLOAD_TYPE_ATTACHMENT);
               $data['attachments']=$saved_files;
            
            
               $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();        
               $officeSrv  = $this->getServiceLocator()->get('OfficeAssignmentService');
            
               error_log(print_r(['Reminder Data',$data],true) );
               $reciepient = $officeSrv->getReminderReciepientList($current_user,$data);
               if(count($reciepient) > 0 ){ 
   	              $this->logMailData($current_user,$data,$reciepient);
               }else{
                  $this->flashMessenger()->setNamespace('error')->addMessage('Select results in zero (0)  reciepient!!');
               }
			
               $messagesSrv  = $this->getServiceLocator()->get('MessagesService');
            
               $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
               $sender_offices=$officeSrv->getActiveOffices($current_user);
               $result = $messagesSrv->generateReminders($sender_offices[0],$reciepient,$data);
            }          
        }else{
	        if($form->user_level == 'national'){
	         $form->get('level_rule')->setValue('local');	
            }
	    }
        $vm = new ViewModel(array('form'=>$form,'reciepient'=>$reciepient,
                                 'result_summary'=>$result[0],'result_details'=>$result[1]));
        return $vm;
    }

	
	private function logMailData($current_user,$data,$reciepients){
			
		if(! is_array($reciepients)){
			$reciepient=array($reciepients);	
		}
		
		$list='';
		foreach ($reciepients as $reciepient) {
			$list = $list.','.$reciepient->__toString();
		}
		$this->getServiceLocator()->get('Logger')->info("Sending reminders:".PHP_EOL.print_r(array('sender'=>$current_user->__toString(),'to'=>$list,'params'=>$data),true));
	}

    public function inboxAction()
    {
        return  $this->indexAction();
    }

    public function sentAction(){
        return  $this->indexAction();
    }

    public function draftAction(){
        return  $this->indexAction();
    }
  
    public function archivedAction(){
        return  $this->indexAction();
    }


    private function canAcccessMessage($message_id){
        
       $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
       $message = $this->getServiceLocator()->get('MessagesService')->getMessage($message_id);
       
       $canAccess = true;
       
       if($message->getSender()->getId() != $current_user->getId()){
           $user_message = $this->getServiceLocator()->get('MessagesService')->getUserMessage($message_id,$current_user);
           //user is not sender only if user is sent this message then user can access
           if($user_message == null ){
               $canAccess = false;
           }
       }
       return $canAccess;        
    } 
    
    public function attachmentAction(){
        $msgId = $this->params()->fromQuery('id');
        $fileName = $this->params()->fromQuery('f');
        $action = $this->params()->fromQuery('a');
        
        if($this->canAcccessMessage($msgId)){
            $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
            $msgSrv = $this->serviceLocator->get('MessagesService');
            $message = $msgSrv->getMessage($msgId);
            $files = $message->getAttachments();
            $file_info=null;
            foreach ($files as $file) {
               if($file['saved_as'] == $fileName){
                   $file_info = $file;
               } 
            } 
            if(!empty($file_info)){
                $response = $this->getServiceLocator()->get('DocUtil')->attchmentToResponse($file_info);
                if($response){
                    return $response;
                }
            }       
        }
        //return simple HTML with error that attachment is not found
    }
    
    public function openAction(){

       $result = array();           
       $result['success']=false;
       $result['error']='Invalid request';

       if(!$this->getRequest()->isPost()){
            return $this->json()->send($result);
       }else{

           $msgId = $this->params()->fromPost('message_id');
           $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();        
           
           $msgSrv = $this->serviceLocator->get('MessagesService');
           $message = $msgSrv->getMessage($msgId);
           if( !$this->canAcccessMessage($msgId) ){
               return $this->json()->send($result);
           }
           //now send message in tempalte to user
           /**
            * @var ViewModel
            */
           $vm = new ViewModel(array('message'=>$message));
           
           $this->layout('layout/layout_ajax.phtml');
           
           return $vm;           
       }
   }
       
   public function readAction(){

       $result = array();

       if(!$this->getRequest()->isPost()){
           
            $result['success']=false;
            $result['error']='Invalid request';
            
       }else{

           $userMsgId = $this->params()->fromPost('user_message_id');
           
           $msgSrv = $this->serviceLocator->get('MessagesService');
           $userMessage = $msgSrv->readMessage($userMsgId);
           
           $message = $userMessage->getMessage();
           
           $result['success']=true;
           $result['time']=SmartFormat::smartFormat($message->getDateSent(),true);
           $result['sent_as']=$message->getSentAs();
           $result['subject']=$message->getSubject();
           $result['html_body']=$message->getHtmlBody();
           $result['is_unread'] = $userMessage->isUnread();
           
       }
       
       return $this->json()->send($result);
           
   }
   
   
   private function getUserMessage(){
        if( ! $this->getRequest()->isPost()){
            return null;
        }

        $user_message_id=$this->params()->fromPost('user_message_id');
        $message_id=$this->params()->fromPost('message_id');
        $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
        
        $user_message;
        if($user_message_id){
            $user_message = $this->getServiceLocator()->get('EntityService')->getObject('UserMessage',$user_message_id);
        }else{
            $user_message = $this->getServiceLocator()->get('MessagesService')->getUserMessage($message_id,$user_id);
        }
        return $user_message; 
   }

}
