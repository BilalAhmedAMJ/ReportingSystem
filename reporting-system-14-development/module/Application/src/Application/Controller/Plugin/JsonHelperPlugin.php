<?php

namespace Application\Controller\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

use Zend\Http\Headers;
use Zend\Http\Response\Stream;


use JMS\Serializer\SerializationContext;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;

 
/**
 * Provids transactional data processing helpers to controller
 * Users can either use begin/cmmit/rollback construct or 
 * provideda call backthat will be wrapped inside a transaction
 * 
 */ 
class JsonHelperPlugin extends AbstractPlugin{
    
    private $context;
    
    public function __construct(){
        //we allow exclusion by MAx depth and by groups        
        $this->context=SerializationContext::create()->enableMaxDepthChecks();
        //always allow default group so we can include fields that do not define any group
        $this->context->setGroups(array(GroupsExclusionStrategy::DEFAULT_GROUP));
    }

    public function serialize($data,array $group=null){
        
        $serializer = $this->getController()->getServiceLocator()->get('jms_serializer.serializer');
       
       if($group){
           //do not mess with default context
           $context = clone $this->context;
           //always add default group
           if( ! in_array(GroupsExclusionStrategy::DEFAULT_GROUP, $group)){
               $group[]=GroupsExclusionStrategy::DEFAULT_GROUP;
           }
           $context->setGroups($group);
       }
       
        return ($serializer->serialize($data, 'json',$this->context));              
        
    }

    public function send($data,aray $group=null){
        
        $json =  $this->serialize($data,$group);

        $response = new Stream();
        $response->setStatusCode(200);
        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Type' => 'application/json',
            'Content-Length' => strlen($json)
        ));
        $response->setHeaders($headers);

		$stream = fopen('php://memory','r+');
		fwrite($stream, $json);
		rewind($stream);

        $response->setStream($stream);
        $response->setStreamName('data.json');


        return $response;
    }

    public function uri(){

        $uri = $this->getController()->getRequest()->getUri();         
        
        //requesteed a json response
        return (substr($uri->getPath(),-5)==="/json");
    }

/*
    public function __invoke($data){
      $serializer = $this->getController()->getServiceLocator()->get('jms_serializer.serializer');
      echo ($serializer->serialize($data, 'json'));              
      exit;
    }
 * 
 */
}
