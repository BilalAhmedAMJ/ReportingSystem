<?php
namespace Application\View\DataGrid;

use ZfcDatagrid\Column\AbstractColumn;

class DepartmentFormatter extends \ZfcDatagrid\Column\Formatter\AbstractFormatter
{
    
    protected $validRenderers = array(
        'jqGrid',
        'bootstrapTable',
    );
	
	private $branch_head_title_col;
    
	public function __construct($branch_head_title_col){
		$this->branch_head_title_col=$branch_head_title_col;
	}

    
    public function getFormattedValue(AbstractColumn $column)
    {
        $row = $this->getRowData();
		$dept_name = $row[$column->getUniqueId()];
        $branch_head_title = $row[$this->branch_head_title_col->getUniqueId()];
        
        if($dept_name === 'President'){
				return $branch_head_title;
        }
        return $dept_name;
    }
    
    
}
