<?php

namespace Application\Util;

class RoutesUtil{

    /**
     * We assum default action i.e. first action is not parameterized
     * rest of the action can have route parameters
     */
	public static function prepareRoute($controller,$actions){
	
		$defaultAction = 'index';
	    if(isset($actions) && is_array($actions) && count($actions)>0){
	        $defaultAction=$actions[0];    
	    }
		$route = array(
	            		'type' => 'Zend\Mvc\Router\Http\Literal',
                        //'type' => 'segment',
	            		'options' => array(
	            				'route'    => '/'.($controller),
	            				'defaults' => array(
	            				    //    '__NAMESPACE__' => 'Application\Controller',
	            						'controller' => ($controller),
	            						'action'     => $defaultAction,
	            				),
	            		),
	            		'may_terminate' => true,
	            		'child_routes' => RoutesUtil::childRoutes($controller,$actions),
	            );
	            
		return $route;	
	}
	
	private static function childRoutes($controller,$actions){
		$childRoutes = array();
		foreach($actions as $action){
		    $action_parts=preg_split('%/%',$action);
			$childRoutes[$action_parts[0]] = array(
	            						'type'    => 'segment',
	            						'options' => array(
	            								//'route'    => '/'.$controller.'/'.$action,
	            								'route'    => '/'.$action.'[/:extra]',
	            								'constraints' =>array(
                                                    'extra'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                                                ),
	            								'defaults' => array(
	                                                'controller' => $controller,
	                                                'action'     => $action_parts[0],
	            								),
	            						),
                                        'may_terminate' => true, 
                                        // 'child_routes' => array( 
                                            // 'wildcard' => array( 
                                                // 'type' => 'Wildcard',//action level wild card route 
                                            // ), 
                                        // ),	            						
	            				);
		}
        
        //Add wild card route by default
        //$childRoutes['wildcard']=array( 'type' => 'Wildcard',);//controller level wild card route
        
		return $childRoutes;
	}
	
}