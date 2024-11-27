<?php

namespace Application\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class CognitoAuthAdapterFactory implements FactoryInterface{
    
    private $serviceLocator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){
               
            $this->serviceLocator = $serviceLocator;  

            $adapter = new \Application\ZfcUser\Authentication\CognitoAuthAdapter($serviceLocator);

            return $adapter;
    }

}
