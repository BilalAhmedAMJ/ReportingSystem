<?php

namespace Application\View\DataGrid;
 
use ZfcDatagrid\Datagrid;
use ZfcDatagrid\Column;
use ZfcDatagrid\Filter;

 
/**
 * 
 */ 
class ElectionList {
    
    
    public function addColumns(DataGrid $grid,$options=array()){

        /**
         * @var \ZfcDatagrid\Column\Select
         */
        $width='20';
        $col = new Column\Select('branch_name','branch');
        $col->setLabel('Branch Name');
        $col->setFilterDefaultOperation(Filter::LIKE);
        $col->setWidth($width);
        $grid->addColumn($col);
		
        $width='20';
        $col = new Column\Select('display_name','modified_by');
        $col->setLabel('Modified By');
        $col->setUserFilterDisabled(true);
        $col->setFormatter(new EncryptedFieldFormatter($options['adapter']));		
        $col->setWidth($width);
        $grid->addColumn($col);

        $width='13';
        $col = new Column\Select('date_modified','election');
        $col->setLabel('Modified');
        $col->setUserFilterDisabled(true);
		$colType = new Column\Type\DateTime( 'Y-m-d H:i:s', \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
		$col->setType($colType);
		
        $col->setWidth($width);
        $grid->addColumn($col);            


	$width='10';
        //completed count
         $col = new Column\Select("COUNTIF(election_reports.report_status,'completed')");
         $col->setLabel('Completed');
         $col->setUniqueId('election_completed_count');
         $col->setUserFilterDisabled(true);
         $col->setUserSortDisabled(true);        
         $col->setWidth(6);
         $grid->addColumn($col);    




        if(key_exists('processed_count', $options)){
	        $col = new Column\Select('id','election');
	        $col->setLabel('Processed');
			$col->setUniqueId('election_processed_count');
			$col->setUserFilterDisabled(true);
			$col->setUserSortDisabled(true);
	        $col->setFormatter(new ElectionCountFormatter($options['processed_count']));
	        $col->setWidth($width);
	        $grid->addColumn($col);    
        }

        $col = new Column\Select('election_type','election');
        $col->setLabel('Type');
        $col->setFormatter(new KeyToValueFormatter());
        $col->setWidth($width);
        $col->setFilterSelectOptions(array(
                                "office_bearer"=>'Office Bearers Election',
                                 "shura"=>'Shura Delegates Election',
                                 "majlis_intikhab"=>'Majlis Intikhab Election'
                                ));
        $grid->addColumn($col);            

			        
        $col = new Column\Select('election_status','election');
        $col->setLabel('Status');
        $col->setWidth(10);
        $col->setFilterDefaultOperation(Filter::NOT_EQUAL);
	$col->setFilterDefaultValue('completed');
        $config = $grid->getServiceLocator()->get('ConfigService')->getConfigValues('election_config');
        $election_status = $config['election_status'];
		
        $types_array = array();
        foreach($election_status as $type){
            $types_array[$type['id']]=$type['label'];
        }
        $col->setFilterSelectOptions($types_array);
        $col->setFilterDefaultOperation(Filter::EQUAL);
        $col->setFormatter(new ElectionTypeFormatter());
        $grid->addColumn($col);

        //$custom_filters[$col->getUniqueId()]='document_type';

        //Add actions columns
        $actions = new Column\Action();
        $actions->setLabel('Actions');
        $actions->setWidth($width);
        $grid->addColumn($actions);
        
        $viewAction = new IconButton();
        $viewAction->setLabel(' Open ');
        $viewAction->addIcon('pencil');
        $rowId = $viewAction->getRowIdPlaceholder();
        $router = $this->serviceLocator->get('router');        
        $base_url = $router->assemble(array(),array('name'=>'election'));        
        $viewAction->setLink($base_url.'/result?id=' . ($rowId));
        $actions->addAction($viewAction);

        $printAction = new IconButton();
        $printAction->setLabel(' ');
        $printAction->addIcon('print');
		$printAction->setAttribute('target','_blank');
        $printAction->setLink($base_url.'/print?id=' . ($rowId));

        $actions->addAction($printAction);

        if(key_exists('admin', $options) && $options['admin']){
            $deleteAction = new IconButton();
            $deleteAction->setLabel(' ');
            $deleteAction->addIcon('remove');
            $deleteAction->setAttribute('target','_blank');
            $deleteAction->setLink($base_url.'/delete?id=' . ($rowId));
    
            $actions->addAction($deleteAction);
        }
        //set  id column as hidden
        $col = new Column\Select('id','election');
        $col->setIdentity(true);
        $col->setHidden(true);
		$col->setWidth('0');
        $grid->addColumn($col);    

        //Add Row Click Action
        $rowAction = new \ZfcDatagrid\Column\Action\Button();
        $rowId = $rowAction->getRowIdPlaceholder();
        $rowAction->setLink($viewAction->getLink());
        $grid->setRowClickAction($rowAction);
            
        //allow chaining
        return $this;
    }


    private $serviceLocator;
    
    public function setServiceLocator($sl){
        $this->serviceLocator=$sl;
    }
}
