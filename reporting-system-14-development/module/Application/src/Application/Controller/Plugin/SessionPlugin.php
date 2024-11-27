<?php

namespace Application\Controller\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ReflectionObject;

 
/**
 * 
 */ 
class SessionPlugin extends AbstractPlugin{
    
    
    public function session(){
        return $this->getController()->getServiceLocator()->get('zfcuser_auth_service')->getStorage();        
    }    
    
    public function __invoke(){
        
        return $this->session();
    }

}
