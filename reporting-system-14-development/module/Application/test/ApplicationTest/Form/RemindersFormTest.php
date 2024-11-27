<?php

namespace ApplicationTest\Entity;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;


class  RemindersFormTest extends PHPUnit_Framework_TestCase{
        
    private $factory;
    
    public function setUp(){

    }

    public function testFormLocalJamaatGS(){
        
        $this->setUser('gsOTW');
        /**
         * @var \Application\Form\RemindersForm
         */
        //$form = new \Application\Form\RemindersForm();
        $form = BootstrapPHPUnit::getService('FormElementManager')->get('Application\Form\RemindersForm');
        
        $el=$form->getElements();
        print_r($el['send_to_rule']);        
        //print_r($el['level_rule']);
        //print_r($el['branch']);
        print_r($el['user_level']);
        print_r($el['rule_level']);        
    }


    public function testFormLocalHalqaGS(){
        
        $this->setUser('gsBRE');
        /**
         * @var \Application\Form\RemindersForm
         */
        //$form = new \Application\Form\RemindersForm();
        $form = BootstrapPHPUnit::getService('FormElementManager')->get('Application\Form\RemindersForm');
        
        $el=$form->getElements();
        print_r($el['send_to_rule']);        
        //print_r($el['level_rule']);
        //print_r($el['branch']);
        print_r($el['user_level']);
        print_r($el['rule_level']);        
    }


    public function testFormImaratGS(){
        
        $this->setUser('gsIBN');
        /**
         * @var \Application\Form\RemindersForm
         */
        //$form = new \Application\Form\RemindersForm();
        $form = BootstrapPHPUnit::getService('FormElementManager')->get('Application\Form\RemindersForm');
        
        $el=$form->getElements();
        print_r($el['send_to_rule']);        
        print_r($el['level_rule']);
        print_r($el['branch']);
        print_r($el['user_level']);
        print_r($el['rule_level']);        
    }


    public function testFormNationalGS(){
        
        $this->setUser('sabih.nasir');
        /**
         * @var \Application\Form\RemindersForm
         */
        //$form = new \Application\Form\RemindersForm();
        $form = BootstrapPHPUnit::getService('FormElementManager')->get('Application\Form\RemindersForm');
        
        //print_r($form->getElements());        
    }

    private function setUser($user_name){
        $user = BootstrapPHPUnit::getService('UserProfileService')->getUserByUsername($user_name);
        BootstrapPHPUnit::setCurrentUser($user);
    }
}