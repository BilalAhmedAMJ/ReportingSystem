<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array( 
	
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    
    
	'application'=>array(
		 'form_dir'=>__DIR__ . '/../view/form/',
	),
	
    'view_manager' => array(
       # 'display_not_found_reason' => true, /*FIXPROD remove for production release*/
        'display_exceptions'       => true, /*FIXPROD remove for production release*/
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        
        'exception_template'       => 'error/index',
        
        'template_map'  => array_merge(
                                array(
                                    'layout/layout'           => __DIR__ . '/../view/layout/layout_ace_html.phtml',
                                    'layout/layout_empty'     => __DIR__ . '/../view/layout/layout.phtml',
                                    'layout/layout_ajax'      => __DIR__ . '/../view/layout/layout_ace.phtml',
                                    'layout/layout_html'      => __DIR__ . '/../view/layout/layout_ace_html.phtml',
                                    //'layout/layout_ajax'      => __DIR__ . '/../view/layout/ajax.html',
                                    'layout/login'           => __DIR__ . '/../view/layout/layout_login.phtml',
                                    'layout/print'           => __DIR__ . '/../view/layout/layout_print.phtml',
                                    'layout/ajax'            => __DIR__ . '/../view/layout/layout_ajax.phtml',
                                    'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
                                    'error/404'               => __DIR__ . '/../view/error/404.phtml',
                                    'error/index'             => __DIR__ . '/../view/error/index.phtml',
                                    'error/403'              =>  __DIR__ . '/../view/error/403.phtml',    
                                ),
                                include __DIR__ .'/../template_map.php'
                        ),        
        'template_path_stack' => array(
            //__DIR__ . '/../view',
            'application'=> __DIR__ . '/../view/application',
            'view'=> __DIR__ . '/../view/',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),        
    ),

//            <button label="" type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

    'view_helper_config' => array(
//        'factories' =>(),
        'flashmessenger' => array(
            'message_open_format'      => '<div%s><a href="#" class="close" data-dismiss="alert">&times;</a><ul><li>',
            'message_separator_string' => '</li><li>',
            'message_close_string'     => '</li></ul></div>',
            
        )
    ),    
    
    'controller_plugins' => array(
        'invokables' => array(
            'Transaction' => 'Application\Controller\Plugin\TransactionPlugin',
            'Logger' => 'Application\Controller\Plugin\LoggerPlugin',
            'crypt' => 'Application\Controller\Plugin\CryptPlugin',
            'json' => 'Application\Controller\Plugin\JsonHelperPlugin',
            'current_user' => 'Application\Controller\Plugin\CurrentUserPlugin',
            'session' => 'Application\Controller\Plugin\SessionPlugin',
            'list_filter_form' => 'Application\Controller\Plugin\ListFilterFormPlugin',
            'csv'=>'Application\Controller\Plugin\DownloadCSVPlugin',
        )
    ),
    
    
   // Placeholder for console routes
   /*'console' => array(
        'router' => array(
            'routes' => array(
                'user-password' => array(
                            //'type' => 'simple',
                            'options' => array(
                                'route'    => 'user password <password>',
                                'defaults' => array(
                                    'controller' => 'auth',
                                    'action'     => 'password'
                                )
                            )
                        )
                ),
        ),
   ),*/
);
