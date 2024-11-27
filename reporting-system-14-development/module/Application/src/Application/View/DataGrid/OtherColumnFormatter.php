<?php
namespace Application\View\DataGrid;

use ZfcDatagrid\Column\AbstractColumn;

class OtherColumnFormatter extends \ZfcDatagrid\Column\Formatter\AbstractFormatter
{
    protected $validRenderers = array(
        'jqGrid',
        'bootstrapTable',
    );

    private $otherColName;
    public function __construct($otherColUniqueId){
        $this->otherColName=$otherColUniqueId;
        // echo('returning '.$otherColUniqueId.'<br/>');
        // exit;
        
    }
    
    public function getFormattedValue(AbstractColumn $column)
    {
        $row = $this->getRowData();
        
        echo('returning '.$row[$this->otherColName].'<br/>');
        exit;
        return $row[$this->otherColName];
    }
    
    
}
