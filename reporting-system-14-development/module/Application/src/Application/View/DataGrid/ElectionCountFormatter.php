<?php
namespace Application\View\DataGrid;

use ZfcDatagrid\Column\AbstractColumn;

use ReflectionObject;
use ReflectionMethod;
use ReflectionClass;


class ElectionCountFormatter extends \ZfcDatagrid\Column\Formatter\AbstractFormatter
{
	private $options;

	public function __construct($options){
		$this->options=$options;
	}

    public function isApply()
    {
        return true;
    }

    public function getFormattedValue(AbstractColumn $column)
    {
        $row = $this->getRowData();
		$id = $row[$column->getUniqueId()];
		
		$service=$this->options['service'];
		
		$reflection = new ReflectionObject($service);
		
		$reflectionMethod = $reflection->getMethod($this->options['method']);
		
        $values = $reflectionMethod->invoke($service,$id,$this->options['arg']);
        
        return '<span style="padding-left:35px;">'.count($values).'</span>';
    }
    
    
}
