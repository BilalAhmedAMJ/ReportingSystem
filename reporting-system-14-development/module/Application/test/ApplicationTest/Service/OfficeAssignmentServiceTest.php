<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

class  OfficeAssignmentServiceTest extends PHPUnit_Framework_TestCase{

    private $factory;
    
    public function setUp(){
        $this->factory = BootstrapPHPUnit::getService('entityFactory');   
    }
    
    
    public function testUserLevel(){
        $user_id =  2221; //test as GS for Immarat Toronot

        $user_srv = BootstrapPHPUnit::getService('UserProfileService');
        
        $user  = $user_srv->getUserById($user_id);
        
        $office_srv = BootstrapPHPUnit::getService('OfficeAssignmentService');
        
        print_r($office_srv->getUserLevel($user));
        
    }
    
    public function testDepartmentList(){
        
        //@var \Application\Service\OfficeAssignmentService
        $office_srv = BootstrapPHPUnit::getService('OfficeAssignmentService');
        
       $user_id='2644';        

       $dept_ids=array();
       $active_offices=$office_srv->getBranchesWithOffices($user_id);
       foreach ($active_offices as $key => $value) {           
           $dept_ids=array_merge($dept_ids,$value);           
       }
       
       //print_r($active_offices);
        // array_map(
                // function($a) use(&$dept_ids){
                         // //$dept_ids=array_merge($dept_ids,$a);
                         // array_push($dept_ids,$a) ;
                // }, array_values());
//         
        
        
        //print_r($dept_ids);
        //print_r($active_offices);
        //exit;
    }
    public function atestGetBranchesWithOffices(){
        
        //@var \Application\Service\OfficeAssignmentService
        $service = BootstrapPHPUnit::getService('OfficeAssignmentService');

        $user_id =  5017; //test as GS for Immarat Toronot

        $branches_with_offices=$service->getBranchesWithOffices($user_id);
 
        //print_r(array('branches_with_offices',$branches_with_offices));

        $this->assertArrayHasKey(77,$branches_with_offices);//imarat toronto is in list
        $this->assertArrayHasKey(18,$branches_with_offices);//scarborough is in list

        //check for two departments for two diffrent branches
        //1=>additional mal, 17=>umur kharijia, 5=>GS
        foreach(array(1,17,5) as $dept){
            $this->assertContains(1,$branches_with_offices[77]);//imarat toronto is in list        
            $this->assertContains(1,$branches_with_offices[18]);//imarat toronto is in list
        }
                        
        #print_r($branches_with_offices);
        
    }    
    
    public function atestGetOfficeRoles(){
        $service = BootstrapPHPUnit::getService('OfficeAssignmentService');

        $user_id =  5017; //test as GS for Immarat Toronot

        $office_roles=$service->getOfficeRoles($user_id);
        
        //print_r($office_roles);
        
        $expected=array(2,16);
        $test=$this;
        array_map(function($value) use(&$expected,$test){$test->assertContains($value->getId(),$expected);}, $office_roles);
        
    }
    
    public function atestReminderRecipientsList(){

        $userSrv = BootstrapPHPUnit::getService('UserProfileService');
        $sender = $userSrv->getUserByUsername('gsIBN');
        
        $officeSrv  = BootstrapPHPUnit::getService('OfficeAssignmentService');
        //$msgSrv  = BootstrapPHPUnit::getService('MessagesService');
        
        //get query for all approved offices that user can access adn then filter based on user criteria
        $filters=array('period'=>'Aug-2014');
        //$filters['send_to_rule']='not_completed';
        //$queryBuilder = $officeSrv->createOfficeAssignmentDataSource($sender,$list_type,$status)->getData();
        $results = $officeSrv->getReminderReciepientList($sender,$filters);
        $this->assertEquals(117,count($results));


        $filters['office']=5;//GS Only
        $results = $officeSrv->getReminderReciepientList($sender,$filters);
        $this->assertEquals(5,count($results));


        $filters['office']=4;//Mal Only
        $results = $officeSrv->getReminderReciepientList($sender,$filters);
        $this->assertEquals(6,count($results));

        unset($filters['office']);//reset office
        $filters['level_rule']='imarat';
        $results = $officeSrv->getReminderReciepientList($sender,$filters);
        $this->assertEquals(22,count($results));

        unset($filters['office']);//reset office
        $filters['level_rule']='local';
        $sender = $userSrv->getUserByUsername('pIBN');
        $results = $officeSrv->getReminderReciepientList($sender,$filters);
        $this->assertEquals(95,count($results));

        $filters['office']=5;//reset office
        $filters['level_rule']='local';
        $sender = $userSrv->getUserByUsername('pIBN');
        $results = $officeSrv->getReminderReciepientList($sender,$filters);
        $this->assertEquals(4,count($results));

        //all national offices
        unset($filters['office']);
        $filters['level_rule']='markaz';
        $sender = $userSrv->getUserByUsername('sabih.nasir');
        $results = $officeSrv->getReminderReciepientList($sender,$filters);
        $this->assertEquals(21,count($results));

        //all imarat offices
        unset($filters['office']);
        $filters['level_rule']='imarat';
        $sender = $userSrv->getUserByUsername('sabih.nasir');
        $results = $officeSrv->getReminderReciepientList($sender,$filters);
        $this->assertEquals(139,count($results));

        //local offices
        unset($filters['office']);
        $filters['level_rule']='local';
        $sender = $userSrv->getUserByUsername('sabih.nasir');
        $results = $officeSrv->getReminderReciepientList($sender,$filters);
        $this->assertEquals(475,count($results));
        
        //local GS
        $filters['office']=5;
        $filters['level_rule']='local';
        $sender = $userSrv->getUserByUsername('sabih.nasir');
        $results = $officeSrv->getReminderReciepientList($sender,$filters);
        $this->assertEquals(37,count($results));

        
        //Imaraat GS
        $filters['office']=5;
        $filters['level_rule']='imarat';
        $sender = $userSrv->getUserByUsername('sabih.nasir');
        $results = $officeSrv->getReminderReciepientList($sender,$filters);
        $this->assertEquals(7,count($results));


        //send_to_rule                  
        unset($filters['office']);//reset office
        $filters['level_rule']='local';
        $filters['period']='Aug-2014';
        $filters['send_to_rule']='not_completed';
        $sender = $userSrv->getUserByUsername('gsIBN');
        $results = $officeSrv->getReminderReciepientList($sender,$filters);
        $this->assertEquals(69,count($results));

        //send_to_rule                  
        unset($filters['office']);//reset office
        $filters['level_rule']='markaz';
        $filters['period']='Jul-2014';
        $filters['send_to_rule']='not_completed';
        $sender = $userSrv->getUserByUsername('sabih.nasir');
        $results = $officeSrv->getReminderReciepientList($sender,$filters);
        $this->assertEquals(12,count($results));


        //send_to_rule                  
        unset($filters['office']);//reset office
        $filters['level_rule']='all';
        $filters['period']='Jul-2014';
        $filters['send_to_rule']='not_completed';
        $sender = $userSrv->getUserByUsername('sabih.nasir');
        $results = $officeSrv->getReminderReciepientList($sender,$filters);
        $this->assertEquals(382,count($results));



    }
}


