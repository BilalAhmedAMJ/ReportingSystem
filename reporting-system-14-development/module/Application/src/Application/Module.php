<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Helper\Navigation as ZendViewHelperNavigation;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\Console\Request as ConsoleRequest;
use Zend\ModuleManager\Feature\FormElementProviderInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Session\Container;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;


use BjyAuthorize\View\RedirectionStrategy;
use Rhumsaa\Uuid\Uuid;


class Module implements FormElementProviderInterface
{	

	private $login_urls=array('/logout','/login','login/legacy','auth/legacy','/login/legacy','/auth/legacy','/reset-password','reset-password','/change-password','/auth/change-password','auth/change-password','auth/logout','auth/login', 'auth/authenticate','/auth/login','auth/cognito-prod','/auth/cognito-prod','auth/cognito','/auth/cognito', '/auth/authenticate');

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);


       // $this->setUnathorizedAccessRedirect($eventManager);
       
       if( !($e->getRequest() instanceof ConsoleRequest) ) {

            //register check login 
            $eventManager->attach('route', array($this, 'checkLogin'));
            //add env marking banner for non prod env
            //$eventManager->attach('finish', array($this, 'finishAll'));

            $view = $e->getRequest()->getQuery('view');
            if($view == 'print'||$view=='ajax'){
                $eventManager->attach('dispatch', function($mvcEvent) use($view){                    
			        $route = $mvcEvent -> getRouteMatch() -> getMatchedRouteName();
                    
    				if (!in_array($route, $this->login_urls) ) {
						//check URI is not login					
		                $mvcEvent->getTarget()->layout('layout/'.$view);
					}				
                });
            }       
            
        }
        //add logging to error on exception
         $sharedManager = $eventManager->getSharedManager();             
         $sharedManager->attach('Zend\Mvc\Application', 'render.error',array($this,'logException'));   
         $sharedManager->attach('Zend\Mvc\Application', 'dispatch.error',array($this,'logException'));

        //start session manager
        $config = $e->getApplication()
                  ->getServiceManager()
                  ->get('Configuration');
                  
        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions($config['session']['config']['options']);
        $sessionManager = new SessionManager($sessionConfig);
        $sessionManager->start();

        ///$sessionManager = $e->getApplication()->getServiceManager()->get('Zend\Session\ManagerInterface');
        //$sessionManager->start();

        
        //integrate zf2 navigation with BjyAuthorize
        //http://akrabat.com/zend-framework-2/integrating-bjyauthorize-with-zendnavigation/
        // Add ACL information to the Navigation view helper
        /*
        $sm = $e->getApplication()->getServiceManager();
        
        $authorize = $sm->get('BjyAuthorizeServiceAuthorize');
        $acl = $authorize->getAcl();
        $role = $authorize->getIdentity();
        ZendViewHelperNavigation::setDefaultAcl($acl);
        ZendViewHelperNavigation::setDefaultRole($role);
        */
    }
    
    public function logException(MvcEvent $oMvcEvent){
        
        $sm = $oMvcEvent->getApplication()->getServiceManager();
        if (!($oMvcEvent->getRequest() instanceof ConsoleRequest ) && $oMvcEvent->getParam('exception')){
            
            $routeMatch = $oMvcEvent->getRouteMatch();
            $request = $oMvcEvent->getRequest();
            
            $logData = array(
                'controller'        => $routeMatch?$routeMatch->getParam('controller'):'',
                'action'            => $routeMatch?$routeMatch->getParam('action'):'',
                'queryParams'       => $request?$request->getQuery()->toString():'',
                'postParams'        => $request?$request->getPost()->toString():'',
                'userAgent'         => $request?$request->getHeader('useragent')->toString():'',
                //'ajaxCall'          => $xRequestedWith,
                'ipAddress'         => $request?$request->getServer()->get('REMOTE_ADDR'):'',
                //'userId'            => $iUserID,
                'logTime'           =>  date('Y-m-d H:i:s'),
                'exception'         =>  $oMvcEvent->getParam('exception')->__toString()
            );
            
            
            //$sm->get('Zend\Log\Logger')->crit($oMvcEvent->getParam('exception'),$logData);
            $msg = print_r($oMvcEvent->getParam('exception')->__toString(),true);
            $msg = $msg."\n".print_r($logData,true);
            error_log($msg);
             $sm->get('Logger')->err(($msg));
        }else{
            $msg = print_r($oMvcEvent->getParam('error'),true);
            error_log($msg);
            
            $sm->get('Logger')->err(($msg));
        }
                
    }


    public function setLocale(MvcEvent $e){

        //Get user's language

        //Check if users language is supported, if so set that as locale

        //otherwise check if a default local is defined for the site
        getenv('DEFAULT_LOCALE');

        //If we don't have user's language or a default for site, we will use en-US as locale
        locale_set_default('en_US');    
    }

    private function setUnathorizedAccessRedirect($eventManager){
        //Redirect to login on un authorized access
        //http://stackoverflow.com/questions/18378201/zf2-how-to-override-bjyauthorize-view-redirectionstrategy
        $strategy = new RedirectionStrategy();      
        // eventually set the route name (default is ZfcUser's login route)
        $uri     = $eventManager->getSharedManager()->getTarget()->getRequest()->getUri();
        $redirectUri = $router->assemble(array(), array('name' => 'auth','query'=>array('redirect'=>$uri)));
        
        $strategy->setRedirectUri($redirectUri);
        $eventManager->attach($strategy); 
        
    }
    public function getConfig()
    {
    	
        $module_config=include __DIR__ . '/../../config/module.config.php';
		// $bjyauth_config=include __DIR__ . '/config/bjyauthorize.config.php';
		// $doctrine_config=include __DIR__ . '/config/doctrine.config.php';
		// $routes_config=include __DIR__ . '/config/routes.config.php';
		
		$final_config=$module_config;		
		// $final_config=array_merge($final_config,$routes_config);
		// $final_config=array_merge($final_config,$doctrine_config);
		// $final_config=array_merge($final_config,$bjyauth_config);
		//var_dump($final_config);
		
		return $final_config;
    }

    public function getAutoloaderConfig()
    {
        return array(

            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ .'/../../autoload_classmap.php',
            ),

            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/',//' . __NAMESPACE__,
                ),
            ),
        );
    }
    
	public function getFormElementConfig() {
	        return array(
	            'factories' => array(
	                'BranchesFieldset' => function($sm) {
	                    // I assume here that the Album\Model\AlbumTable
	                    // dependency have been defined too
						
	                    $serviceLocator = $sm->getServiceLocator();
	                    $fieldset = new \Zend\Form\Element\Select();
						$fieldset->setValueOptions(array(''=>'','78'=>'Brampton','37'=>'Canada'));
	                    return $fieldset;
	                },
					'DepartmentsFieldset' => function($sm) {
	                    // I assume here that the Album\Model\AlbumTable
	                    // dependency have been defined too
	                    $serviceLocator = $sm->getServiceLocator();
	                    $fieldset = new \Application\Form\BranchesFieldset();
						$fieldset->setValueOptions(array(''=>'','26'=>'President','5'=>'General Secretary','4'=>'Mal'));
	                    return $fieldset;
	                },
	            	'ElectionProposalFieldset'=>function($sm){
	            		return new \Application\Form\ElectionProposalFieldset();
	            	}    
	            )
				
	        );
	    } 
  

    public function finishAll(MvcEvent $e){

        if(getenv('APPLICATION_ENV') != 'production' && ! (preg_match( '/JsonModel$/',get_class($e->getViewModel()) )) ){
          echo '<footer><div style="height:inherit;min-height:inherit;text-align:center;color:white;z-index:99999" class="navbar navbar-inverse navbar-fixed-top label-warning">';
          echo getenv('APPLICATION_ENV') . '&nbsp; from &nbsp;' . (ROOT_PATH);
          echo '</div></footer>';
        }
    }

    public function checkLogin(MvcEvent $e) {

        $app = $e -> getApplication();
        $em = $app -> getEventManager();
        $sm = $app -> getServiceManager();        
        $auth = $sm->get('zfcuser_auth_service');
        

        $response = $e -> getResponse();

        $route = $e -> getRouteMatch() -> getMatchedRouteName();

        if (!in_array($route, $this->login_urls) && !$auth->hasIdentity()) {
            
            $target = urlencode($route.'?'.$e->getRequest()->getUri()->getQuery());
            $view = $e->getRequest()->getQuery('view');
            $view = ($view && $view=='ajax')?'&view=ajax':'';
            $response -> getHeaders() -> addHeaderLine('Location', '/login/login?target='.$target.$view);
            //$response -> getHeaders() -> addHeaderLine('Location', '/login/login');
            $response -> setStatusCode(302);
            $response -> sendHeaders();
            exit ;
        }
        
        //If user is already Authenticated add dynamic roles to user
        if($auth->hasIdentity()){
            
            //link session user to entitymanager
            $session_user = $sm->get('zfcuser_auth_service')->getIdentity();//$auth->getStorage()->read();
            $office_srv = $sm->get('OfficeAssignmentService');
            $roles = $office_srv->getOfficeRoles($session_user);
            //print_r(array('<pre>',count($roles),'</pre>'));exit;
            $session_user->addDynamicRoles($roles); 
            //foreach($roles as $role){error_log('Module-Add-Dynamic-Role => '.$role->getRoleId());}
            if(substr($route,0,1)!='/'){
                $route='/'.$route;
            }
            if(empty($roles) && !$session_user->hasRole('ami-integration') && !$session_user->hasRole('sys-admin') && !$session_user->hasRole('upload-documents') && !in_array($route, array('/logout','/login','/user-profile'))){
                $router = $e->getRouter();
                $url = $router->assemble(array(),array('name'=>'user-profile'));
                $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(302);
                $response->sendHeaders();
            }
        }
        
    }
    
    /**
     * 
     * {@inheritDoc}
     */
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                //add view helper to check if privileges on a route based on route guards
                'navPlus' => function (AbstractPluginManager $pluginManager) {
                    $serviceLocator = $pluginManager->getServiceLocator();
                    /* @var $authorize \BjyAuthorize\Service\Authorize */
                    $authorize = $serviceLocator->get('BjyAuthorize\Service\Authorize');

                    return new \Application\View\Helper\NavPlus($authorize,$pluginManager);
                },

                'messages' => function (AbstractPluginManager $pluginManager) {
                     $serviceLocator = $pluginManager->getServiceLocator();
                    /* @var $authorize \BjyAuthorize\Service\Authorize */
                    $auth = $serviceLocator->get('zfcuser_auth_service');

                    return new \Application\View\Helper\MessagesHelper($auth,$serviceLocator);

                },
                
                'truncate'=>function (AbstractPluginManager $pluginManager) {

                    return new \Application\View\Helper\TruncateHelper($pluginManager);
                },
                
                'smart_format'=>function (AbstractPluginManager $pluginManager) {

                    return new \Application\View\Helper\SmartDateTimeFormatHelper($pluginManager);
                },
                
                //view helper to access configuration for application
                'config' => function (AbstractPluginManager $pluginManager) {
                    $serviceLocator = $pluginManager->getServiceLocator();
                    /* @var $authorize \BjyAuthorize\Service\Authorize */
                    $configService = $serviceLocator->get('ConfigService');

                    return new \Application\View\Helper\ConfigHelper($configService,$pluginManager);
                },

                'report_period'=> function (AbstractPluginManager $pluginManager) {
                    $serviceLocator = $pluginManager->getServiceLocator();
                    /* @var $authorize \BjyAuthorize\Service\Authorize */
                    $configService = $serviceLocator->get('ConfigService');
                    $authService = $serviceLocator->get('zfcuser_auth_service');
                    $currentUser = $authService->getIdentity();
                    
                    return new \Application\View\Helper\ReportPeriodHelper($currentUser,$configService,$pluginManager); 
                    
                },
                //view helper to access configuration for application
                'crypt' => function (AbstractPluginManager $pluginManager) {
                    $serviceLocator = $pluginManager->getServiceLocator();
                    /* @var $authorize \BjyAuthorize\Service\Authorize */
					$adapter = $this->getServiceLocator()->get('DoctrineEncryptAdapter');

                    return new \Application\View\Helper\CryptHelper($adapter,$pluginManager);
                },

            ),
        );
    }
    
}
