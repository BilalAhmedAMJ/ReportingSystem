<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;

use ApplicationTest\BootstrapPHPUnit;

use Application\Service\DashboardService;
use Application\View\HighChart\DataTransform;

class  DBEncryptionTest extends PHPUnit_Framework_TestCase{


    public function atestSha3(){

        print_r("\n".sha3('')."\n");

    }
    
    public function testKeyIterations(){
        
        $config = BootstrapPHPUnit::getService('config');        
        $adapter = $config['doctrine']['encryption']['orm_default']['adapter']();
        print_r(array($adapter->decrypt('1b8d8c134d710213613cc684eeec5aa3a3de74541c260764d0f7771b2caa64b3zhVysfviNZLBt+pPckPJOBHALAbVBa9pWebyeOgIx34die5yH2gPketOS7Q6sbDAdgqAFVw5HWhi8sobS7JMVp6of65cn0c59WqUNS4gsj2/qLsBIjDdgtNZIerLDaif')));
    }
    
    //
     static $ids = array(
                        // 'id < 2 AND id > 0',
                        // 'id < 4300 AND id > 0',
                        // 'id < 4400 AND id >= 4300',
                        // 'id < 4500 AND id >= 4400',
                        // 'id < 4600 AND id >= 4500',
                        // 'id < 4700 AND id >= 4600',
                        // 'id < 4800 AND id >= 4700',
                        // 'id < 4900 AND id >= 4800',
                        // 'id < 5000 AND id >= 4900',
                        // 'id < 5100 AND id >= 5000',
                        // 'id < 5200 AND id >= 5100',
                        // 'id < 5300 AND id >= 5200',
                        // 'id < 6300 AND id >= 5300',
                        // 'id < 6400 AND id >= 6300',
                        // 'id < 6500 AND id >= 6400',
                        // 'id < 8400 AND id >= 6500'
        );
        
