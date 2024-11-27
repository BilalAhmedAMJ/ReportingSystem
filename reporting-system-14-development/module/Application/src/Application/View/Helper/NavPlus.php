<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Navigation\AbstractContainer;

use BjyAuthorize\Service\Authorize;

/**
 * Adds more functions to ease usage of naviations and menus 
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class NavPlus extends AbstractHelper
{
    /**
     * @var Authorize
     */
    protected $authorizeService;
	protected $pluginManager;
    /**
     * @param Authorize $authorizeService
     */
    public function __construct(Authorize $authorizeService,$pluginManager)
    {
        $this->authorizeService = $authorizeService;
		$this->pluginManager=$pluginManager;
    }

    /**
     * @param mixed      $resource
     * @param mixed|null $privilege
     *
     * @return bool
     */
    public function haveAccess($page=null, $privilege = null)
    {
        $haveAccess=false;//forbid by default
        $hasChildren = false;
        if($page!=null && is_string($page)){
            $haveAccess = $this->authorizeService->isAllowed($page, $privilege);
            //error_log("haveAccess: $page => $haveAccess");
        }elseif($page!=null && is_object($page)){
            $hasChildren = $page->hasPages();
            if (!$hasChildren){//if no children check access on page it self
                $haveAccess=$this->checkAccess($page, $privilege);
                //error_log("haveAccess: $page => $haveAccess");
            }else{ //if have children grant access on parent as long as any child can be acccessed
                foreach ($page->getPages() as $child){
                    $childAccess = $this->checkAccess($child, $privilege);
                    $haveAccess = ($haveAccess || $childAccess);
                    //error_log("haveAccess: $page / $child  => $childAccess");
               }
                //error_log("haveAccess: $page with $hasChildren => $haveAccess");
            }
            return $haveAccess;
        }
        else {
            return false;        
        }
    }
    
    public function checkAccess($page = null,$privilege = null){
        
        $resource=null;
        $controller=$page->getController();
        $action=$page->getAction();
        if($controller){
            $resource='controller/'.$controller;
        }
        //Check for access on controller level
        $haveAccess = $this->authorizeService->isAllowed($resource, $privilege);
    
        
        //if not authorized at controller level check at controller->action level
        if(!$haveAccess && $action){
            $resource=$resource.':'.$action;
            $haveAccess = $this->authorizeService->isAllowed($resource, $privilege);
        }

        //if we still don't have access check route for the page to see if that can be accessed
        $route=$page->getRoute();
        if(! $haveAccess && ! empty($route)){
            $routeParts=explode("/",$route);
            $resourceController='controller/'.$routeParts[0];
            $haveAccess = $this->authorizeService->isAllowed($resourceController, $privilege);

            if(!$haveAccess && count($routeParts)>1){
                $resourceAction='controller/'.$routeParts[0].':'.$routeParts[1];
                $haveAccess = $this->authorizeService->isAllowed($resourceAction, $privilege);
            }
        }
        return $haveAccess;        
    }

	public function isActive($page){
        $hasChildren = $page->hasPages();
        $isActive=false;//not active by default
        $matchedPage=null;
        if (!$hasChildren){//if no children check access on page it self
			$isActive=$page->isActive() && $this->isQueryMatch($page);
		}else{
			$isActive=false;
			foreach ($page->getPages() as $child){
				if($child->isActive()){
					$isActive = ($isActive || $this->isQueryMatch($child));					
				}
			}
		}		

		return $isActive;
	}


	private function isQueryMatch($page){
		//double check i.e. fix problem when route in nav have defined query parameters but route in routes have not
		//we assume that main page does not have query params		
		$href=$page->getHref();
		$matchedQuery=null;
		$pageQuery=null;
		if(preg_match('/\?/',$href)){
			$matchedQuery=preg_replace('/^.*\?/', '', $href);
			$pageQuery=($this->pluginManager->getServiceLocator()->get('request')->getUri()->getQuery());
			return ($matchedQuery === $pageQuery);
		}
		return $page!=null;//route for page does not have a query string as part of it, so we assume it matches
	}
    
    public function getResource($page){
        $route=is_object($page)?$page->getRoute():$page;
        $resourceAction='';
        $base='controller/';
        if(! empty($route)){
            $routeParts=explode("/",$route);
            if(count($routeParts)>1)
                $resourceAction=$base.$routeParts[0].':'.$routeParts[1];        
            else
                $resourceAction=$base.$routeParts[0];
        }
        return $resourceAction;
    }
    
    public function controllerAccess($controller,$action){
        if($controller==null||empty($controller))
            return false;
        $resource=$this->getResource($controller.($action?(':'.$action):''));
        $acl = $this->authorizeService->getAcl();
        $resourceObj=$acl->getResource($resource);
        $access = $acl->isAllowed($this->authorizeService->getIdentity(),$resourceObj,null);

	# error_log( $resource ."\nActual\n".$this->authorizeService->getAcl()->getResource($resource));

        return $access;
    }
}
