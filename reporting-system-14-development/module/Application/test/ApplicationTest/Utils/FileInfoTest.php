<?php

namespace ApplicationTest\Utils;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

use Rhumsaa\Uuid\Uuid;

use Application\Entity\User;

class  FileInfoTest extends PHPUnit_Framework_TestCase{

    public function testMime(){

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        print_r($finfo->file('/Users/haroon/Downloads/CMTGMONN.xls'));    
    }    
}