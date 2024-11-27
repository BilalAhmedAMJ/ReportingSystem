<?php

namespace ApplicationTest\Entity;

use PHPUnit_Framework_TestCase;

use Application\Entity\Period;

use ApplicationTest\BootstrapPHPUnit;

class  PeriodTest extends PHPUnit_Framework_TestCase{
    
    
    private $factory;
    
    public function setUp(){
        $this->factory = BootstrapPHPUnit::getService('entityFactory');   
        $this->entityManager = BootstrapPHPUnit::getEntityManager();
    }
    
    public function testHasValidPeriodCode(){
                
        $period = $this->factory->getPeriod();
        
        $this->assertNotNull($period);
        
        $this->assertFalse($period->isPeriodCodeValid());

        $period->setPeriodCode('1');
        $this->assertFalse($period->isPeriodCodeValid());


        $period->setPeriodCode('Jan');
        $this->assertFalse($period->isPeriodCodeValid());

        $period->setPeriodCode('Jan-2014');
        $this->assertTrue($period->isPeriodCodeValid());

        $period->setPeriodCode('Jun-2014');
        $this->assertTrue($period->isPeriodCodeValid());

        $period->setPeriodCode('Jun-2014');
        $this->assertTrue($period->isPeriodCodeValid());
    }
    
    
    
    public function testCalendarMonth(){
        $period = $this->factory->getPeriod();
        $this->assertNotNull($period);
        
        $period->setPeriodCode('Jan-2014');
        $this->assertTrue($period->isPeriodCodeValid());
        $this->assertEquals('Jan',$period->getCalendarMonth());

        $period->setPeriodCode('Dec-2014');
        $this->assertTrue($period->isPeriodCodeValid());
        $this->assertEquals('Dec',$period->getCalendarMonth());

        $period->setPeriodCode('Jun-2014');
        $this->assertTrue($period->isPeriodCodeValid());
        $this->assertEquals('Jun',$period->getCalendarMonth());
    }
    
    
    
    public function testYearCodeAfterStartMonth(){
        $period = $this->factory->getPeriod();
        $this->assertNotNull($period);
        
        $period->setPeriodCode('Aug-2014');
        $this->assertTrue($period->isPeriodCodeValid());

        $period::create('Aug-2014',$period);
        $this->assertEquals("2014-2015",$period->getYearCode(),"Aug is after default start month of Jul");
    }    

    public function testPeriodStartEnd(){
        $period = $this->factory->getPeriod();
        $this->assertNotNull($period);
        
        $period::create('Jun-2014',$period);

       #print_r($period->getPeriodStart());
       #print_r($period->getPeriodEnd());
        
       $this->assertEquals($period->getPeriodStart(), new \DateTime("Jun 01, 2014"),"Start time is not correct");
       $this->assertEquals($period->getPeriodEnd(),   new \DateTime("Jun 30, 2014 23:59:59"),"End time is not correct");
    }
    
    public function testYearCodeBeforeStartMonth(){
        $period = $this->factory->getPeriod();
        $this->assertNotNull($period);
        
        $period->setPeriodCode('Aug-2014');
        $this->assertTrue($period->isPeriodCodeValid());

        $period::create('Jun-2014',$period);
        $this->assertEquals("2013-2014",$period->getYearCode(),"Jun is before default start month of Jul");
    }
    
    
    public function testGetYearsAfterLast(){
        $period = $this->factory->getPeriod();
        
        $start = $period::create("Jan-2015");
        
        $end = $start->addYears(1,Period::DEFAULT_YEAR_START);
        
        $this->assertEquals('Jun-2015',$end->getPeriodCode(),"Second Year is not correct");
        
        $start = $period::create("Jul-2014");
        
        $end = $start->addYears(1,Period::DEFAULT_YEAR_START);
        
        $this->assertEquals('Jun-2015',$end->getPeriodCode(),"getting end period of current year does not work");

        $end = $start->addYears(2,Period::DEFAULT_YEAR_START);
        
        $this->assertEquals('Jun-2016',$end->getPeriodCode(),"getting end period of next year does not work");
         
        $end = $start->addYears(3,Period::DEFAULT_YEAR_START);
        
        $this->assertEquals('Jun-2017',$end->getPeriodCode(),"getting end period of three year does not work");
   }    
 
    public function testGetLastMonth(){
        $config = BootstrapPHPUnit::getService('ConfigService');
        
        $month = $config->getLastMonth('Jan');
        $this->assertEquals('Jan-'.date('Y'),$month->getPeriodCode());
    }

    public function testTerm(){
        $term='2016-2019';
        $term_start = Period::getTermStart($term); 
        $term_end   = Period::getTermEnd($term);
        $now = new \DateTime();
        
        $from_period=Period::createFromDate($term_start);
        if($now > $term_start){
            $from_period=(Period::createFromDate($now));
        }
                   
                
        $to_period = NULL;
        if(empty($to_period)){
            $to_period = (Period::createFromDate($term_end));
        }
        print_r([$from_period,$to_period]);                
    }
    public function atestCreateAll(){

        $date = new \DateTime('2016-07-01');
        $now = new \DateTime('2020-12-31');
        
        
        while($date<$now){
            $code=$date->format('M-Y');
            $month =  new \DateInterval('P1M');
            $date = date_add($date,$month);
            $period = Period::create($code);
            print_r($period->__toString()."\n");
            if($code=='Jul-2017') continue;
            BootstrapPHPUnit::getEntityManager()->persist($period);
        }
        BootstrapPHPUnit::getEntityManager()->flush();
    }   
    


}

