<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

class  ConfigServiceDepartmentTest extends PHPUnit_Framework_TestCase{

    public function testDepartmentList(){
        
        $configService = BootstrapPHPUnit::getService('ConfigService');
        
        $this->assertNotNull($configService,'Test number of departments');
        $this->assertCount(27,$configService->listDepartments(),'Test number of departments');
    }    
    
}
