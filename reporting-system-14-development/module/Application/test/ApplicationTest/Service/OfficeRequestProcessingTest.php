<?php


namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

    /**
     * Processes an office assignment approval
     * 
     *   Office Assignment request approval combinations to test
     *   
     *   +-----------+-------------------+-----------+---------------------+     
     *   | User      | User Status       | Office    | Office Status
     *   +-----------+-------------------+-----------+---------------------+     
     *   | New       | N/A               | None      | N/A
     *   | New       | N/A               | Exists    | Locked/Disabled
     *   | New       | N/A               | Exists    | Active
     * 
     *   | Exists    | Locked/Disabled   | None      | N/A
     *   | Exists    | Locked/Disabled   | Exists    | Locked/Disabled
     *   | Exists    | Locked/Disabled   | Exists    | Active
     * 
     *   | Exists    | Active            | None      | N/A
     *   | Exists    | Active            | Exists    | Locked/Disabled
     *   | Exists    | Active            | Exists    | Active
     *   +-----------+-------------------+-----------+---------------------+     
     *   
     */

class  OfficeRequestProcessingTest extends PHPUnit_Framework_TestCase{

    private $factory;

    /**
     * @var \Application\Service\OfficeAssignmentService
     */
    private $service;
    
    /**
     * @var \Application\Service\UserProfileService
     */
    private $userSrvice;


    private $clean_up = array('req'=>array(),'office'=>array(),'user'=>array()); 
    

    
    public function setUp(){
        $this->factory = BootstrapPHPUnit::getService('entityFactory');
        $this->service = BootstrapPHPUnit::getService('OfficeAssignmentService');
        $this->userSrvice = BootstrapPHPUnit::getService('UserProfileService');
           
        BootstrapPHPUnit::setCurrentUser($this->userSrvice->getUserByUsername('sabih.nasir'));
    }
    
    public function tearDown(){
        
        print 'delete from office_assignment_requests where id in ('.implode(',', $this->clean_up['req']).");\n";
        print 'delete from office_assignments  where id in ('.implode(',', $this->clean_up['office']).");\n";
        print 'delete from users where id in ('.implode(',', $this->clean_up['user']).");\n";

    }
    
    /**
     * create a new office assignemnt request based on passed parameters
     */
    private function createOfficeAssignmentRequest($test,$num,$branch=66,$dept=11){
        
        $data=array();
        
        
        $data['department']=$dept;        
        $data['branch']=$branch;
        $data['term']='2013-2016';

        $data['expires_period']='';
        $data['request_id']='';
        $data['alt_phone']='';
        
        $data['full_name']='Office Bearer '.$test;
        $data['email']='email_'.$num.'@address.com';
        
        $data['username']='office.bearer.'.$num;        
        
        $data['member_code']=$num.'999999';
        
        $data['primary_phone']='416-'.$num.'23-1234';        
        $data['request_reason']='Request for Test:'.$test;
                
        $req = $this->service->updateOfficeAssignmentRequest($data);
        
        return array($data,$req);        
    }

    /**      
     *   Test 1       
     *   +-----------+-------------------+-----------+---------------------+     
     *   | User      | User Status       | Office    | Office Status
     *   +-----------+-------------------+-----------+---------------------+     
     *   | New       | N/A               | None      | N/A

     *  - every thing is brand new, no setup needed
     *  - remove office and user once done
    */
    public function atestNewNone(){
            
        $result = $this->createOfficeAssignmentRequest('One', '1',66,27);

        $this->clean_up['req'][]=$result[1]->getId();    
        
        $result[0]['request_id']=$result[1]->getId();

        $result[0]['approval_status']='approved'; 
        $result[0]['processor_comments']='Approved Test One';       
            
        $req = $this->service->processOfficeAssignmentRequest($result[0]);
        
        $office = $req->getOfficeAssignment();
        $user = $office->getUser();
        
        $this->clean_up['office'][]=$office->getId();
        $this->clean_up['user'][]=$user->getId();
        
    }


