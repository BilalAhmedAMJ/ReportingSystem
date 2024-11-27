<?php



return array(
    
    'service_manager' => array(
        'factories' => array(
        
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'CreateFormService'=>'Application\Service\CreateFormService',
            'QuestionElementFactory'=>'Application\Form\QuestionElementFactory',
            'ConfigService'=>'Application\Service\ConfigService',
            'EntityService'=>'Application\Service\EntityService',
            'entityFactory'=>'Application\Service\CreateEntityFactory',
            'BranchManagementService'=>'Application\Service\BranchManagementService',
            'UserProfileService' => 'Application\Service\UserProfileService',
            'Logger'=>'Application\Service\LoggerService',            
            'OfficeAssignmentService'=>'Application\Service\OfficeAssignmentService', 
            'EmailService'  => 'Application\Service\EmailService',
            'ReportSubmissionService' => 'Application\Service\ReportSubmissionService',
            'SummaryReportService' => 'Application\Service\SummaryReportService',
            'DocumentService' => 'Application\Service\DocumentService',
            'MessagesService' => 'Application\Service\MessagesService',
            'DashboardService' => 'Application\Service\DashboardService',
            
            'ElectionService' => 'Application\Service\ElectionService',
            
            'JSONService' => 'Application\Service\JSONService',

            'MemberService' => 'Application\Service\MemberService',
            
            'ReportAnalysisService' => 'Application\Service\ReportAnalysisService',
            'DocUtil' => 'Application\Util\DocumentUtil',
                
            // Configures the default SessionManager instance
            'Zend\Session\ManagerInterface' => 'Zend\Session\Service\SessionManagerFactory',
            // Provides session configuration to SessionManagerFactory
            'Zend\Session\Config\ConfigInterface' => 'Zend\Session\Service\SessionConfigFactory',

            'DataGridHelper'=> 'Application\View\DataGrid\DataGridViewHelperService',
            
            'EncryptUtil'   =>  'Application\Factory\EncryptUtilFactory',
                       
            'DoctrineEncryptAdapter' => 'Application\Doctrine\DoctrineEncryptAdapter',
            //Override zfcuser_user_mapper to allow loading user by "username" that uses uhash
            'zfcuser_user_mapper' => 'Application\Factory\ZfcUserMapperFactory',                                     

            'doctrine.cache.orm_cache' => 'Application\Factory\DoctrineORMCacheFactory',
             
            'SwitchUserAuthAdapter'    => 'Application\Factory\SwitchUserAuthAdapterFactory',
            'CognitoAuthAdapter'       => 'Application\Factory\CognitoAuthAdapterFactory',
            
            'ZfcDataGridFactory' => 'Application\Factory\ZfcDataGridFactory',
                        
            'DBCleanupService'  => 'Application\Service\DBCleanupService',
            
            //'Zend\Db\Adapter\Adapter' => 'Application\Factory\BjyDBProfilerFactory',             

/*
            'ArrayObjHydrator'=>function($sm){
                                        $objHyd=new \Zend\Stdlib\Hydrator\ClassMethods(true);
                                        return $objHyd; 
                                  },
            'DoctrineObjHydrator'=>function($sm){
                                        $objHyd=new DoctrineModule\Stdlib\Hydrator\DoctrineObject($sm->get('Doctrine\ORM\EntityManager'));
                                        return $objHyd; 
                                  },

 * 
 */

        )//factories

        ,'abstract_factories' => array(
//            'Application\Service\CreateEntityFactory',
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        
        'aliases' => array(
            'translator' => 'MvcTranslator',
            'zfcuser_doctrine_em' => 'Doctrine\ORM\EntityManager',
        ),
    ),
);