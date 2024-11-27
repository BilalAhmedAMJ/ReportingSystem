<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Form\Form;
use Zend\Form\Element;



use ZfcDatagrid\Column;

use Application\View\HighChart\DataTransform;


class QuestionSetupController extends AbstractActionController
{


    const REPORT_CONFIG='monthly_report';
    const REPORT_ID='17';

    private function setupForm($form,$post){

        $configSrv = $this->getServiceLocator()->get('ConfigService');
        
        $months = $configSrv->getConfig('months');
        //$month_value = $months_simple[];
        $monthYear = new Element\MonthSelect('monthyear');
        $monthYear->setMinYear(2014);
        $monthYear->setShouldRenderDelimiters(false);
        
        $monthYear->getMonthElement()->setValueOptions($months);

        if(key_exists('monthyear', $post)){
            $monthYear->getMonthElement()->setValue($post['monthyear']['month']);
        }      
        
        $monthYear->getMonthElement()->setEmptyOption('All');
        $monthYear->getYearElement()->setEmptyOption('All');
        $form->add($monthYear);
        
        $dept_ids=array();
        $departments = $configSrv->listDepartmentNames($dept_ids,array('reportable'=>1));
        $form->setOption('departments',$departments);

        $branchSrv = $this->getServiceLocator()->get('BranchManagementService');        
        $form->setOption('branch_levels',$branchSrv::BRANCH_LEVEL);
                        
        if($this->getRequest()->isPost() ){
            $form->setData($post);
        }
        
        $form->prepare();

        return $form;
    }
    
    private function setupReport($post_data,$report){

        $entity_srv = $this->serviceLocator->get('EntityService');

        $report->setBranch($entity_srv->getObject('Branch',$post_data['branch'])); 
        $report->setDepartment($entity_srv->getObject('Department',$post_data['department'])); 
        $report->setPeriodFrom($entity_srv->getObject('Period',$post_data['period_from']));
        $report->setPeriodTo($report->getPeriodFrom());
        $report->setStatus($post_data['report_status']);

        return $report;        
    }

    public function emptyAction(){
        $this->layout('layout/ajax');
        return "";
    }
    public function viewAction(){

        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();

        $entityFactory = $this->getServiceLocator()->get('entityFactory');
        $entity_srv = $this->serviceLocator->get('EntityService');
        $report_srv = $this->getServiceLocator()->get('ReportSubmissionService');
        $entity_mgr  = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $entity_mgr->getConnection()->beginTransaction();
        $report = $entity_srv->getObject('Report',$this::REPORT_ID);

        $report_form = $this->getServiceLocator()->get('CreateFormService')->getForm('report_details');
        $template = 'application/question-setup/view';
        if($this->getRequest()->isPost()){

            $post_data=$this->params()->fromPost();
            
            $report = $this->setupReport($post_data,$report);
            $post_data['branch_level']=$report->getBranch()->getBranchLevel();
            $questions = $report_srv->getQuestions($this::REPORT_CONFIG,$post_data);
            $answers = array();
            foreach ($questions as $question) {
                $num_answers = 1;//each quesiton will have at least one answer
                $constrt = $question->getConstraintsArray(); 
                if($constrt && is_array($constrt)&& key_exists('answers_count', $constrt)){
                    $num_answers=$constrt['answers_count'];
                }
                
                    for ($i=1; $i <= $num_answers ; $i++) { 
                        $answer = $entityFactory->getAnswer();
                        $answer->setCaption($report->replaceTokens($question->getCaption()));
                        $answer->setQuestion($question);
                        $answer->setReport($report);
                        $answer->setAnswerNumber($i);
                        $answers[]= $answer;
                    }
            }
            $entity_mgr->persist($report);
            foreach ($answers as $ans) {               
                $entity_mgr->persist($ans);
            }
            $entity_mgr->flush();

            //Setup Report Data for Report_Form
            $report_form->setData($post_data);

            $saved_answers = $report_srv->getAnswers($report);
            error_log('Saved Answers ::'.count($saved_answers));
            //setup questions for Report_form
            $this->getServiceLocator()->get('CreateFormService')->addAnswers($report_form,$saved_answers);

            //Free our entities from DB connection
            $entity_mgr->detach($report);
            foreach ($answers as $ans) {               
                $entity_mgr->detach($ans);
            }
            $template = 'application/report/report.phtml';
            $this->layout('layout/ajax');
        }
        
        //Rollback transaction
        if ($entity_mgr->getConnection()->isTransactionActive()) {
            $entity_mgr->getConnection()->rollBack();
        }
        //$entity_mgr->clear();

        //$layout='normal';
        
        $viewModel = new ViewModel(array('report'=>$report,'form'=>$report_form,
                                   'isPost'=>$this->getRequest()->isPost()                                    
                                   ));

        $viewModel->setTemplate($template);
        return $viewModel;
    }

