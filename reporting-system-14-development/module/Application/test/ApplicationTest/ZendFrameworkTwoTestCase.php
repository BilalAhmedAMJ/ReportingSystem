<?php

namespace ApplicationTest;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;


class ZendFrameworkTwoTestCase extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__.'/../../../../config/application.config.php'
        );
        parent::setUp();
    }

}