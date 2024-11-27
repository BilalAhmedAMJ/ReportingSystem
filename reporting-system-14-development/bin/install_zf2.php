<?php

chdir(__DIR__.DIRECTORY_SEPARATOR .'..'.DIRECTORY_SEPARATOR);


$ld_lib_path=$_SERVER['LD_LIBRARY_PATH'];


function php_found($exec){
	$out=shell_exec("$exec --version 2>&1");
	if (preg_match('/PHP 5\./', $out)){
		return true;
	}else{
		return false;
	}
}



system($ld_lib_path.'\php.exe'. ' composer.phar update' );
