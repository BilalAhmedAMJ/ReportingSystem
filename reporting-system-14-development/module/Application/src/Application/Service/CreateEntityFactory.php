<?php


namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\AbstractFactoryInterface;

use ReflectionMethod;
use ReflectionClass;
    
    class CreateEntityFactory implements  FactoryInterface{
    
    const NAMESPACE_PREFIX='Application\Entity\\';
    const ENTITY_PREFIX='entity.';

    private $serviceLocator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       
        $this->serviceLocator = $serviceLocator;  
        $this->entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');

        return $this;
    }


    public function canCreateServiceWithName(ServiceLocatorInterface $locator, $name, $requestedName)
    {
        $enitytName=str_replace($this::ENTITY_PREFIX, '', $requestedName);
        if (substr_compare($requestedName, $this::ENTITY_PREFIX, 0,7)==0 &&
            class_exists($this::NAMESPACE_PREFIX.$enitytName)){
            return true;
        }
        
        return false;
    }
    
    public function __call($getName,$parameters){
        $entityName = str_replace('get', '', $getName);
        $obj =  $this->createServiceWithName($this->serviceLocator, $entityName, $entityName);

       //if($entityName == 'OfficeAssignment')
       // var_dump($obj); 

        
        return $obj;
    }
    
    
     public function createServiceWithName(ServiceLocatorInterface $locator, $name, $requestedName)
     {
        try{

            $entityClassName = $this::NAMESPACE_PREFIX.str_replace($this::ENTITY_PREFIX, '', $requestedName);
            
            $entityObject = new $entityClassName();
             
            return $entityObject;
            
        }catch(Exception $e){
            print_r($e);
            throw $e;
        }
     }

        

}
