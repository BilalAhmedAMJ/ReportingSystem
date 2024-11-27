<?php

namespace Application\Form;

use Zend\Form\Fieldset;

use Zend\Form\FormInterface;


/**
 * This is a filedset that is used for election proposals.
 * 
 */
class ElectionProposalFieldset extends Fieldset{

	private $yes_no_list=array(''=>'','Yes'=>'Yes','No'=>'No');
	private $yes_no_commit_list=array(''=>'','Yes'=>'Yes','No'=>'No','Commits'=>'Commits');

	public function __construct($name='election_proposals',$options=array() ){
		
		parent::__construct($name);
		
        $this->add(array(
        	'name'=>'id',
            'type'=>'Hidden'  
		));

        $this->add(array(
        	'name'=>'election_report',
            'type'=>'Hidden'
		));
        $this->add(array(
        	'name'=>'election_report_id',
            'type'=>'Hidden'
		));
		
        $this->add(array(        
            'type'=>'Text',
            'name'=>'proposal_number',            
            'options'=>array('readonly' => 'true','should_create_template' => true),
            'attributes'=>array('style'=>'width:100%!important','readonly'=>'true')
		));

        $this->add(array(
        	'name'=>'member_code',        
            'type'=>'Number',
            'attributes'=>array('style'=>'width:55px!important')
			,'options'=>array('should_create_template' => true)
		));

        $this->add(array(
        	'name'=>'proposed_name',
            'type'=>'Text'    
            ,'attributes'=>array('style'=>'width:100%!important','class'=>'proposed_name_intput')
			,'options'=>array('should_create_template' => true)
			
		));
						
        $this->add(array(
        	'name'=>'regular_in_prayers',        
            'type'=>'Select',
            'options'=>array('value_options'=>$this->yes_no_list,'should_create_template' => true)    
			,'attributes'=>array('style'=>'height:112%!important;width:100%!important')
		));
		
		$this->add(array(
        	'name'=>'basharah_chanda',        
            'type'=>'Select',
            'options'=>array('value_options'=>$this->yes_no_list,'should_create_template' => true)    
			,'attributes'=>array('style'=>'height:112%!important;width:100%!important')
		));
		
		$this->add(array(
        	'name'=> 'have_beard',        
            'type'=>'Select',
            'options'=>array('value_options'=>$this->yes_no_commit_list,'should_create_template' => true)    
			,'attributes'=>array('style'=>'height:112%!important;width:100%!important')
		));
		   
        $this->add(array(
        	'name'=>'proposed_by',        
            'type'=>'Text'    
            ,'attributes'=>array('style'=>'width:100%!important')
			,'options'=>array('should_create_template' => true)
                        
		));
		
        $this->add(array(
        	'name'=>'seconded_by',        
            'type'=>'Text'    
            ,'attributes'=>array('style'=>'width:100%!important')
		));


        $this->add(array(
        	'name'=>'branch_name',        
            'type'=>'Text'    
            ,'attributes'=>array('style'=>'width:100%!important')
		));
	

	
        $this->add(array(
        	'name'=>'votes',        
            'type'=>'Number',
            'attributes'=>array('style'=>'width:100%!important'),            
		));

        $this->add(array(
        	'name'=>'introduction',        
            'type'=>'TextArea',
            'attributes'=>array('style'=>'width:100%!important;min-height:100px'),            
		));
		 
	}	
	
}


