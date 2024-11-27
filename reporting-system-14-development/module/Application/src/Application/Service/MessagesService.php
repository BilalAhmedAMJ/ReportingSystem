<?php


namespace Application\Service;

use Application\Entity\UserToken;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Mail\Address as EmailAddress;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManager;

use Application\Entity\User;
use Application\Entity\Message as Message;
use Application\Entity\OfficeAssignment;
use Application\Entity\UserMessage;
use Application\Entity\ReportMessage;

class MessagesService implements FactoryInterface{
    
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
    
/*    
    public function getRecipientList($user,$recipient_rule){
            
        $config = $this->serviceLocator->get('ConfigService');    
        
        $officeService = $this->serviceLocator->get('OfficeAssignmentService');        
                
        $queryDQL = "
                select user from \Application\Entity\User user 
                join user.office_assignments  office
                join office.department department
                join office.period_from period_from
                join office.period_to period_to
                join office.branch branch
                
                where 
                period_from.period_start <= :datetime and period_to.period_end >= :datetime
                and office.status in (:status)
                and 
                (branch = :branch 
                OR
                branch in (select branchsq from \Application\Entity\Branch branchsq where branchsq.parent = :branch_as_parent)
                )
                
                and department in (:department)
        ";
        
        / * *
         * @var \Doctrine\ORM\Query;
         * /
        $query = $this->entityManager->createQuery($queryDQL);

        $query->setParameter('datetime',new \DateTime());                
        //$query->setParameter('datetime2',new \DateTime());                

        $query->setParameter('status',explode(",","active,approved"));                

        $offices = $officeService->getActiveOffices($user);

        if($recipient_rule==$this::Recipient_Rule_SubBranches){
            $query->setParameter('branch',$offices[0]->getBranch());                    
            $query->setParameter('branch_as_parent',$offices[0]->getBranch());                    
            $query->setParameter('department',$offices[0]->getDepartment());  
                          
        }elseif ($recipient_rule==$this::Recipient_Rule_ParentBranch){//parent 
            $query->setParameter('branch',$offices[0]->getBranch()->getParent());                    
            $query->setParameter('branch_as_parent',null);          
            $query->setParameter('department',$offices[0]->getDepartment());          
                                                    
        }elseif($recipient_rule==$this::Recipient_Rule_SameBranch){//same level, pick all dept
            $query->setParameter('branch',$offices[0]->getBranch());                    
            $query->setParameter('branch_as_parent',null);                    
            $query->setParameter('department',$config->listDepartments());
              
                            
        }else{//no rules match return  given user back
            $query->setParameter('branch',$offices[0]->getBranch());                    
            $query->setParameter('branch_as_parent',null);                    
            $query->setParameter('department',$offices[0]->getDepartment());                
        }
                      
        return $query->getResult();
    }
*/
    
    public function getUserMessages($user,$deleted=false)
    {
        $queryDQL = "
                select user_message from \Application\Entity\UserMessage  user_message 
                join user_message.message message
                join user_message.user user
                
                where 
                message.status = 'SENT' and
                user = :user
        ";

        if(! $deleted){
            $queryDQL = $queryDQL."
                and user_message.date_deleted is null 
                ";
        }    
        
        $queryDQL = $queryDQL."
                order by message.date_sent desc
                ";
                        
        /**
         * @var \Doctrine\ORM\Query;
         */
        $query = $this->entityManager->createQuery($queryDQL);
        $query->setParameter('user',$user);
        
        return $query->getResult();
    }

    public function getMessage($message_id){
        return $this->entityService->getObject('Message',$message_id);
    }

    public function readMessage($user_msg_id){
        /**
         * @var \Application\Entity\UserMessage
         */
        $userMessage = $this->serviceLocator->get('EntityService')->getObject('UserMessage',$user_msg_id);   
        
        $userMessage->setDateRead(new \DateTime());
        
        $this->entityManager->transactional(function($em) use(&$userMessage){
           $em->persist($userMessage);
       });
       
       return $userMessage;
    }
    
    public function updateUserMessages($action,$user_id,$message_ids){
    	
		$qb = $this->entityManager->createQueryBuilder();
		$qb = $qb->update('\Application\Entity\UserMessage', 'um');
		
    	if($action=='delete'){
    		$qb = $qb->set('um.date_deleted' ,':date')
    				 ->setParameter('date', new \DateTime());		
					 	
    	}elseif($action=='read'){
    		
    		$qb = $qb->set('um.date_read',':date')
    				 ->setParameter('date', new \DateTime());
					 			
    	}elseif($action=='unread'){
    		
    		$qb = $qb->set('um.date_read',':date')
					 ->setParameter('date',null);
			
    	}else{
    		throw new \Exception("Invalid action [$action]");
    	}
		
	    $qb = $qb->where('um.user = :user')
				 ->andWhere('um.id in (:messages) ')
				 ->setParameter('user', $user_id)
				 ->setParameter('messages', $message_ids);
		
		$q = $qb->getQuery();

	    $result = null;
		
		$this->entityManager->transactional(function($em) use(&$q,&$result){
               $result=$q->execute();
        });
		
		$this->entityManager->flush();
		
		return array('messages-updated'=>$result,'bulk-action'=>$action);    	    				
    }
	
