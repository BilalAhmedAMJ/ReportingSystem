<?php

namespace ApplicationTest\Utils;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

use Rhumsaa\Uuid\Uuid;

use Application\Entity\User;

class  UUIDTest extends PHPUnit_Framework_TestCase{

  public function testUuidFileName(){
    $tokenString = (hash('crc32', 'Form_RequestforApprovalofAppointmentsofOfficeBearer.pdf'));        
    $tokenString .= Uuid::uuid4()->getHex();
    echo "[$tokenString]";      
  }
  
  public function testUserClass(){
    
      $service = BootstrapPHPUnit::getService('UserProfileService');
      $user = $service->getUserByUsername('gsIBN');
      
      if($user){
          echo ">>>>".(($user->getUsername()))."<<<<";
          echo ">>>>".(get_class($user))."<<<<"; 
          echo ">>>>".($user instanceof User)."<<<<\n\n"; 
      }
      
      //{'report_messag':{'link_type':'help','from_office':'report','to_office':'parent','report_status':'verified','title':'Help Requested by ','template':''}}
      print_r(json_decode('{"report_messag":{"link_type":"help","from_office":"report","to_office":"parent","report_status":"verified","title":"Help Requested by ","template":""}}',true));
      print_r(json_last_error());

      echo "\n\n>>>>END<<<<"; 
  }
    
}