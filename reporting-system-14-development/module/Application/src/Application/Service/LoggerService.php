<?php


namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Doctrine\ORM\Query;


class LoggerService implements FactoryInterface{
    
    private $serviceLocator;
    private $logger;
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){
    	
		if($this->logger){
			return $this->logger;
		}
		
        $this->serviceLocator = $serviceLocator;  
		
        $config = $serviceLocator->get('Config');
		
		$dir = $config['application']['log_dir'];
		
        $this->logger = new \Zend\Log\Logger();
        $writer = new \Zend\Log\Writer\Stream($dir.'/amj_reports.log');       
        $this->logger->addWriter($writer);
                    
        return $this->logger;
    }
    
    public function logger(){
        return $this->logger;
    }
}
