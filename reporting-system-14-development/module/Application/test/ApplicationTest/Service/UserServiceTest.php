<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

class  UserServiceTest extends PHPUnit_Framework_TestCase{

        
    
    public function atestExportEmail(){
        
        $srv = BootstrapPHPUnit::getService('UserProfileService');
        $user = $srv->getUserByUsername('sysadmin');
        BootstrapPHPUnit::setCurrentUser($user);

	$users = $srv->findUsersBy(array('status'=>'active'));	

	print(sizeof($users));

        $file='./u_e_a.csv';
        $file_h = fopen($file,'w');

        foreach($users as $u){
              fputcsv($file_h,array($u->getMembercode(),$u->getDisplayName(),$u->getEmail()), ',','"');
        }
        fclose($file_h);	
        
     }

     public function testLogin(){
        
        BootstrapPHPUnit::setCurrentUserByName('sabih.nasir');
        $current_user = BootstrapPHPUnit::getService('UserProfileService')->getCurrentUser();

        $service = BootstrapPHPUnit::getService('UserProfileService');
        
        $this->assertNotNull($service);
        
        $userLogin = $service->addLogin($current_user,'local','success');

        $this->assertNotNull($userLogin);

        $id = '9271af62863547c194114af5cabd07e0';

        $userLogin = $service->updateLogout($id);

        $this->assertNotNull($userLogin);
        $this->assertNotNull($userLogin->getLogoutTime());

        $userLogin = $service->addLogin($current_user,'local','success',$service->getUserById(1));

        $this->assertNotNull($userLogin);


     }
}