    /**
     * 
     *   Test 2 
     *   +-----------+-------------------+-----------+---------------------+     
     *   | User      | User Status       | Office    | Office Status
     *   +-----------+-------------------+-----------+---------------------+     
     *   | New       | N/A               | Exists    | Locked/Disabled
     * 
     * - pick office that that exists and lock / disable it (1)
     * - create new user
     * - clean up, reset status for office in (1)
     * - delete user
     * 
     */
    public function atestNewDisabled(){
            
        $result = $this->createOfficeAssignmentRequest('Two', '2',66,5);

        $this->clean_up['req'][]=$result[1]->getId();    

        $result[0]['request_id']=$result[1]->getId();

        $result[0]['approval_status']='approved'; 
        $result[0]['processor_comments']='Approved Test Two';       
            
        $req = $this->service->processOfficeAssignmentRequest($result[0]);
        
        $office = $req->getOfficeAssignment();
        $user = $office->getUser();
        
        $this->clean_up['office'][]=$office->getId();
        $this->clean_up['user'][]=$user->getId();
        
        //   update office_assignments set status='disabled', period_to='Jun-2016' where id=2498;
    
    }


    /**
     * 
     *
     *  Test 3
     *   +-----------+-------------------+-----------+---------------------+     
     *   | User      | User Status       | Office    | Office Status
     *   +-----------+-------------------+-----------+---------------------+     
     *   | New       | N/A               | Exists    | Active
     * 
     *  - pick office that that exists  (2)
     *  - create new user
     *  - clean up, reset status for office in (2)
     *  - delete user
     * 
     */      
    public function atestNewActive(){
            
        $result = $this->createOfficeAssignmentRequest('Three', '3',66,2);

        $this->clean_up['req'][]=$result[1]->getId();    

        $result[0]['request_id']=$result[1]->getId();

        $result[0]['approval_status']='approved'; 
        $result[0]['processor_comments']='Approved Test Three';       
            
        $req = $this->service->processOfficeAssignmentRequest($result[0]);
        
        $office = $req->getOfficeAssignment();
        $user = $office->getUser();
        
        $this->clean_up['office'][]=$office->getId();
        $this->clean_up['user'][]=$user->getId();

        //   update office_assignments set status='active', period_to='Jun-2016' where id=2401;
    
    }


    /**
     * 
     *
     *  Test 4
     *   +-----------+-------------------+-----------+---------------------+     
     *   | User      | User Status       | Office    | Office Status
     *   +-----------+-------------------+-----------+---------------------+     
     *   | Exists    | inactive          | None      | N/A
     * 
     *  - Create a user, change status to disabled and persist
     *  - create new office and persist
     *  - clean up, delete office and delete user
     * 
     */      
    public function testExistNone(){
            
        $user = $this->userSrvice->createUser(array(
                'username'=>'office.bearer.4',
                'email' => 'email_4@address.com',
                'full_name' => 'Office Bearer Four',
                'member_code'=>'4999999',
                'status' => 'inactive',
                'phone_primary'=>'416-423-1234',
                'phone_alternate'=>''
        ),false);    
        
         $result = $this->createOfficeAssignmentRequest('Four', '4',66,9);
 
        $this->clean_up['req'][]=$result[1]->getId();    

        $result[0]['request_id']=$result[1]->getId();

        $result[0]['approval_status']='approved'; 
        $result[0]['processor_comments']='Approved Test Four';  
        $result[0]['user']=array('member_code'=>$result[0]['member_code'],'username'=>$result[0]['username']);     
                    
        $req = $this->service->processOfficeAssignmentRequest($result[0]);
        
        $office = $req->getOfficeAssignment();
        $user = $office->getUser();
        
        $this->clean_up['office'][]=$office->getId();
        $this->clean_up['user'][]=$user->getId();

    
    }
    
//     *   | Exists    | inactive   | Exists    | Disabled
//     *   | Exists    | inactive   | Exists    | Active

     
}