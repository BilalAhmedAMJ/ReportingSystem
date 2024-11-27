<?php

return array(
    'ZfcDatagrid' => array(

        'renderer' => array(
            
            'bootstrapTable' => array(
                /*
                // Daterange bootstrapTable filter configuration example
                'daterange' => array(
                    'enabled' => true,
                    'options' => array(
                        'ranges' => array(
                            'Last Month' => new \Zend\Json\Expr("[moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]"),
                            'Last 3 Months' => new \Zend\Json\Expr("[moment().subtract('month', 3).startOf('month'), moment().subtract('month', 3).endOf('month')]"),
                            'Last 12 Month' => new \Zend\Json\Expr("[moment().subtract('month', 12).startOf('month'), moment().subtract('month', 12).endOf('month')]")
                        ),
                        'locale' => \Locale::getDefault(),
                        'format' => 'YYYY-MM-DD'
                    )
                ),
                 * 
                 */
                'templates' => array(
                    //'toolbar' => 'partial/bootstrap_datagrid/reports_toolbar.phtml',
                    'toolbar' =>'',
                    'layout'=>'partial/bootstrap_datagrid/layout'
                )
            ),
            
            'jqGrid' => array(
                'parameterNames' => array(
                    // Internal => jqGrid
                    'currentPage' => 'currentPage',
                    'itemsPerPage' => 'itemsPerPage',
                    'sortColumns' => 'sortByColumns',
                    'sortDirections' => 'sortDirections',
                    'isSearch' => 'isSearch',
                    
                    'columnsHidden' => 'columnsHidden',
                    'columnsGroupByLocal' => 'columnsGroupBy',
                    
                    'massIds' => 'ids'
                )
            ),
                        
            
         ), 
        'settings' => array(
             'export' => array(
                'enabled' => true,

                // currently only A formats are supported...
                'papersize' => 'A4',

                // landscape / portrait (we preferr landscape, because datagrids are often wide)
                'orientation' => 'landscape',

                'formats' => array(
                    // renderer -> display Name (can also be HTML)
                    'PHPExcel' => 'Excel',
                    'tcpdf' => 'PDF'
                ),

                // The output+save directory
                'path' => ROOT_PATH.'/public/exports',

                'mode' => 'direct'
            )
        ),   
        'cache' => array(            
            'adapter' => array(
                'name' => 'Filesystem',
                'options' => array(
                    'ttl' => 3600,//720000, // cache with 200 hours,
                    'cache_dir' => 'data/cache'
                )
            ),
        ),     
    ),

    'view_manager' => array(
        
        'strategies' => array(
            'ViewJsonStrategy'
        ),
        
        'template_map' => array(
            'zfc-datagrid/renderer/jqGrid/layout' => __DIR__ . '/../../view/partial/zfc-datagrid/renderer/jqGrid/layout.phtml'
        ),
        
    )        
);


