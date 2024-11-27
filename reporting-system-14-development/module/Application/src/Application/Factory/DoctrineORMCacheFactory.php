<?php

namespace Application\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class DoctrineORMCacheFactory implements FactoryInterface{
    
    private $serviceLocator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       

        $this->serviceLocator = $serviceLocator;  
        $cache=null;
        
        if(getenv('APPLICATION_ENV')=='Production'){//memcache for production env
            
            $cache = new \Doctrine\Common\Cache\MemcacheCache();
            $memcache = new \Memcache();
            $memcache->connect('localhost', 11211);
            $cache->setMemcache($memcache);
            
        }else{//file cache for all other env including development
            
            $config = $serviceLocator->get('Config');    
            $dirname = $config['application']['cache_dir'];             
            $cache = new \Doctrine\Common\Cache\FilesystemCache($dirname);

        }
        return $cache;
    }

}
