<?php


namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Form;
use Zend\Form\Factory as FormFactory;


class CreateFormService implements FactoryInterface{
    
    private $config;

    private $element_factory;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       
        $this->config = $serviceLocator->get('Config');  
        $this->element_factory=$serviceLocator->get('QuestionElementFactory');
        return $this;
    }
    
    public function getForm($formName){
    	
        $formFactory = new FormFactory();
        
        //By conventions forms are defined as aray in files, such that file name is same as form_name.phtml
        //and are located under module/MODULE_NAME/view/form
        $formDir=($this->config['application']['form_dir']);      
        $fromFileName=$formDir.$formName.'.phtml';
        
        $formSpecs=$this->simpleSpecsToZendSpecs(include $fromFileName);
        
        $form    = $formFactory->createForm($formSpecs);
		if(!key_exists('type', $formSpecs) || !($formSpecs['type'])=='Fieldset'){
	        $form->prepare();
		}
        
        return $form;
                
    }
    
    
    public function addAnswers($element_container,array $answers){
        //var_dump("Start call for ".$element_container->getName()."\n");
        foreach ($answers as $answer) {
            if($this->skipQuestion($answer->getQuestion(),$answer->getReport(),$answer->getValue())){
                continue;
            }
            $element = $this->element_factory->createElement($answer);                           
            if($answer->hasChildren()){
                //echo "Going in for ".count($answer->getAnswersForChildQuestions())." children \n";
                
                $this->addAnswers($element, $answer->getAnswersForChildQuestions());
            }
            $element_container->add($element);
        }                
        //var_dump("Done call for ".$element_container->getName()."\n");
        return $element_container;
    }
        
        
    private function skipQuestion($question,$report,$value){
        $displayConfig = json_decode($question->getDisplayConfig(),true);
        $skip_question=false;
        //when question have a display config show_for_status only show this question when current status of report is in config status list
        if($displayConfig && is_array($displayConfig) && key_exists('show_for_status', $displayConfig)){
            $show_for_status = array_values($displayConfig['show_for_status']);
            //skip this question if curent status  is not in show_for_status list
            $skip_question = (! in_array($report->getStatus(),$show_for_status));
        }

        //when question have a display config hide_for_level only show this question when level or reporting branch is not in config level list
        if($displayConfig && is_array($displayConfig) && key_exists('hide_for_level', $displayConfig)){
            $hide_for_level = array_values($displayConfig['hide_for_level']);
            //skip this question if curent status  is not in show_for_status list
            $skip_question = $skip_question || in_array($report->getBranch()->getBranchLevel(),$hide_for_level);
        }

        if($displayConfig && is_array($displayConfig) && key_exists('hide_empty', $displayConfig)){
            $skip_question = $skip_question || empty($value);
        }

        
        //no restrictions for display
        return $skip_question;
    }
        
    private $attribute_keys = array('id','class','readonly','style','required');
    
    private function simpleSpecsToZendSpecs($specs){
        if(key_exists('simple_specs', $specs)){
        	$elements = array();
            foreach ($specs['simple_specs'] as $id => $simple) {
                $element=array(
                    'spec'=>array(
                        'name'=>$id,
                        'type'=>$simple['type'],
                        'attributes'=>array('id'=>$id)
                    )//end of element-spec
                );//end element                
                foreach ($this->attribute_keys as $key) {
                    if(key_exists($key, $simple)){
                        $element['spec']['attributes'][$key]=$simple[$key];
                    }
                }//end-for attribute keys
                
                //merge attributes from simple
                if(key_exists('attributes', $simple) && is_array($simple['attributes'])){
	                foreach ($simple['attributes'] as $key => $value) {
	                    $element['spec']['attributes'][$key]=$value;
	                }
                }
                if(key_exists('options', $simple) && is_array($simple['options'])){
                    $element['spec']['options']=$simple['options'];
                }
                                
                $elements[]=$element;
				//if($id=='proposal_number'){print_r($element);exit;}
            }//end-for each simple spec

            if(!key_exists('elements', $specs)){
                $specs['elements']=$elements;    
            }else{
                //need to merge with existing elements
                $specs['elements']=array_merge($elements,$specs['elements']);
            }
                        
        }//end-if specs contain simple specs
        return $specs;
    }
}
