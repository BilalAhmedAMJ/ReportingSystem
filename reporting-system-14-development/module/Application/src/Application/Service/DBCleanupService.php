<?php

namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;

use Doctrine\ORM\EntityManager;

class DBCleanupService implements FactoryInterface{
    
    /**
     * @ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * @SerializationContext
     */
    private $context;

    /**
     * @EntityManager
     */
    private $entityManager;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       

        $this->serviceLocator = $serviceLocator;  

        $this->entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');

        //we allow exclusion by MAx depth and by groups        
        $this->context=SerializationContext::create()->enableMaxDepthChecks();
        //always allow default group so we can include fields that do not define any group
        $this->context->setGroups(array(GroupsExclusionStrategy::DEFAULT_GROUP));

        return $this;
    }


    public function deleteOldReports(){
        /*
        $sqlDeleteAnswers='DELETE 
                FROM answers
                WHERE report_id IN (
                    SELECT r.id FROM reports r JOIN periods p ON p.period_code = r.period_from
                    WHERE p.period_start < '2016-01-01'
                )';
        $sqlDeleteReports='DELETE 
                FROM reports 
                WHERE period_from IN (
                    SELECT p.period_code FROM periods p WHERE p.period_start < '2016-01-01'
                )';
        */      
    }

/*
    public function getOffices($nationalGS){
        $oa_service = $this->serviceLocator->get('OfficeAssignmentService');
        
        $qb = $oa_service->createOfficeAssignmentDataSource($nationalGS,'all','active')->getData();

        $query=$qb->getQuery();

        $offices = $query->execute();

        return $offices;
    }
*/
    public function getOffices($nationalGS){
        $oa_service = $this->serviceLocator->get('OfficeAssignmentService');
        
        $qb = $oa_service->createOfficeAssignmentDataSource($nationalGS,'all','active')->getData();

        $query=$qb->getQuery();

        $offices = $query->execute();

        return $offices;
    }

    public function encryptSample($sample){
        return 
            $this->serviceLocator->get('EncryptUtil')->encrypt($sample);
    }

    public function fixUser($office,$emailDomain){
        $code = $this->getDummyCode($office);
        $user = $office->getUser();
        $user->setMemberCode($code);
        $user->setEmail($code.'@'.$emailDomain);
        $user->setUsername($code.'@'.$emailDomain);
        $user->setDisplayName($office->getTitle(true));
        $user->setPassword($code);
        $user->setPhonePrimary('');
        $user->setPhoneAlternate('');
        $user->setMigratedUserId('');

        return $user;
    }

    /**
    * @param OfficeAssignment $office_assignment
    */
    private function getDummyCode($office_assignment){
        $base=100000000;
        $branch=10000;
        $dept=1;
        return $base 
                +
               ($branch*$office_assignment->getBranch()->getId())
                +
               ($dept*$office_assignment->getDepartment()->getId())
               ;
    }        

    /**
    * ( (ID*5557)+(5557*5557) )
    */
    public function cleanUpNames($names,$emailDomain){
        $factor=5557;
        $users =   $this->entityManager
                    ->createQuery('select user from Application\Entity\User user')
                    ->getResult();

        $rand_last=count($names)-1;
        $em = $this->entityManager;
        $file = fopen('sample_codes_file.csv','w');

        print("Total Users:".count($users));

        $count=0;
        foreach ($users as $user) {
            $code = ($user->getId()*$factor)+($factor*$factor);
            $user->setMemberCode($code);
            $user->setEmail('e'.$code.'@'.$emailDomain);
            $user->setUsername('u'.$code);
            $user->setDisplayName($names[rand(0,$rand_last)].' '.$names[rand(0,$rand_last)]);
            $user->setPassword('p'.$code);
            $user->setPhonePrimary('');
            $user->setPhoneAlternate('');
            $user->setMigratedUserId('');
            $user->setPasswordLastReset(new \DateTime());

            #$em->persist($user);

             $this->entityManager->transactional(function($em) use(&$user){
                $em->persist($user);
             });

            fwrite($file,"".$user->getId().", u$code".",p$code\n");
            // if( $user->getId() % 100 == 0) 
            //     fflush($file);

            $count++;
        }
        fclose($file);
        print("Done Users:".$count);
    }

    public function cleanReports(){

        $count=0;
        $em = $this->entityManager;
        for ($i=70; $i<150 ; $i++) {         
            $min= $i    * 10000;
            $max=($i+1) * 10000;
            $answers =   $em->createQuery('select a from Application\Entity\Answer a '
                            . " where a.id >= $min and a.id <= $max")
                        ->getResult();

            print("min <=> max : $min <=> $max : ".count($answers)."\n");    

            foreach ($answers as $answer) {
                //$report = $answer->getReport();
                //$report->setSubmittedByName('Submittig User');                
                //$em->persist($report);
                $answer = $this->updateAnswerWithDummyValues($answer);
                $em->persist($answer);                
                $count++;
            }
            $em->flush();
            unset($answers);
        }
        print("Total: $count \n");
    }

    private function updateAnswerWithDummyValues($answer){
        if($answer->getQuestion()->getAnswerType() == 'MEMO' ){
            $answer->setValue($this->memo);
        }elseif ($answer->getQuestion()->getAnswerType() == 'DATE' ){
            $answer->setValue($this->date20191205);
        }elseif ($answer->getQuestion()->getAnswerType() == 'NUMBER' ){
            $answer->setValue($this->number1234);
        }elseif ($answer->getQuestion()->getAnswerType() == 'TEXT' ){
            $answer->setValue($this->textValue);
        }elseif ($answer->getQuestion()->getAnswerType() == 'REPORT_NOTES' ){
            $answer->setValue($this->memo);
        }else{
            $answer->setValue('');
        }
        return $answer;
    }
    private $number1234="b0578c74c4f2c7ae7f0986739a7b3b2b6939c1b21a791c6c93f7be704c994703SpxMEiWI61o+LkPLWXWmUnsZ/60p850Vof1tZlkzSALw1J92odXSaFJJ83sSDZIlEJPvoV4H2VeC0HKwD4QlUg==";

    private $date20191205="ab9ad9466f5c1c0154e68f1d1fe8ac78a617b5625407a3d220549fd809a3e3334Smcxkg0wE9fmw++Qpd5je/Ow2WMa4jgs7qBLdH8sP+/GRxkrgd6K6qdKPYp/7bRlvzR5Kjb0o9en+6B4aye8w==";

    private $textValue="fe92da743f033e86b3f1ae9459c0ed7d1e2582e780043c4760c45a295b5249dcPruuME5Asrxm2Bi1x7ctu9L1ftLfgikyyfAUafzqKepAcbSLA9QP4nNrxrGIv19pcD15Ys64smc+Z9MCLYNK5Q==";

    private $memo="
