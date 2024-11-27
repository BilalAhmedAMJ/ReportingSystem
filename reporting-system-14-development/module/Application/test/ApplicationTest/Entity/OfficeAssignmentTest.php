<?php

namespace ApplicationTest\Entity;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;


class  OfficeAssignmentTest extends PHPUnit_Framework_TestCase{
        
    private $factory;
    
    public function setUp(){
        $this->factory = BootstrapPHPUnit::getService('entityFactory');   
    }

    public function testCreation(){
        $oa = $this->factory->getOfficeAssignment();
        $this->assertNotNull($oa);
        
        $searchSrv=BootstrapPHPUnit::getService('EntityService');
        $period = $searchSrv->getObject('Period','Jul-2014');
        $user   =  $searchSrv->getObject('User','5');
        $dept   =  $searchSrv->getObject('Department','8');
        $branch   =  $searchSrv->getObject('Branch','78');
        
        $oa->setPeriodFrom($period);        
        $oa->setPeriodTo($period);        
        $oa->setUser($user);        
        $oa->setDepartment($dept);        
        $oa->setBranch($branch);
        $oa->setStatus('pending');
		
		#print_r(BootstrapPHPUnit::getService('EntityService')->runNativeQuery('select @@autocommit;'));
		
		//var_dump(BootstrapPHPUnit::getService('Doctrine\ORM\EntityManager')->getConnection());
		
        #$trans=BootstrapPHPUnit::getTransactionPlugin();
        #$trans->begin();
        BootstrapPHPUnit::getService('EntityService')->saveObject($oa);
        #$trans->commit();
		
		#print_r(BootstrapPHPUnit::getService('EntityService')->runNativeQuery(''));
 		#$this->assertNotNull();
    }    
}

