<?php
namespace Application\View\DataGrid;

use ZfcDatagrid\Column\AbstractColumn;

class EncryptedFieldFormatter extends \ZfcDatagrid\Column\Formatter\AbstractFormatter
{
    
    protected $validRenderers = array(
        'jqGrid',
        'bootstrapTable',
    );
	
	private $adapter;
    
	public function __construct($adapter){
		$this->adapter=$adapter;
	}

    
    public function getFormattedValue(AbstractColumn $column)
    {
        $row = $this->getRowData();
		$field_value = $row[$column->getUniqueId()];
        
        return $this->adapter->decrypt($field_value);
    }
    
    
}