    public function getUserMessage($message_id,$user_id){
        $queryDQL = "
                select user_message from \Application\Entity\UserMessage  user_message 
                join user_message.message message
                join user_message.user user
                
                where 
                message = :message and
                user = :user                
        ";
        
        /**
         * @var \Doctrine\ORM\Query;
         */
        $query = $this->entityManager->createQuery($queryDQL);
        $query->setParameter('user',$user_id);
        $query->setParameter('message',$message_id);
                
        return $query->getOneOrNullResult();
    }


    const Recipient_Rule_SameBranch='samebranch';
    const Recipient_Rule_ParentBranch='parentbranch';
    const Recipient_Rule_SubBranches='subbranches';
    const Message_Type_Internal='internal';
    const Message_Type_Email='email';
    const Message_Status_Draft='DRAFT';
    const Message_Status_Sent='SENT';
    const Message_Status_Unsent='UNSENT';    
    
    /**
     * @param $sender user sending this message
     * @param $subject 
     * 
     */
    public function composeMessage($sender,$subject,$messageBody,
    								$recipients,
    								$type,
    								$sender_office,
    								$status=MessagesService::Message_Status_Draft,    								
    								$message_id=null,$messageText='',
    								$no_transaction=true
    								){
    									
		$message=null;										
		if(! $message_id ) {
			/**
			 *@var Message 
			 */
			$message=$this->entityFactory->getMessage();
		}
		else{
			$message=$this->entityService->getObjet('Message',$message_id);
		}

		$message->setDateModified(new \DateTime());
        $message->setSender($sender);
        $message->setSubject($subject);
        $message->setHtmlBody($messageBody);
        $message->setTextBody($messageText);
		$message->setMessageType($type);
		$message->setStatus($status);
        if(MessagesService::Message_Status_Sent == $status){
            $message->setDateSent(new \DateTime());
        }
        if($sender_office){
            $message->setSentAs($sender_office->getTitle(true));
        }
        
        if(is_array($recipients)){
            $recipients = $recipients;                        
        }elseif($recipients instanceof User){
            $recipients = array($recipients);
        }else{
            throw new \InvalidArgumentException("Invalid recipientRule, recipientRule must be either a string or a recipient user");
        }
 
        foreach ($recipients as $recipient) {
            /**
             * @var UserMessage;
             */
            $userMessage = $this->entityFactory->getUserMessage();
            $userMessage->setMessage($message);
            $userMessage->setUser($recipient);
            $message->addUserMessage($userMessage);
        }	
		
        
       if($no_transaction){
           $this->entityManager->transactional(function($em) use(&$message){
               $em->persist($message);
           });
       }
        
		return $message;
        
    }
       
   private function replaceTokens($sender_office,$recipient_office,$message,$data){
       $tokens=array(
           'MonthName'=>$data['period'],
           'SenderTitleWithBranch'=>$sender_office->getTitle(true),
           'RecipientTitleWithBranch'=>$recipient_office->getTitle(true),
           'ParentBranchHead' => $recipient_office->getBranch()->getBranchHeadTitle(),
       );
       $result = $message;
       foreach ($tokens as $key => $value) {
           $result = preg_replace('/##'.$key.'##/', $value, $result);
       }
       return $result;
   }
    
   private function createReportMessageLink($message,$subject,$link_type,$report){

          /**
           * @var Application\Entity\ReportMessage
           */
          $report_message = $this->entityFactory->getReportMessage();
          $report_message->setMessage($message);
          $report_message->setLinkType($link_type);
          $report_message->setReport($report);
          $report_message->setDescription($subject);
          
          return $report_message;
       
   }
    
