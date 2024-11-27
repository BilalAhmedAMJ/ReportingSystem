<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Navigation\AbstractContainer;


/**
 * Adds more functions to ease usage of naviations and menus 
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class MessagesHelper extends AbstractHelper
{

    protected $authService;
    protected $serviceLocator;
    
    protected $currentUser;
    protected $messageService;
    
    /**
     * @param Authorize $authorizeService
     */
    public function __construct($authService,$serviceLocator)
    {
        $this->authService = $authService;
        $this->currentUser = $authService->getIdentity();
        $this->serviceLocator=$serviceLocator;
        $this->messageService = $serviceLocator->get('MessagesService');
    }
    
    /**
     * Returns un-read messages for the current user that are not feedback/help type link
     *
     * Default notification is unread messages 
     */
    public function notifications(){
        $this->init();
        return $this->notification;
    }
    
    public function unread(){
        $this->init();
        return $this->unread;
    }

    /**
     * Returns un-read messages for the current user
     *
     * Default notification is unread messages 
     */
    public function feedback(){
        $this->init();                
        return $this->feedback;
        
    }
    
    private $user_messages;
    private $unread;
    private $notification;
    private $feedback;

    protected function init(){
        
        if(!$this->user_messages){
            $this->user_messages = $this->messageService->getUserMessages($this->currentUser);
            $this->feedback=array();
            $this->notification=array();
            $this->unread=array();
            
            foreach ($this->user_messages as $user_message) {
                
                if($user_message->isUnread()){
                    $message = $user_message->getMessage();
                    $this->unread[] = $message;
                    $link_type=$message->getReportMessageType();            
                    //print_r("[".$message->getReportMessageType()."]\n");
                    if(empty($link_type)){
                        $this->notification[]=$user_message->getMessage();
                    }else{
                        $this->feedback[]=$user_message->getMessage();
                    }

                }
            }
        }
    }

    public function outstandingCount(){
        $this->init();
        
        return 0 + count($this->feedback) + count($this->notification);
    }

    public function inbox(){
        
        $messages = $this->messageService->getUserMessages($this->currentUser);
                    
        return $messages;    
    }

}