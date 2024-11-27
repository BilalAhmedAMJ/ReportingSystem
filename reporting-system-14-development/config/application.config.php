<?php

if (!defined('DEFAULT_PASSWORD_RESET_EXPIRY')  )  define('DEFAULT_PASSWORD_RESET_EXPIRY', '+2 hours');
if (!defined('DEFAULT_USER_TOKEN_EXPIRY')  )      define('DEFAULT_USER_TOKEN_EXPIRY', '+2 days');
if (!defined('SESSION_TTL')  )                    define('SESSION_TTL','+120 minutes');
if ( !defined('ROOT_PATH')   )                    define('ROOT_PATH', dirname(__DIR__));


use desktopd\SHA3\Sponge as SHA3;

if(!function_exists('sha3')){

    require ROOT_PATH.'/vendor/desktopd/PHP-SHA3-Streamable/namespaced/desktopd/SHA3/Sponge.php';

    function sha3($data){
        $sponge = SHA3::init (SHA3::SHA3_512);
        $sponge->absorb ($data);
        // fixed size (512 bits) output
        return bin2hex ($sponge->squeeze ());
    }
}

return array(
    // This should be an array of module namespaces used in the application.
    'modules' => array(
    
        //'ZendDeveloperTools',
        //'BjyProfiler',
        
        'DoctrineModule',
        'DoctrineORMModule',
        'BjyAuthorize',
        'ZfcBase',
        'ZfcUser',
        'ZfcUserDoctrineORM',
        
        'ZfcDatagrid',
        
        'JMSSerializerModule',
        
        'DoctrineEncryptModule',
        
        'Application'
    ),

    // These are various options for the listeners attached to the ModuleManager
    'module_listener_options' => array(
        // This should be an array of paths in which modules reside.
        // If a string key is provided, the listener will consider that a module
        // namespace, the value of that key the specific path to that module's
        // Module class.
        'module_paths' => array(
            './module',
            './vendor',
        ),

        // An array of paths from which to glob configuration files after
        // modules are loaded. These effectively override configuration
        // provided by modules themselves. Paths may use GLOB_BRACE notation.
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php',
            'module/*/config/autoload/{,*.}config.php',
        ),

        'config_cache_enabled'     => true,
        'module_map_cache_enabled' => true,
        'cache_dir'                => 'data/cache/',

        // Whether or not to enable a configuration cache.
        // If enabled, the merged configuration will be cached and used in
        // subsequent requests.
        //'config_cache_enabled' => $booleanValue,

        // The key used to create the configuration cache file name.
        //'config_cache_key' => $stringKey,

        // Whether or not to enable a module class map cache.
        // If enabled, creates a module class map cache which will be used
        // by in future requests, to reduce the autoloading process.
        #'module_map_cache_enabled' => $booleanValue,
        'module_map_cache_enabled' => true,

        // The key used to create the class map cache file name.
        //'module_map_cache_key' => $stringKey,

        // The path in which to cache merged configuration.
        //'cache_dir' => $stringPath,

        // Whether or not to enable modules dependency checking.
        // Enabled by default, prevents usage of modules that depend on other modules
        // that weren't loaded.
        // 'check_dependencies' => true,
    ),

    // Used to create an own service manager. May contain one or more child arrays.
    //'service_listener_options' => array(
    //     array(
    //         'service_manager' => $stringServiceManagerName,
    //         'config_key'      => $stringConfigKey,
    //         'interface'       => $stringOptionalInterface,
    //         'method'          => $stringRequiredMethodName,
    //     ),
    // )

   // Initial configuration with which to seed the ServiceManager.
   // Should be compatible with Zend\ServiceManager\Config.
   // 'service_manager' => array(),
	"timezone" => array(
		"zone" => "America/Toronto",
		"success" => date_default_timezone_set("America/Toronto")
		
	),
	
);
