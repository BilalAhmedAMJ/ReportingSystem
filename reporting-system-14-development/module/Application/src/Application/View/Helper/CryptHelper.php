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
class CryptHelper extends AbstractHelper
{

    /**
     * @var Authorize
     */
    protected $configService;
    protected $pluginManager;
	protected $adapter;
    /**
     * @param Authorize $authorizeService
     */
    public function __construct(DoctrineEncryptAdapter $adapter,$pluginManager)
    {
        $this->configService = $configService;
        $this->pluginManager=$pluginManager;
	
		$this->adapter=$adapter;
    }

	
    
}
