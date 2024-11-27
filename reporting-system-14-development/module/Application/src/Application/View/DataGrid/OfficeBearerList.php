<?php

namespace Application\View\DataGrid;

 
use ZfcDatagrid\Datagrid;
use ZfcDatagrid\Column;
use ZfcDatagrid\Filter;
use ZfcDatagrid\Column\Type\PhpString;
use ZfcDatagrid\Column\Select;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
 
 
/**
 * Provids transactional data processing helpers to controller
 * Users can either use begin/cmmit/rollback construct or 
 * provideda call backthat will be wrapped inside a transaction
 * 
 */ 
class OfficeBearerList implements ServiceLocatorAwareInterface{
    
    private $options;
    private $decryptor;
    
    public function  __construct($options){
       if(!is_array($options)){
           //must be array
           throw new \Exception('Must be array');
       }
       //add default options
       if(!key_exists('branch', $options)){
           $options['branch']='branch';           
        }
       if(!key_exists('department', $options)){
           $options['department']='department';
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

        //branch head title hidden
        $branch_head_title_col = new SelectGridColumn('branch_head_title',$this->options['branch']);
        $branch_head_title_col->setHidden(true);
        $grid->addColumn($branch_head_title_col);

        $col = new SelectGridColumn('branch_name',$this->options['branch']);
        $col->setLabel('Branch');
        $col->setWidth('20');
        $col->setFilterDefaultOperation(Filter::EQUAL);
        $col->setSortDefault(1, 'ASC');
        $grid->addColumn($col);
        $custom_filters[$col->getUniqueId()]='branch_list';

        $col = new SelectGridColumn('branch_name','pbranch');
        $col->setLabel('Reports To');
        $col->setWidth('20');
        $col->setSortDefault(1, 'ASC');
        $grid->addColumn($col);

        $col = new SelectGridColumn('sort_order','department');
        $col->setLabel('DepartmentSortOrder');
        $col->setSortDefault(2, 'ASC');
        $col->setUserFilterDisabled(true);
        $col->setHidden(true);
        $grid->addColumn($col);


        $col = new SelectGridColumn('department_name','department');
        $col->setLabel('Department');
        $col->setWidth('20');
        $col->setFilterDefaultOperation(Filter::EQUAL);
        $col->setFormatter(new DepartmentFormatter($branch_head_title_col));
        $grid->addColumn($col);
        $custom_filters[$col->getUniqueId()]='department_list';

        $col = new SelectGridColumn('display_name','user');
        $col->setLabel('Name');
        $col->setFormatter($this->decryptor);
        $col->setUserFilterDisabled(true);
        $col->setWidth('20');
        $grid->addColumn($col);


        if(key_exists('fields',$this->options) && $this->options['fields']=='view'){
            
            $col = new SelectGridColumn('phone_primary','user');
            $col->setLabel('Phone');
            $col->setWidth('10');
            $col->setFormatter($this->decryptor);
            $col->setUserFilterDisabled(true);
            $grid->addColumn($col);
    
            $col = new SelectGridColumn('email_address','user');
            $col->setLabel('Email');
            $col->setWidth('30');
            $col->setFormatter($this->decryptor);
            $col->setUserFilterDisabled(true);
            $grid->addColumn($col);
        }

        // $col = new SelectGridColumn('username','user');
        // $col->setLabel('Username');
        // $grid->addColumn($col);

        // $col = new SelectGridColumn('period_code','period_to');
        // $col->setLabel('Expires');
        // $col->setUserFilterDisabled(true);
        // $grid->addColumn($col);

        if(key_exists('fields',$this->options) && $this->options['fields']=='edit'){

            $col = new SelectGridColumn('username_id','user');
            $col->setLabel('UserId');
            $col->setWidth('8');
            $col->setFormatter($this->decryptor);
            $col->setUserFilterDisabled(true);
            $grid->addColumn($col);
    
            
            $col = new SelectGridColumn('status','office');
            $col->setLabel('Status');
            $col->setFilterDefaultOperation(Filter::EQUAL);
            $col->setFormatter(new StatusFormatter(false));//not fixed width label      
            
            $col->setFilterSelectOptions(array('active'=>'Active','disabled'=>'Disabled'));
            $col->setFilterDefaultValue('active');
            
            $custom_classes[$col->getUniqueId()]='hidden-print';
            $grid->addColumn($col);

            if(key_exists('edit',$this->options) && $this->options['edit']){
    
                $viewAction = new IconButton();
                $viewAction->setLabel('Edit');
                $viewAction->addIcon('pencil-o');
                $rowId = $viewAction->getRowIdPlaceholder();
    
                $router = $this->serviceLocator->get('router');            
                $base_url = $router->assemble(array(),array('name'=>'office-assignment/update'));          
                
                $viewAction->setLink("javascript:dialogForm('${base_url}?id=${rowId}')");
                
                $actions = new Column\Action();
                $actions->setLabel('');
                $actions->addAction($viewAction);
                
                $grid->addColumn($actions);
                
            }

        }

        // $col = new Select('logins');
        // $col->setLabel('Logins');
        // $col->setUserFilterDisabled(true);
        // $col->setWidth('20');
        // $col->setType(new PhpString());        
        // $grid->addColumn($col);
        
        //set  id column as hidden
        $col = new SelectGridColumn('id','office');
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
