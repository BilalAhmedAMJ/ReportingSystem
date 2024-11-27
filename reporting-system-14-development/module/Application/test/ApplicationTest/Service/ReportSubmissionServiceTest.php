<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

class  ReportSubmissionServiceTest extends PHPUnit_Framework_TestCase{

    public function atestCreateReportDataSource(){
        
        $service = BootstrapPHPUnit::getService('ReportSubmissionService');

       $user_id =  6545; //test as User with Amin in Imarat Brampton and Mal in Halqa Br. FlowerTown

       $data_source =$service->createReportsDataSource($user_id);
       
        $qb = $data_source->getData();
        $dqlParts = $qb->getDQLParts();
        
        $countOneFunction = $qb->getEntityManager()
            ->getConfiguration()
            ->getCustomStringFunction('COUNT_ONE');
        if ($countOneFunction !== null) {
            $qb->select('COUNT_ONE() AS rowCount');
        } else {
            $fromPart = $dqlParts['from'];
            $qb->select('COUNT('.$fromPart[0]->getAlias().')');
        }

        $rowCount = $qb->getQuery()->getSingleScalarResult();
        $this->assertGreaterThanOrEqual(13,$rowCount);
        #print_r($rowCount);
    }    

    public function atestCreateReport(){
        $report_service = BootstrapPHPUnit::getService('ReportSubmissionService');
        $user_id =  5017;//GS for Tronto Imarat; //6545 test as User with Amin in Imarat Brampton and Mal in Halqa Br. FlowerTown 
        $user_service = BootstrapPHPUnit::getService('UserProfileService');
        $user = $user_service->getUserById($user_id);
        $data=array('report_config'=>'gs_report_monthly',//'secretary_report_monthly',
                    'period_from'=>'Feb-2015',
                    'department'=>5,//2
                    'branch'=>77,//78
                    );
        $report = $report_service->createReport($user,$data);

        print_r(count($report));

        #$this->fail('Not implemented');
    }  
    
    public function atestGetAnswers(){

       /**
        * @var Application\Service\ReportSubmissionService */
       $report_service = BootstrapPHPUnit::getService('ReportSubmissionService');
       
       $report = $report_service->getReport(77, 'Feb-2015', 'gs_report_monthly',5);

       $answers =  $report_service->getAnswers($report);  
       
       foreach ($answers as $value) {
           print_r(count($value->getAnswersForChildQuestions())."\n");
       }
    }
    
    
    public function atestAddAnswersToForm(){
        $report_id='5792';        
        $report_srv = BootstrapPHPUnit::getService('ReportSubmissionService');
        $report = $report_srv->getReportById($report_id);
        $answers = $report_srv->getAnswers($report);
        /** 
         *@var Zend\Form\Form */
        $form = BootstrapPHPUnit::getService('CreateFormService')->getForm('report_details');
        
        BootstrapPHPUnit::getService('CreateFormService')->addAnswers($form,$answers);
        
        foreach ($form as $element) {
            echo $element->getName()."\n";
            if($element instanceof \Zend\Form\FieldsetInterface){
                foreach ($element as $elem2) {                    
                    echo "    ".$elem2->getName()."\n";
                    if($elem2 instanceof \Zend\Form\Fieldset){
                        foreach ($elem2 as $elem3) {
                            echo "         ".$elem3->getName()."\n";
                        }
                    }
                }
            }
        }
    }

    public function testGetAnswer(){
        $report_id='5800';        
        $report_srv = BootstrapPHPUnit::getService('ReportSubmissionService');
        $report = $report_srv->getReportById($report_id);
        
        $answers = $report_srv->getAnswer($report,173,array('answer_number'=>1));
        
        $this->assertNotEmpty($answers);
    }
    public function atestCanEdit(){
        $draft=524;
        $completed=4711;
        $verified=1751;
        $received=3286;
        
        $owner = 4897;
        $president=4896;
        $parent=4231;
        
        $entity_srv = BootstrapPHPUnit::getService('EntityService');
        $report_service = BootstrapPHPUnit::getService('ReportSubmissionService');
        
        //Draft Report
        $report = $entity_srv->getObject('Report',$draft);
        $user = $entity_srv->getObject('User',$owner);
        $this->assertTrue($report_service->canEdit($user,$report));
        
        $user = $entity_srv->getObject('User',$president);
        $this->assertFalse($report_service->canEdit($user,$report));

        $user = $entity_srv->getObject('User',$parent);
        $this->assertFalse($report_service->canEdit($user,$report));
        
        //Completed Report
        $report = $entity_srv->getObject('Report',$completed);
        $user = $entity_srv->getObject('User',$owner);
        $this->assertFalse($report_service->canEdit($user,$report));
        
        $user = $entity_srv->getObject('User',$president);
        $this->assertTrue($report_service->canEdit($user,$report));

        $user = $entity_srv->getObject('User',$parent);
        $this->assertFalse($report_service->canEdit($user,$report));


        //Verified Report
        $report = $entity_srv->getObject('Report',$verified);
        $user = $entity_srv->getObject('User',$owner);
        $this->assertFalse($report_service->canEdit($user,$report));
        
        $user = $entity_srv->getObject('User',$president);
        $this->assertFalse($report_service->canEdit($user,$report));

        $user = $entity_srv->getObject('User',$parent);
        //print_r($user->getDisplayName());exit;
        $this->assertTrue($report_service->canEdit($user,$report));

    }
      
}
