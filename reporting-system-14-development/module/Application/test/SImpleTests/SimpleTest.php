<?php


$paths = array('vendor/zendframework/zendframework/library',
                'module/Application/src',
                'vendor/rhumsaa/uuid/src/',
              );

$inc_path=get_include_path();
foreach ($paths as $dir) {
    $inc_path  = ($inc_path . PATH_SEPARATOR . __DIR__.'/../../../../'.$dir);	
}
set_include_path($inc_path);


use Rhumsaa\Uuid\Uuid;


echo (join("\n",explode(PATH_SEPARATOR, get_include_path()))."\n");

$a1=array("q1"=>"","q2"=>"","q3"=>"",);
$d1=array("q1"=>"","q2"=>"","q3"=>"","a1"=>"","q4"=>"",);

//var_dump(array_diff_key($d1, $a1));



echo strtolower("ABCD\n"); 

echo preg_replace("/##office_bearer_designation##/", "Amin", "Comments by ##office_bearer_designation## ##office_bearer_title##\n");

echo preg_replace("/##office_bearer_designation22##/", "Amin", "Comments by ##office_bearer_designation## ##office_bearer_title##\n");


//var_dump(json_decode('{"validation":[{"empty":false}]}'));

//use DateTime;

date_default_timezone_set('America/Toronto');

$now = new DateTime();
$now->modify('-10 days');
var_dump($now->format('M d/y'));

echo time();



 $now = new DateTime();
 
 $date = clone $now;
 
 
  $date->modify('-7 days');
  echo "\n\nDATE".$date->format('F j, Y, g:i a')."";
 
  $today = clone $now;
  $today->setTime(0,0,0); 
  
  echo "\n\nTODAY".$today->format('F j, Y, g:i a')."\n\n";
  
  echo smartFormat($date);

  function smartFormat($date){
      $now = new \DateTime();
      $today = clone $now;
      $today->setTime(0,0,0); 
      $date_is_today=false;
      if($today < ($date)){
          $date_is_today=true;
      }
      
      $date_is_yesterday=false;
      $yesterday = clone $today;
      $yesterday = $yesterday->modify('-1 day');
      if($yesterday < $date){
          $date_is_yesterday=true;
      }
      
      $date_in_one_week=false;
      $one_week= clone $yesterday;
      $one_week->modify('-5 days');
      if($one_week < $date){
          $date_in_one_week=true;
      }
      
      if($date_is_today){
          return $date->format('h:i a');
      }elseif($date_is_yesterday){
          return 'Yesterday';
      }elseif($date_in_one_week){
          return $date->format('D');
      }else{
          return $date->format('F j, Y, g:i a');
      }
  }
  
  
  function test_uuid_file_name(){
    $tokenString = (hash('crc32', 'Form_RequestforApprovalofAppointmentsofOfficeBearer.pdf'));        
    $tokenString .= Uuid::uuid4()->getHex();
    echo $tokenString;      
  }
  //test_uuid_file_name();

  
  print_r(new DateTime("14-June-2015"));
  
  
  $branch='5,';
  $branch = trim($branch,',');
  print_r(split(',', $branch));
  
  
  $branch='5,9,';
  $branch = trim($branch,',');
  print_r(split(',', $branch));

  $report_messages=array(array('message'=>'Message One from firs tmessage'),array('message'=>'Second message from item 2'));
  
  $result = array_map(function($e){return $e['message'];}, $report_messages);
  //$result = call_user_func_array('array_merge',$report_messages);
  print_r($result);  
  
?>