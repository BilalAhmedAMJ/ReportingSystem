<?php

namespace ApplicationTest\Import;

use PHPUnit_Framework_TestCase;

use ApplicationTest\BootstrapPHPUnit;


class  FixUsersTest extends PHPUnit_Framework_TestCase{
    
    private $userSrv;
    private $reportSrv;
    private $entitySrv;
    private $entityFactory;
    private $entityManager;
    private $currentUser;

    
    
  public function setUp(){
        
        $this->entityManager = BootstrapPHPUnit::getService('Doctrine\ORM\EntityManager');
        
        $this->entitySrv = BootstrapPHPUnit::getService('EntityService');
        
        $this->offsSrv = BootstrapPHPUnit::getService('OfficeAssignmentService');   
            
        $this->userSrv = BootstrapPHPUnit::getService('UserProfileService');
        
        $this->currentUser = $this->userSrv->getUserByUsername('sysadmin');
        
    }
    
 
    
    public function atestFixUsers(){
        $data_file = (ROOT_DIR.'data/missingcodes.csv');
        $row=0;
        $fixed_codes=array();
        if (($handle = fopen($data_file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if(!empty($data[1]) && $data[1] != 'MemberCode'){
                    echo "$data[0] : $data[1]\n";
                    $fixed_codes[$data[0]] = $data[1];
                    $row++;
                }                
            }
            fclose($handle);
        }
                
        foreach ($fixed_codes as $id  => $mcode) {
            echo "updating $id ... ";
            $user = $this->entityManager->find('Application\Entity\User',$id);    
            $user->setMemberCode($mcode);
            $this->entityManager->persist($user);
            echo "Done\n";
        }
        $this->entityManager->flush();
        
    }
 
    public function testGetUsersToFix(){
 
        $users =   $this->entityManager
                    ->createQuery("select user from Application\Entity\User user where user.status = 'active'")
                    //->setMaxResults(10)
                    ->getResult()
                    ;
        
        print_r("Start\n");
        foreach ($users as $user) {
            if($user->getMembercode()!='') continue;
            
            $offices = $this->offsSrv->getActiveOffices($user);
            $off_names =''; 
            array_map(function($off) use (&$off_names){ $off_names .= $off->getTitle(true);return 1;}, $offices);
            printf('"%s","%s","%s","%s"%s',$user->getId(),$user->getDisplayName(),$user->getEmail(),$off_names,"\n");            
            //printf('"%s","%s","%s","%s"',$user->getId(),$user->getFullName(),$user->getEmail(),'$offices[0]->__toString()');            
        }
        print_r('done');
    }       
    
    
}    