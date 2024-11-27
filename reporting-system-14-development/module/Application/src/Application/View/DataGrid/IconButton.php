<?php
namespace Application\View\DataGrid;


use ZfcDatagrid\Column\Action\AbstractAction;


class IconButton extends AbstractAction
{
    protected $label = '';

    public function __construct()
    {
        parent::__construct();

        $this->addClass('btn btn-sm btn-minier btn-round btn-white btn-primary ');

    }

    /**
     *
     * @param string $name
     */
    public function setLabel($name)
    {
        $this->label = (string) $name;
    }

    /**
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    protected $icons_before='';
    protected $icons_after='';


    public function addIcon($iconClass){
        $this->icons_before .= '<i class="ace-icon fa fa-'.$iconClass.'"></i>';
    }

    
    /**
     *
     * @return string
     */
    protected function getHtmlType()
    {
        // if ($this->getLabel() == '') {
            // throw new \InvalidArgumentException('A label is required for this action type, please call $action->setLabel()!');
        // }
        
        return $this->icons_before . $this->getLabel() . $this->icons_after;
    }
}
