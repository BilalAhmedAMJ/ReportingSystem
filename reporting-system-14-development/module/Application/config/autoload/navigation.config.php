<?php


return array(
    'navigation' => array(
    
        'default' => array(
        
            array(
                'label' => 'Home',
                'route' => 'home/members',
                'controller'=>'home',
                'action'=>'members',
                'icon'  =>'<i class="menu-icon fa fa-home"></i>',
                'detailed'=>true,
                
            ),
            
            array(
                'label' => 'Reports',
                'route' => 'report/list',
                'query'=>array('filter'=>'own'),
                'icon'  =>'<i class="menu-icon fa fa-newspaper-o"></i>',
                'addclass'=>'green',
                'pages' => array(
                    array(
                        'label' => 'Create',
                        'route' => 'report/create',
                        'icon'=>'<i class="menu-icon fa fa-plus"></i>',
                        //'query' => array('docType'=>'form'),
                    ),                    
                    array(
                        'label' => 'List',
                        'route' => 'report/list',
                        'icon'=>'<i class="menu-icon fa fa-table"></i>',
                        'query'=>array('filter'=>'own')
                    ),
                array(
                        'label' => 'Report Submission',
                        'route' => 'report/analysis',
                        'icon'=>'<i class="menu-icon fa fa-bar-chart-o"></i>',
                        //'query' => array('docType'=>'form'),
                    ), 
                array(
                        'label' => 'Amila Meetings & Ijlasaat',
                        'route' => 'report/gs-report',
                        'icon'=>'<i class="menu-icon fa fa-bar-chart-o"></i>',
                        //'query' => array('docType'=>'form'),
                    ),                     
                array(
                        'label' => 'All Departments Summary',
                        'route' => 'report/all-department-summary',
                        'icon'=>'<i class="menu-icon fa fa-bar-chart-o"></i>',
                        //'query' => array('docType'=>'form'),
                    ),                     
                array(
                        'label' => 'Tarbiyat Assessment',
                        'route' => 'report/tarbiyat',
                        'icon'=>'<i class="menu-icon fa fa-bar-chart-o"></i>',
                        //'query' => array('docType'=>'form'),
                    ), 
                array(
                        'label' => 'Monthly Report Status',
                        'route' => 'report/monthly-report',
                        'icon'=>'<i class="menu-icon fa fa-bar-chart-o"></i>',
                        //'query' => array('docType'=>'form'),
                    ), 
                array(
                        'label' => 'Markaz Monthly Status Report',
                        'route' => 'report/markaz-monthly-report',
                        'icon'=>'<i class="menu-icon fa fa-bar-chart-o"></i>',
                        //'query' => array('docType'=>'form'),
                    ), 
                array(
                        'label' => 'Custom Analytics',
                        'route' => 'report/summary-list',
                        'icon'=>'<i class="menu-icon fa fa-bar-chart-o"></i>',
                        //'query' => array('docType'=>'form'),
                    ), 
                  )                
            ),

            //document
            array(
                'label' => 'Documents',
                'route' => 'document/list',
                'icon' => '<i class="menu-icon fa fa-folder-open-o"></i>',
                'addclass'=>'red',
                'class' => 'open',
                'pages' => array(
                    array(
                        'label' => 'List',
                        'route' => 'document/list',
                        'icon'=>'<i class="menu-icon fa fa-table"></i>',
                        //'query' => array('docType'=>'form'),
                    ),
                    array(
                        'label' => 'Uploaded',
                        'route' => 'document/list',
                        //'url'=>'document/list?filter=uploaded',
                        'query' => array('filter'=>'uploaded'),
                    ),
                    // array(
                        // 'label' => 'Others',
                        // 'route' => 'document/list',
                        // 'query' => array('docType'=>'others'),
                    // ),
                    array(
                        'label' => 'Upload',
                        'route' => 'document/upload',
                        'query' => array('docType'=>'upload'),
                        'resource'=>'document',
                        'icon'=>'<i class="menu-icon fa fa-upload"></i>',
                    ),
                ),
            ),
            //document
            //Email
            array(
                'label' => 'Messages',
                'addclass'=>'light-blue',                
                'route' => 'message',
                'icon' => '<i class="menu-icon fa fa-envelope-o"></i>',
                
                'pages' => array(
                    array(
                        'label' => 'Inbox',
                        'route' => 'message/inbox',
                        //'query' => array('action'=>'inbox'),
                        'icon' => '<i class="menu-icon fa fa-inbox"></i>',
                    ),
                    array(
                        'label' => 'Reminder',
                        'icon' => '<i class="menu-icon fa fa-clock-o"></i>',
                        'route' => 'message/reminder',
                        //'query' => array('action'=>'send'),
                    ),
                    array(
                        'label' => 'Compose',
                        'icon' => '<i class="menu-icon fa fa-edit"></i>',
                        'route' => 'message/reminder',
                        //'route' => 'message/compose',
                        //'query' => array('action'=>'send'),
                    ),
                ),
            ),
            //Email
                        

            //Office-Assignments
            array(
                'label' => 'Office Bearers',
                'route' => 'office-assignment/list',
                'query'=>array('filter'=>'own'),
                'addclass'=>'purple',
                'icon' => '<i class="menu-icon fa fa-users"></i>',
                'pages' => array(
                    array(
                        'label' => 'Office Bearers List',
                        'route' => 'office-assignment/list',
                        'icon' => '<i class="menu-icon glyphicon glyphicon-phone-alt"></i>',
                        'query'=>array('filter'=>'own')
                    ),
                    array(
                        'label' => 'Edit Users',
                        'route' => 'office-assignment/list-edit',
                        'icon' => '<i class="menu-icon fa fa-icon ace ace-icon fa-user bigger-110"></i>',
                        'query'=>array('filter'=>'own')
                    ),
/*
                    array(
                        'label' => 'New Request',
                        'route' => 'office-assignment/request',
                        'icon' => '<i class="menu-icon fa fa-plus-square-o bigger"></i>',
                        //'query' => array('action'=>'manage'),
                    ),
                    array(
                        'label' => 'Pending Requests',
                        'route' => 'office-assignment/requests',
                        'icon' => '<i class="menu-icon fa fa-tasks bigger"></i>',
                    ),
*/
                    array(
                        'label' => 'Search',
                        'route' => 'user-profile/search',
                        'icon' => '<i class="menu-icon fa fa-search bigger"></i>',
                    ),
                ),
            ),
            //User

/*
            array(
                'label' => 'Elections',
                'route' => 'election/list',
                'detailed'=>true,
                'icon'  =>'<i class="menu-icon fa fa-user-plus"></i>',
                'addclass'=>'green',
                'pages' => array(
                    array(
                        'label' => 'Elections List',
                        'route' => 'election/list',
                        'icon' => '<i class="menu-icon fa fa-user-plus"></i>',
                        //'query'=>array('filter'=>'own')
                    ),
                    array(
                      'label' => 'New Election Result',
                      'route' => 'election/create',
                      'icon' => '<i class="menu-icon fa fa-user-plus"></i>',
                    ),                        
                    array(
                        'label' => 'Export Results',
                        'route' => 'election/export',
                        'icon'=>'<i class="menu-icon fa fa-save"></i>',
                        //'query' => array('docType'=>'form'),
                    )                    
                                        
                )
            ),
  */          
            
            //config
            array(
                'label' => 'Config',
                'route' => 'config/user-role',
                'detailed'=>true,
                'icon' => '<i class="menu-icon fa fa-gears"></i>',
                'pages' => array(
                    array(
                        'label' => 'Jama`at Setup',
                        'route' => 'config/branch',
                        'icon' => '<i class="menu-icon fa fa-building-o"></i>',
                        //'query' => array('docType'=>'form'),
                    ),
                     array(
                         'label' => 'User Role',
                         'route' => 'config/user-role',
                     ),
                     array(
                         'label' => 'Department',
                         'route' => 'config/department',
                     ),
                     array(
                         'label' => 'Questions',
                         'route' => 'config/report-questions',
                         'icon' => '<i class="fa fa-comments-o"></i>',
                     ),
                ),
            ),
            //config

        array(
                'label' => 'Logout',
                'route' => 'auth/logout',
                'controller'=>'auth',
                'action'=>'logout',
                'icon'  =>'<i class="menu-icon fa fa-power-off"></i>',
                'detailed'=>true,
                
            ),
            
        ),
    ),
);
