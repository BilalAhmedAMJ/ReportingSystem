<?php

namespace ApplicationTest\Import;

use PHPUnit_Framework_TestCase;

use ApplicationTest\BootstrapPHPUnit;


class  ImportReportsTest extends PHPUnit_Framework_TestCase{
    
    private $userSrv;
    private $reportSrv;
    private $entitySrv;
    private $entityFactory;
    private $entityManager;
    private $currentUser;

    /**
     * Assumes that temp table is created for reports to be imported as follows:
     *  - ami_all_reports_combined: all col from ami_all_reports and ami_all_gs_reports with col name in col_title field
     *  - reports meta data in ami_all_report_to_import
     * 
     */
    private $reports_data_query='
        SELECT *
        FROM ami_reports_to_import
        WHERE report_period IS NOT NULL and report_period!=""
        AND date_posted > "2015-08-01"
        AND dept_id >0
        AND branch_id in (14,18,21,22,26,28,47,50,57,58,75,77)
        ';
        
        //AND branch_id in (82,73,74,49,63,51)
        //AND branch_code="IVN" and period_from="Nov-2015"
        // (10,37,38,39,40,79)
        //,42,15,97,64,60,33,35,61,84,34,83,3
        //(98,4,100,96,59,53,7,66,8,36,92,12,65,13,81,23,93,94,48)

    private $report_answers_data_query="
        SELECT * FROM ami_reports_combined        
        WHERE   question_id IS NOT NULL 
        AND rid=?
        ";
        //AND `answerValue` IS NOT NULL AND trim(answerValue)!='' AND trim(answerValue)!='0'  AND trim(answerValue)!='0000-00-00'

    
    public function setUp(){
        
        $this->entityManager = BootstrapPHPUnit::getService('Doctrine\ORM\EntityManager');
        
        $this->entitySrv = BootstrapPHPUnit::getService('EntityService');
        
        $this->entityFactory = BootstrapPHPUnit::getService('entityFactory');   
    
        $this->reportSrv =    BootstrapPHPUnit::getService('ReportSubmissionService');
        
        $this->userSrv = BootstrapPHPUnit::getService('UserProfileService');
        
        $this->currentUser = $this->userSrv->getUserByUsername('sysadmin');
        
    }
    
        
    
    private function getSourceData($query,$params=array()){
        try {
            $source_db = new \PDO('mysql:host=127.0.0.1;dbname=amjc_reports_import', 'root', 'password');
            $result=array();
            if($source_db!=null){
                $stmt=$source_db->prepare($query);
				if(is_array($params)){
					foreach ($params as $ind => $value) {
						$stmt->bindParam($ind+1,$value);	
					}					
				}
                
				$stmt->execute();				
                $result=$stmt;
                $source_db = null;
                return $result;
            }else{
                throw new \Exception('Unable to connect to DB');
            }
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "\n";
            exit();
        }        
    } 
    
    
    public function atestEmailSend(){

        $email_srv = BootstrapPHPUnit::getService('EmailService');
        $to='haroonzc@gmail.com';
        $msg="This is Test email\nFrom ZF2 application\nThis is to test email";
        $subject='Email from zf2';
        $email_srv->sendHTMLEmail($to,$subject,$msg,$msg);
    }



    public function testImportReports(){
        
        
        $this->assertNotNull($this->currentUser);
        
        $processed=0;
        foreach ($this->getSourceData($this->reports_data_query) as $row) {
            
            $processed++;
            
            //$row['branch_id']='100';
            //print $row['rid']."\n";
            $this->entityManager->beginTransaction();
            
            print("\nWorking on rid[".$row['rid']."] ... ");
			$report = $this->createReport($row);
            print('new report ['.$report->getId()."] ");
			
			//Get answers for Report
            $this->addAnswers($report, $row);
            
            $report->setDateModified(new \DateTime($row['date_posted']));
            
            $this->entityManager->persist($report);
            
            $this->entityManager->flush();            
            $this->entityManager->commit();
        }
         
        print(" Processed $processed Reports!\n");       

    }

