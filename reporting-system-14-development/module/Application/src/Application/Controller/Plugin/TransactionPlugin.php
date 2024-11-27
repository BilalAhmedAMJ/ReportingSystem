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
class TransactionPlugin extends AbstractPlugin{
    
    /**
     * Start a transaction, will require commit to save data
     */
    public function begin(){
        $sm = $this->getController()->getServiceLocator();
        $em = $sm->get('Doctrine\ORM\EntityManager');
        $em->getConnection()->beginTransaction();
    }

    /**
     * Will complere a trnsaction by committing values 
     * By default it will flush entity manager before commit
     * however, passing a false value will do commit without flushing
     * May throw error if no transation is open when called
     * 
     * 
     * */
    public function commit($flush=true){
        $sm = $this->getController()->getServiceLocator();
        $em = $sm->get('Doctrine\ORM\EntityManager');
        if($flush){
            $em->flush();
        }
        $em->getConnection()->commit();
    }
    
    /**
     * Rolls back currently open transaction.
     * May throw error if no transation is open when called
     * 
     */
    public function rollback(){
        $sm = $this->getController()->getServiceLocator();
        $em = $sm->get('Doctrine\ORM\EntityManager');
        $em->getConnection()->rollback();
    }
    
    private $lastError;//keeps reference to last error encountered by this plugin.
    
    /**
     * Assumes first argument to be a callback and rest of the args are arguments to callback.
     * Will wrap a call to given callback inside a transaction (begin/commit/rollback construct).
     * It will always flush entitimanager before commit.
     * @return A true value is returned upon successful completion of a transaction. 
     * A false value is returned if 1st arg is not a callback or if there is an error raised during call, 
     * in later case getLastError() can be used to retrieve exception.
     */
    public function wrap(/*$callBack,$args[]*/){
        $args=func_get_args();        
        $obj=array_shift($args);
        $methodName=array_shift($args);
        if(is_object($obj)&&is_string($methodName)){
            $reflectObj=new ReflectionObject($obj);
            try{//try to find given method form the obj
                $method=$reflectObj->getMethod($methodName);
                if($method){
                    try{
                        $this->begin();
                        $method->invokeArgs($obj,$args);  
                        $this->commit();  
                        return true;      
                    }catch(Exception $e){ //problem in calling method
                        $this->rollback();
                        $this->lastError=$e;//an error in transaction data processing return exception to user
                        return false;
                    }   
                } //we found method to call   
            }catch(Exception $e){
                $this->lastError=$e;
                return false;//no method was found
            }
        }else{ //not valid obj or method name is not string
                return false;//no call back provided
        }
    }
    
    /**
     * return last error encountered by 
     */
    public function getLastError(){
        return $this->lastError;
    }
}