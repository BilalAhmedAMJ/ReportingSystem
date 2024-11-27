<?php

namespace Application\Util;

use Zend\Crypt\BlockCipher;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;


class EncryptUtil implements FactoryInterface{
    
    
    private $decrypt_adapter;
    private $encrypt_adapter;
    private $initial_vector_def;
    
    
    public function __construct(){
        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, 'ctr');
        $this->initial_vector_def = mcrypt_create_iv($ivSize, MCRYPT_DEV_URANDOM);
        
    }

    public function getIV(){
        return $this->initial_vector_def;
    }
    public function createService(ServiceLocatorInterface $serviceLocator){       

        $this->serviceLocator = $serviceLocator;  
    
        #$this->decrypt_adapter = $this->createFirstAdapter();
        #$this->decrypt_adapter = new PlainTextSymmetricImpl();
        
         //Encrpt Cipher         
        $this->encrypt_adapter = $this->createDefaultAdapter(); 
        $this->decrypt_adapter = $this->encrypt_adapter;
        //$this->encrypt_adapter = new PlainTextSymmetricImpl();

        return $this;        
    }

    public function createPlainTextAdapter(){
        return new PlainTextSymmetricImpl();
    }
    
    public function createDefaultAdapter(){
         
      $cipher = \Zend\Crypt\BlockCipher::factory('mcrypt',
                                            array(
                                            'key'=>sha3(`/var/local/amj/hex_generator`),
                                            'algo'=>MCRYPT_RIJNDAEL_256,'mode'=>'ctr'
                                            ,'salt'=>$this->initial_vector_def
                                          )
                                    );
        ///
        $cipher->setKey(sha3(`/var/local/amj/hex_generator`) ); 
        $cipher->setKeyIteration(0);
        return $cipher;        
        
    }

    private function createFirstAdapter(){
         
         $cipher = \Zend\Crypt\BlockCipher::factory('mcrypt',
                                            array(
                                            'key'=>sha3(`/var/local/amj/hex_generator`),
                                            'algo'=>MCRYPT_RIJNDAEL_256,'mode'=>MCRYPT_MODE_CBC
                                            ,'salt'=>$this->initial_vector_def
                                          )
                                    );
         $cipher->setKey(sha3(`/var/local/amj/hex_generator`)); 
         $cipher->setKeyIteration(500);
        return $cipher;        
    }
    
    public function getEncryptAdapter(){
        return $this->encrypt_adapter;
    }
    

    public function getDecryptAdapter(){
        return $this->decrypt_adapter;
    }

    public function encrypt($data){
        if( (!empty($data)) && is_string($data)){
            return $this->encrypt_adapter->encrypt($data);
        }
        return $data;
    }

    public function decrypt($data){
        if( (!empty($data)) && is_string($data)){
            return $this->decrypt_adapter->decrypt($data);
        }
        return $data;
    }

}
