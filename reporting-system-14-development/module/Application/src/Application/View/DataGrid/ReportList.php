<?php

namespace Application\View\DataGrid;
 
use ZfcDatagrid\Datagrid;
use ZfcDatagrid\Column;
use ZfcDatagrid\Filter;

use IntlDateFormatter;

use ZfcDatagrid\Column\Type\DateTime;
 
/**
 * Provids transactional data processing helpers to controller
 * Users can either use begin/cmmit/rollback construct or 
 * provideda call backthat will be wrapped inside a transaction
 * 
 */ 
class ReportList {
    
	private $options;
	public function  __construct($options){
        
       if(!is_array($options)){
           //must be array
           throw new \Exception('Must be array');
       }
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
        
        /**
         * @var \ZfcDatagrid\Column\Select
         */
        $col = new Column\Select('period_code','period_from');
        $col->setLabel('Month');
        $col->setFilterDefaultOperation(Filter::LIKE);
        $col->setWidth('20%');
        $grid->addColumn($col);
        
        $custom_filters[$col->getUniqueId()]='period_list';
        
        $col = new Column\Select('year_code','period_from');
        $col->setLabel('YearCoode');
        $col->setFilterDefaultOperation(Filter::EQUAL);
        $col->setHidden(true);
        $col->setWidth('0%');
        $grid->addColumn($col);
        
        $custom_filters[$col->getUniqueId()]='year_code';
        

        $col = new Column\Select('period_end','period_to');
        $type=new \ZfcDatagrid\Column\Type\DateTime();
        $col->setType($type);
        $col->setHidden(true);
        $col->setWidth('0%');
        $col->setSortDefault(1,'DESC');
        $grid->addColumn($col);


        $col = new Column\Select('branch_name','branch');
        $col->setLabel('Branch');
        $col->setFilterDefaultOperation(Filter::EQUAL);
        $col->setSortDefault(2,'ASC');
        $col->setWidth('25%');
		$col->setFilterSelectOptions($this->options['branches']);

        $grid->addColumn($col);
        //$custom_filters[$col->getUniqueId()]='branch_list';

        

        $col = new Column\Select('department_name','department');
        $col->setLabel('Department');
        $col->setFilterDefaultOperation(Filter::EQUAL);
        $col->setSortDefault(3,'ASC');
        $col->setWidth('25%');
		$col->setFilterSelectOptions($this->options['departments']);

        $grid->addColumn($col);
        //$custom_filters[$col->getUniqueId()]='department_list';		

        $col = new Column\Select('date_modified','report');
        $col->setLabel('Updated');
        $col->setUserFilterDisabled(true);
        //$col->setFilterDefaultOperation(Filter::EQUAL);
        //$col->setFormatter(new StatusFormatter());
        $col->setType(new DateTime('Y-m-d H:i:s',IntlDateFormatter::MEDIUM,IntlDateFormatter::MEDIUM));
        $col->setWidth('20%');
        $grid->addColumn($col);    
        //$custom_filters[$col->getUniqueId()]='date_modified';

        $col = new Column\Select('date_verified','report');
        $col->setLabel('Verified');
        $col->setUserFilterDisabled(true);
        //$col->setFilterDefaultOperation(Filter::EQUAL);
        //$col->setFormatter(new StatusFormatter());
        $col->setType(new DateTime('Y-m-d',IntlDateFormatter::MEDIUM));
        $col->setWidth('20%');
        $grid->addColumn($col);    

        $col = new Column\Select('status','report');
        $col->setLabel('Status');
        $col->setFilterDefaultOperation(Filter::EQUAL);
        $col->setFormatter(new StatusFormatter());
        $col->setWidth('5%');
        $grid->addColumn($col);    
        $custom_filters[$col->getUniqueId()]='report_status';


        $col = new Column\Select('branch_level','branch');
        $col->setLabel('Level');
        $col->setFilterDefaultOperation(Filter::EQUAL);
        $col->setUserFilterDisabled(true);
        $level=array('','Halqa','Jama\'at','Imarat');
        $col->setHidden(true);
        $col->setFilterSelectOptions(array_combine($level, $level));

        $grid->addColumn($col);    


        //set report id column as hidden
        $col = new Column\Select('id','report');
        $col->setIdentity(true);
        $col->setHidden(true);
        $grid->addColumn($col);    

        $grid->getViewModel()->setVariable('custom_filters',$custom_filters);
		$grid->getViewModel()->setVariable('custom_classes',$custom_classes);    
        //allow chaining
        return $this;
    }

}
