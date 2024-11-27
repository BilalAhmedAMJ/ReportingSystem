<?php


ini_set('display_errors', 1); 
ini_set('display_errors','stdout'); 

error_reporting(E_ALL | E_STRICT);


$app_root=__DIR__.'/../../../';
chdir($app_root);



$_SERVER['argv'][]='orm:generate:entities';
$_SERVER['argv'][]='--generate-annotations="ALL"';
$_SERVER['argv'][]=__DIR__.'/../src';

include $app_root.'/vendor/doctrine/doctrine-module/bin/doctrine-module.php';


