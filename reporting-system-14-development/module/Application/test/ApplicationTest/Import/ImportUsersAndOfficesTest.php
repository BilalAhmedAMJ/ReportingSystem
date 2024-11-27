<?php

namespace ApplicationTest\Import;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;


class  ImportUsersAndOfficesTest extends PHPUnit_Framework_TestCase{
    
    /**
     * Assumes that temp table is created for users to be imported as follows:
     *  - Export office bearer list from approval system
     *  - match Dept, Branch to the exported list
     *  - create unique users (based on member code) from exported list
     *  - import email information from old system based on validated member code
     *      + for validation match based on code and if name is not a match exclude 
     * 
     */
    private $imported_users_query='
         SELECT DISTINCT a.fldAamilaID,b.branch_id, d.dept_id,
           u.membercode, u.`Name` AS full_name, u.HomePhone, u.CellPhone, u.email_imported
        FROM offbearer_export_dept d 
        JOIN office_bearer_export_all a ON a.OFCDE = d.OFCDE AND d.dept_id>0 
        JOIN offbearer_export_users u ON a.membercode = u.membercode AND email_imported IS NOT NULL
        JOIN offbearer_export_branch b ON b.`halqa` = a.halqa AND b.branch_id>0   
        WHERE b.branch_id in (60,33,35,61,84,34,83)


        ';
/*
        UNION
        
         SELECT DISTINCT a.fldAamilaID,b.branch_id, d.dept_id,
           u.membercode, u.`Name` AS full_name, u.HomePhone, u.CellPhone, u.email_imported
        FROM offbearer_export_dept d 
        JOIN office_bearer_export_all a ON a.OFCDE = d.OFCDE AND d.dept_id>0 
        JOIN offbearer_export_users u ON a.membercode = u.membercode AND email_imported IS NOT NULL
        JOIN offbearer_export_branch b ON b.`halqa` = a.halqa AND b.branch_id>0   
        WHERE b.branch_id!=27
        
 * */        
    
    private function getSourceData($query,$db=array('name'=>'amjc_reports_import','user'=>'import_user','password'=>'password')){
        try {
            $source_db = new \PDO('mysql:host=127.0.0.1;dbname='.$db['name'], $db['user'],$db['password']);
            $result=array();
            if($source_db!=null){
                $result=$source_db->query($query);
                $source_db = null;
                return $result;
            }else{
                throw new \Exception('Unable to connect to DB');
            }
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "\n";
            exit();
        }        
    } 
    
    
    public function atestImportUsers(){
        
        $data=array('term'=>'2014-2016','expires_period'=>'','status'=>'active');

        $officeSrv = BootstrapPHPUnit::getService('OfficeAssignmentService');
        /**
         * @var \Application\Service\UserProfileService
         */ 
        $userSrv =    BootstrapPHPUnit::getService('UserProfileService');
        
        $entityManager = BootstrapPHPUnit::getService('Doctrine\ORM\EntityManager');
        
        $entitySrv = BootstrapPHPUnit::getService('EntityService');
        
        $processed=0;
        $aamila_id=array();
        foreach ($this->getSourceData($this->imported_users_query) as $row) {
            
            $processed++;
            $key='a'.$row['fldAamilaID'];
            if(key_exists($key, $aamila_id)){
                throw new \Exception("Error, processing $key twice");
            }else{
                $aamila_id[$key]='done';
            }
            
            //$entityManager->beginTransaction();

            $branch = $entitySrv->getObject('Branch',$row['branch_id']);
            $data['branch']=$branch;    

            $department = $entitySrv->getObject('Department',$row['dept_id']);
            $data['department']=$department;
            
            //check if office already exists, if so raise error
            $office = $officeSrv->getBranchDepartmentOffice($branch,$department->getDepartmentName());
            if($office){                
                print("WARNING: Duplicate office! ".$office." exists and adding ".implode('|', $row)."\n");
                if($row['dept_id']!=27){
                    continue;//allow multiple VP/Naib Sadr
                }
                
            }            
                                                
            $user=$userSrv->getUserByMembercode($row['membercode']);            
            if(!$user){
                //create new user
                $user = $this->createUser($row,$userSrv);
            }
            $data['user']=$user;
            
            //create new office
            $office = $officeSrv->createOfficeAssignment($data);
            
            $entityManager->persist($user);
            $entityManager->persist($office);
            $entityManager->flush();
            
            //$entityManager->commit();

                        
        }
         
        print("Processed $processed users!\n");       
    }


