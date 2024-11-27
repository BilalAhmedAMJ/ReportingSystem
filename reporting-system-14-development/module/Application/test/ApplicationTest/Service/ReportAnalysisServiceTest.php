<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;


use Application\Service\ReportAnalysisService;
use Application\Entity\Period;


class  ReportAnalysisServiceTest extends PHPUnit_Framework_TestCase{

    public function testGetDashBoardOffice(){
        $userSrv = BootstrapPHPUnit::getService('UserProfileService');
        //$sender = $userSrv->getUserByUsername('gsnational');
        $srv = BootstrapPHPUnit::getService('ReportAnalysisService');
        
        $user = $userSrv->getUserByUsername('feBRF.anIBN');
        $office = $srv->getDashBoardOffice($user);
        print_r(array($office->getDepartment()->getDepartmentName(),$office->getBranch()->getBranchName()));


        $user = $userSrv->getUserByUsername('pTHN');
        $office = $srv->getDashBoardOffice($user);
        //print_r(array($office->getDepartment()->getDepartmentName(),$office->getBranch()->getBranchName()));

    }
    
     public function testGetBasicDashBoardSubBranch(){
         
        $userSrv = BootstrapPHPUnit::getService('UserProfileService');
        
        $srv = BootstrapPHPUnit::getService('ReportAnalysisService');
        
        $em =  BootstrapPHPUnit::getEntityManager();
        $branch = $em->find('\Application\Entity\Branch',78);
        
        $data =  $srv->getBasicDashBoardSubBranch($branch);
       
        print_r( (\Application\View\HighChart\DataTransform::convertToColumn($data['last_six_month'],'period_code','branch_name','report_cont')));
     
    }
    
}