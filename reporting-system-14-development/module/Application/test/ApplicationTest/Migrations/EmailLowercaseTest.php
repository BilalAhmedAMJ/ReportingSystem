<?php

namespace ApplicationTest\Import;

use PHPUnit_Framework_TestCase;

use ApplicationTest\BootstrapPHPUnit;


class  EmailLowercaseTest extends PHPUnit_Framework_TestCase{
    
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
    
  public function testEmailLowercase(){
       $qb = $this->entityManager->createQueryBuilder();
       $qb->select('u')        
            ->from('\Application\Entity\User','u')
           ;
        
       $users = $qb->getQuery()->getResult();
       
       foreach ($users as $u) {
           if($u->getEhash() != sha3($u->getEmail())){
               print("update users set ehash ='".sha3($u->getEmail())."' where id=".$u->getId().";\n");
           }
       }
   }

}
