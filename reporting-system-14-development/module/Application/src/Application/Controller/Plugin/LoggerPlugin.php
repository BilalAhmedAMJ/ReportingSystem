<?php

namespace Application\Controller\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ReflectionObject;

 
/**
 * Provids transactional data processing helpers to controller
 * Users can either use begin/cmmit/rollback construct or 
 * provideda call backthat will be wrapped inside a transaction
 * 
 */ 
class LoggerPlugin extends AbstractPlugin{
    
    public function info($msg){
        $sm = $this->getController()->getServiceLocator();
        $logger = $sm->get('Logger')->logger();
        $logger->info($msg);
    }
}
