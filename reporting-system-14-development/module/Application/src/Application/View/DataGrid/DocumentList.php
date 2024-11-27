<?php

namespace Application\View\DataGrid;
 
use ZfcDatagrid\Datagrid;
use ZfcDatagrid\Column;
use ZfcDatagrid\Filter;
use ZfcDatagrid\Column\Type\DateTime as ColDateTime;
 
/**
 * Provids transactional data processing helpers to controller
 * Users can either use begin/cmmit/rollback construct or 
 * provideda call backthat will be wrapped inside a transaction
 * 
 */ 
class DocumentList {
    
   public function  __construct($options){
       if(is_array($options)){
	   $this->decryptor = new EncryptedFieldFormatter($options['adapter']);
       }
   }
   

 
    public function addColumns(DataGrid $grid,$edit=false,$branch=false){

        // $custom_filters=$grid->getViewModel()->getVariable('custom_filters');
        // if(! isset($custom_filters)){
            // $custom_filters=array();
        // }
        
        /**
         * @var \ZfcDatagrid\Column\Select
         */
         $width='60';
         if($branch){
             $width='20';
         }
        $col = new Column\Select('title','document');
        $col->setLabel('Document Name');
        $col->setFilterDefaultOperation(Filter::LIKE);
        $col->setWidth($width);
        $grid->addColumn($col);
        $filter='';
        
        if($branch){
            $filter='?filter=uploaded';
            $col = new Column\Select('branch_name','branch');
            $col->setLabel('Branch Name');
            $col->setFilterDefaultOperation(Filter::LIKE);
            $col->setWidth($width);
            $grid->addColumn($col);
        }        
                
        
        
        
        $col = new Column\Select('document_type','document');
        $col->setLabel('Type');
        //$col->setFilterDefaultOperation(Filter::LIKE);
        $types = $grid->getServiceLocator()->get('ConfigService')->getConfigValues('document_type');
        $upload_types = $grid->getServiceLocator()->get('ConfigService')->getConfigValues('document_upload_types');
        $types_array = array();
        foreach($types as $type){
            if(array_key_exists('id', $type) && (!$branch || in_array($type['id'],$upload_types)))
                $types_array[$type['id']]=$type['label'];
        }
        $col->setFilterSelectOptions($types_array);
        $col->setFilterDefaultOperation(Filter::EQUAL);
        $col->setFormatter(new DocTypeFormatter());
        $col->setWidth('15');
        $document_type_col=$col;
        $grid->addColumn($col);


        if($branch){
            $col = new Column\Select('date_modified','document');
            $col->setLabel('Date');

            $colType = new ColDateTime(
                    'Y-m-d H:i:s',
                    \IntlDateFormatter::MEDIUM,
                    \IntlDateFormatter::MEDIUM
                );
            $col->setType($colType);
            $col->setWidth($width);
            $grid->addColumn($col);


           $col = new SelectGridColumn('display_name','created_by');
           $col->setLabel('Uloaded By');
           $col->setFormatter($this->decryptor);
           $col->setWidth('20');
           $grid->addColumn($col);
            
        }



        //$custom_filters[$col->getUniqueId()]='document_type';

        //Add actions columns
        $actions = new Column\Action();
        $actions->setLabel('Actions');
        $actions->setWidth('15');
        $grid->addColumn($actions);
        
        $viewAction = new IconButton();
        $viewAction->setLabel(' Download ');
        $viewAction->addIcon('floppy-o');
        $rowId = $viewAction->getRowIdPlaceholder();
        $router = $this->serviceLocator->get('router');        
        $base_url = $router->assemble(array(),array('name'=>'document'));        
        $viewAction->setLink($base_url.'/download/' . ($rowId).$filter);
        $actions->addAction($viewAction);

        if($edit){

            $editAction = new IconButton();
            $editAction->setLabel(' Delete ');
            $editAction->addIcon('remove');
            //$editAction->setLink($base_url.'/edit/' . ($rowId).$filter);        
            $editAction->setLink('javascript:deleteDocument('.($rowId).');');        
            $actions->addAction($editAction);
            
        }        

        //set  id column as hidden
        $col = new Column\Select('id','document');
        $col->setIdentity(true);
        $col->setHidden(true);
        $col->setWidth($width);
        $grid->addColumn($col);    

        //$grid->getViewModel()->setVariable('custom_filters',$custom_filters);

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