    private function createReport($row){
        
        $data =$row;        

        $report_config = $this->entitySrv->getObject('ReportConfig','monthly_report');

        
        //first check if a report with same branch,dept,period combo exist return that report 

        $report = $this->reportSrv->getReport($row['branch_id'],$data['period_from'],$report_config,$data['dept_id']);

        if($report==null || empty($report)){
           //create new report 
           print_r(" ... report not found, creating new report ...");
           
           $report=$this->entityFactory->getReport();
           print_r('Got report that is not null'.($report!=null));
        } //else just update it
    
    
        $data['branch']=$this->entitySrv->getObject('Branch',$data['branch_id']);
        
         
        $data['department']=$this->entitySrv->getObject('Department',$data['dept_id']);


       //populate report with data 
       $report->setBranch($this->entitySrv->getObject('Branch',$data['branch']));
       $report->setDepartment($this->entitySrv->getObject('Department',$data['department']));
       $report->setDateCreated(new \DateTime($row['date_posted']));
       $report->setDateModified(new \DateTime($row['date_posted']));
       $report->setPeriodFrom($this->entitySrv->getObject('Period',$data['period_from']));
       $report->setPeriodTo($report_config->getReportEndingPeriod($report->getPeriodFrom()));
       $report->setReportConfig($report_config);
       $report->setUserCreated($this->currentUser);
       $report->setUserModified($this->currentUser); 
       $report->setSubmittedByName($data['prepared_by']);
       $report->setStatus($data['report_status']);
        
       $this->entityManager->persist($report); 
       $this->entityManager->flush(); 
        
       return $report;
        
    }
    
    private function addAnswers($report,$row){
        
       $answers = $this->getSourceData($this->report_answers_data_query,array($row['rid']));
       $result=array(); 
       foreach ($answers as $data) {
           
           if(empty($data['answer_num'])){
               $data['answer_num']=1;
           }
           
           print_r('Adding answer for q['.$data['question_id']."]\n");    
           
           $question = $this->entitySrv->getObject('Question',$data['question_id']);
           
           $child = $question; 
           
           while($child && $child->getParent() && $child->getParent()->getId()!=$child->getId()){
               print(' -Adding parent for q['.$child->getId()."]\n");
               //this is a child question and a parent question exists
               $parent = $child->getParent();
               $answer_p = $this->getAnswer($report,$parent,$data['answer_num']);//for parent question we should have only one answer so answer_num=1
                              
               $this->entityManager->persist($answer_p);     
               $this->entityManager->flush();
               $child = $child->getParent();//check parent if that have parent untill we reach at top in hirarchy
           }
           
           $answer = $this->getAnswer($report,$question,$data['answer_num']);
           
           $value = ($this->getAnswerValue($question,$data) );
           $oldValue = $answer->getValue();
           if($data['question_id']=='169' && !empty($oldValue)){
               $value = $oldValue. ' ' .$value;
           }
           $answer->setValue($value); 
           
           
           $this->entityManager->persist($answer);     
           $this->entityManager->flush();
                      
           $result[]=$answer;
       }
        return $result;
    }

    private function getAnswer($report,$question,$answer_num){
       
       //try to find existing answers 
       $answer = $this->reportSrv->getAnswer($report,$question,array('answer_number'=>$answer_num));
       
       if(empty($answer) || $answer==null){
           //if not create one
           $answer = $this->reportSrv->createAnswer($question,$report,$answer_num);
           if(!$answer){
               print_r(array($report->getId(),$question->getId(),$answer_num));exit;
           }
       }elseif(is_array($answer)){
           //we want first answer if there are more than one
           $answer = $answer[0];
       }
       return $answer;
    }


    private function getAnswerValue($question,$data){
        $value = trim($data['answerValue']);
        
        if($question->getQuestionType()=='MEMO'){
            $value='<pre>'.$value.'</pre>';
        }
        if($question->getQuestionType()=='DATE' && $value=='0000-00-00'){
            $value=null;
        }
        
        if($question->getQuestionType()=='TEXT' && $value=='0'){
            $value='';
        }
        
        if($question->getQuestionType()=='TEXT' && $value=='0'){
            $value='';
        }

        if($question->getQuestionType()=='NUMBER' && $value=='0'){
            $value='';
        }

        if($question->getQuestionType()=='FILE' && !empty($value) ){
           $parts = explode('.', $value);
           $ext = $parts[count($parts)-1]; 
           $type = key_exists($ext,$this->types)?$this->types[$ext]:$this->types['binary'];  
           $value = sprintf('[{"name":"%s","size":-1,"type":"%s","saved_as":"%s","ext":"%s"}]',$value,$type,$value,$ext);
                       
        }
        return $value;        
    }    
    private $types=array(
        'xls'=>'application/vnd.ms-excel',
        'xlsx'=>'application/vnd.ms-excel',
        'ppt'=>'application/vnd.ms-powerpoint',
        'pptx'=>'application/vnd.ms-powerpoint',
        'doc'=>'application/msword',
        'docx'=>'application/msword',
        'pdf'=>'application/pdf',
        'zip'=>'application/zip',
        'text'=>'text/plain',
        'txt'=>'text/plain',
        'binary'=>'application/octet-stream'
        );
}
        