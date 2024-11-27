<?php

use Application\Util\RoutesUtil as RoutesUtil;

return array(

     'controllers' => array(
        'invokables' => array(
            'zfcuser' => null,//disable zfcuser controller 
            //'Application\Controller\Index' => 'Application\Controller\IndexController',
            'home'    => 'Application\Controller\IndexController',
            'auth'    => 'Application\Controller\AuthController',
            'report'    => 'Application\Controller\ReportController',
            'document'    => 'Application\Controller\DocumentController',
            #'member'    => 'Application\Controller\MemberController',
            'message'    => 'Application\Controller\MessageController',
            'config'    => 'Application\Controller\ConfigController',
            'user-profile'       => 'Application\Controller\UserProfileController',
            'office-assignment'  => 'Application\Controller\OfficeAssignmentController',
            'election'  		 => 'Application\Controller\ElectionController',
            'admin-cli'  		 => 'Application\Controller\AdminCliController',
            'question-setup'  	 => 'Application\Controller\QuestionSetupController',
            'ami-synch'          => 'Application\Controller\AmiSynchController',
        ),
    ),    


    'router' => array(
        'routes' => array(
         // 'auth' => RoutesUtil::prepareRoute('auth', array('validate','authenticate')),

         'report' => RoutesUtil::prepareRoute('report', array('list','report','create','answers-data','answers-save','report-config','report-config-departments','analysis','attachment','tarbiyat','tarbiyat-report','tarbiyat-report-details','tarbiyat-report-bymembers','monthly-report','monthly-report-status','markaz-monthly-report','markaz-monthly-report-status','history','summary','summary-report-data','summary-repor-questions','update-summary-repor','summary-list', 'gs-report', 'gs-report-process', 'all-department-summary', 'all-department-summary-process')),
          #'member' => RoutesUtil::prepareRoute('member', array('index','manage','list')),
          'message' => RoutesUtil::prepareRoute('message', array('index','inbox','compose','read','reminder','draft','sent','open','attachment','bulk-action','systemreminder')),
          'document' => RoutesUtil::prepareRoute('document', array('list','index','upload','download/:id','edit/:id')),
          'config' => RoutesUtil::prepareRoute('config', array('branch-code','config-value','index','user-departments','user-branches','branch','user-role','department','department-json','periods','report-questions','update-enc-field','config-value-for-user')),
          'office-assignment' => RoutesUtil::prepareRoute('office-assignment', array('list','list-edit','request','requests','process','update')),
          'user-profile' => RoutesUtil::prepareRoute('user-profile', array('index','confirm','validate-user-name','update','settings','search')),
          
          'election' => RoutesUtil::prepareRoute('election', array('index','result','submit','print','list','save','create','export','delete','mlist')),

          'question-setup' => RoutesUtil::prepareRoute('question-setup', array('index','view','empty','export')),

          'admin-cli' => RoutesUtil::prepareRoute('admin-cli', array('index','import-member')),

          'ami-synch' => RoutesUtil::prepareRoute('ami-synch', array('synch')),

           'home' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'home',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true, 
                'child_routes' => array(
                    'members' => array( 
                        'type' => 'Literal',
                        'options' => array(
                            'route'    => 'members',
                            'defaults' => array(
                                'controller' => 'home',
                                'action'     => 'members',
                            ),
                        ),
                    ),
                    'help' => array( 
                        'type' => 'Literal',
                        'options' => array(
                            'route'    => 'help',
                            'defaults' => array(
                                'controller' => 'home',
                                'action'     => 'help',
                            ),
                        ),
                    ), 
                    ),                
            ),
            
            'ace_html' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/home/html',
                    'defaults' => array(
                        'controller' => 'home',
                        'action'     => 'html',
                    ),
                ),
                'may_terminate' => true, 
                'child_routes' => array( 
                    'wildcard' => array( 
                        'type' => 'Wildcard', 
                    ), 
                ),                 
            ),
            'ace_email' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/home/email',
                    'defaults' => array(
                        'controller' => 'home',
                        'action'     => 'email',
                    ),
                ),
                'may_terminate' => true, 
                'child_routes' => array( 
                    'wildcard' => array(
                        'type' => 'Zend\Mvc\Router\Http\Wildcard',
                        'options' => array(
                            //'key_value_delimiter' => '&',
                            //'param_delimiter' => '?',
                        ),
                        'may_terminate' => true,
                    ),
                ),                 
            ),
                        
            'ajax' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/home/ajax',
                    'defaults' => array(
                        'controller' => 'home',
                        'action'     => 'ajax',
                    ),
                ),
				'may_terminate' => true, 
            	'child_routes' => array( 
                	'wildcard' => array( 
                    	'type' => 'Wildcard', 
                	), 
            	),                 
            ),
                  
            'auth' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/login',
                    'defaults' => array(
                        'controller' => 'auth',
                        'action'     => 'login',
                    ),
                ),
                'may_terminate' => true, 
                'child_routes' => array(
                    'authenticate' => array( 
                        'type' => 'Literal',
                        'options' => array(
                            'route'    => '/authenticate',
                            'defaults' => array(
                                'controller' => 'auth',
                                'action'     => 'authenticate',
                            ),
                        ),
                    ),                    
                    'legacy' => array( 
                            'type' => 'Literal',
                            'options' => array(
                                'route'    => '/legacy',
                                'defaults' => array(
                                    'controller' => 'auth',
                                    'action'     => 'legacy',
                                ),
                            ),
                        ),            
                    'login' => array( 
                            'type' => 'Literal',
                            'options' => array(
                                'route'    => '/login',
                                'defaults' => array(
                                    'controller' => 'auth',
                                    'action'     => 'login',
                                ),
                            ),
                        ),            
                    'logout' => array( 
                            'type' => 'Literal',
                            'options' => array(
                                'route'    => '/logout',
                                'defaults' => array(
                                    'controller' => 'auth',
                                    'action'     => 'logout',
                                ),
                            ),
                        ),
                                    
                    'switch-user'=>array( 
                            'type' => 'Literal',
                            'options' => array(
                                'route'    => '/switch-user',
                                'defaults' => array(
                                    'controller' => 'auth',
                                    'action'     => 'switch-user',
                                ),
                            ),
                        ),
                    'change-password' => array( 
                            'type' => 'Literal',
                            'options' => array(
                                'route'    => '/change-password',
                                'defaults' => array(
                                    'controller' => 'auth',
                                    'action'     => 'change-password',
                                ),
                            ),
                        ), 
                    'cognito' => array( 
                            'type' => 'Literal',
                            'options' => array(
                                'route'    => '/cognito/',
                                'defaults' => array(
                                    'controller' => 'auth',
                                    'action'     => 'cognito',
                                ),
                            ),
                        ), 
                    'cognito-prod' => array( 
                            'type' => 'Literal',
                            'options' => array(
                                'route'    => '/cognito',
                                'defaults' => array(
                                    'controller' => 'auth',
                                    'action'     => 'cognito',
                                ),
                            ),
                        ), 
                ),//auth child routes                 
            ),//auth route
            'reset-password' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/reset-password',
                    'defaults' => array(
                        'controller' => 'auth',
                        'action'     => 'reset-password',
                    ),
                ),
            ),
                        
          

/*
              // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            */
        ), //routes_config_end
    ),
      
    'console' => array(
        'router' => array(
            'routes' => array(
                'user-password' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route'    => 'user password',
                        'defaults' => array(
                            'controller' => 'Application\Controller\User',
                            'action'     => 'password'
                        )
                    )
                ),        
                
                'message-systemreminder' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route'    => 'message systemreminder [--send_to=] [--level=]',
                        'defaults' => array(
                            'controller' => 'message',
                            'action'     => 'systemreminder'
                        )
                    ),              
                ),

            )
        )
    )
);