    public function exportAction(){

        $entityFactory = $this->getServiceLocator()->get('entityFactory');
        $entity_srv = $this->serviceLocator->get('EntityService');
        $report_srv = $this->getServiceLocator()->get('ReportSubmissionService');
        $entity_mgr  = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $data = array();
         
        $dql = "select report.id, branch.branch_name, branch.branch_name parent_branch,period.period_code,dept.department_name department_name, dept.id did
                      from \Application\Entity\Report report 
                      join report.branch branch
                      join report.department dept
                      join report.period_from period
                      where report.id in (83556, 83890, 84125, 84178, 84230, 84314, 84421, 84491, 84524, 84563, 84612, 84702, 84751, 84784, 84843, 84911, 85038, 85091, 85283, 85387, 85640, 85801, 83741, 83906, 84157, 84183, 84262, 84328, 84438, 84496, 84536, 84568, 84620, 84709, 84756, 84797, 84860, 84949, 85059, 85096, 85289, 85484, 85642, 85853, 82846, 83879, 83920, 84161, 84189, 84283, 84359, 84459, 84507, 84544, 84577, 84644, 84713, 84778, 84816, 84861, 85023, 85071, 85110, 85365, 85540, 85653, 86066, 83223, 83883, 84118, 84171, 84192, 84303, 84417, 84488, 84521, 84550, 84585, 84651, 84741, 84780, 84828, 84889, 85028, 85074, 85227, 85382, 85626, 85763)
                      order by period.period_start
                      ";

/**
 * 
                      where period.period_start >= '2020-06-01'
                      and dept.id in (5)

 *  */                      

        $em =  $entity_mgr;
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
                      and question.question_type not in ('MEMO','MEMO_YES_NO','REPORT_NOTES','FILE')
                      ";
                
        $data =  $query->getArrayResult();

        $encryptor = $this->getServiceLocator()->get('DoctrineEncryptAdapter');
        
        $myfile = fopen("/var/www/dev_rep/data/upload/reports_20200802.csv", "w");

        fwrite($myfile, "Empty LINE\nrid,Branch,Parent Branch,Month,deptid,qid,Question,Value\n");
        foreach ($data as $rep) {
            error_log('Exporting '.$rep['id']);
            $a_query = $em->createQuery($rep_dql);
            $a_query->setParameter('rep_id',$rep['id']);
            
            $answers_data =  $a_query->getArrayResult();
            foreach ($answers_data as $a_data) {
                fwrite($myfile, $rep['id'].','.$rep['department_name'].','.$rep['branch_name'].','.$rep['parent_branch'].','.$rep['period_code'].','.$rep['did'].
                        ','.$a_data['question_id'].','.$a_data['caption'].',"'.trim($encryptor->decrypt($a_data['value'])).'"'."\n");    
            }
            
        }
        fwrite($myfile, "Empty LINE: DONE\n");
        fclose($myfile);
        echo "\nEmpty LINE: DONE\n";

        return $vm = new ViewModel(array('count'=>count($data)));
    }
    
}
