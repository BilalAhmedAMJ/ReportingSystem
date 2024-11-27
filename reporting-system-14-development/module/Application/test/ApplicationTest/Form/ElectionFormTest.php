<?php

namespace ApplicationTest\Entity;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;


class  ElectionFormTest extends PHPUnit_Framework_TestCase{
        
    private $factory;
    
    public function setUp(){
    }

    public function testFormCustomElements(){
        
        $form_election= BootstrapPHPUnit::getService('CreateFormService')->getform('election');
        $this->assertNotNull($form_election);

        $form_elect_rep= BootstrapPHPUnit::getService('CreateFormService')->getform('election_report');
        $this->assertNotNull($form_elect_rep);


        $form_elect_proposal= BootstrapPHPUnit::getService('CreateFormService')->getform('election_proposal');
        $this->assertNotNull($form_elect_proposal);

		$form = BootstrapPHPUnit::getService('FormElementManager')->get('Form');
		$this->assertNotNull($form);
		
		$form->setName('election_form');
		$form->add($form_election);
		$form->add($form_elect_rep);
		$form->add($form_elect_proposal);
		
		$form->setData(array());
		$form->isValid();
		
		print_r($form->getData());
		
		$now = new \DateTime();
		print_r($now->format('d-M-y'));
    }

    public function testFormLocalJamaatGS(){
        
        $this->setUser('sabih.nasir');
        /**
         * @var \Application\Form\RemindersForm
         */
        //$form = new \Application\Form\RemindersForm();
    }
    
     private function setUser($user_name){
        $user = BootstrapPHPUnit::getService('UserProfileService')->getUserByUsername($user_name);
        print_r($user->getDisplayName());
        BootstrapPHPUnit::setCurrentUser($user);
    }


}