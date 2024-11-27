<?php

if (!defined('DEFAULT_PASSWORD_RESET_EXPIRY')  )  define('DEFAULT_PASSWORD_RESET_EXPIRY', '+2 hours');
if (!defined('DEFAULT_USER_TOKEN_EXPIRY')  )      define('DEFAULT_USER_TOKEN_EXPIRY', '+2 days');
if (!defined('SESSION_TTL')  )                    define('SESSION_TTL','-3 minutes');
if ( !defined('ROOT_PATH')   )                    define('ROOT_PATH', dirname(__DIR__));

if ( !defined('ROOT_KEY')   ){
	define('ROOT_KEY', '5d02c1748185aa1907b8a4eb72a5407e39876bb1f556ed8e478aa2d1627477e8c020d9fed8194d13ef72cd371e001e41f8252dd90909cbe576872b5b07abd57145136618119dc1383a40f7b3f8be7faa8412972940e3a65a01253f617d3edfaca25d82ec116068d19ea7be9bbde381065e75daeaf47b6cc4b8336fe166a0e6a27f26651e96b7a7b1616f795ab5f15ea042e530b6bdde40228a64d395006762bbfdaf2f04f0ceeb7f0d88709bf07fcd9093848b2f8924e491059b41d77ab426c0d2e24aa6e7c9ac33a748371481bf90465a40c80f9e312e609035ed0fca78f4550f71a3a43b0f474f928b28da42f983c35b55abe1522b31a5fac1e0eccdaf11c4c3f1a2d530f3ee86aa6b089eebe2229d8ed0fb856ac8f8434bf64d39e14e5cce53537e00e32da95edb8c86c280a95bbbde7bb40eda747ffef8e0eb4b2ea360dbbb72b3ae751ca7e60cef8927436c4aa9d4e68fbe69e1213e826f07b74bc7ccfbd8f505835ec785a5627e93c14e4bc115a53191118cbf3d9b6327e7f88fbdf66eceb8246497fad663703af587fa9c30183507960b77384fa4aa8a01d1b53b19bef1fb1f9e1a02ec95cd6b8f692f302d5026a89a19a980176ff8340fcc1234ff20f71727569b4a18fa301d10ee8ee6fc9132a7b72f7fcfeb3eee52aa11dc96ab65f8b52c430de291a45968e9e58988951e98b691fe5f50ac26da060b17139c4e96389c44797c09d70db562b093e931b099e3149039c170c4756364e06f96f549aba1d37c1e934e6aa95280b094d6eb477533ad50d312db533eae2b9650d3a252495f2d218b3a8d1279ec378927648e59b473263956430b20bd3fcc958a42700d630dfe55cdbbcdbb886bcabcf8670a0b4ee3d0a865c83587fe2847653e5950305189110dbb47b5e43d34023f3ff93823bd4326a607e4de09d673219af901c612b3ad3e755b143f6c122cbe00f510505926b95103f04269c513c06366a75db2ccd605e595408c50a9e1f69a6c8fed9eeab3e5c94e1e8c1c8d7a62a6db844a969ec080e265aea76baeb23989a9442a3bfa12a2b26c2485791370e0f267e88b62947b13706f626ed4c70dc067d6f74b544e460f4230b1a3f74e386ec795c45f0df7946dd9dbc80c12e3832ff547bc800fb6e59e7ad11acf33ebe1ddd8dff2aa4286de225c012d8e3eda8cb27c7b2116682431460bbc85493eed8b6384921805aafcd08514de96be6a41849afd59d7d6d7e1226ac357dc7e1c2af48539deb8e22b8fb0c1b09de0130d28ccb06a6c695e8b8010db60b5e2e1138100dcc652efcceaaed578adcd1b2ea02d39f78efb7d4c881dc2b02c00ebd6d9c732318028ce27103b3fbdde68ffca62b2e756a5047cb65cb6ee921fb517dc0dbe35ff3865c8b0d56cf4c12da95c51be6e68174d922b5c56196caa20c513428c87f6adb9d3487561281d1b36c27d2d548a8ec90f8ee19e9b5df24af288ffd8a049191e2bf16e690a31b1694acacd04cee8451b853f259bf07a425662a11877791b0d375609393c32bdcc19c46c504bfdf1c5baa2d1e6d9a07b31471bb7ae179d3fb2f1991dd63bb77a9ec70da22b7b88c2d07f977fe61829734180357c746e7c1290da84977b95de6974ec34c38b9ae7c42bbf51c9d83860f2cd91454d8246754e02e588c96e03596b1893ca3a9d0e843fc2f4f74309e009a9f1f88cc12e553061b0d8ee043b4f831b374a88b4932501074b1e3ad9eb9206672a1dd9689e423f5208a167fb5d282aba11564b328be604995965f46a28d08867b0502624a8698b1e2cdf9168a9470aea1449f7d11a3f57a55b8bd3c6e536f4e04b9ae04c51e141955a964c57611a740da7513d249122fa11d83a1856ef72195a23b11ac92b13f09ee696fab0813529ab54a4fd1bab9c5c2a1073a01006f9ad0934c281440d6c58ca68899e8a101fa0a6e472f412af25ed187ee0bf78c0e51d3b0424b37bbadec43941a466134f7e3d1778ee82b363946b18ce05d3e187140cb7af5949a78b8ddee743a8c2ecf7353023703d908eb13d33ae6e2a4240d807449585e915e65a8058672458f15b3e7bb8af95c3ff04b4b80e8383ee5457891c27f532efb3122d3d15b1262741c814d558d31d2e83dfec1f3fec0d12996e06636524a290d10fbe22c6087b908cb04f788354f7573cb768d949be0b35299f7e2614568ddc433328a421c9200dbe1cbd19f7a9bca69b135fab839346c814d132febee2717dc17b5eb04354cee975549c4694dd4eaeb557aabfad9fa0eb62cc19c7f9ee1897575c30b83ba0df90714c806b90a1406236ed5533ffba5cd039fe80c0aa1e4f97b308ac30d84decf55c9d351997f700ef68f452c5885d8ac0af2f77b946a70a05dcdbf21cb4fc2e8d5b86f82a93ef073a8516409e292af290ef309c8c7d89338463cc923d75da49ca58551351317ca4dae4f7821c14fcea6ffe313dba47978f54899988ebf770ce27a8b06fbf52ec38e47072186557757883cec83abd6c2b0c4a3f721d59a1193d0a43721a4664499a5d20065b7d8c702663f3d0888ff4553343a3cbf47a34837a5252e7572efdff4003137995f23152d2fe6b092432d5281f8cda606d2965b606c190c5253d0f730a8955891f11aaa182c58d9c1df2a46d6284ea094ecc1386a068b66f642bb63822e67c2f97ef5c08cd1a64ed49b046dc76e9b29b4a97f4a45dbe1b16161d6d2c507bc071f62422e881f03a138498e80105ab484169d1aece419c3897fe31b850b050fab1b577bc5a1c23f4663aa71b16598a15fcf94bfdc38c24aed929dfa6d4235eae7b0960135990be6bc64b6d96611a919e25b9c129bbf23f139f5d437cf8a1517d9ddff7f76864ee863a7139a4571d0732b1672321d5358d50f9d3825e5189cb02273566daf2f0a765efc79df16d033272c924123c278d828a0cfe505b1166a5ebffcac7b550acd65e0c713f5be879eb3ea12e1b12902413d43925df32495502aec189fc92ee66d0ef2439f47e39a2d60861c15aae25e09c4482b01356d6b8');	
}

use desktopd\SHA3\Sponge as SHA3;

require ROOT_PATH.'/vendor/desktopd/PHP-SHA3-Streamable/namespaced/desktopd/SHA3/Sponge.php';

if(!function_exists('sha3')){
	
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
