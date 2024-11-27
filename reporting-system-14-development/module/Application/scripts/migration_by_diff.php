<?php

ini_set('display_errors', 1); 
ini_set('display_errors','stdout'); 

error_reporting(E_ALL | E_STRICT);


$app_root=__DIR__.'/../../../';
chdir($app_root);


#$_SERVER['argv'][]='list';
#$_SERVER['argv'][]='migrations:diff'; 
#$_SERVER['argv'][]='migrations:generate'; 
#$_SERVER['argv'][]='orm:validate-schema';


include $app_root.'/vendor/doctrine/doctrine-module/bin/doctrine-module.php';

