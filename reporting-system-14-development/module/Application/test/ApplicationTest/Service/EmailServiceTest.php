<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

class  EmailServiceTest extends PHPUnit_Framework_TestCase{

    public function atestSendTemplatedEmail(){
        $userSrv = BootstrapPHPUnit::getService('UserProfileService');
        $user = $userSrv->getUserByUsername('sysadmin');
        $token = $userSrv->createUserUpdateToken($user,'confirm');        
        $vars=array('user'=>$user,'token'=>$token);

        $emailSrv = BootstrapPHPUnit::getService('EmailService');
        
       $emailSrv->sendTemplatedEmail('PHP Unit email test',$user->getEmail(),'new_account',$vars);
        
    }

    public function testEmailSend(){

        $email_srv = BootstrapPHPUnit::getService('EmailService');
        
        $email_srv->setTransport($this->awsTransport());
        
        $to='haroonzc@hotmail.com';
        $msg="This is Test email<br/>\nFrom <b>ZF2</b> application\nThis is to test email";
        $subject='Email from zf2';
        $email_srv->sendHTMLEmail($to,$subject,$msg,$msg,null,'reports_aws@ahmadiyya.ca');
                
    }


}
