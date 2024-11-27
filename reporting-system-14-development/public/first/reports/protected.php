<?
include("../incl/ssl.inc");

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
													 // always modified
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");                          // HTTP/1.0

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
   include("login.php");
   exit();
}
$amjwho = "Ahmadiyya Muslim Jama`at Canada";
$nat_branch = "CA";
$session_id = $myary['ID'];
$user_id = $myary['user_id'];

#echo "<h1>user id:" . $user_id . "</h1>";

$user_type = $myary['user_type'];
$branch_code = $myary['branch_code'];
$user_level = $myary['user_level'];
$user_email= $myary['user_email'];
$user_dept = $myary['user_dept'];
$allow_attachments = $myary['config_allow_attachments'];

//print "session id:$session_id";
//print "user_id:$user_id";
//print "user_type:$user_type";
//print "branch_code:$branch_code";
//print "user_level:$user_level";
//print "user_dept:$user_dept";
$months = array ("01" => "January",
				 "02" => "February",
				 "03" => "March",
				 "04" => "April",
				 "05" => "May",
				 "06" => "June",
				 "07" => "July",
				 "08" => "August",
				 "09" => "September",
				 "10" => "October",
				 "11" => "November",
				 "12" => "December");
?>
