<?php


namespace Application\Form;

use Zend\Form\Factory as FormFactory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class QuestionElementFactory implements FactoryInterface{
    
    private $config;
    private $serviceLocator;
    private $formFactory;
    
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       
        $this->config = $serviceLocator->get('Config');  
        $this->serviceLocator=$serviceLocator;
    
        $this->formFactory = new FormFactory();
        

        return $this;
    }


    /**
     * @param Application\Entity\Answer
     */
    public function createElement($answer){
        /**
         * @var Application\Entity\Answer */
         $answer=&$answer;
        /**
         * @var Application\Entity\Question */
        $question = $answer->getQuestion();
        
 ##$serializer = $this->serviceLocator->get('jms_serializer.serializer');
 ##var_dump($serializer->serialize($question->getQuestionType(), 'json'));
        
        $answerType=$question->getAnswerType();
        $questionType=$question->getQuestionType();
        $isChildQuestion=$question->isChild();
        $displayConfig=array();
        if($question->getDisplayConfig()){
            try{
             $displayConfig = json_decode($question->getDisplayConfig(),true);
            }catch(Error $e){}
        }
        $caption=$answer->getReport()->replaceTokens($answer->getCaption());
        $constraints=array();
        if($question->getConstraints()){
            try{
             $constraints = json_decode($question->getConstraints(),true);
            }catch(Error $e){}
        }
        $name=$answer->getAnswerKey();
        $type_info=$this->questionTypeToZendType($questionType);
        $type=$type_info[0];

        $value = $this->wrapValue($answer->getValue(),$questionType);
        
        $element=array(
//        'spec'=>array(
            'name'=>$name,
            'type'=>$type,
            'attributes'=>array(
                'id'=>$name,
                'value'=>$value,
                'title'=>$caption.' '.$question->getDetails(),
                'data-qtype'=>$questionType,
                'data-qcaption'=>$question->getCaption(),
                'data-has-children'=>$answer->hasChildren(),
                'data-is-child'=>$isChildQuestion,
                'data-display'=>$type_info[1],
                'data-sort-order'=>$question->getSortOrder(),
                'data-answer_no'=>$answer->getAnswerNumber(),
                'data-question_id'=>$question->getId()
                
            ),
            'options'=>array(
                'label'=>$caption
            )
//        )
        ); 

       if(isset($type_info[2])){
           $element['attributes']['class']=$type_info[2];
       }

       if( $this->isReadOnly($answer) ){
           $element['attributes']['readonly']='true';
       }

        //TODO FIX FOR OPTION_LIST QUESTIONS
        //for option_list questions we would have in contraints a constraint 'options_list'
        //that will be used to setup constraints
        if( ($questionType === 'OPTION_LIST' || $questionType === 'OPTION_MULTI' )
            && key_exists('options_list', $constraints) ){
                
            $element['options']['value_options']=$constraints['options_list'];
        }
        
        if(isset($type_info[3])&&is_array($type_info[3])){
            foreach ($type_info[3] as $key => $value) {
                $element['options'][$key]=$value;
            }
        }   
            
            
        //$additional=array_merge(is_array($displayConfig)?$displayConfig:array(),is_array($constraints)?$constraints:array());
        $additional=array_merge($displayConfig,$constraints);
        
        unset($additional['display_levels']);
        $additional['rollup'] = false;
        if( isset($additional['rollup_settings']) ) {
            if( isset( $additional['rollup_settings'][$answer->getReport()->getBranch()->getBranchLevel()] )){
                $additional['rollup'] = true;
            }
            unset($additional['rollup_settings']);
        }
        
        //setup constraints
        foreach ($additional as $const_key => $constraint) {
            $element['options'][$const_key]=$constraint;
            $attr_name = 'data-' . $const_key;                
            if(is_array($constraint)){
                foreach ($constraint as $sub_key => $sub_constraint) {
                    $value = is_array($sub_constraint)?json_encode($sub_constraint):$sub_constraint;                    
                    $element['attributes'][preg_replace('/\s/','_',$attr_name).'-'.preg_replace('/\s/','_',$sub_key)]=$value;
                    
                }// end-multi-level constraints
            }else{
                $element['attributes'][$attr_name]=$constraint;
            }
        }

// if($answer->getId()===45986){
// $fld=$this->formFactory->create($element);    
// echo "<pre>";    
// //var_dump(array($fld));
// var_dump(array($element));
// echo "</pre>";
// exit;
// }

        return $this->formFactory->create($element);
    }

    private function isReadOnly($answer){
    
       $readonly=false;
       $status = $answer->getReport()->getStatus();
       $caption = $answer->getQuestion()->getCaption();
       $completed_comments='/Comments by ##branch_head_title##/';
       $verified_comments='/Comments by ##parent_office_bearer_designation## ##office_bearer_title##/';
       switch ($status) {
          case 'draft':
              $readonly=false;
              break;
              
          case 'completed':
              $readonly=true;
              if( preg_match($completed_comments, $caption)  >0){
                $readonly=false;
              }
              break;
          
          case 'verified':
              error_log('BEFORE '.$caption . '  ['.$readonly.']');
              $readonly=true;
              if( preg_match($verified_comments, $caption) >0){
                $readonly=false;
              }
              error_log('AFTER'.$caption . '  ['.$readonly.']');
              break;

          case 'received':
              $readonly=true;
              break;

          default:
              //Unknown status, make it readonly
              $readonly=true;
              break;
      }
      return $readonly;
    }


    private $typesMap = array(
            'TEXT'          =>  array( 'Zend\Form\Element\Text'             ,'simple',  'form-input-style input-large'),
            'YES_NO'        =>  array( 'Zend\Form\Element\Checkbox'         ,'checkbox',  'ace ace-switch ace-switch-5 ',array('use_hidden_element'=>false,)),
            'OPTION_LIST'   =>  array( 'Zend\Form\Element\Select'           ,'simple',  'form-input-style report-select-input'),
            'OPTION_MULTI'  =>  array( 'Zend\Form\Element\Select'           ,'simple',  'form-input-style report-select-input'),
            'DATE'          =>  array( 'Zend\Form\Element\Text'             ,'datepicker',  'date-picker input-small  form-date-style '),
            'NUMBER'        =>  array( 'Zend\Form\Element\Text'             ,'simple',  ' form-input-style input-small '),
            'MEMO'          =>  array( 'Zend\Form\Element\Textarea'         ,'richtext'     ),
            'MEMO_YES_NO'   =>  array( 'Zend\Form\Element\Textarea'         ,'richtext'     ),
            'GROUP_CHECKBOX'=>  array( 'Zend\Form\Element\Checkbox'         ,'group'        ),
            'GROUP_RADIO'   =>  array( 'Zend\Form\Element\Radio'            ,'group'        ),
            'HIDDEN'        =>  array( 'Zend\Form\Element\Hidden'           ,'simple'       ),
            'FILE'          =>  array( 'Zend\Form\Element\File'             ,'file',  ' form-input-style input-medium '),
            'BUTTON'        =>  array( 'Zend\Form\Element\Button'           ,'button'),
            'GRID'          =>  array( 'Zend\Form\Fieldset'                 ,'grid'),    
//            'GROUP_SECTION' =>  array( 'Application\Form\GroupingFieldset'  ,'complex'),
            'GROUP_MIXED'   =>  array( 'Application\Form\GroupingFieldset'  ,'complex'),
            'TABLE'         =>  array( 'Application\Form\GroupingFieldset'  ,'table'),
            'HEADER'        =>  array( 'Zend\Form\Element\Label'            ,'header'),
            'LABEL'         =>  array( 'Zend\Form\Element\Hidden'            ,'label'),
            'REPORT_NOTES'  =>  array( 'Zend\Form\Element\Textarea'         ,'report_notes'),
    );
    
    private function questionTypeToZendType($questionType){
        
        //For types that don't have a map use a generic element
        $type_array=array('Zend\Form\Element','simple');
        if(key_exists($questionType, $this->typesMap)){
            $type_array= $this->typesMap[$questionType];
        }
        
        return $type_array;
        //array('Zend\Form\Element','simple') ;
    }

    private function wrapValue($origValue,$questionType){
        $valid_checked_values=array('on','yes');
        if($questionType==='YES_NO' && in_array(strtolower($origValue), $valid_checked_values)){
            return 1;
        }else{
            return $origValue;
        }        
    }    
}    
