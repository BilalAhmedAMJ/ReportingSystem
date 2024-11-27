<?php


namespace Application\Form;

use Zend\Form\Form;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RemindersForm extends Form  implements ServiceLocatorAwareInterface{
    
    private $all = array(''=>'All');
    
    private $service_locator;
    
    private $user;
    
    private $offices;
    
    private $office_names;
    
    private $branches;
    
    private $send_to_filters;
    
    private $level_filters;

    private $config;   
         
    public $rule_level;

    public $user_level;
    
    private $months;
    // private $template='report_reminder';
    // private $subject;
    // private $message;
            
    //public function __construct($name='ReminderMessagesForm',$options=array() ){
    private function setupForm(){        
        //parent::__construct($name,$options);
        
        //$this->service_locator=$this->getFormFactory()->getFormElementManager()->getServiceLocator();
        
        $userSrv = $this->service_locator->get('UserProfileService');
        $this->user=$userSrv->getCurrentUser();

        $officeSrv = $this->service_locator->get('OfficeAssignmentService'); 
        
        $this->user_level = $officeSrv->getUserLevel($this->user);
        
        if($this->user_level=='halqa'||$this->user_level=='jamaat') {
            
            $this->rule_level='local';
            
        }else{
            $this->rule_level=$this->user_level;   
        }
        
        $cfgSrv = $this->service_locator->get('ConfigService');

        $this->months = $cfgSrv->getConfig('months');
        
        $this->config = $cfgSrv->getConfig('reminder_rules');
        
        
        
        if(key_exists($this->rule_level, $this->config['send_filter'])){
            $this->send_to_filters = $this->config['send_filter'][$this->rule_level];
            //add All
            //array_unshift($this->send_to_filters,$this->all);
        }

        if(key_exists($this->rule_level, $this->config['levels'])){
            $this->level_filters = $this->config['levels'][$this->rule_level];
            //add All
            //array_unshift($this->level_filters,$this->all);
        }

                
        $branches_with_office=($officeSrv->getBranchesWithOffices($this->user));
        
        $dept_ids=array();
        foreach ($branches_with_office as $key => $value) {
        	foreach($value as $dept){
        		$dept_ids[$dept]=$dept;
        	}	
        }
        $dept_ids = array_keys($dept_ids);
        
        $this->offices = $cfgSrv->listDepartmentNames( ($dept_ids) );
        
        $this->office_names=array();

        foreach ($this->offices as $key => $value) {
            $this->office_names[]=array('id'=>$key,'text'=>$value);
        }
        $all_depts = implode(',',array_unique(array_keys($this->offices)) );
        $this->office_names = array_merge(array(array('id' => $all_depts,'text' => 'All')),$this->office_names);
        
        $branch_ids=array_keys($branches_with_office);

        if($this->user_level=='halqa'||$this->user_level=='jamaat'){
                $this->branches_halqa_jamaat = $branch_ids[0];
        }

        $branch_srv = $this->service_locator->get('BranchManagementService');
        $this->branches = $branch_srv->listBranchNames($branch_ids);

        //add All
        #array_unshift($this->branches,'All');
        $this->branches = array_merge(array(implode(',',array_unique($branch_ids)) => 'All'),$this->branches);
 
       #print_r($this->branches);exit;  

        //$this->init(); 
    }
    
    public function init(){
    
        $this->setupForm();

        //Months     
        $now=getdate();   
        $date = new \DateTime("now",new \DateTimeZone("America/Toronto"));
        $date->modify("-15 days"); //Show current month after mid month otherwise show last month
        $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'month',
             'options' => array(
                     'label' => 'Month',
                     'value_options' => $this->months,                     
             ), 
            'attributes' => array(
                     'value'=>$date->format("M"),
                     'class'=>'select2',
                     'style'=>'width:250px'
                )                       
        ));

        //Office/Department        
        $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'office',
             'options' => array(
                     'label' => 'Office',
                     'value_options' => $this->office_names,
                     'label_options' =>array('disable_html_escape'=>true)
             ),           
            'attributes' => array(
                     'id'=>'office',
                     #'class'=>'select2',
                     #'style'=>'width:250px'
                     //'value'=>''
                )
        ));

        //branch and level flter is not to show for "local" level users
        if($this->rule_level!='local'){
            //Branch        
            $this->add(array(
                 'type' => 'Zend\Form\Element\Select',
                 'name' => 'branch',
                 'options' => array(
                         'label' => 'Branch',
                         'value_options' => $this->branches,
                 ),           
             'attributes' => array(
                     'id'=>'branch',
                     'class'=>'select2',
                     'style'=>'width:250px'
                )
            ));
            
            
            //Level_Rule        
            $this->add(array(
                 'type' => 'Zend\Form\Element\Select',
                 'name' => 'level_rule',
                 'options' => array(
                         'label' => 'Level',
                         'value_options' => $this->level_filters,
                 ),           
             'attributes' => array(
                     'id'=>'level_rule',
                     'class'=>'select2',
                     'value'=>'all',
                     'style'=>'width:250px'
                )           
            ));
        }
        else {
                 $this->add(array(
                        'type' => 'Zend\Form\Element\Hidden',
                        'name' => 'branch',
                        'attributes' => array(
                                'value' => $this->branches_halqa_jamaat,
                        )           
                ));
        }
        
        //Send_to_Rule        
        $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'send_to_rule',
             'options' => array(
                     'label' => 'Send to',
                     'value_options' => $this->send_to_filters,
             ),           
             'attributes' => array(
                     'id'=>'send_to_rule',
                     'class'=>'select2',
                     'value'=>'not_completed',
                     'style'=>'width:250px'
                )           
                        
        ));
        

        //custom message        
        $this->add(array(
             'type' => 'Zend\Form\Element\Checkbox',
             'name' => 'custom_message',
             'options' => array(
                     'label' => 'Custom message',
             ),           
             'attributes' => array(
                     'id'=>'custom_message',
                     'value'=>'0',
                     'class'=>'ace ace-switch-3',
                )           
        ));
        
        //message attachments        
        $this->add(array(
             'type' => 'Zend\Form\Element\File',
             'name' => 'file_attachments',
             'options' => array(
                     'label' => 'Attachments',
             ),           
             'attributes' => array(
                     'id'=>'file_attachments',
                     'value'=>'0',
                    'multiple'=>true,
                     'class'=>'file-upload'
                )           
                        
        ));


       $this->add(array(
             'type' => 'Zend\Form\Element\Text',
             'name' => 'subject',
             'options' => array(
                     'label' => 'Subject',
                     'value' => '',
             )           
       ));

       $this->add(array(
             'type' => 'Zend\Form\Element\Textarea',
             'name' => 'custom_message_body',
             'options' => array(
                     'label' => 'Custom Message',
                     'value' => '',
             )           
       ));

        
       $this->add(array(
             'type' => 'Zend\Form\Element\Hidden',
             'name' => 'user_level',
             'options' => array(
                     'label' => 'User Level',
                     'value' => $this->user_level,
             )           
       ));
        
       $this->add(array(
             'type' => 'Zend\Form\Element\Hidden',
             'name' => 'rule_level',
             'options' => array(
                     'label' => 'Rule Level',
                     'value' => $this->rule_level,
             )           
       ));
        
    }
    
    protected $formElementManager;
    public function setServiceLocator(ServiceLocatorInterface $fem)
    {
        $this->service_locator = $fem->getServiceLocator();
        $this->formElementManager=$fem;
    }

    public function getServiceLocator()
    {
        return $this->formElementManager;
    }    
    
    public function getOffices(){
        return $this->offices;
    }
    
    public function getOfficeNames(){
        return $this->office_names;
    }
    
    
    public function getBranches(){
        return $this->branches;
    }
    
}
