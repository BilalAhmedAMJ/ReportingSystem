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
class CryptPlugin extends AbstractPlugin{
    
    protected $adapter;

/**
     * Must accept data and return encrypted data 
     */
    public function encrypt($data){
        $this->adapter=$this->getController()->getServiceLocator()->get('DoctrineEncryptAdapter');
        return $this->adapter->encrypt($data);
    }

    /**
     * Must accept data and return decrypted data 
     */
    public function decrypt($data){
        $this->adapter=$this->getController()->getServiceLocator()->get('DoctrineEncryptAdapter');
        return $this->adapter->decrypt($data);
    }

}
