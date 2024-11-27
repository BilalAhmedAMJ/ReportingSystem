<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

use Application\Service\DashboardService;
use Application\View\HighChart\DataTransform;

class  EncryptUtilTest extends PHPUnit_Framework_TestCase{


    public function testEncrypt(){

        $encUtil = BootstrapPHPUnit::getService('EncryptUtil');

        $this->assertNotNull($encUtil->getEncryptAdapter());
        $this->assertNotNull($encUtil->getDecryptAdapter());

        $source='ABC';
        $enc = $encUtil->encrypt($source);
        $dec = $encUtil->decrypt($enc);
        print_r("ID\n".$enc."\n");
        print_r("SEC\n".$dec."\n");
    }
    
}
