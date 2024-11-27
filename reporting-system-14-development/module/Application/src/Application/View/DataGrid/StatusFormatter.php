<?php
namespace Application\View\DataGrid;

use ZfcDatagrid\Column\AbstractColumn;

class StatusFormatter extends \ZfcDatagrid\Column\Formatter\AbstractFormatter
{
    protected $validRenderers = array(
        'jqGrid',
        'bootstrapTable',
    );
	
	private $fixed_width='status-label';
	public function __construct($fixed_width=true){
		$this->fixed_width=($fixed_width)?"status-label":"";
	}

    private $status_mapping = array(
                                'active'    =>"<span class=' %s label label-lg label-success'    ><i class='ace-icon fa fa-check-square'></i> Active</span>",
                                'approved'  =>"<span class=' %s label label-lg label-success'    ><i class='ace-icon fa fa-bookmark-o'></i> Approved</span>",
                                'completed' =>"<span class=' %s label label-lg label-orange'       ><i class='ace-icon fa fa-bell'></i> Completed</span>",
                                'deleted'   =>"<span class=' %s label label-lg label-waring'     >Deleted</span>",
                                'denied'    =>"<span class=' %s label label-lg label-danger'     >Denied</span>",
                                'disabled'  =>"<span class=' %s label label-lg label-grey'       >Disabled</span>",
                                'draft'     =>"<span class=' %s label label-lg label-inverse'       ><i class='ace-icon fa fa-pencil-square-o'></i> Draft</span>",
                                'expired'   =>"<span class=' %s label label-lg label-warning'    >Expired</span>",
                                'former'    =>"<span class=' %s label label-lg label-grey'       >Former</span>",
                                'locked'    =>"<span class=' %s label label-lg label-yellow'     >Locked</span>",
                                'pending'   =>"<span class=' %s label label-lg label-yellow'     >Pending</span>",
                                'received'  =>"<span class=' %s label label-lg label-success'    ><i class='ace-icon fa fa-check-square'></i> Received</span>",
                                'requested' =>"<span class=' %s label label-lg label-info'       >Requested</span>",
                                'verified'  =>"<span class=' %s label label-lg label-info'><i class='ace-icon fa fa-bookmark'></i> Verified</span>",    
                                );    

    public function getFormattedValue(AbstractColumn $column)
    {
        $row = $this->getRowData();
		$status_value= $row[$column->getUniqueId()];
        if(key_exists($status_value, $this->status_mapping)){
                $status1 = $this->status_mapping[$status_value];
                $status=sprintf($status1,$this->fixed_width);				
				return $status;
        }
        
        return sprintf("<span class=' %s label label-lg label-danger'><i class='icon-warning-sign'></i>",$this->fix_width).$row[$column->getUniqueId()].'</span>';
    }
    
    
}
