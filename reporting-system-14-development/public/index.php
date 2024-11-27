<?php

#error_reporting(E_ALL);
#ini_set('display_errors', true);


 /**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

if ( !defined('ROOT_PATH') ) define('ROOT_PATH', dirname(__DIR__));


$session_time_out= 4*60*60; // 2 hr

//ini_set('session.gc_probability', 1);
//ini_set('session.gc_divisor', 1);
#ini_set('session.cookie_secure', FALSE);
#ini_set('session.use_only_cookies', TRUE);
#ini_set('session.cookie_lifetime',0);
#ini_set('session.gc_maxlifetime', $session_time_out); 

//Validate session

$time = $_SERVER['REQUEST_TIME'];

/** Following https://solutionfactor.net/blog/2014/02/08/implementing-session-timeout-with-php/ */
/**
 * Here we look for the user’s LAST_ACTIVITY timestamp. If
 * it’s set and indicates our $timeout_duration has passed, 
 * blow away any previous $_SESSION data and start a new one.
 */
if (isset($_SESSION['LAST_ACTIVITY']) && ( ($time - $_SESSION['LAST_ACTIVITY']) > $session_time_out) ) {
  session_unset();     
  session_destroy();
  session_start();    
}

function mailFileName ($trans){ return 'Message_' . microtime(true) . '_' . mt_rand() . '.txt'; }

    
/**
 * Finally, update LAST_ACTIVITY so that our timeout 
 * is based on it and not the user’s login time.
 */
$_SESSION['LAST_ACTIVITY'] = $time;


//session_set_cookie_params($session_time_out);

//session_start();

//define('REQUEST_MICROTIME', microtime(true));


// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

define('REQUEST_MICROTIME', microtime(true));

// Setup autoloading
require 'init_autoloader.php';
  

// Run the application!
$application=Zend\Mvc\Application::init(require 'config/application.config.php');

header('Content-type: text/html; charset=utf-8');

$application->run();

