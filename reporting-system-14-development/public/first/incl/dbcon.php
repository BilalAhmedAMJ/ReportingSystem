<?php

  $dbname   = 'amjc_reports_import'; 
  $hostname = 'localhost';
  $username = 'root'; 
  $password = 'password';
  $id_link = mysql_connect($hostname, $username, $password);

  if (!$id_link) {
    print "Error connecting to mysql server.";
    exit();
  }
?>
