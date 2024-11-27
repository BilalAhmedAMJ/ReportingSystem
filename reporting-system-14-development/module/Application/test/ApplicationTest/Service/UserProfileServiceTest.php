<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

use Application\Service\DashboardService;
use Application\View\HighChart\DataTransform;

namespace ApplicationTest\Import;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;


class  UserProfileServiceTest extends PHPUnit_Framework_TestCase{

    
    
    public function testProposedName(){
        
        $full_name='Ch. Zameer Ul-Mulk';
        $userSrv = BootstrapPHPUnit::getService('UserProfileService');
        
        //print_r(array($userSrv->proposeUserName($full_name)));
        $row=array('full_name'=>'Muhammad Amin Tahir Memon');
        
        $username = $userSrv->proposeUserName($row['full_name']);
        $increment=1;
        while($username==null && $increment<100){
            $name=$row['full_name'].''.($increment++);
            print_r("$name \n");
            $username = $userSrv->proposeUserName($name);            
        }
                
        print_r(array($userSrv->proposeUserName('Muhammad Amin Tahir Memon')));
        
    }
    public function atestCanPerform(){
        
        $gs_ito = 'gsITO';
        $fe_ito = 'feITO';
        $gs = 'sabih.nasir';
        $gs_aap = 'gsAAP';
        $fe_aap = 'feAAP';
        
        
        $userSrv = BootstrapPHPUnit::getService('UserProfileService');
        
        $gs_ito = $userSrv->getUserByUsername($gs_ito);
        $fe_ito = $userSrv->getUserByUsername($fe_ito);
        $gs = $userSrv->getUserByUsername($gs);
        $gs_aap = $userSrv->getUserByUsername($gs_aap);
        $fe_aap = $userSrv->getUserByUsername($fe_aap);
        
        BootstrapPHPUnit::setCurrentUser($fe_ito);
        $this->assertTrue($userSrv->canPerform($fe_ito,'password','update'));
        $this->assertTrue($userSrv->canPerform($fe_ito,'password','reset'));

        $this->assertFalse($userSrv->canPerform($gs_ito,'password','update'));
        $this->assertFalse($userSrv->canPerform($gs_ito,'password','reset'));


        BootstrapPHPUnit::setCurrentUser($gs_ito);
        $this->assertTrue($userSrv->canPerform($fe_ito,'password','reset'));
        $this->assertTrue($userSrv->canPerform($fe_ito,'password','reset'));
        $this->assertTrue($userSrv->canPerform($gs_aap,'password','reset'));
        $this->assertTrue($userSrv->canPerform($fe_aap,'password','reset'));

        $this->assertTrue($userSrv->canPerform($fe_ito,'password','update',$fe_ito));
        $this->assertTrue($userSrv->canPerform($fe_ito,'password','update',$fe_ito));
        $this->assertTrue($userSrv->canPerform($gs_aap,'password','update'));
        $this->assertTrue($userSrv->canPerform($gs_aap,'password','update'));

    }
   
}
?>