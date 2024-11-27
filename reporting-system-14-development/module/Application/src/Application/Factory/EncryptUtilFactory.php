<?php

namespace Application\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class EncryptUtilFactory implements FactoryInterface{
    
    private $serviceLocator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       

            $enc = new \Application\Util\EncryptUtil();
            return $enc->createService($serviceLocator);
    }

}
