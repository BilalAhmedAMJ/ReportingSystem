<?php
namespace Application\View\DataGrid;

use ZfcDatagrid\Column\AbstractColumn;

class ElectionTypeFormatter extends \ZfcDatagrid\Column\Formatter\AbstractFormatter
{
    protected $validRenderers = array(
        'jqGrid',
        'bootstrapTable',
    );

    public function isApply()
    {
        return true;
    }

    private $status_mapping = array(
                                'draft'    =>"<i class='ace-icon fa fa-edit'></i> Draft",
                                'submitted'  =>"<i class='ace-icon fa fa-share-square-o'></i> Submitted",
                                'pending'  =>"<i class='ace-icon fa fa-info-circle'></i> Pending",    
                                'processed'  =>"<i class='ace-icon fa fa-check-square-o'></i> Processed"
                                );    

    public function getFormattedValue(AbstractColumn $column)
    {
        $row = $this->getRowData();
        if(key_exists($row[$column->getUniqueId()], $this->status_mapping)){
                return $this->status_mapping[$row[$column->getUniqueId()]];
        }
        
        return "<span class=''><i class='icon-warning-sign'></i>".$row[$column->getUniqueId()].'</span>';
    }
    
    
}
