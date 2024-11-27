<?php

namespace ApplicationTest;//Change this namespace for your test

use Application\Controller\Plugin\TransactionPlugin;
// use Zend\Loader\AutoloaderFactory;
// use Zend\Mvc\Service\ServiceManagerConfig;
// use Zend\ServiceManager\ServiceManager;
// use Zend\Stdlib\ArrayUtils;
// use RuntimeException;

use Zend\EventManager\StaticEventManager;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\DispatchableInterface as Dispatchable;
use Doctrine\DBAL\Driver\PDOSqlite\Driver;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Application as Cli;


use JMS\Serializer\SerializationContext;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;



error_reporting(E_ALL | E_STRICT);

$app_root=__DIR__.'/../../../';
define("APP_ROOT", $app_root);
define('ROOT_PATH', $app_root);

chdir(APP_ROOT);


class BootstrapPHPUnit
{
    public static $application;
    
    static public  function init(){
        
        if (static::$application) {
            return $this::application;
        }
        
        //now setup application
                
        //now configure app
        $appConfig = static::getApplicationConfig();
        static::$application  = Application::init($appConfig);

        $events = static::$application->getEventManager();
        $events->detach(static::$application->getServiceManager()->get('SendResponseListener'));
        
        return static::$application->getConfig();
    }
    
    
    public static function migrateTestDB(){
        $inputDef = new \Symfony\Component\Console\Input\InputDefinition();
        
                
        $inputDef->addArgument(new InputArgument('version'));
        $inputDef->addOptions(array(new InputOption('no-interaction'),new InputOption('db-configuration'),new InputOption('configuration')));
        
        
        $input = new \Symfony\Component\Console\Input\StringInput('--no-interaction migrations:migrate', $inputDef);
        $output = new \Symfony\Component\Console\Output\BufferedOutput();

        $cli = new Cli();
        $helperSet = new HelperSet();
        $helperSet->set(new ConnectionHelper());
        $cli->setHelperSet($helperSet);
        
        $migrat = new  \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand();
        $migrat->setApplication($cli);

        $migrat->execute($input,$output);        
        
        write(STDOUT,$output->fetch());
    }

    public static function createTestDB(){
        //first create basic DB structure
        //setup test DB fixture                               
        $tmpfname = tempnam(sys_get_temp_dir(), 'TESTDB');
        define("TESTDB_FILE", $tmpfname);        
        
        include APP_ROOT.'/data/DoctrineORMModule/Migrations/db_create_basic.php';
        
        register_shutdown_function(
            function(){
               unlink(TESTDB_FILE);
            }
        );
        
    }
    
    public static function getApplicationConfig(){
        $appConfig = include 'config/application.config.php';
//        $appConfig['module_listener_options']['config_glob_paths'][]= 'module/*/test/test.*.config.php';
        return $appConfig;
    }
    
    public static function getService($serviceName){
        return static::$application->getServiceManager()->get($serviceName);
    }
    
    public static function getEntityManager(){
        return static::$application->getServiceManager()->get('Doctrine\ORM\EntityManager');
    }
    
    public static function beginTransaction(){
        return static::$application->getServiceManager()->get('Doctrine\ORM\EntityManager')->getConnection()->beginTransaction();
    }

    public static function commit(){
        return static::$application->getServiceManager()->get('Doctrine\ORM\EntityManager')->getConnection()->commit();
    }
    
    public static function setCurrentUser($user){
         
        $roles = static::getService('OfficeAssignmentService')->getOfficeRoles($user);
        $user->addDynamicRoles($roles); 
         
        $auth_srv = static::getService('zfcuser_auth_service');
        $storage=static::getService('ZfcUser\Authentication\Storage\Db');
        $storage->write($user);
        $auth_srv->setStorage($storage);
        
        return $user;         
    }
    
    public static  function setCurrentUserByName($user_name){
        $user = BootstrapPHPUnit::getService('UserProfileService')->getUserByUsername($user_name);
        print_r($user->getDisplayName());
        BootstrapPHPUnit::setCurrentUser($user);
    }
    
    public static function toJson($data){
       $context=SerializationContext::create()->enableMaxDepthChecks();
       //always allow default group so we can include fields that do not define any group
       $context->setGroups(array(GroupsExclusionStrategy::DEFAULT_GROUP));
       $serializer = BootstrapPHPUnit::getService('jms_serializer.serializer');
       
        //return ($serializer->serialize($data, 'json',$context));
        return ($serializer->serialize($data, 'yml',$context));              
        
    }
}

class ConnectionHelper implements \Symfony\Component\Console\Helper\HelperInterface{
    private $conn;
    private $helperSet;
    public function setHelperSet(HelperSet $helperSet = null){ $this->helperSet=$helperSet;}
    public function getHelperSet(){ return $this->helperSet;}
    public function getName(){ return 'connection';}
    public function getConnection(){
         $params = array('path'=>TESTDB_FILE);
         $driver = new \Doctrine\DBAL\Driver\PDOSqlite\Driver($params);
         return new \Doctrine\DBAL\Connection($params,$driver); 
         }
}

BootstrapPHPUnit::init();

#define applicaiton
#$GLOBALS['APPLICATION']=BootstrapPHPUnit::$application;    
#define service manager
#$GLOBALS['ServiceManager']=BootstrapPHPUnit::$application->getServiceManager();
   