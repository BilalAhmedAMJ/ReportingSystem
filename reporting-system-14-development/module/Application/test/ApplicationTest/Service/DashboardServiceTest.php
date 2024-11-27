<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

use Application\Service\DashboardService;
use Application\View\HighChart\DataTransform;

class  DashboardServiceTest extends PHPUnit_Framework_TestCase{

    static $userName = 'gsITO';
    
    public function atestreportSubmission(){
        
        $userSrv = BootstrapPHPUnit::getService('UserProfileService');
        
        $user = $userSrv->getUserByUsername($this::$userName);
        
        $dashSrv = BootstrapPHPUnit::getService('DashboardService');
        
        $this->assertNotNull($user);

        $data = $dashSrv->reportsSubmission($user->getId(),'2014','Aug-2014');

        //print_r($user->getDisplayName()."\n");   
        //print_r($data['own']);     
        //print_r($data['own']['status_ytd']);
        //print_r($data['subordinate']['last']);
        //print_r($data['subordinate']['status_last']);
        //exit;
    }
    
    public function atestReportSubmissionTransform(){

        $userSrv = BootstrapPHPUnit::getService('UserProfileService');
        
        $user = $userSrv->getUserByUsername($this::$userName);
        
        $dashSrv = BootstrapPHPUnit::getService('DashboardService');
        
        $src_data = $dashSrv->reportsSubmission($user->getId(),'2014','Aug-2014',false);

        //print_r($src_data['subordinate']['data'][0]);        
        //$summary_result = DataTransform::summarizeReportsByBranchPeriod($src_data['subordinate']['data'],$src_data['subordinate']['branches'],$src_data['subordinate']['all_periods']);        
        
        $summary_result = $src_data['subordinate']['data'];
        
        //print_r($summary_result);

        $data = DataTransform::convertToColumn($summary_result,'period_code','report_status');
        
        //print_r($data);
        
    }
    
    public function testReportSubmissionCounts(){

        $userSrv = BootstrapPHPUnit::getService('UserProfileService');
        
        $user = $userSrv->getUserByUsername('sabih.nasir');
        
        $dashSrv = BootstrapPHPUnit::getService('DashboardService');
        
        $src_data = $dashSrv->reportsSubmission($user->getId(),'2014','Jul-2014');

        
        print_r(($src_data['samebranch']['status_ytd']));        
        print_r(count($src_data['samebranch']['data']));
        
        //$periods=array();
        foreach ($src_data['samebranch']['all_periods'] as $key => $value) {
            $periods[]=$value->__toString();
        }
        //print_r($periods);
                //exit;
    }    
}
?>