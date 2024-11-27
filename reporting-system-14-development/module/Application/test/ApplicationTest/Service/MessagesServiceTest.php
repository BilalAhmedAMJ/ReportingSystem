<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;


use Application\Service\MessagesService;


class  MessagesServiceTest extends PHPUnit_Framework_TestCase{

    public function atestGetRecipientListByRule(){
        $userSrv = BootstrapPHPUnit::getService('UserProfileService');
        //$sender = $userSrv->getUserByUsername('gsnational');
        $msgSrv = BootstrapPHPUnit::getService('MessagesService');
        
        $user = $userSrv->getUserByUsername('gsIBN');

        
        $recipients = $msgSrv->getReciepientList($user,'');        
        $this->assertEquals(count($recipients),1);
        $this->assertEquals('Saeed Khalid',($recipients[0]->getDisplayName()));


        $recipients = $msgSrv->getReciepientList($user,'subbranches');        
        $this->assertEquals(7,count($recipients));


        $recipients = $msgSrv->getReciepientList($user,'samebranch');        
        $this->assertEquals(22,count($recipients));
        

        $recipients = $msgSrv->getReciepientList($user,'parentbranch');        
        $this->assertEquals('Sabih Nasir',($recipients[0]->getDisplayName()));

    }
    
    public function atestLoadMessage(){
        
        $userSrv = BootstrapPHPUnit::getService('UserProfileService');
        $sender = $userSrv->getUserByUsername('gsIBN');
        $msgSrv = BootstrapPHPUnit::getService('MessagesService');
        
        $message = $msgSrv->getMessage(7);

        print_r($message->getHtmlBody());
        
        $this->assertNotNull($message);
    }
    
    public function atestComposeMessage(){
        
        $userSrv = BootstrapPHPUnit::getService('UserProfileService');
        $sender = $userSrv->getUserByUsername('gsIBN');
        
        $officeSrv = BootstrapPHPUnit::getService('OfficeAssignmentService');
        $offices = $officeSrv->getActiveOffices($sender); 
        $msgSrv = BootstrapPHPUnit::getService('MessagesService');
        $messageBody='
        Aslamo Alaikum wa Rahmatullah,<br/>
        <br/>
        Dear Brothers,<br/>
        <br/>
        This is a test message, for testing message compose service.<br/>
        <br/>
        This is HTML version so you should see text in <b>bold</b> and <i>italic</i> <u>etc</u>.<br/>
        <br/>
        This is an internal message. You will not get an email for this message, 
        rather it will show up in online reporting tools messages list as a new message.<br/>
        <br/>
        Wassalam<br/>
        <br/>
        {$sender->getDisplayName()}<br/>
        ';
        //$msgSrv->getReciepientList($sender,'parentbranch');
        $rule='';
        $message = $msgSrv->composeMessage($sender,'Test Message : Composed by Service',
                                            $messageBody,$rule,MessagesService::Message_Type_Internal,
                                            $offices[0]
                                            );
        $this->assertEquals('Saeed Khalid',$message->getSender()->getDisplayName());
        $this->assertEquals('internal',$message->getMessageType());
        $this->assertEquals('DRAFT',$message->getStatus());
        $this->assertEquals(new \DateTime(),$message->getDateModified());
        $this->assertEquals(null,$message->getDateSent());
        $this->assertEquals(1,count($message->getUserMessages()));
        $this->assertEquals('Saeed Khalid',$message->getUserMessages()->get(0)->getUser()->getDisplayName());
        $this->assertEquals(null,$message->getUserMessages()->get(0)->getDateRead());
        $this->assertEquals(null,$message->getUserMessages()->get(0)->getDateDeleted());
        $this->assertFalse($message->getUserMessages()->get(0)->isDeleted());                                                        
        $this->assertTrue($message->getUserMessages()->get(0)->isUnread());
        
        $rule='parentbranch';
        $message = $msgSrv->composeMessage($sender,'Test Message : Composed by Service',$messageBody,$rule,MessagesService::Message_Type_Internal);
        $this->assertEquals('Sabih Nasir',$message->getUserMessages()->get(0)->getUser()->getDisplayName());
        $this->assertEquals(1,count($message->getUserMessages()));

        $rule='subbranches';
        $message = $msgSrv->composeMessage($sender,'Test Message : Composed by Service',$messageBody,$rule,MessagesService::Message_Type_Internal);
        $this->assertEquals(7,count($message->getUserMessages()));


        $rule='samebranch';
        $message = $msgSrv->composeMessage($sender,'Test Message : Composed by Service',$messageBody,$rule,MessagesService::Message_Type_Internal);
        $this->assertEquals(22,count($message->getUserMessages()));
        
        $found=false;
        foreach ($message->getUserMessages() as $um) {
            if($um->getUser()->getDisplayName()=='Mirza Mubarak Ahmad')
                $found=true;
            //print('{$um->getUser()->getDisplayName()}\n');
        }
        $this->assertTrue($found);

    }


    private $users = array(
                'National_Sec'=>'2166',   'National_GS'=>'2146',
                'Imaraat_GS'=>'2200',     'Imaraat_Sec'=>'2209',
                'Halqa_GS'=>'2768',       'Halqa_Sec'=>'2773',
                'Jamaat_GS'=>'2519',      'Jamaat_Sec'=>'2521'
                );
    
    private $offices = array('GS'=>5,'P'=>'26','Mal'=>4);
    
    private $send_to_rule = array('not_completed'=>'not_completed','not_verified'=>'not_verified','not_received'=> 'not_received','all'=> 'all');
    
    private $level_rule = array('all'=>'all','local'=>'local', 'imarat'=> 'imarat','markaz'=>'markaz');
    
    
    public function testMessageListNationalGS(){
        
            //National GS $data = array('branch'=>0,'month'=>'May','office'=>$this->offices['GS'],'send_to_rule'=>'not_completed');
            //National Sec
            $data = array('branch'=>0,'month'=>'','office'=>'4');
            
            $current_user = BootstrapPHPUnit::getService('UserProfileService')->getUserById($this->users['Halqa_Sec']);        
            
            if($data['branch']==0){
                unset($data['branch']);
            }            
            
            $officeSrv  = BootstrapPHPUnit::getService('OfficeAssignmentService');
            
            $data['period'] = $this->getPeriod($data);
            
            $reciepient = $officeSrv->getReminderReciepientList($current_user,$data);
            
            print_r($data);
            print("\n");
            foreach ($reciepient as $value) {
                print_r($value->__toString()."\n");    
            }
            
    }

    public function testPeriod(){
        print_r($this->getPeriod(array('month'=>'Dec') ));
        print_r($this->getPeriod(array('month'=>'Nov') ));
        
        print_r($this->getPeriod(array('month'=>'Jan') ));
        print_r($this->getPeriod(array('month'=>'Feb') ));
        
        print_r($this->getPeriod(array('month'=>'Mar') ));
        print_r($this->getPeriod(array('month'=>'Apr') ));
        print_r($this->getPeriod(array('month'=>'May') ));
    } 
    
    
    private function getPeriod($data){
                    //convert month to period
            if($data['month']){
                 
                $monthDate = new \DateTime($data['month']);
                $nowDate = new \DateTime();
                //if period is in future decrement year to get last year period
                if($nowDate<$monthDate){
                    $monthDate->modify('-1 year');
                }
                $period = $monthDate->format('M-Y');
                return $period; 
            }
            return null;
    }
}
