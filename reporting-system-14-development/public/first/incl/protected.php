<?
Header("Expires: " . gmdate("D, d M Y H:i:s") . "GMT");
Header("Cache-control: no-cache"); 

// Include this at the top of pages which 
// should require the user to log in before
// viewing

// Note: This file will include login.php if the user attempts 
// to access a protected page before logging in. Because of this
// you cannot include this file on pages which are not in the same
// directory as login.php.

session_start();
$myary = $_SESSION['login'];
  if (!$myary["ID"]) {
	//  $url = "http://localhost" . $_SERVER['REDIRECT_URL'];
   include("login.php");
   exit();
  }
$sessionlogin = $myary['login'];

?>