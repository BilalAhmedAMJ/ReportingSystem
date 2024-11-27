<?php
/**
 *
 */

return array( 
   'console' => array(
        'router' => array(
            'routes' => array(
                'import-user' => array(
                            'options' => array(
                                'route'    => 'import-user',
                                'defaults' => array(
                                    'controller' => 'import',
                                    'action'     => 'user'
                                )
                            )
                    ),

                'import-member' => array(
                            'options' => array(
                                'route'    => 'import-member',
                                'defaults' => array(
                                    'controller' => 'import',
                                    'action'     => 'import-member'
                                )
                            )
                ),

                'admin-cli-test' => array(
                            'options' => array(
                                'route'    => 'test-admin-cli',
                                'defaults' => array(
                                    'controller' => 'admin-cli',
                                    'action'     => 'test'
                                )
                            )
                ),
                'admin-cli-sample-db' => array(
                            'options' => array(
                                'route'    => 'setup-sample-db',
                                'defaults' => array(
                                    'controller' => 'admin-cli',
                                    'action'     => 'setup-sample-db'
                                )
                            )
                ),
                // 'message-systemreminder' => array(
                //     'options' => array(
                //         'route'    => 'message systemreminder [--send_to=] [--level=]',
                //         'defaults' => array(
                //             'controller' => 'message',
                //             'action'     => 'systemreminder'
                //         )
                //     ),              
                // ),                
            ),
        ),
   ),
);