<?php
$_bjyConfig = array(

'zfcuser' => array(
        // telling ZfcUser to use our own class
        'user_entity_class'       => 'Application\Entity\User',
        // telling ZfcUserDoctrineORM to skip the entities it defines
        'enable_default_entities' => false,
        
        'allowed_login_states' => array(1),
        'enable_user_state'=>true,
        /**
         * Modes for authentication identity match
         *
         * Specify the allowable identity modes, in the order they should be
         * checked by the Authentication plugin.
         *
         * Default value: array containing 'email'
         * Accepted values: array containing one or more of: email, username
         */
        'auth_identity_fields' => array( 'username' ),
    
        //redirect to this page after login, default home page
        'login_redirect_route'=>'home/members',
        
        //use_redirect_parameter_if_present, ther is a 
        //"redirect" parameter in GET or POST redirect to that page after login 
        'use_redirect_parameter_if_present' => true,
        
        /**
         * Logout Redirect Route
         *
         * Upon logging out the user will be redirected to the enterd route
         *
         * Default value: 'zfcuser/login'
         * Accepted values: A valid route name within your application
         */
        'logout_redirect_route' => 'auth/login',
        
        
        /**     
         * Authentication Adapters
         *
         * Specify the adapters that will be used to try and authenticate the user
         *
         * Default value: array containing 'ZfcUser\Authentication\Adapter\Db' with priority 100
         * Accepted values: array containing services that implement 'ZfcUser\Authentication\Adapter\ChainableAdapter'
         */
        'auth_adapters' => array(  90 => 'SwitchUserAuthAdapter',
                                   95 => 'CognitoAuthAdapter' ,
                                  100 => 'ZfcUser\Authentication\Adapter\Db'
                                ),
)
/*
'session_config' => array(
    
    'config_class' =>'Zend\Session\Config\StandardConfig',   
    // Set the session and cookie expires to 30 minutes
    'cache_expire' => 14400,
    'cookie_lifetime' => 14400,
    'remember_me_seconds'=>14400,
    'use_cookies' => true,
    'gc_maxlifetime' => 14400,
        
    ),
 */
);