    public function createReportNotification(array $notification_data){
        
        $this->renderer = $this->serviceLocator->get('ViewRenderer');  
        $contentHtml = $this->renderer->render('email/'.$notification_data['template'], $notification_data);
    
        $message = $this->composeMessage($notification_data['current_user'],
                              $notification_data['subject'],
                              $contentHtml, $notification_data['to_office']->getUser(),
                              $this::Message_Type_Email,
                              $notification_data['from_office'],
                              MessagesService::Message_Status_Sent                              
                              );    

        $report_messages=array();

        if(!empty($notification_data['link_type'])){
              $report_message = $this->createReportMessageLink($message,$notification_data['subject']
                                                ,$notification_data['link_type'],$notification_data['report']);
              $report_messages[]=$report_message; 
        }

        $emailService = $this->serviceLocator->get('EmailService');
        
        $emailService->sendTemplatedEmail($notification_data['subject'], 
                                         $notification_data['to_email'],
                                         $notification_data['template'],
                                         $notification_data);

        //create report linked messages if requested
        if(is_array($notification_data['report_messages'])){
            foreach ($notification_data['report_messages'] as $report_msg_data) {
                $contentHtml = $this->renderer->render('email/'.$report_msg_data['template'], $report_msg_data);
                $message=$this->composeMessage($notification_data['current_user'],
                              $report_msg_data['subject'],
                              $contentHtml, $report_msg_data['to_office']->getUser(),
                              $this::Message_Type_Internal,
                              $report_msg_data['from_office'],
                              MessagesService::Message_Status_Sent                              
                              );
              /**
               * @var Application\Entity\ReportMessage
               */
              $report_message = $this->createReportMessageLink($message,$report_msg_data['subject']
                                                ,$report_msg_data['link_type'],$report_msg_data['report']);
              
              $report_messages[]=$report_message; 
          }//create report_messages 
          
        }
      // save / persist report_messages 
      $this->entityManager->transactional(  function($em) use(&$report_messages){            
          foreach ($report_messages as $key=>$report_message) {
              $em->persist($report_message);
          }
      });
            
    }// createReportNotification


    /**
     * @param $sender_office office of the sending user
     * @param $recipients list of offices of recipient users 
     * 
     **/
    public function generateReminders($sender_office,$recipients,$data){
        
        if($recipients instanceof OfficeAssignment){
            $recipients = array($recipients);
        }elseif(!is_array($recipients) ){
            throw new \Eception('Recipients need to be either an office or list of offices');
        }
          
        $message_tempalte_name='reminder_submit_report';
        if(isset($data['send_to_rule']) && $data['send_to_rule']=='not_received'||
           isset($data['send_to_rule']) && $data['send_to_rule']=='not_verified'
          ){
            $message_tempalte_name='reminder_'.$data['send_to_rule'];    
        }
        
        $tempaltes = $this->serviceLocator->get('ConfigService')->getConfig('email_templates');

        $message_tempalte = $tempaltes[$message_tempalte_name];

        $emailService = $this->serviceLocator->get('EmailService');
        
        
        $results =array();
        $results_summary =array(
                    'inactive'=>0,
                    'email'=>0,
                    'noemail'=>0,
                    'error'=>0,
                    'unknown'=>0
                );
        
        foreach ($recipients as $recipient) {
            $user = $recipient->getUser();
            $action=null;
            if(!$recipient->isValid()){
                                
                $action='inactive';
                       
            }elseif($user && $user->getEmail()){
                
                $action='email';
                
            }elseif($user){
                
                $email= 'noemail';
                
            }else{
                //valid office with no user???
                $action='unknown';
            }
            /** 
             * @var Message
             */
            $message=null;
            if(in_array($action ,array('email','inactive','noemail'))){
                $messageText='';
                $subject='';                
                if(key_exists('custom_message',$data) &&($data['custom_message']==1 || $data['custom_message']=='on') ){
                    //custome message
                    $messageText = $this->replaceTokens($sender_office, $recipient, $data['custom_message_body'], $data);
                    $subject=$data['subject'];            
                }else{
                    //template based message
                    $messageText = $this->replaceTokens($sender_office, $recipient, $message_tempalte['message'], $data);
                    $subject=$message_tempalte['subject'];
                }
                
                $message = $this->composeMessage($sender_office->getUser(),
                                      $subject,
                                      $messageText, $recipient->getUser(), 
                                      $this::Message_Type_Email,
                                      $sender_office,
                                      $this::Message_Status_Sent,
                                      null,
                                      '',
                                      false//do not wrap creation of message in transaction, we will do that
                                      );    
                //handle attachments
                $message->setAttachments($data['attachments']);
                                                      
                if($action='email'){
                    $sender_user = $sender_office->getUser();
                    $sent_result = $emailService->sendHTMLEmail($user->getEmail(),
                                                     $subject,
                                                     '',$messageText,
                                                     $data['attachments'],
                                                     array('email'=>$sender_user->getEmail(),
                                                           'name'=>$sender_office->getTitle(true)));

                    if(!$sent_result){
                        $action='error';
                    }                             
                }//send email
                if($action!='email'){
                    $message->setMessageType(self::Message_Type_Internal);
                }              
            }//create message
            $results[]=array($action,$recipient,$message);
            $results_summary[$action]++;
        }//for all recipients
        
        
        $this->entityManager->transactional(function($em) use(&$results){
            foreach ($results as $result) {
                if($result[2]){
                    $em->persist($result[2]);
                }    
            }               
        });
        
        
        return array($results_summary,$results);
    }//generateReminders

}

