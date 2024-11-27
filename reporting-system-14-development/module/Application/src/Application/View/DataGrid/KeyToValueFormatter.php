<?php
namespace Application\View\DataGrid;

use ZfcDatagrid\Column\AbstractColumn;

class KeyToValueFormatter extends \ZfcDatagrid\Column\Formatter\AbstractFormatter
{
    protected $validRenderers = array(
        'jqGrid',
        'bootstrapTable',
    );
	

    public function getFormattedValue(AbstractColumn $column)
    {
        $row = $this->getRowData();
		$col_value= $row[$column->getUniqueId()];
        $words = explode('_', $col_value);
        $words = array_map(function($v){return ucfirst($v);}, $words);
        return implode(' ', $words);
    }
    
    
}
