<?php

return array(
    'doctrine' => array(
        'driver' => array(
            // overriding zfc-user-doctrine-orm's config
            'application_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' =>'array',
                'paths' => __DIR__ .'/../../src/Application/Entity',
            ),//application_entity

			
            'orm_default' => array(
                'drivers' => array(
                    'Application\Entity' => 'application_entity',
                    'ZfcUser\Entity' =>null,## We don't need zfcuser entity that is mapped, so let's remove it
                ),
            ),//driver->orm_default
            
        ),//driver
        'encryption' => array (
            'orm_default' => array (

                //The adapter can be a reference to another service locator key, a Fully qualified class name
                //or an anonymous function. However, it must implement \Zend\Crypt\BlockCipher or
                //DoctrineEncrypt\Encryptors\EncryptorInterface
                'adapter' => 'DoctrineEncryptAdapter',

                //The reader will read encryption configuration information off of the entities
                //You probably don't have to change this
                'reader' => 'Doctrine\Common\Annotations\AnnotationReader',
            )
        ),//encryption
       
        'eventmanager' => array(
            'orm_default' => array(
                'subscribers' => array(
                    'doctrine.encryption.orm_default'
                ),
            ),
        ),//event register encryption

        'configuration' => array(
            'orm_default' => array(
                'metadata_cache'    => 'orm_cache',
                'query_cache'       => 'orm_cache',
                'result_cache'      => 'orm_cache',
                'hydration_cache'   => 'orm_cache',
              )
        ),//config (add cache)
        'cache' => array(
            'memcache' => array(
                'instance' => 'doctrine.cache.orm_cache',
            ),
         )//cache
    ),//doctrine
);