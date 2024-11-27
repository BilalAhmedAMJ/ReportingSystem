<?php

namespace ApplicationTest\Utils;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

class  UserFixTest extends PHPUnit_Framework_TestCase{
      
     public function testLogin(){
        
        BootstrapPHPUnit::setCurrentUserByName('sysadmin');
        $current_user = BootstrapPHPUnit::getService('UserProfileService')->getCurrentUser();

        $service = BootstrapPHPUnit::getService('UserProfileService');
        
        $this->assertNotNull($service);
        
        $emails = array(
        );
        
        foreach ($emails as $code => $email) {
         $user = $service->getUserByMembercode($code);
         $service->updateEmail($user,$email);
        }
         
     }
}