        public function testUpdate_0(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_1(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_2(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_3(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_4(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_5(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_6(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_7(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_8(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_9(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_10(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_11(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_12(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_13(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_14(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_15(){ $this->UpdateDBAndEncryptUser();}
        public function testUpdate_16(){ $this->UpdateDBAndEncryptUser();}
    

    public function atestDefaultAdapterDecrypt(){
        
        $util = BootstrapPHPUnit::getService('EncryptUtil');
        $cipher = $util->createDefaultAdapter();
        
        // $entityManager = BootstrapPHPUnit::getService('Doctrine\ORM\EntityManager');
        // $conn = $entityManager->getConnection();
// 
// 
        // print "Trans started\n";            
        // $stmt = $conn->executeQuery('select * from messages limit 1000');      
//         
        // print "Fetch started\n"; 
        // while($row=$stmt->fetch()){
            // print($this->enc($cipher,$row['html_body'])."\n");
            // print($this->enc($cipher,$row['text_body'])."\n");
            // print($this->enc($cipher,$row['subject'])."\n");
        // }        
        // print "Fetch Done\n";
        // $conn->executeQuery('commit');
        // print "Trans Done\n";
        
        //$cipher->setKeyIteration(1);

        //print($cipher->decrypt('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')."\n\n");

        //print($cipher->decrypt('b3c3d44f2e680363a85f8a6368261d4851aa9a9f0377b68f61e5a4b4ec5744249ZHxyV4hkjK9CLVISz9zPluIC9i5Oi/QsyIdxTIz3hLwxlstGvmBZtoXZiPfUyyK8k9mG+Uve3ua3gqyGtDMhMDJmonGmt6+w+ApDFpISnNSQ90R0AqYFe14/C3StOEG')."\n\n");
        
        $entityManager = BootstrapPHPUnit::getService('Doctrine\ORM\EntityManager');
        
        $m = $entityManager->find('Application\Entity\Message',33);
        
        print("[".$m->getSubject()."]\n\n");
        
    }
    public function UpdateDBAndEncryptUser(){
        
        $config = BootstrapPHPUnit::getService('config');
        
        $adapter = $config['doctrine']['encryption']['orm_default']['adapter']();
        
        // $adapter->setKeyIteration(500);
                       
        $entityManager = BootstrapPHPUnit::getService('Doctrine\ORM\EntityManager');
        $conn = $entityManager->getConnection();
        
        $row=null;
        
        $sql_hash = 'update users set ehash=?, mhash=?, uhash=?,
                     username=?, email=?, display_name=?, 
                     member_code=?,phone_primary=?, phone_alternate=?
                     where id=?';

        
        
        $done=0;
 
        $conn->executeQuery('begin');
        
        $id = array_shift($this::$ids);
        
        if(empty($id)){
            print "No ids to process [$id]\n";
            return;
        }else{
            print "Processing [$id]\n";
        }
        
        print "Trans started for [$id]\n";            
        $stmt = $conn->executeQuery('select * from users where '.$id);      
        
        print "Fetch started\n"; 
         while($row=$stmt->fetch()){
            
            print "started ";
            
            $start = explode(' ',microtime());
            $enc_array=array(
                 sha3(strtolower($row['email_bk'])),sha3(strtolower($row['membercode_bk'])),sha3(strtolower($row['username_bk'])),
                 $this->enc($adapter,strtolower($row['username_bk'])),$this->enc($adapter,strtolower($row['email_bk'])),$this->enc($adapter,$row['displayname_bk'])
                ,$this->enc($adapter,$row['membercode_bk']),$this->enc($adapter,$row['phoneprimary_bk']),$this->enc($adapter,$row['phonealternate_bk'])
                ,$row['id']
                )
            ;
            $end = explode(' ',microtime());
            print ("  ... enc done in [".( ($end[1]*1000 + $end[0]) - ($start[1]*1000 + $start[0])  )."] ms");
                    
            $conn->executeUpdate($sql_hash,
                                $enc_array,
                                array( \PDO::PARAM_STR,\PDO::PARAM_STR,\PDO::PARAM_STR,
                                        \PDO::PARAM_STR,\PDO::PARAM_STR,\PDO::PARAM_STR,
                                        \PDO::PARAM_STR,\PDO::PARAM_STR,\PDO::PARAM_STR,
                                        \PDO::PARAM_STR
                                      )
                                );
            
            $done++;
            print " .... completed ... $done\n";
            flush();
        }
        print "Fetch Done\n";
        $conn->executeQuery('commit');
        print "Trans Done\n";
    }


    public function atestUpdateDBAndEncryptMessages(){
        
        $config = BootstrapPHPUnit::getService('config');
        
        $adapter = $config['doctrine']['encryption']['orm_default']['adapter']();
        
        // $adapter->setKeyIteration(500);
                       
        $entityManager = BootstrapPHPUnit::getService('Doctrine\ORM\EntityManager');
        $conn = $entityManager->getConnection();
        
        $row=null;
        
        $sql_hash = 'update messages set subject=?, text_body=?, html_body=?
                     where id=?';
                     
        $done=0;
 
        $conn->executeQuery('begin');
        print "Trans started for mesages\n";            
        $stmt = $conn->executeQuery('select * from messages');      
        
        print "Fetch started\n"; 
        
         while($row=$stmt->fetch()){
            
            print "started ";
            
            $start = explode(' ',microtime());

            $enc_array=array(
                 $this->enc($adapter,$row['subject']),$this->enc($adapter,$row['text_body']),$this->enc($adapter,$row['html_body'])
                ,$row['id']
            );
            
            $end = explode(' ',microtime());
            
            print ("  ... enc done in [".( ($end[1]*1000 + $end[0]) - ($start[1]*1000 + $start[0])  )."] ms");
                    
            $conn->executeUpdate($sql_hash,
                                $enc_array,
                                array( \PDO::PARAM_STR,\PDO::PARAM_STR,\PDO::PARAM_STR,
                                        \PDO::PARAM_STR
                                      )
                                );
            
            $done++;
            print " .... completed ... $done\n";
            flush();
            
         }        
        print "Fetch Done\n";
        $conn->executeQuery('commit');
        print "Trans Done\n";

    }

    public function atestEncDepartments(){
        $this->GenericTableEncAndUpdate('departments', array('rules','guide_lines'));
    }

    public function atestEncAnswers(){
        $this->GenericTableEncAndUpdate('answers', array('value'));
    }
    

    
    private function GenericTableEncAndUpdate($table,$fields,$adapter=null){
        
        if(!$adapter){
            $config = BootstrapPHPUnit::getService('config');        
            $adapter = $config['doctrine']['encryption']['orm_default']['adapter']();
        }
        
        // $adapter->setKeyIteration(500);
                       
        $entityManager = BootstrapPHPUnit::getService('Doctrine\ORM\EntityManager');
        $conn = $entityManager->getConnection();
        
        $row=null;
        
        $sql_hash = 'update '.$table.' set ';
        $types_array=array();             
        foreach ($fields as $field) {
            $sql_hash .= ' '.$field.' = ? ,';
            $types_array[]=\PDO::PARAM_STR;
        }
        
        $sql_hash = rtrim($sql_hash,',');
        
        $sql_hash .=' where id=? ';        
        $types_array[]=\PDO::PARAM_STR;//for ID
                
        $done=0;
 
        $conn->executeQuery('begin');
        print "Trans started for $table\n";            
        $stmt = $conn->executeQuery('select * from '.$table);      
        
        print "Fetch started\n"; 
        
         while($row=$stmt->fetch()){
            
            print "started ";
            
            $start = explode(' ',microtime());

            $enc_array=array();
            foreach ($fields as $field) {
                $enc_array[]=$this->enc($adapter,$row[$field]);
            }
            
            $enc_array[] = $row['id'];
            
            
            $end = explode(' ',microtime());
            
            print ("  ... enc done in [".( ($end[1]*1000 + $end[0]) - ($start[1]*1000 + $start[0])  )."] ms");
                    
            $conn->executeUpdate($sql_hash,
                                $enc_array,
                                $types_array
                                );
            
            $done++;
            print " .... completed ... $done\n";
            flush();
            
         }        
        print "Fetch Done\n";
        $conn->executeQuery('commit');
        print "Trans Done for $table\n";        
    }


   public function atestChangEncAdapter(){
       $this->ChangeEncAdapter('messages',array('subject','text_body','html_body'));
       $this->ChangeEncAdapter('departments',array('rules','guide_lines'));
       $this->ChangeEncAdapter('answers',array('value'));
       $this->ChangeEncAdapter('users',array('username','display_name','email','member_code','phone_primary','phone_alternate'));
   }

    public function ChangeEncAdapter($table,$fields){
        
        $enc_util = BootstrapPHPUnit::getService('EncryptUtil');        
        
        $dec_adapter = $enc_util->createPlainTextAdapter();

        //$enc_adapter =  $enc_util->createDefaultAdapter();

        $config = BootstrapPHPUnit::getService('config');        
        $enc_adapter = $config['doctrine']['encryption']['orm_default']['adapter']();
        
        
        
        // $dec_adapter = $enc_util->createDefaultAdapter();
        // $enc_adapter =  $enc_util->createPlainTextAdapter();

        $entityManager = BootstrapPHPUnit::getService('Doctrine\ORM\EntityManager');
        $conn = $entityManager->getConnection();
        
        $row=null;
        
        $sql_hash = 'update '.$table.' set ';
        $types_array=array();             
        foreach ($fields as $field) {
            $sql_hash .= ' '.$field.' = ? ,';
            $types_array[]=\PDO::PARAM_STR;
        }
        
        $sql_hash = rtrim($sql_hash,',');
        
        $sql_hash .=' where id=? ';        
        $types_array[]=\PDO::PARAM_STR;//for ID
                
        $done=0;
 
        $conn->executeQuery('begin');
        print "Trans started for $table\n";            
        $stmt = $conn->executeQuery('select * from '.$table);      
        
        print "Fetch started\n"; 
        
         while($row=$stmt->fetch()){
            
            print "started ";
            
            $start = explode(' ',microtime());

            $enc_array=array();
            foreach ($fields as $field) {
                $enc_array[]=$this->enc($enc_adapter,$this->dec($dec_adapter, $row[$field]));
            }
            
            $enc_array[] = $row['id'];
            
            
            $end = explode(' ',microtime());
            
            print ("  ... enc done in [".( ($end[1]*1000 + $end[0]) - ($start[1]*1000 + $start[0])  )."] ms");
                    
            $conn->executeUpdate($sql_hash,
                                $enc_array,
                                $types_array
                                );
            
            $done++;
            print " .... completed ... $done\n";
            flush();
            
         }        
        print "Fetch Done\n";
        $conn->executeQuery('commit');
        print "Trans Done for $table\n";        
    }
    
    private function enc($adapter,$data){
            
        if(empty($data))    return $data;
                
        $val = $adapter->encrypt($data);
        
        return $val;
    }

    private function dec($adapter,$data){
            
        if(empty($data))    return $data;
                
        $val = $adapter->decrypt($data);
        
        return $val;
    }

    public function atestSimple(){
            
        $key = rtrim(`/var/local/amj/hex_generator`);
        $key_packed = pack("H*",$key);
        $key_packed = 'KEY';
        $this->assertEquals(1,1);
        
        //print_r([substr(strrev($key),0,1),substr(($key),0,1)]);
        
        //$cipher = \Zend\Crypt\BlockCipher::factory('aes');
        //$cipher->setKey($key_packed);
        //$cipher->setBinaryOutput(true);
        //print_r($cipher->getMode());
        //print_r(bin2hex($cipher->encrypt('Test123')));
        
        //print_r($cipher->decrypt(hex2bin('89B6405211BB9BC455B1A499FD5382CFDA1D63EBCD1F9B2E8C98BC76B87F266EDBFF001BCF400CAB22207B1EEA0E2C658E1BC2BFF7F28FFBD69670B287B9A88A34CEBE8AE5EAAADD11150867ECDABA0B') ));
    }
    
    public function atestDBConnection(){
            
        $entityManager = BootstrapPHPUnit::getService('Doctrine\ORM\EntityManager');
        
        //$users = $entityManager->findAll('\Application\Entity\User');
        
        $user =  $entityManager->find('\Application\Entity\User',5);
        
        //print_r([$user->getEmail('test@email.com')]);
        
        $user->setEmail('test@email.com');
        
        $this->assertNotNull($user);
        
        $entityManager->persist($user);
        
        $entityManager->flush();
        
        //ae2171c0a5dbbc9baec482d78a812c5835c6dd0ef1da8c9871c8c7a904ce82ceDR63yKAgwotri3z9AM1vC98sidjhItJPY9mdE8PgckI=
        
        $config = BootstrapPHPUnit::getService('config');
        $adapter = $config['doctrine']['encryption']['orm_default']['adapter']();
        print_r(get_class($adapter));
        $enc1 = $adapter->encrypt('test@email.com');
        $enc2= $adapter->encrypt('test@email.com');
        //print_r([$enc1,$enc2,$enc1 == $enc2,$adapter->decrypt($enc1),$adapter->decrypt($enc2) ]); 
        
        /**
         * @var \DoctrineEncrypt\Subscribers\DoctrineEncryptSubscriber
         */
        //$subscriber = BootstrapPHPUnit::getService('doctrine.encryption.orm_default');
                
         //$subscriber->

        //foreach ($users as $user) {
            
        //}
        
    }


    public function testCognitoCOnf(){

        $configSrv = $BootstrapPHPUnit::getService('ConfigService');
        $conf = $configSrv->getConfigValues('cognito');
        $encUtil = BootstrapPHPUnit::getService('EncryptUtil')->createDefaultAdapter();
        print_r($encUtil->decrypt($conf[clientId]));
        

    }

}

/*
 
| hex(aes_encrypt(unhex(@key),'Test123'))                                                                                                                          |
+------------------------------------------------------------------------------------------------------------------------------------------------------------------+
|
  89B6405211BB9BC455B1A499FD5382CFDA1D63EBCD1F9B2E8C98BC76B87F266EDBFF001BCF400CAB22207B1EEA0E2C658E1BC2BFF7F28FFBD69670B287B9A88A34CEBE8AE5EAAADD11150867ECDABA0B
  33373237326535643237346465313363386537346464636137383636633532613636616531306233336161333365663335646137613235656130343235333936a7814765bffb0b37a8712c01a3804d8aa9aef6edbb07c88a50ca60432e2a2a32
 * |

*/