// if (!Zend\Console\Console::isConsole()) {
    $_bjyConfig['bjyauthorize'] = array(

        // set the 'guest' role as default (must be defined in a role provider)
        // 'default_role' => 'guest',
    
        /* this module uses a meta-role that inherits from any roles that should
         * be applied to the active user. the identity provider tells us which
         * roles the "identity role" should inherit from.
         *
         * for ZfcUser, this will be your default identity provider
         */
        //'identity_provider' => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',
         
         //Not using zfcuser, override Id provider 
         //'identity_provider' => 'SimpleAuthIdentityProviderFactory',
    
        /* If you only have a default role and an authenticated role, you can
         * use the 'AuthenticationIdentityProvider' to allow/restrict access
         * with the guards based on the state 'logged in' and 'not logged in'.
         */
           'default_role'       => 'guest',         // not authenticated
           'authenticated_role' => 'user',          // authenticated
           'identity_provider'  => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',
    
            // Template name for the unauthorized strategy
            'template'              => 'error/403',
    
    
        /* role providers simply provide a list of roles that should be inserted
         * into the Zend\Acl instance. the module comes with two providers, one
         * to specify roles in a config file and one to load roles using a
         * Zend\Db adapter.
         */
        'role_providers' => array(
    
            /*
            'BjyAuthorize\Provider\Role\Config' => array(
                    'guest' => array(),
                ),
            */
            /* here, 'guest' and 'user are defined as top-level roles, with
             * 'admin' inheriting from user
             */
            'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' => array(
                    'object_manager'    => 'doctrine.entitymanager.orm_default',
                    'role_entity_class' => 'Application\Entity\Role',
            ),
    
    /*
     *         // this will load roles from the user_role table in a database
            // format: user_role(role_id(varchar), parent(varchar))
            'BjyAuthorize\Provider\Role\ZendDb' => array(
                'table'             => 'user_role',
                'role_id_field'     => 'roleId',
                'parent_role_field' => 'parent_id',
            ),
    */
            // this will load roles from the 'BjyAuthorize\Provider\Role\Doctrine'
            // service
            // 'BjyAuthorize\Provider\Role\Doctrine' => array(),
        ),
    
        // resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => array(
            // 'BjyAuthorize\Provider\Resource\Config' => array(
            //     'pants' => array(),
            // ),
    
            'BjyAuthorize\Provider\Resource\Config' => array(
                //'Collections\Controller\CollectionsController' => array('admin','index'),
                'report' => array(),
                'config' => array(),
                'document' => array(),
                'email-send' => array(''),
                'member-list' => array(''),
                'member-manage' => array(''),
                #'config-report' => array(),
                #'config' => array(),
            ),
        ),
    
        /* rules can be specified here with the format:
         * array(roles (array), resource, [privilege (array|string), assertion])
         * assertions will be loaded using the service manager and must implement
         * Zend\Acl\Assertion\AssertionInterface.
         * *if you use assertions, define them using the service manager!*
         */
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    // allow guests and users (and admins, through inheritance)
                    // the "wear" privilege on the resource "pants"
                    // array(array('guest', 'user'), 'pants', 'wear')
                    //array(array('sys-admin'), 'Collections\Controller\CollectionsController', 'index'),
                    
                    #array(array('sys-admin'),'config',array('branch','user-role','department','department-json')),
                    #array(array('sys-admin','admin'),'config-report',array('report')),
                    #array(array('sys-admin','admin'),'config-report',array('report')),
                ),
    
                // Don't mix allow/deny rules if you are using role inheritance.
                // There are some weird bugs.
                'deny' => array(
                    // ...
                     // array(array('admin', 'guest'), 'collections', 'add')
                ),
            ),
        ),
    
        /* Currently, only controller and route guards exist
         *
         * Consider enabling either the controller or the route guard depending on your needs.
         */
        'guards' => array(
            /* If this guard is specified here (i.e. it is enabled), it will block
             * access to all controllers and actions unless they are specified here.
             * You may omit the 'action' index to allow access to the entire controller
             */
            'BjyAuthorize\Guard\Controller' => array(
                
                //array('controller' => 'Collections\Controller\CollectionsController', 'roles' => array('sys-admin', 'guest')),
    
                // You can also specify an array of actions or an array of controllers (or both)
                // allow "guest" and "admin" to access actions "list" and "manage" on these "index",
                // "static" and "console" controllers
                // array(
                //     'controller' => array('index', 'static', 'console'),
                //     'action' => array('list', 'manage'),
                //     'roles' => array('guest', 'admin')
                // ),
    
                //no-login is needed for console 
                array('controller' => array('admin-cli','import'), 'roles' => array('sys-admin') ),  
    
                //Prohibit every one except sys-admin to use this invlaid route
                array('controller' => 'zfcuser', 'roles' => array('sys-admin') ),  
                
                //allow users to login by opening auth controlelr to all            
                array('controller' => 'auth', 'roles' => array('guest','user') ),              
                array('controller' => 'auth','action'=>array('switch-user'), 'roles' => array('sys-admin') ),  
    
                //TODO once auth is working and we have a template force login by removing this page and making it defualt redurect
                // Below is the default index action used by the ZendSkeletonApplication
                //array('controller' => 'home','action'=>array('index','ajax','html'), 'roles' => array('guest')),
                array('controller' => 'home','action'=>array('index','html'), 'roles' => array('guest')),
                
               //default login page action needs user to be logged in 
                array('controller' => 'home','action'=>array('help','index'), 'roles' => array('user')),
                array('controller' => 'home','action'=>array('members','email'), 'roles' => array('office-bearer')),
                
               //TEST action to make sure authorization is working
                #array('controller' => 'home','action'=>'stuff', 'roles' => array('sys-admin')),
    
                
               //application controllers
                array('controller' => 'report','action'=>array('markaz-monthly-report','markaz-monthly-report-status', 'monthly-report-status','monthly-report','decrypt-answers'), 'roles' => array('national-general-secretary','national-amir')),
                array('controller' => 'report','action'=>array('all-department-summary', 'all-department-summary-process','summary','summary-report-data','summary-repor-questions','update-summary-repor','summary-list'), 'roles' => array('national-secretary','national-general-secretary','national-amir')),
                array('controller' => 'report','action'=>array('gs-report-process', 'gs-report'), 'roles' => array('national-secretary','national-general-secretary','national-amir', 'national-secretary','amarat-general-secretary','local-amir')),
                array('controller' => 'report','action'=>array('analysis'), 'roles' => array('national-secretary','president','general-secretary')),
                array('controller' => 'report','action'=>array('tarbiyat','tarbiyat-report','tarbiyat-report-details','tarbiyat-report-bymembers'), 'roles' => array('tarbiyat','president','general-secretary')),
                array('controller' => 'report','action'=>array('analysis'), 'roles' => array('national-secretary','president','general-secretary')),
                array('controller' => 'report','action'=>array('list','report','create','answers-data','answers-save','report-config','report-config-departments','attachment','history'), 'roles' => array('office-bearer')),
               //Messages are viewable to all
               //compose is restricted roles  
                array('controller' => 'message','action'=>array('index','inbox','read','draft','sent','open','attachment','bulk-action'), 'roles' => array('office-bearer')),
                array('controller' => 'message','action'=>array('reminder','compose'), 'roles' => array('national-secretary','national-amir','amarat-secretary','local-amir','president','general-secretary','amarat-general-secretary','national-general-secretary')),
                array('controller' => 'message','action'=>array('systemreminder'), 'roles' => array()),
    
                array('controller' => 'member',array(), 'roles' => array('office-bearer')),
                
                array('controller' => 'document','action'=>array('edit'), 'roles' => array('sys-admin','national-general-secretary','admin')),
                array('controller' => 'document','action'=>array('upload'), 'roles' => array('sys-admin','national-general-secretary','upload-documents')),
                array('controller' => 'document','action'=>array('index','list','download'), 'roles' => array('office-bearer','upload-documents')),
                
                //array('controller' => 'document','action'=>array('upload'), 'roles' => array('admin','national-secretary')),
                
                #readonly config items open to all logged in users
                array('controller' => 'config','action'=>array('branch-code','user-branches','user-departments','config-value','periods','department-json','config-value-for-user'), 'roles' => array('office-bearer','admin','sys-admin','upload-documents')),
                
                #installation level config items managable by admin
                array('controller' => 'config','action'=>array('index','branch','user-role','department','report-questions','update-enc-field'), 'roles' => array('national-general-secretary','admin','sys-admin')),
                
                array('controller' => 'question-setup','action'=>array('index','view','empty','export'), 'roles' => array('national-general-secretary','admin','sys-admin')),
                
                #system level config items managable only by sys-admin
                #array('controller' => 'config',array(), 'roles' => array('sys-admin','sys-admin')),
                
                array('controller' => 'user-profile','action'=>array('index','confirm','validate-user-name','update','settings'), 'roles' => array('user')),
                
                array('controller' => 'user-profile','action'=>array('search'), 'roles' => array('sys-admin')),
                
                array('controller' => 'office-assignment', 'action'=>array('list'),'roles'=>array('office-bearer')),
                array('controller' => 'office-assignment', 'action'=>array('list-edit','requests','request','update'),'roles'=>array('admin','general-secretary','president')),
                array('controller' => 'office-assignment', 'action'=>array('process'),'roles'=>array('admin','amarat-general-secretary','local-amir','national-general-secretary','national-amir')),
                
    
#                array('controller' => 'election', 'action'=>array('export','delete'), 'roles' => array('sys-admin','admin','national-general-secretary')),
#                array('controller' => 'election', 'action'=>array('index','result','submit','print','list','save','create','mlist'),'roles' => array('sys-admin','admin','election-data-entry')),
                
                array('controller' => 'admin-cli', 'action'=>array('index','import-member',),'roles' => array('sys-admin')),
                            
                array('controller' => 'ami-synch','action'=>array('synch'), 'roles' => array('sys-admin','national-general-secretary','admin','ami-integration')),

                //array('controller' => 'zfcuser', 'roles' => array('sys-admin')),             
                //array('controller' => 'zfcuser', 'roles' => array()),
            ),
    
            /* If this guard is specified here (i.e. it is enabled), it will block
             * access to all routes unless they are specified here.
             */
             /* 
              * HZC: We will only use controller guards
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'zfcuser', 'roles' => array('sys-admin', 'guest')),
                array('route' => 'zfcuser/logout', 'roles' => array('sys-admin', 'guest')),
                array('route' => 'zfcuser/login', 'roles' => array('sys-admin', 'guest')),
                array('route' => 'zfcuser/register', 'roles' => array('guest', 'sys-admin')),
                // Below is the default index action used by    the ZendSkeletonApplicationarray('route' => 'zfcuser/register', 'roles' => array('guest', 'admin')),
                array('route' => 'collections/index', 'roles' => array('guest', 'sys-admin')),
                array('route' => 'home', 'roles' => array('guest', 'sys-admin')),
            ),
            */
        )
    );
// }

return $_bjyConfig;
