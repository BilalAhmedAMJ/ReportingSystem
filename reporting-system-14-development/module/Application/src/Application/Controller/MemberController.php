<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Crypt\Password\Bcrypt as Bcrypt;

class MemberController extends AbstractActionController
{

    public function indexAction()
    {
        $view =  new ViewModel(array('caller'=>' index' ));
		
		
		
		return $view;
    }

    public function manageAction()
    {
        $view =  new ViewModel(array('caller'=>' index' ));
        
        
        
        return $view;
    }


    public function listAction()
    {
        $q='';
        foreach (($this->getRequest()->getQuery()) as $key => $value) {
            $q="$q; $key => $value ";
            
        }
        $view =  new ViewModel(array('caller'=>' list [' . $q .']' ));

        //$view->getTemplate('application/document/index');

        return $view;
    }

  

}