    private $new_user_data=array(
        array('branch_id'=>'0','dept_id'=>'0','membercode'=>'0','full_name'=>'User Name','HomePhone'=>'xxx-xxx-xxxx','CellPhone'=>'','email_imported'=>'email@example.com')
     );
    
    public function testCreateNewUser(){
        /** @var Application\Service\UserProfileService */
        $userSrv = BootstrapPHPUnit::getService('UserProfileService');
        $entityManager = BootstrapPHPUnit::getService('Doctrine\ORM\EntityManager');        
        $entitySrv = BootstrapPHPUnit::getService('EntityService');
        $officeSrv = BootstrapPHPUnit::getService('OfficeAssignmentService');
        
        foreach ($this->new_user_data as $data) {
            $row=$data;
            $data['username'] = $userSrv->proposeUserName($data['full_name']);
            $branch = $entitySrv->getObject('Branch',$data['branch_id']);
            $data['branch']=$branch;    

            $department = $entitySrv->getObject('Department',$data['dept_id']);
            $data['department']=$department;
            $user = $userSrv->getUserByMembercode($data['membercode']);
            if(!$user){
                $user = $this->createUser($data, $userSrv);    
            }
            $data['user']=$user;
            $data['status']='active';
            $data['term']='2014-2016';
            $data['expires_period']='';
            
            //check if office already exists, if so raise error
            $office = $officeSrv->getBranchDepartmentOffice($branch,$department->getDepartmentName());
            if($office){
                print("SKIPPING: office already exists! ".implode('|', $row)."\n");
                continue;
            }
            
            //create new office
            $office = $officeSrv->createOfficeAssignment($data);
            
            $entityManager->persist($user);
            $entityManager->persist($office);
            $entityManager->flush();
        }
    }
    
    private function createUser($row,$userSrv){

        $data =$row;        
        $data['username'] = $userSrv->proposeUserName($row['full_name']);        
        $data['email'] = $row['email_imported'];        
        // Row already have full_name $data['full_name']);
        $data['member_code'] = $row['membercode'];
        $data['status']='active';
        
        $data['phone_primary']=$row['HomePhone'];
        $data['phone_alternate']=$row['CellPhone'];
        $data['password']='Yvnn9x4U3HWUaOJURTNuQhVjNnZBRkTG';
        $sendEmail=false;
        $user = $userSrv->createUser($data,$sendEmail);
        
        return $user;
        
    }
    
    
    function atestDuplicateUsernames(){
        
        $sql = 'select uhash, count(uhash) duplicates from users group by uhash having count(uhash)>1';
        $processed=0;
        
        $userSrv = BootstrapPHPUnit::getService('UserProfileService');
        
        BootstrapPHPUnit::setCurrentUser($userSrv->getUserByUsername('sabih.nasir'));
        
        print "\nChecking usernames\n";
        foreach ($this->getSourceData($sql,array('name'=>'dbname','user'=>'user','password'=>'password')) as $row) {            
            $processed++;
            //print $row['uhash'].' - '.$row['duplicates'] ."\n";
            $users = $userSrv->findUsersBy(array('uhash'=>$row['uhash']));
            foreach ($users as $user) {
                $user_name = $user->getUsername();
                $valid_user_name = $userSrv->proposeUserName($user->getDisplayName());
                if($user_name!=$valid_user_name){
                    $userSrv->updateUsername($user,array('username'=>$valid_user_name));
                }
                print $user_name.": ".$user->getDisplayName()."  ... ".$user_name.'=>'.$valid_user_name."\n";
            } 
        }
        
    }
    
}
        
