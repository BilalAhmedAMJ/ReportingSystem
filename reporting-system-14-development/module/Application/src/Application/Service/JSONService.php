<?php

namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;


class JSONService implements FactoryInterface{
    
    /**
     * @ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * @SerializationContext
     */
    private $context;
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       
        $this->serviceLocator = $serviceLocator;  

        //we allow exclusion by MAx depth and by groups        
        $this->context=SerializationContext::create()->enableMaxDepthChecks();
        //always allow default group so we can include fields that do not define any group
        $this->context->setGroups(array(GroupsExclusionStrategy::DEFAULT_GROUP));

        return $this;
    }

    public function serialize($data,array $group=null){
        
        $serializer = $this->serviceLocator->get('jms_serializer.serializer');
       
       if($group){
           //do not mess with default context
           $context = clone $this->context;
           //always add default group
           if( ! in_array(GroupsExclusionStrategy::DEFAULT_GROUP, $group)){
               $group[]=GroupsExclusionStrategy::DEFAULT_GROUP;
           }
           $context->setGroups($group);
       }
       
        $json = ($serializer->serialize($data, 'json',$this->context));
        
        return json_encode(json_decode($json),JSON_PRETTY_PRINT);              
        
    }
    
}