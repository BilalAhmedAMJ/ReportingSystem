<?php
namespace Application\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class BjyDBProfilerFactory implements FactoryInterface{
    
    private $serviceLocator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       

            $dbParams = array(
                'database'  => 'amj_reports_dev2',
                'username'  => 'root',
                'password'  => 'password',
                'hostname'  => 'localhost'
            );
            $adapter = new \BjyProfiler\Db\Adapter\ProfilingAdapter(array(
                'driver'    => 'pdo',
                'dsn'       => 'pgsql:dbname='.$dbParams['database'].';host='.$dbParams['hostname'],
                'database'  => $dbParams['database'],
                'username'  => $dbParams['username'],
                'password'  => $dbParams['password'],
                'hostname'  => $dbParams['hostname'],
                ));
 
                $adapter->setProfiler(new \BjyProfiler\Db\Profiler\Profiler);
                $adapter->injectProfilingStatementPrototype();
                return $adapter;
    }

}
