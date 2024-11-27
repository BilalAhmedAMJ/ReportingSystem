<?php


ini_set('display_errors', 1); 
ini_set('display_errors','stdout'); 
error_reporting(E_ALL | E_STRICT);


#$_SERVER['argv'][]='list';
#$_SERVER['argv'][]='migrations:status';
$_SERVER['argv'][]='migrations:migrate'; 
#$_SERVER['argv'][]='orm:validate-schema';

#$argv[]='orm:validate-schema';
#ob_start();
$app_root=__DIR__.'/../../../';
chdir($app_root);

include $app_root.'/vendor/doctrine/doctrine-module/bin/doctrine-module.php';
#$contents = ob_get_contents();
#ob_end_clean();
#print("$contents \n");


#putenv("Path=".$_SERVER['Path'].PATH_SEPARATOR.$_SERVER['LD_LIBRARY_PATH']);

#exec('php '.$app_root.'/vendor/doctrine/doctrine-module/bin/doctrine-module.php'. ' migrations:migrate' );

