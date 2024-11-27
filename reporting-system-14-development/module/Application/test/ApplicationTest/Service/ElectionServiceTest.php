<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

class  ElectionServiceTest extends PHPUnit_Framework_TestCase{

      private $data = array(
            'election_id' => '',
            'election_date' => '2016-04-04',
            'branch_id' => '78',
            'election_term'=>'2016-2019',
            'chanda_payers' => '450',
            'defaulters' => '450',
            'attendance_at_start' => '320',
            'election_call' => '2nd',
            'presided_by' => 'National Rep',
            'presiding_officer_comments' => 'No comments',
            'eligible_voters_list' => '{"file":"attached"}',
            'witness_name_one' => 'Some Witness',
            'witness_phone_one' => '1234567890',
            'witness_name_two' => 'SomeOther Witnesss',
            'witness_phone_two' => '1234567891',
            'election_status' => ''
        );
        
    
    public function atestAddElection(){
        
        $user = BootstrapPHPUnit::getService('UserProfileService')->getUserByUsername('sabih.nasir');
        BootstrapPHPUnit::setCurrentUser($user);
        
        $this->assertTrue($user!=null);


        $service = BootstrapPHPUnit::getService('ReportAnalysisService');
		
        $service = BootstrapPHPUnit::getService('ElectionService');

        $form= BootstrapPHPUnit::getService('CreateFormService')->getform('election_report');
        $this->assertNotNull($form);


        //$form= BootstrapPHPUnit::getService('CreateFormService')->getform('election_proposal');
        //$this->assertNotNull($form);

        $form= BootstrapPHPUnit::getService('CreateFormService')->getform('election');
        $this->assertNotNull($form);
        

        //$form->setData($this->data);
        //$form->isValid();
        //$data = $form->getData();
		  
        //$election=$service->saveElection($data);
		
		$election=null;  
		  
        $this->assertTrue($election!=null);
        
        //print_r(BootstrapPHPUnit::toJson($election));

        print_r($service->validate($election));
        //$this->assertTrue(count($user->getOfficeAssignments())>0);
    }    
    
	public function testExport(){
				
        $user = BootstrapPHPUnit::getService('UserProfileService')->getUserByUsername('sabih.nasir');
        BootstrapPHPUnit::setCurrentUser($user);
        
        $this->assertTrue($user!=null);


        $service = BootstrapPHPUnit::getService('ReportAnalysisService');
		
        $service = BootstrapPHPUnit::getService('ElectionService');
		
		 /*

3->calgary
79->Mississauga
80->peace village
81->vancouver
82->vaughan
		  		  
		 */
		$ent_srv = BootstrapPHPUnit::getService('EntityService');

		$elections = $ent_srv->findAll('Election');
		 
		$file=$_SERVER['HOMEPATH'].'/exported_elections_calgary.csv';
		$file_h = fopen($file,'w');
		$first=true;
		foreach ($elections as $election) {
				
			if($election->getBranch()->getParent()->getId()!='3'){ continue; }
			if($election->getElectionStatus()=='draft'){ continue; }
			if($election->getBranch()->getBranchType()=='Markaz') {continue;}
			
			print "exporting ".$election->getBranch()->__toString()." => ".$election->getId()."\n";			
			
			$election_proposals = $service->exportElection($election);
			foreach ($election_proposals as $election_proposal) {
				if($first){
					fputcsv($file_h,array_keys($election_proposal), ',','"');
					$first=false;
				}
				fputcsv($file_h,array_values($election_proposal), ',','"');
			}
		}
		fclose($file_h);	
		print_r($file);
		

		$file=$_SERVER['HOMEPATH'].'/exported_elections_pv.csv';
		$file_h = fopen($file,'w');
		$first=true;
		foreach ($elections as $election) {
				
			if($election->getBranch()->getParent()->getId()!='80'){ continue; }
			if($election->getElectionStatus()=='draft'){ continue; }
			if($election->getBranch()->getBranchType()=='Markaz') {continue;}
			
			print "exporting ".$election->getBranch()->__toString()." => ".$election->getId()."\n";			
			
			$election_proposals = $service->exportElection($election);
			foreach ($election_proposals as $election_proposal) {
				if($first){
					fputcsv($file_h,array_keys($election_proposal), ',','"');
					$first=false;
				}
				fputcsv($file_h,array_values($election_proposal), ',','"');
			}
		}
		fclose($file_h);	
		print_r($file);

		$file=$_SERVER['HOMEPATH'].'/exported_elections_vancouver.csv';
		$file_h = fopen($file,'w');
		$first=true;
		foreach ($elections as $election) {
				
			if($election->getBranch()->getParent()->getId()!='81'){ continue; }
			if($election->getElectionStatus()=='draft'){ continue; }
			if($election->getBranch()->getBranchType()=='Markaz') {continue;}
			
			print "exporting ".$election->getBranch()->__toString()." => ".$election->getId()."\n";			
			
			$election_proposals = $service->exportElection($election);
			foreach ($election_proposals as $election_proposal) {
				if($first){
					fputcsv($file_h,array_keys($election_proposal), ',','"');
					$first=false;
				}
				fputcsv($file_h,array_values($election_proposal), ',','"');
			}
		}
		fclose($file_h);	
		print_r($file);
		
		
		$file=$_SERVER['HOMEPATH'].'/exported_elections_vaughan.csv';
		$file_h = fopen($file,'w');
		$first=true;
		foreach ($elections as $election) {
				
			if($election->getBranch()->getParent()->getId()!='82'){ continue; }
			if($election->getElectionStatus()=='draft'){ continue; }
			if($election->getBranch()->getBranchType()=='Markaz') {continue;}
			
			print "exporting ".$election->getBranch()->__toString()." => ".$election->getId()."\n";			
			
			$election_proposals = $service->exportElection($election);
			foreach ($election_proposals as $election_proposal) {
				if($first){
					fputcsv($file_h,array_keys($election_proposal), ',','"');
					$first=false;
				}
				fputcsv($file_h,array_values($election_proposal), ',','"');
			}
		}
		fclose($file_h);	
		print_r($file);
		
	}
}



