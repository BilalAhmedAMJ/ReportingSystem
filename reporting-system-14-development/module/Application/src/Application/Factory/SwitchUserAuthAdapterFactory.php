<?php

namespace Application\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class SwitchUserAuthAdapterFactory implements FactoryInterface{
    
    private $serviceLocator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){
               
            $this->serviceLocator = $serviceLocator;  

            $adapter = new \Application\ZfcUser\Authentication\SwitchUserAuthAdapter($serviceLocator);

            return $adapter;
    }

}
