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

use Application\Service\ConfigService;

/**
 * Adds more functions to ease usage of naviations and menus 
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class ConfigHelper extends AbstractHelper
{

    /**
     * @var Authorize
     */
    protected $configService;
    protected $pluginManager;
    /**
     * @param Authorize $authorizeService
     */
    public function __construct(ConfigService $configService,$pluginManager)
    {
        $this->configService = $configService;
        $this->pluginManager=$pluginManager;
    }


    public function __invoke($configItem,$subItem=null){
        $item = $this->configService->getConfig($configItem);
        //if a config sub key is provided and exists in config return value of sub key 
        if($item && $subItem && is_array($item) && key_exists($subItem, $item)){
            return $item[$subItem];
        }else{
            //otherwise return config main item
            return $item;
        }
    }
    
}
