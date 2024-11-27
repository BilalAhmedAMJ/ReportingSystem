<?php

namespace ApplicationTest\Controller;

#use ApplicationTest\ZF2TestCase;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;


class IndexControllerTest extends AbstractHttpControllerTestCase
{

    protected $traceError = true;
    
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__.'/../../../../../config/application.config.php'
        );
        parent::setUp();
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('application');
        $this->assertControllerName('home');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('home');
    }
}