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



/**
 * Adds more functions to ease usage of naviations and menus 
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class SmartDateTimeFormatHelper extends AbstractHelper
{
    
    
    public function __invoke($date){
        return $this::smartFormat($date);
    }


   public static function smartFormat($date,$full=false){
      $now = new \DateTime();
      $today = clone $now;
      $today->setTime(0,0,0); 
      $date_is_today=false;
      if($today < ($date)){
          $date_is_today=true;
      }
      
      $date_is_yesterday=false;
      $yesterday = clone $today;
      $yesterday = $yesterday->modify('-1 day');
      if($yesterday < $date){
          $date_is_yesterday=true;
      }
      
      $date_in_one_week=false;
      $one_week= clone $yesterday;
      $one_week->modify('-5 days');
      if($one_week < $date){
          $date_in_one_week=true;
      }
      
      $time = $date->format('h:i a');
      
      if($date_is_today){
          return ($full?'Today, ':'').$date->format('h:i a');
      }elseif($date_is_yesterday){
          return 'Yesterday'.($full?', '.$time:'');
      }elseif($date_in_one_week){
                    
          return $date->format('D').' '.$time;
      }else{
          return $date->format('M j').' '.$time;
      }
  }}