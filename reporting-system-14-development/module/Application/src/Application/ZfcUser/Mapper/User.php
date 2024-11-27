<?php

namespace Application\ZfcUser\Mapper;

//use ZfcUser\Mapper\User as ZfcUserMapper;
//use ZfcBase\Mapper\AbstractDbMapper;
//use ZfcUser\Entity\UserInterface as UserEntityInterface;
//use Zend\Stdlib\Hydrator\HydratorInterface;

class User //extends ZfcUserMapper implements UserInterface
{
 
 
  private $service_locator;
  
  public function __construct($sm){
      
    $this->service_locator=$sm;
        
  }
  
  public function findByUsername($username)
    {
        
        return $this->service_locator->get('UserProfileService')->getUserByUsername($username);
    }
    
  public function findById($user_id)
    {
        
        return $this->service_locator->get('UserProfileService')->getUserById($user_id);
    }

  public function  update(){

  }

}
