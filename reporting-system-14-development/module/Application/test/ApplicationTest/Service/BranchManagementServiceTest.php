<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

class  BranchManagementServiceTest extends PHPUnit_Framework_TestCase{

    public function testAddOfficeAssignment(){
        
        $service = BootstrapPHPUnit::getService('BranchManagementService');
        #$trans=BootstrapPHPUnit::beginTransaction();
        $oa=$service->addOfficeAssignment('muhasibjamaat','IBN','MB','Jul-2014');
        #$trans=BootstrapPHPUnit::commit();

        #print_r($oa->getId());
        
        $user = BootstrapPHPUnit::getService('UserProfileService')->getUserByUsername('muhasibjamaat');
        
        $this->assertTrue(count($user->getOfficeAssignments())>0);
    }    
    
}
