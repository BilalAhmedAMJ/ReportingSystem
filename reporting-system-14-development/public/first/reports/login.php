<?php

include("../incl/ssl.inc");

$debug=0;

//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
													 // always modified
//header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
//header("Cache-Control: post-check=0, pre-check=0", false);
//header("Pragma: no-cache");                          // HTTP/1.0
$Error="";

if ($use_email=="1")
{
	$emailId=$userId;
	$userId="";
} 
$time = time(); 
$datetime = date('Y-m-d H:i:s',$time);
$ip = $_SERVER['REMOTE_ADDR']?:($_SERVER['HTTP_X_FORWARDED_FOR']?:$_SERVER['HTTP_CLIENT_IP']);

if($debug) print "\n<pre>";    

if ($userId !="") {
	//check pin #
	$filename='.pin';
	if (file_exists($filename)) {
		if (!$fp = fopen($filename, 'r')) {
             if($debug) print "\n Unable to open  PIN file ";
			 $Error="Error";
		}
		else{
			 $fsize = filesize($filename);
			 $contents = fread ($fp, filesize($filename));
			if ( trim($contents) != trim($pin) ){
			    if($debug) print "\n Invalid pin expect [$contents] GOT [trim($pin)]";
				$Error="Error";
			}
		}
	}
	else{
	    if($debug) print "\nNo PIN file error";
		$Error="Error";
	}
	fclose($fp);

	$myUser = trim($_REQUEST['userId']);
	$myPass = trim($_REQUEST['passwd']);
	$myuserpass= trim($_REQUEST['user_pass']);

if($debug) print "\nmyUser:$myUser";
if($debug) print "\nmyPass:$myPass";
if($debug) print "\nmyuserpass:$myuserpass";

	include("../incl/dbcon.php");

	if($debug) print "\ndbname:$dbname";
	if($debug) print "\ndbhost:$hostname";
	if (!$id_link)
		if($debug) print "\nNOT CONNECTED";
	if ($myuserpass != "")
		$query = "SELECT * FROM ami_users WHERE user_id = '$myUser' and user_pass='$myuserpass' and status = '1'"; //ami_users
	else
		$query = "SELECT * FROM ami_users WHERE user_id = '$myUser' and user_pass=old_password('$myPass') and status = '1'"; //ami_users
    
	if($debug) print "\nquery:$query\n";
	
	$result = @mysql_db_query($dbname,$query);
	
	if($debug) print "result:".mysql_error($id_link);
    
	while ($row = mysql_fetch_array($result)) {
		 $myPass2 = $row['user_pass'];
		 $myID = $row['user_id'];
		 $myType = $row['user_type'];
		 $myBCode = $row['branch_code'];
		 $myLevel = $row['user_level'];
		 $myEmail1= $row['user_email'];
		 $myDept = $row['user_dept'];
		 $myChange_pw = $row['change_pw'];
		 $myExpiry_date = $row['expiry_date'];
	}

	if($debug) print "\nmyChange_pw:$myChange_pw";
	if($debug) print "\nmyExpiry_date:$myExpiry_date";
	if($debug) print "\nErrort:$Error";
	if($debug) print "\nAuthValid :".(($myChange_pw!="") && ($myExpiry_date!="") && ($Error!="Error"));
    if($debug) print "</pre>";
    if($debug) exit;
    
	if (($myChange_pw!="") && ($myExpiry_date!="") && ($Error!="Error")) {

	// USER AUTHENTICATED - Must log user. 

	$insert_log = "INSERT INTO ami_users_log (datetime, user_id, ip, status) VALUES ('$datetime', '$myUser', '$ip', '1')";
	@mysql_db_query($dbname,$insert_log,$id_link);
		
	/////////////////////////////////
	// Load configuration file
	/////////////////////////////////
	$allow_attachments = "0";
	$filename='config';
	$line = "";
	if (file_exists($filename)) {
		if (!$fp = fopen($filename, 'r')) {
			 $Error="Error - Unable to load configurations";
		}
		else{
		  while (!feof($fp)) {
			$line = fgets($fp);
			if  (strncmp($line,"ALLOW_ATTACHMENTS",17)==0)
					$allow_attachments = "1"; 
		  } // while
		}	
	fclose($fp);
	}
	else
	{
		// config file does not exist;
		//$Error="Error ";
			$allow_attachments = "file not found";
	}

	/////////////////////////////////

		$pdt = date("Y-m-d");
		if ($myChange_pw<=$pdt){

			session_start();
			$_SESSION['login'] = array ("login" => $myUser,
						"ID" => session_id(),
						"user_id" => $myID,
						"user_type" => "",
						"user_level" => "",
						"user_dept" => "",
						"user_email" => $myEmail1,
						"config_allow_attachments" => $allow_attachments,
						"branch_code" => "");
			$Error="";
			//include("change_password.php");
			header("Location: change_password.php");
			exit();
		} else {
			if ($myExpiry_date<$pdt){
				$Error="Error";
			} else {
				if(($myUser) && ($myPass || $myuserpass) && ($myPass2) && ($Error!="Error")){

					session_start();
					$_SESSION['login'] = array ("login" => $myUser,
												"ID" => session_id(),
												"user_id" => $myID,
												"user_type" => $myType,
												"user_level" => $myLevel,
												"user_dept" => $myDept,
												"user_email" => $myEmail1,
												"config_allow_attachments" => $allow_attachments,
												"branch_code" => $myBCode);
					header("Location: list_reports.php");
				}
				else
				{
					//print "Invalid user or password";
					//include("login.php");
					$Error="Error";
				}
			}
		}
	} else
	{

		$insert_log = "INSERT INTO ami_users_log (datetime, user_id, ip, status) VALUES ('$datetime', '$myUser', '$ip', '0')";
		@mysql_db_query($dbname,$insert_log,$id_link);
		$Error="Error";
	}
}
else if ($emailId !="") 	// Logging in using e-mail
{
	//check pin #
	$filename='.pin';
	if (file_exists($filename)) {
		if (!$fp = fopen($filename, 'r')) {
			 $Error="Error";
				print "ERROR 772";
		}
		else{
			 $fsize = filesize($filename);
			 $contents = fread ($fp, filesize($filename));
			if ($contents != $pin){
				$Error="Error";
				print "ERROR 773";
			}
		}
	}
	else{
		$Error="Error";
		print "PIN ERROR";
	}
	fclose($fp);

	$myEmail= $emailId;
	$myPass = trim($_REQUEST['passwd']);

	include("../incl/dbcon.php");

	$query = "SELECT * FROM ami_users WHERE user_email= '$myEmail' and user_pass=old_password('$myPass') and status = '1'"; //ami_users
	$result = @mysql_db_query($dbname,$query);
	//print "result:$result";
	//print "query:$query";

	?>

	<html>
	<head>
	<title>Ahmadiyya Muslim Community Canada</title>
	<link href="../style.css" rel="stylesheet" type="text/css">
	<? include '../incl/headscript.inc'; ?>
<!-------------------------------------><center>
			 <table class="BoxCSS" border="0">
                                                        <tr>
                                                        <td>
                                                          <form name="loginForm" method="post" action="login.php">
                                                          <table width="350" border="0">
                                                                <tr>
                                                                        <th colspan=2 bgcolor="#F2FAFB"><span class="pageheader">Login Confirmation</span></th>
								<tr>
								<tr>
									<td colspan=2><hr><b>Please select user account to continue</b><hr></td>
                                                                </tr>
                                                                <tr>
                                                                  <td bgcolor="#FCF8FA" width="16%">User ID: </td>
                                                                  <td bgcolor="#FCF8FA" width="84%">
							<select name="userId"> 
						<? while ($row = mysql_fetch_array($result)) { 
							$val_id = $row['user_id'];
							$val_branch= $row['branch_code'];
							$val_dept = $row['user_dept'];
							 $val_type = $row['user_type'];
							 $val_level= $row['user_level'];
							 $val_userpass= $row['user_pass'];
							?>

							<option value=<? print "\"$val_id\""; ?>><? print "$val_id";  ?></option>

						<? } ?>
							</select>
								</td>
								</tr>
                                                                <!--tr>
                                                                  <td ><p class="normaltxt">Password:</p></td>
                                                                  <td ><input name="passwd" type="password" class="BoxCSS" id="passwd"></td>
                                                                </tr-->
                                                                <tr>
                                                                  <td colspan="2" align="center">
                                                                        <input type="submit" name="Submit" value="Login" class="newstylebutton">
                                                                  </td>
                                                                </tr>
                                                          </table>
								<input name="user_pass" type="hidden" class="BoxCSS" id="user_pass" value=<? print "\"$val_userpass\"";?>>
								<input name="pin" type="hidden" class="BoxCSS" id="pin" value=<? print "\"$pin\"";?>>
                                                                </form>
                                                        </td>
                                                        </tr>
                                                        </table>
<!------------------------------------->
	</body></html>
<?
	$use_email=="0";
	exit();
}
?>
<html>
<head>
<title>Ahmadiyya Muslim Community Canada</title>
<link href="../style.css" rel="stylesheet" type="text/css">
<? include ('../incl/headscript.inc'); ?>
<script language="JavaScript" type="text/JavaScript">
<!--
function submit_onclick() {


	if (document.loginForm.userId.value == "" || document.loginForm.userId.value == " "){
		//alert('You are missing user id!');
		document.loginForm.userId.value = "<Enter User ID>";
		document.loginForm.userId.select(); 
		document.loginForm.userId.focus();
		return false;
	}

	if (document.loginForm.passwd.value == "" || document.loginForm.passwd.value == " "){
		document.loginForm.passwd.focus();
		return false;
	}
	if (document.loginForm.pin.value == "" || document.loginForm.pin.value == " "){
		document.loginForm.pin.focus();
		return false;
	}
}
//-->
</script>
</head>
<!--body bgcolor="#3b80be"-->
<body bgcolor="#808080">
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0"">
  <tr>
    <td align="right" valign="top">
    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="2" valign="top"><table class="newstyletable" width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr valign="top">
                <!--td valign="top" bgcolor="#CEE3F6" ><br><br><center-->
                <td valign="top" ><br><br><center>
                  <table width="770" height=450 border="0" cellspacing="0" cellpadding="0" background="images/amjbanner_2013.png"> 
                    <tr>
						<td width=50% align="center">		<br><br><br><br><br><br>
					<!--<center>-->
							
							<?php if ($Error=="Error") {
								print "<span class='pagetextAltColorSmall' align='center'>Invalid user information</span>";
							} ?>
							<table border="0">
							<tr>
							<td><center>
							  <form name="loginForm" method="post" action="login.php">
							  <table width="250" border="0">
								<tr>
								  <td width="16%">
									 <select name="use_email" class="newstyleselect">
										<option value="0" selected class="newstyletxt">User ID</option>
										<option value="1" >Email</option>
										</select>
									</select>
								 </td>
								  <td width="84%"><input name="userId" class="newstyletxtinput" type="text" id="userId"></td>
								</tr>
								<tr>
								  <td ><font color="black"><div id=passwdlbl><p class="newstyletxt">Password:</p></div></font></td>
								  <td ><input name="passwd" type="password" class="newstyletxtinput" id="passwd"></td>
								</tr>
								<tr>
								  <td ><font color="black"><p  id=passwdlbl class="newstyletxt">Pin:</p></font></td>
								  <td ><input name="pin" type="password" class="newstyletxtinput" id="pin"></td>
								</tr>
								<tr>
								  <td ></td>
								  <td align=left>
								 	<input type="submit" name="Submit" value="Login" class="newstylebutton" onclick="return submit_onclick()">
									<br><br><a href="resetpwd.php"><font color="#808080">Forgot Password?</font></a>	
								  </td>
								</tr>
								<tr>
								  <td colspan="2" align="center"><br><br>
								 	<!--<a href="newuser.php"><font color="#c0c0c0">New User</font></a>&nbsp;&nbsp;&nbsp;&nbsp;-->
								  </td>
								</tr>
							  </table>
								</form>
							</td>
<td width="50%" align=center valign=top ><img src="images/l4ablue.png" border=0></td>
							</tr>
							</table>
						</td>
					  </tr>
					</table></td>
              </tr>
            </table></td>
          <!--td ></td-->
        </tr>
      </table></td>
  </tr>
</table>
<?php //include '../incl/bottombar.inc'; ?>
<?php //include '../incl/preload.inc'; ?>
</body>
</html>
