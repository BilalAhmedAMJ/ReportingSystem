<?php


$phpunit_runner=__DIR__.'/../../../vendor/phpunit/phpunit/phpunit';

define('ROOT_DIR',__DIR__.'/../../../');

chdir(__DIR__);

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1); 
ini_set('display_errors','stdout'); 

#print_r([__DIR__,$phpunit_runner]);
#exit;

$file = '';

if(count($argv)>1 && $argv[1]!='phpunit_runner.php' ){
    $file_parts = explode('.',$argv[1]);    
    $file=$file_parts[count($file_parts)-2];        
    unset($argv[1]);
    unset($_SERVER['argv'][1]);
}else{
//$_SERVER['argv'][]='ApplicationTest.*';
//$_SERVER['argv'][]='ApplicationTest.*MessagesServiceTest.*';
//$_SERVER['argv'][]='ApplicationTest.*ReportSubmissionServiceTest.*';
//$_SERVER['argv'][]='UserProfileServiceTest.*';
//$_SERVER['argv'][]='.*MessagesServiceTest.*';
//$_SERVER['argv'][]='.*OfficeRequestProcessingTest.*';
//$_SERVER['argv'][]='.*DBEncryption.*';
//$_SERVER['argv'][]='.*PeriodTest.*';
//$_SERVER['argv'][]='.*ImportReports.*';
//$_SERVER['argv'][]='.*ImportReports.*';        
//$_SERVER['argv'][]='.*ImportUsers.*';
#$_SERVER['argv'][]='ApplicationTest.*EmailServiceTest.*';
#$_SERVER['argv'][]='ApplicationTest.Entity.PeriodTest::testYearCodeAfterStartMonth';
  $file = '.*PeriodTest.*';
  
  $file = '.*RemindersFormTest.*';
  $file = '.*UserServiceTest.*';
  $file = '.*EncryptUtil.*';
}

print "running for $file \n";

#$_SERVER['--no-globals-backup'];

#$_SERVER['argv'][]='--color';

$_SERVER['argv'][]='--debug';
$_SERVER['argv'][]='--verbose';

#$_SERVER['argv'][]='--stderr';

#$_SERVER['argv'][]='--log-junit';
#$_SERVER['argv'][]='--testdox-text';
#$_SERVER['argv'][]='--testdox';
#$_SERVER['argv'][]='--tap';
#$_SERVER['argv'][]='--help';

$_SERVER['argv'][]='--filter';
$_SERVER['argv'][]='.*'.$file.'.*';


//print_r($_SERVER);

include $phpunit_runner ;

