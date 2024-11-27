<?php


namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;


class  ExportReportDataTest extends PHPUnit_Framework_TestCase{


    public function testExportReportData(){

        $data = array();
         
        $dql = "select report.id, branch.branch_name, pbranch.branch_name parent_branch,period.period_code, dept.id did
                      from \Application\Entity\Report report 
                      join report.branch branch
                      join branch.parent pbranch
                      join report.department dept
                      join report.period_from period
                      where dept.id > 0
                      and period.period_start >= '2015-07-01'
                      order by period.period_start
                      ";

        $em =  BootstrapPHPUnit::getEntityManager();
        $query = $em->createQuery($dql);
        //$query->setParameter('branch',$branch);
        //$query->setParameter('period_start',$period_start->getPeriodStart());
        //$query->setParameter('period_end',$period_end->getPeriodEnd());        
        //$query->setParameter('year',$year.'%');

        $rep_dql = "select report.id,  question.id as question_id, answer.caption, answer.value
                      from \Application\Entity\Answer answer 
                      join answer.question question
                      join answer.report report
                      where report.id = :rep_id
                      and question.id in (370,373,376,379,382,385,388,391,394,397,400,403,406,409,412,415,418,421,424,427,430,433,436,439,460,368,371,374,377,380,383,386,389,392,395,398,401,404,407,410,413,416,419,422,425,428,431,434,437,458,369,372,375,378,381,384,387,390,393,396,399,402,405,408,411,414,417,420,423,426,429,432,435,438,459)
                      ";
                
        $data =  $query->getArrayResult();

        $encryptor = BootstrapPHPUnit::getService('DoctrineEncryptAdapter');
        
        $myfile = fopen("/Users/haroon/report_tarbiyat_20161227.csv", "w");
                
        fwrite($myfile, "Empty LINE\nrid,Branch,Parent Branch,Month,deptid,qid,Question,Value\n");
        foreach ($data as $rep) {
            $a_query = $em->createQuery($rep_dql);
            $a_query->setParameter('rep_id',$rep['id']);
            
            $answers_data =  $a_query->getArrayResult();
            foreach ($answers_data as $a_data) {
                fwrite($myfile, $rep['id'].','.$rep['branch_name'].','.$rep['parent_branch'].','.$rep['period_code'].','.$rep['did'].
                        ','.$a_data['question_id'].','.$a_data['caption'].','.$encryptor->decrypt($a_data['value'])."\n");    
            }
            
        }
        fwrite($myfile, "Empty LINE: DONE\n");
        fclose($myfile);
        echo "\nEmpty LINE: DONE\n";

        //print_r($data);
    }

}