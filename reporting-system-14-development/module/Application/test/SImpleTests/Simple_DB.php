<?php

ini_set('display_errors','stdout'); 



  $dbname   = 'old_reports'; 
  $hostname = 'localhost';
  $username = 'old_reports'; 
  $password = 'old_reports';
  $id_link = mysql_connect($hostname, $username, $password);

  if (!$id_link) {
    print "Error connecting to mysql server.";
    exit();
  }


  $result = @mysql_db_query($dbname,"select user_name from ami_users");

  $user_count = array();
  $multiple=0;
  $real=0;
  while ($row = mysql_fetch_array($result)) {
    $name_parts=explode(" ", $row['user_name']);             
    $user_name=$name_parts[0];
    if(count($name_parts)>1){
        $user_name .= substr($name_parts[count($name_parts)-1],0,1);
    }
    
    //check user count
    if(key_exists($user_name, $user_count)){
        $multiple++; //duplicate username
        $user_count[$user_name]['count']++;
        if($user_count[$user_name]['name'] != $row['user_name']){
            $real++;
            $names=array();
            $names[]=$row['user_name'];
            $names[]=$user_count[$user_name]['name'];
            $user_count[$user_name]['name']=$names;
        }
    }else{
        $user_count[$user_name]=array('name'=>$row['user_name'],'count'=>1);
    }
  }

   print_r($real);
?>