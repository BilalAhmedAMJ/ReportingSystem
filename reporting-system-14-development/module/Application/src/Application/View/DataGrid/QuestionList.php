<?php

namespace Application\View\DataGrid;
 
use ZfcDatagrid\Datagrid;
use ZfcDatagrid\Column;
use ZfcDatagrid\Filter;

 
/**
 * Provids transactional data processing helpers to controller
 * Users can either use begin/cmmit/rollback construct or 
 * provideda call backthat will be wrapped inside a transaction
 * 
 */ 
class QuestionList {
    
    
    public function addColumns(DataGrid $grid){

        $custom_filters=$grid->getViewModel()->getVariable('custom_filters');
        if(! isset($custom_filters)){
            $custom_filters=array();
        }
        
        /**
         * @var \ZfcDatagrid\Column\Select
         */
        $col = new Column\Select('id','question');
        $col->setLabel('Id');
        $col->setWidth(10);
        $grid->addColumn($col);

        $col = new Column\Select('caption','question');
        $col->setLabel('Caption');
        $col->setFilterDefaultOperation(Filter::LIKE);        
        $col->setWidth(45);
        $grid->addColumn($col);

        $col = new Column\Select('department_name','department');
        $col->setLabel('Department_name');
        $col->setWidth(20);
        $dept_name_id=$col->getUniqueId();
        $depts=array();
        foreach($this->serviceLocator->get('ConfigService')->listDepartments() as $d){
            $depts[$d['department_name']]=$d['department_name'];
        }
        $col->setFilterSelectOptions($depts);
        $col->setFilterDefaultOperation(Filter::EQUAL);
        $grid->addColumn($col);

        $col = new Column\Select('question_type','question');
        $col->setLabel('Question Type');
        $col->setFilterDefaultOperation(Filter::LIKE);        
        $col->setWidth(15);
        $grid->addColumn($col);

        //$col = new Column\Select('id','parent');
        //$col->setLabel('Parnet Question');
        //$col->setWidth(10);
        //$grid->addColumn($col);

        $col = new Column\Select('sort_order','question');
        $col->setLabel('Position');
        $col->setSortDefault(true);
        $col->setWidth(10);
        $grid->addColumn($col);


        //add hidden column as hidden
        foreach (array('active_question','answer_type','details','display_config','constraints') as $value) {
            //set column as hidden
            $col = new Column\Select($value,'question');
            $col->setHidden(true);
            if ($value=='id')
                $col->setIdentity(true);
            $grid->addColumn($col);    
        }


        // $col = new Column\Select('id','department');
        // $col->setLabel('department_id');
        // $col->setHidden(true);
        // $grid->addColumn($col);


        $col = new Column\Select('report_code','reportconfig');
        $col->setLabel('report_config');
        $col->setHidden(true);
        $grid->addColumn($col);

        //Add actions columns
        $router = $this->serviceLocator->get('router');
        $base_url = $router->assemble(array(),array('name'=>'config/report-questions'));
        
        $actions = new Column\Action();
        $actions->setLabel('Actions');
        $actions->setWidth('20');

        foreach (array(array(' Edit ','edit','pencil-square-o'),array(' Delete ','remove','close')) as $value) {
            $action = new IconButton();
            $rowId = $action->getRowIdPlaceholder();
            $action->setLabel($value[0]);            
            $action->setLink($base_url.'/'.$value[1].'/' . ($rowId));
            $action->addIcon($value[2]);
            $actions->addAction($action);
        }
        
        $grid->addColumn($actions);


        if(count($custom_filters)>0)
            $grid->getViewModel()->setVariable('custom_filters',$custom_filters);
    
        //allow chaining
        return $this;
    }


    private $serviceLocator;
    
    public function setServiceLocator($sl){
        $this->serviceLocator=$sl;
    }
}
