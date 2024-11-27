<?php

namespace ApplicationTest\Entity;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;


class  MessageTest extends PHPUnit_Framework_TestCase{
        
    private $factory;
    
    public function setUp(){
        $this->factory = BootstrapPHPUnit::getService('entityFactory');   
    }

    public function testListMessage(){
        
        /**
         * @var \Application\Entity\Message
         */
        $msg = BootstrapPHPUnit::getService('EntityService')->getObject('Message',1);
        $userMessages = $msg->getUserMessages();
        $str='';
        foreach ($userMessages as $userMessage) {
            $str = $str .'| ('. $userMessage->getUser()->getDisplayName()
                   . ' , '.$userMessage->getDateRead(). ' , '.$userMessage->getDateDeleted().')' ;           
        }
        print_r(array('Sent as: '=>$msg->getSentAs(),'Sender: '=>$msg->getSender()->getDisplayName(),
                       'Message: '=>$msg->getSubject().' | '.$msg->getMessageType() . ' | '.$msg->getStatus(),
                       'Sent to: '=>count($userMessages). ' | ' . $str
                        ));
    }
}