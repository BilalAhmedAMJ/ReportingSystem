<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

class DoctrineObjHydrator extends PHPUnit_Framework_TestCase{

    private  $factory;
    
    public function setUp(){
        $this->factory = BootstrapPHPUnit::getService('entityFactory');   
        
    }
    public function testDoctrineObjHydrator(){
        
        
        $data=array(
            'id'=>1,
            'department_name'=>'Department',
            'department_code'=>'CD',
            'status'=>'active',
            'rules'=>'Department TJ Rules',
            'guide_lines'=>'Department Guidelines',       
        );
        $dept=$this->factory->getDepartment();
        
        //$this->assertNotNull($objHyd,"Unable to create DoctrineObjHydrator");
        //$objHyd=BootstrapPHPUnit::getService('DoctrineObjHydrator');
        // $dept=$objHyd->hydrate($data,$dept);
  
        $hyd=BootstrapPHPUnit::getService('ArrayObjHydrator');
        $dept=$hyd->hydrate($data,$dept);
  
       $this->assertEquals($data['id'],$dept->getId(),"Dept id not set");
       $this->assertEquals($data['department_name'],$dept->getDepartmentName(),"Dept id not set");
    }

}