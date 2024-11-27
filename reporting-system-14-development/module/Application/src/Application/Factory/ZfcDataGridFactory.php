<?php

namespace Application\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Stdlib\Parameters;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\MvcEvent;

use ZfcDatagrid\Datagrid;


class ZfcDataGridFactory implements FactoryInterface{
    
    private $serviceLocator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       

        $this->serviceLocator = $serviceLocator;  

        return $this;
    }
    
   public function createGrid($gridName=null)
    {

        $config = $this->serviceLocator->get('config');

        if (! isset($config['ZfcDatagrid'])) {
            throw new InvalidArgumentException('Config key "ZfcDatagrid" is missing');
        }

        /* @var $application \Zend\Mvc\Application */
        $application = $this->serviceLocator->get('application');

        //default event is current event
        $event = $application->getMvcEvent();

        $grid = new Datagrid();
        $grid->setOptions($config['ZfcDatagrid']);
        $grid->setMvcEvent($event);
        $grid->setServiceLocator($this->serviceLocator);
        if ($this->serviceLocator->has('translator') === true) {
            $grid->setTranslator($this->serviceLocator->get('translator'));
        }

        
        $request = $this->serviceLocator->get('Request');
        
        $gridDefaultId = 'zfcgrid_'.md5($request->getUri()->getPath());
        if(!empty($gridName)){
            $gridDefaultId = $gridName;
        }
        //set grid id before getting session
        $grid->setId($gridName);

        //setup session if not exist
        //$session = new Container($gridDefaultId);
        $session = $grid->getSession();
        if (!$session->offsetExists('postParameters')) {
            $session->postParameters = array();
        }
        
        if ($request->getMethod() == Request::METHOD_POST) {
            $session->postParameters = $request->getPost()->toArray();
        } elseif (count($session->postParameters) > 0) {
            $request = new Request();
            $request->setPost(new Parameters($session->postParameters));
            $request->setMethod('POST');
            $mvcEvent = new MvcEvent();
            $mvcEvent->setRequest($request);
            $grid->setMvcEvent($mvcEvent);
        }
        
        $grid->init();
        return $grid;
    }    

}