<?php

namespace Application\Controller\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ReflectionObject;

 
/**
 * 
 */ 
class CurrentUserPlugin extends AbstractPlugin{
    
    
    public function auth(){
        return $this->getController()->getServiceLocator()->get('zfcuser_auth_service');        
    }    
    
    public function __invoke(){
        
        return $this->auth()->getIdentity();
    }

}
