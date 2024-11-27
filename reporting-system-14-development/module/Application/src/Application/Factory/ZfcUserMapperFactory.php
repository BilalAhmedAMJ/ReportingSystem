<?php

namespace Application\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ZfcUserMapperFactory implements FactoryInterface{
    
    private $serviceLocator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){
               
            $this->serviceLocator = $serviceLocator;  

            $options = $serviceLocator->get('zfcuser_module_options');
            $mapper = new \Application\ZfcUser\Mapper\User($serviceLocator);
            // $mapper->setDbAdapter($sm->get('zfcuser_zend_db_adapter'));
            // $entityClass = $options->getUserEntityClass();
            // $mapper->setEntityPrototype(new $entityClass);
            // $mapper->setHydrator(new Mapper\UserHydrator());
            // $mapper->setTableName($options->getTableName());
            return $mapper;
    }

}
