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
class ReportPeriodHelper extends AbstractHelper
{
    /**
     * @var Authorize
     */
    protected $authorizeService;
	protected $pluginManager;
    protected $configService;
    protected $serviceLocator;
    
    /**
     * @param Authorize $authorizeService
     */
    public function __construct($currentUser,$configService,$pluginManager)
    {
        $this->currentUser = $currentUser;
        $this->configService=$configService;
		$this->pluginManager=$pluginManager;
        $this->serviceLocator=$pluginManager->getServiceLocator();
    }

    public function reportPeriodSelect($name,$filter=''){
            
        $data = $this->getPeriods($filter);
        $select = new \Zend\Form\Element\Select();
        $select->setValueOptions($data);
        $select->setName($name);
        $select->setAttribute('id',$name);
        
        if($filter && strtolower($filter) != 'all' && strtolower($filter) != 'last'){
            $select->setValue($filter);    
        }
        
        return $select;
    }

    private function getPeriods($filter){
        
      $from = new \DateTime('NOW');
      if($filter && strtolower($filter)=='all'){
          $from=new \DateTime(\Application\Entity\Period::START_PERIOD);
          $past='';
      }elseif($filter && strtolower($filter)=='last'){
          $from->modify('-1 month');
      }elseif($filter && preg_match('/[a-zA-Z]{3}-[0-9]{3}/', $filter)){
          $srv = $this->serviceLocator->get('EntityService');
          $period = $srv->getObject('Period',$filter);
          if($period){
              $from=$period->getPeriodStart();
              $from->modify('-1 month');    
          }
      }
      
      $config=$this->serviceLocator->get('ConfigService');

      $periods=$config->getPeriods($from,$filter,'asc');            
      $data = array();
      foreach ($periods as $period) {
          $data[$period->getPeriodCode()]=$period->getPeriodCode();
      }
      return $data;           
    }    

    public function __invoke($name,$filters=''){
        return $this::reportPeriodSelect($name,$filters);
    }
    
}
