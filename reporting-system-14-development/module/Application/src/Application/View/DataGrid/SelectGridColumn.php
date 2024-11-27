<?php

namespace Application\View\DataGrid;
 
use ZfcDatagrid\Datagrid;
use ZfcDatagrid\Column;
use ZfcDatagrid\Filter;

use ZfcDatagrid\Column\Select;
use IntlDateFormatter;

use ZfcDatagrid\Column\Type\DateTime;
 
/**
 * Provids transactional data processing helpers to controller
 * Users can either use begin/cmmit/rollback construct or 
 * provideda call backthat will be wrapped inside a transaction
 * 
 */ 
class SelectGridColumn extends  Select{
    
    public function unsetFilterDefaultValue(){
        unset($this->filterDefaultValue);
    }

}
