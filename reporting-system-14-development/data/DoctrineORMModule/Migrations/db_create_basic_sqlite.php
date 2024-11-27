<?php

    $sql=array();
    $sql[]="
            CREATE TABLE role (
              id  integer PRIMARY KEY AUTOINCREMENT,
              parent_id int(11) DEFAULT NULL,
              role_id varchar(255)  DEFAULT NULL
            )          
           ";
    $sql[]="        
            CREATE TABLE users (
              id integer PRIMARY KEY AUTOINCREMENT ,
              username varchar(255)  DEFAULT NULL,
              email varchar(255)  NOT NULL,
              displayName varchar(50)  DEFAULT NULL,
              password varchar(128)   NOT NULL
            ) 
           ";
    $sql[]="                 
            CREATE TABLE user_role_linker (
              user_id integer,
              role_id integer
            )               
           ";
    $sql[]="        
        INSERT INTO role VALUES (1,NULL,'guest')
        ,(2,1,'user')
        ,(3,2,'admin')
        ,(4,3,'sys-admin')
           ";
    $sql[]="        
        INSERT INTO user_role_linker VALUES (1,4);        
           ";

    $sql[]="
        INSERT INTO users VALUES (1,'sysadmin','sysadmin@email.com','Sys Admin','$2y$14\$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu')
        ,(2,'gsnational','gsnational@email.com','GS National','$2y$14\$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu')
        ,(3,'muhasibnational','muhasibnational@email.com','Muhasib National','$2y$14\$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu')
        ,(4,'gsjamaat','gsjamaat@email.com','GS Jamaat','$2y$14\$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu')
        ,(5,'muhasibjamaat','muhasibjamaat@email.com','Muhasib Jamaat','$2y$14\$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu')
        ,(6,'gshalqa','gshalqa@email.com','GS Halqa','$2y$14\$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu')
        ,(7,'muhasibhalqa','muhasibhalqa@email.com','Muhasib Halqa','$2y$14\$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu')
             
       ";

print_R("\n".TESTDB_FILE."\n");
$db = new PDO('sqlite:'.TESTDB_FILE);  


// works regardless of statements emulation
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);  
$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

print_r("\nStarting DB Setup\n");

foreach ($sql  as $sql1) {
    $result=$db->query($sql1);
    #var_dump($result);	
}
$db=null;

/*
 * 
 $result=($db->query('select * from role'));
foreach($result as $r)
    print_r($r);
*/
print_r("\nDONE DB SETUP\n");



