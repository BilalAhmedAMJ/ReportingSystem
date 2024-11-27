<?php

namespace ApplicationTest\Utils;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

use Rhumsaa\Uuid\Uuid;

use Application\Entity\User;

class  CreatePasswordTest extends PHPUnit_Framework_TestCase{

    public function testCreate(){

            $zfc_user=BootstrapPHPUnit::getService('zfcuser_user_service');
            $bcrypt = new \Zend\Crypt\Password\Bcrypt;
            $bcrypt->setCost( $zfc_user->getOptions()->getPasswordCost());
            
            $pwds = array('clg2015');
            
            foreach ($pwds as $pwd) {
                print_r(array($pwd=>$bcrypt->create($pwd)));
            }
            
    }    
}

