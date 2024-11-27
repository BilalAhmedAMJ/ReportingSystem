<?php

namespace Application\Controller\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

use Zend\Http\Headers;
use Zend\Http\Response\Stream;
use Zend\Session\Container;
 
/**
 * Provids transactional data processing helpers to controller
 * Users can either use begin/cmmit/rollback construct or 
 * provideda call backthat will be wrapped inside a transaction
 * 
 */ 
class PostTokenHelperPlugin extends AbstractPlugin{
    
    private $context;

    public function __invoke(){
      
        $controller = $this->getController();
        $request = $controller->getRequest();
        $response = $controller->getResponse();
        $uri = $request->getUri();
        $name = 'pt_'.urlencode($uri->getPath());
        $cookie = new Zend\Http\Cookie($name,
                               $uri->getHost(),
                               time() + 4*60*60,//valid for 4 hours
                               $uri->getPath());
        //save token as cookie 
        $response->getHeaders()->addHeader($cookie);
        //also save token in session
        $session = new Container('post_token');
                
      
    }

}
