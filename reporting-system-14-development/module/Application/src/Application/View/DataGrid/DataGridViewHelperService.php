<?php


namespace Application\View\DataGrid;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Doctrine\ORM\Query;


class DataGridViewHelperService implements FactoryInterface{
    
    private $serviceLocator;
    private $grid_helpers=array();
    
    const NAME_SPACE_PREFIX='Application\View\DataGrid\\';
    
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
    
    public function grid($gridName,$options=array()){
        
        $class_name=$this::NAME_SPACE_PREFIX.$gridName;
        
        if(key_exists($gridName,$this->grid_helpers)){
                
            return $this->grid_helpers[$gridName];
            
        }elseif (class_exists($class_name)) {
            
            $helper =  new  $class_name($options);
            if(method_exists($helper, 'setServiceLocator')){
                $helper->setServiceLocator($this->serviceLocator);
            }
            $this->grid_helpers[$gridName] = $helper;
            
            return $this->grid_helpers[$gridName];

        }else{
            throw new \Exception('Grid Helper with name '.$gridName.' is not defined!');
        }
    }
    
    public function filtersFromCache($grid,$request){
        //apply filters from cache
        $cache = $grid->getCache();
        $cacheData = $cache->getItem($grid->getCacheId()) ;

        $this_page = $request->getUri()->getPath();
        $referer =   $request->getHeader('Referer')?$request->getHeader('Referer')->uri()->getPath():'';
        $print_view = $request->getQuery('view') &&  $request->getQuery('view') =='print';

        //only if no filters are given from user in this request 
        // and this request was not from same page
        // or this is a print request
        if($cacheData && key_exists('filters', $cacheData) ){
            $filters = $grid->getRenderer()->getFilters();

            if ( ( !($filters) || count($filters)<1) && ($this_page!=$referer || $print_view)){
                
                $grid->getRenderer()->setFilters($cacheData['filters']);

                 foreach ($cacheData['filters'] as $filter) {
                     $grid->getColumnByUniqueId($filter->getColumn()->getUniqueId())->setFilterActive($filter->getDisplayColumnValue());
                 }
            }
        }
    }
    
    
    
}
