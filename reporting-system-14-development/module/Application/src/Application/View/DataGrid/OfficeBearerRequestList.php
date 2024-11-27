<?php

namespace Application\View\DataGrid;

 
use ZfcDatagrid\Datagrid;
use ZfcDatagrid\Column;
use ZfcDatagrid\Filter;
use ZfcDatagrid\Column\Type\DateTime;
 
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
 
 
/**
 * Provids transactional data processing helpers to controller
 * Users can either use begin/cmmit/rollback construct or 
 * provideda call backthat will be wrapped inside a transaction
 * 
 */ 
class OfficeBearerRequestList implements ServiceLocatorAwareInterface{
    
    private $options;
    private $decryptor;
    
    public function  __construct($options){
        
       if(!is_array($options)){
           //must be array
           throw new \Exception('Must be array');
       }

       $this->decryptor = new EncryptedFieldFormatter($options['adapter']);
       
       $this->options=$options; 
    }
    
    public function addColumns(DataGrid $grid){


        $custom_filters=$grid->getViewModel()->getVariable('custom_filters');
        if(! isset($custom_filters)){
            $custom_filters=array();
        }

        $custom_classes=$grid->getViewModel()->getVariable('custom_classes');
        if(! isset($custom_classes)){
            $custom_classes=array();
        }        
                     

        $col = new SelectGridColumn('branch_name','branch');
        $col->setLabel('Branch');
        $col->setWidth('20');
        $col->setFilterDefaultOperation(Filter::EQUAL);
        $col->setSortDefault(1, 'ASC');
        $grid->addColumn($col);
        $custom_filters[$col->getUniqueId()]='branch_list';

        $col = new SelectGridColumn('department_name','department');
        $col->setLabel('Department');
        $col->setWidth('20');
        $col->setFilterDefaultOperation(Filter::EQUAL);
        $col->setSortDefault(2, 'ASC');
        $grid->addColumn($col);
        $custom_filters[$col->getUniqueId()]='department_list';
    
        //'Phone'=>'phone_primary'
        foreach (array('Membercode'=>'member_code','Name'=>'full_name','Email'=>'email') as $col_name=>$fld) {
            $col = new SelectGridColumn($fld,'request');
            $col->setLabel($col_name);
            $col->setFormatter($this->decryptor);
            $col->setWidth('20');
            $col->setUserFilterDisabled(true);
            $grid->addColumn($col);
        }

            $dateTimeType = new DateTime();
    
            $dateTimeType->setSourceDateTimeFormat('Y-m-d');
        
            $col = new SelectGridColumn('date_requested','request');
            $col->setLabel('Requested');
            //$col->setFormatter($this->decryptor);
            $col->setWidth('20');
            $col->setUserFilterDisabled(true);
            $col->setType($dateTimeType);
            $grid->addColumn($col);

            $col = new SelectGridColumn('date_processed','request');
            $col->setLabel('Processed');
            //$col->setFormatter($this->decryptor);
            $col->setWidth('20');
            $col->setUserFilterDisabled(true);
            $col->setType($dateTimeType);
            $grid->addColumn($col);
    
    
        $viewAction = new IconButton();
        $viewAction->setLabel('Edit');
        $viewAction->addIcon('pencil-o');
        $rowId = $viewAction->getRowIdPlaceholder();

        $router = $this->serviceLocator->get('router');            
        $base_url = $router->assemble(array(),array('name'=>'office-assignment/request'));          
        
        //$viewAction->setLink("${base_url}?request_action=edit&request_id=${rowId}");
        $viewAction->setLink("javascript:actionsFunction(${rowId})");
        
        $actions = new Column\Action();
        $actions->setLabel('');
        $actions->addAction($viewAction);
        
        $grid->addColumn($actions);

        //set  id column as hidden
        $col = new SelectGridColumn('id','request');
        $col->setIdentity(true);
        $col->setHidden(true);
        $grid->addColumn($col);
        
        
        $grid->getViewModel()->setVariable('custom_filters',$custom_filters);
        $grid->getViewModel()->setVariable('custom_classes',$custom_classes);
                
        return $this;
    }


    private $serviceLocator;
    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator){
        $this->serviceLocator=$serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator(){
        $this->serviceLocator;
    }
    
}
