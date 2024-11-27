<?php

namespace ApplicationTest\Import;

use PHPUnit_Framework_TestCase;

use ApplicationTest\BootstrapPHPUnit;


class  QuestionsMarkazTest extends PHPUnit_Framework_TestCase{
    
    private $userSrv;
    private $reportSrv;
    private $entitySrv;
    private $entityFactory;
    private $entityManager;
    private $currentUser;

    
    
  public function setUp(){
        
        $this->entityManager = BootstrapPHPUnit::getService('Doctrine\ORM\EntityManager');
        
        $this->entitySrv = BootstrapPHPUnit::getService('EntityService');
        
        $this->offsSrv = BootstrapPHPUnit::getService('OfficeAssignmentService');   
            
        $this->userSrv = BootstrapPHPUnit::getService('UserProfileService');
        
        $this->currentUser = $this->userSrv->getUserByUsername('sysadmin');
        
    }
    
    public function testConfigBranchHeadComments(){
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('q')        
            ->from('\Application\Entity\Question','q')
            ->where('q.department!=25')
            ->andWhere("q.caption like '%Comments by ##branch_head_title##%'")
           ;
        
        $questions = $qb->getQuery()->getResult();
       
       foreach ($questions as $q) {
           $constraints = json_decode($q->getConstraints(),true);

           $update_roles = $constraints['update_roles'];
           if(!empty(array_diff(array("local_president", "local_amir"),$update_roles))){
               print_r([$q->getId(),$q->getCaption(),$q->getDepartment()->getDepartmentName(),$update_roles]);
           }
           $msg_cfg = $constraints['report_messages'];
           if(  $msg_cfg['link_type']!='comments' ||
                $msg_cfg['from_office']!='president' ||
                $msg_cfg['to_office']!='report' ||
                $msg_cfg['report_status']!='received' ||
                $msg_cfg['template']!='' ||
                $msg_cfg['title']!='Comments by '  
             ){
               print_r([$q->getId(),$q->getCaption(),$q->getDepartment()->getDepartmentName(),$msg_cfg]);
             }
           
           $display_cfg = (json_decode($q->getDisplayConfig(),true)); 
           if(!empty(array_diff($display_cfg['show_for_status'],array()))){
               print_r([$q->getId(),$q->getCaption(),$q->getDepartment()->getDepartmentName(),$display_cfg]);
           }
           
       }
   }

    public function testConfigParentOffice(){
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('q')        
            ->from('\Application\Entity\Question','q')
            ->where('q.department!=25')
            ->andWhere("q.caption like '%Comments by ##parent_office_bearer_designation##%'")
           ;
        
        $questions = $qb->getQuery()->getResult();
       
       foreach ($questions as $q) {
           $constraints = json_decode($q->getConstraints(),true);
           $update_roles = $constraints['update_roles'];
           if(!empty(array_diff(array("national_secretary", "secretary_imarat"),$update_roles))){
               print_r([$q->getId(),$q->getCaption(),$q->getDepartment()->getDepartmentName(),$update_roles]);
           }
           
           $msg_cfg = $constraints['report_messages'];
           if(  $msg_cfg['link_type']!='feedback' ||
                $msg_cfg['from_office']!='parent' ||
                $msg_cfg['to_office']!='report' ||
                $msg_cfg['report_status']!='received' ||
                $msg_cfg['template']!='' ||
                $msg_cfg['title']!='Feedback Provided by '  
             ){
               // $constraints['report_messages']['link_type']     = 'feedback';
               // $constraints['report_messages']['from_office']   = 'parent'  ;
               // $constraints['report_messages']['to_office']     = 'report'  ;
               // $constraints['report_messages']['report_status'] = 'received';
               // $constraints['report_messages']['template']      = ''        ;
               // $constraints['report_messages']['title']         = 'Feedback Provided by ';
               // $q->setConstraints(json_encode($constraints));

                print_r([$q->getId(),$q->getCaption(),$q->getDepartment()->getDepartmentName(),$msg_cfg]);          
               
             }
           
           $display_cfg = json_decode($q->getDisplayConfig(),true);
           
           //$display_cfg['hide_for_level']=array('Markaz');           
           //$q->setDisplayConfig(json_encode($display_cfg));
           
           $display_cfg = json_decode($q->getDisplayConfig(),true);
           
           print_r($display_cfg);
           
           //print_r([$q->getId(),$q->getCaption(),$q->getDepartment()->getDepartmentName(),array_keys($constraints)]);
           
           // $this->entityManager->transactional(function($em) use(&$questions){
             // foreach ($questions as $q) {               
                // $em->persist($q);
             // }
           // });
           
       }
   }

}