6b5eeca9a23b457de19ddf30cebeaf0c1e52731d134c2dd429881dc53cd6a12d1BNMKF4TJe1Y04dpL7jB40+c6aJT+T8Ssf7+ZKfh0a0kFkrYReNZ0CoS/ef6j4oWOAhTaGJA7KVwwtoX00qeAbCtynOm4cjOq7RcOHbIV5LGQugXtdNn7SuUKazXxAYoUbhoMjCsCujkr9pewbDdatXjBrC8Suta4lJnqMDrpJnfnPAuqLP/RdPsqmS24/mUJnd7vMW1JUJHD4Wb2sm5zJnvJLMkgjCfhsfsACGuZVkDoJYc4hgKLIan8bHNJ02VZc96mWMrspuVf+Esc+J1FRnHOTyVmt6ZKP8rh5KAaHLlX0k+0nL0gDpO5VNLH4dF+cu1KfBAjupfmaQoJFppeC5OcCWMFymp7TPMf3pXXsFAiLs3yGKbSGkVzGUSCCMbPYwmI82IChrYf98P4tQnZFxnPgSE0+YCk/YhXUzgZbk3G74ZPe/2P46q7GIJAOxJ7i+7xTJaOKKFYTT4Y/QUdlrjTB3oOCh8kjG+vtSMhC4CGub8tPBZGnbT0rc51VbUbcSxpONMK7ZDHYtFMwvzZ5H80b+2iiE8oWxWeowMIAM0t6ruw5/esDn/OCY8ksQLH605VDLfFWR2WFLcdcu/nuIOaZUnG9AILyInyZ0ZOa85YbA/wYkXxLTvsTWk09jw1Xl3XP7ZiU2OoroobqkGawBsurmWyWn3EC/0b5y0UqY5SEq1R9xeORPF11KMhen0UOIZeOoKjSU/X2PPxL1REzYFLoZvZyFVB8ApGdM+zkxgjcfbFCL/Ay/uQpqLj/ulRiuZXHkLxqOZESC9Dw/5zf5ysVAHrUSWwqWjWl4dipOFxfVFSYOtSf5vsqg//MlUpYFTO04H74adD+QZPxvtiKagSusOaUR5Fb88FQO1xxU8h0e0LvZCNTws9ojUHCBNoKSAEppQvRYqqN3o7Kr23wFtjEKRYCdbyn3/4aN+gGcrMWqrntQzjFPo+jsj7iCZkTkkEuq6PvK2fE1a71cEk0AWzI0gdqurPudduGX0a5GS/p5ekvbTX9Vr0nXRgTkTXfuFHToJQ91/HqpP2hPLdLG3ncWQmv/S9B998gjEDnihmxLhiop6ZquIkDD2Vp4gsEi2w1bM+3c5zS+UkkPGkg==";

}
