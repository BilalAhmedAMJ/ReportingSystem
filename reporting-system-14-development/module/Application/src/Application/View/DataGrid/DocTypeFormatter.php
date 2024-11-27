<?php
namespace Application\View\DataGrid;

use ZfcDatagrid\Column\AbstractColumn;

class DocTypeFormatter extends \ZfcDatagrid\Column\Formatter\AbstractFormatter
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
                                'form'    =>"<i class='ace-icon fa fa-list-alt'></i> Form",
                                'procedures'  =>"<i class='ace-icon fa fa-check'></i> Procedures",
                                'policy'  =>"<i class='ace-icon fa fa-legal'></i> Policy",    
                                'other'  =>"<i class='ace-icon fa fa-file-o'></i> Other",
                                'reports'  =>"<i class='ace-icon fa fa-newspaper-o'></i>Reports",
                                'data_file'  =>"<i class='ace-icon fa fa-table'></i> Data File",
                                'memo'  =>"<i class='ace-icon fa fa-file-text-o'></i> Memo",
                                'presentation'  =>"<i class='ace-icon fa fa-photo'></i> Presentation",
                                'media'  =>"<i class='ace-icon fa fa-image-o'></i>Media",
                                'election_lists'  =>"<i class='ace-icon fa fa-list'></i> Election Lists",
                                'tajneed_lists'  =>"<i class='ace-icon fa fa-list'></i> Tajneed Lists",
                                'tajnid_lists'  =>"<i class='ace-icon fa fa-list'></i> Tajneed Lists",
                                'mal_report'  =>"<i class='ace-icon fa fa-list'></i> Mal Report",
                                'system_data'  =>"<i class='ace-icon fa fa-list'></i> System Data",
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
