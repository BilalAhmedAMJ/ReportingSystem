<?php


namespace Application\Doctrine;


use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

use DoctrineEncrypt\Encryptors\EncryptorInterface;

class DoctrineEncryptAdapter implements EncryptorInterface,FactoryInterface{

    private $cipher;
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       
        $this->serviceLocator = $serviceLocator;  
        // $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, 'ctr');
        // $iv =  mcrypt_create_iv($ivSize, MCRYPT_DEV_URANDOM);

        $iv = $serviceLocator->get('EncryptUtil')->getIV();
        
        $cipher = \Zend\Crypt\BlockCipher::factory('mcrypt',
                                                array(
                                                'key'=>sha3(`/var/local/amj/hex_generator`),
                                                'algo'=>MCRYPT_RIJNDAEL_256,'mode'=>'ctr'
                                                ,'salt'=>$iv
                                              )
                                        );
         ///
        $cipher->setKey(sha3(`/var/local/amj/hex_generator`) ); 
        $cipher->setKeyIteration(0);
 
        $this->cipher=$cipher;

        return $this;        
    }
 
    /**
     * Must accept data and return encrypted data 
     */
    public function encrypt($data){
        if(empty($data))
            return $data;

        return $this->cipher->encrypt($data);
    }

    /**
     * Must accept data and return decrypted data 
     */
    public function decrypt($data){
        if(empty($data))
            return $data;

        return $this->cipher->decrypt($data);
    }
    
}
