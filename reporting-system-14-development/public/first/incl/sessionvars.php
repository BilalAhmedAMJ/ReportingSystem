<?

// Include this at the top of pages 
// where you want to check the user type
// or get the username of the person logged in

session_start();
$myary = $_SESSION['login'];
$username = $myary['login'];
$usertype = $myary['status'];
?>