<?php
if (($myChange_pw!="") && ($myExpiry_date!=""))
	session_start();
else 
	include("../incl/ssl.inc");

?>

<?php

function generatePassword($length=7, $strength=7) {
	$vowels = 'aeuy';
	$consonants = 'bdghjmnpqrstvz';
	if ($strength & 1) {
		$consonants .= 'BDGHJLMNPQRSTVWXZ';
	}
	if ($strength & 2) {
		$vowels .= "AEUY";
	}
	if ($strength & 4) {
		$consonants .= '23456789';
	}
	if ($strength & 8) {
		$consonants .= '@#$%';
	}
 
	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length; $i++) {
		if ($alt == 1) {
			$password .= $consonants[(rand() % strlen($consonants))];
			$alt = 0;
		} else {
			$password .= $vowels[(rand() % strlen($vowels))];
			$alt = 1;
		}
	}
	return $password;
}

//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                                                                                                         // always modified
//header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
//header("Cache-Control: post-check=0, pre-check=0", false);
//header("Pragma: no-cache");                          // HTTP/1.0
$Error="";
$Error_Msg="";
$attempt_no = 0;
if ($user_id !="") {
        //check pin #
		$attempt_no = $_REQUEST['attempt_no'];
		$attempt_no++;
		if ($attempt_no > 3)
		{
			print "To many attempts. Please try again later.";
			exit();	
		}
        $filename='.pin';
        if (file_exists($filename)) {
                if (!$fp = fopen($filename, 'r')) {
                         $Error="Error";
                }
                else{
                         $fsize = filesize($filename);
                         $contents = fread ($fp, filesize($filename));
                        if ($contents != $pin){
                                $Error="Error";
                        }
                }
        }
        else{
                $Error="Error";
        }
        fclose($fp);

        $myUser = trim($_REQUEST['user_id']);
        $myEmail= trim($_REQUEST['user_email']);

        include("../incl/dbcon.php");

        $query = "SELECT * FROM ami_users WHERE user_id = '$myUser' and user_email='$myEmail' and status = '1'"; 
        $result = @mysql_db_query($dbname,$query);
        //print "result:$result";
        while ($row = mysql_fetch_array($result)) {
                 $myPass2 = $row['user_pass'];
                 $myChange_pw = $row['change_pw'];
                 $myEmail= $row['user_email'];
                 $myExpiry_date = $row['expiry_date'];
        }
//print "pass:$myPass2";
//print "myChange:$myChange_pw";
//print "myExpiry:$myExpiry_date";
//print "Error:$Error";

        if (($myChange_pw!="") && ($myExpiry_date!="") && ($Error!="Error")) {	// user found
                $pdt = date("Y-m-d");
                        if ($myExpiry_date<$pdt){	// account expired
                                $Error="Error";
                                $Error_Msg = "Your account has been expired (Re-registration required)";
                        } 
			else if(($myUser) && ($myEmail) && ($myPass2) && ($Error!="Error"))
			{
				//session_start();
				$change_pw1 = $pdt-1;
				$newpwd = generatePassword();
				$insert_data = "UPDATE ami_users SET change_pw='$changed_pw1', user_pass=old_password('$newpwd') WHERE user_id='$myUser' AND user_email='$myEmail'"; 
				//user_pass = old_password('$user_pass'),
				//print "$insert_data";
				$result=@mysql_db_query($dbname,$insert_data,$id_link);
				if ($result == "1")
				{
					$Error = "User updated successfully!";	// change password enforced
					// now e-mail password and set change_pw to 24 hours from now.

					$message = "\n\nAssslam o Alaikum wa Rahmatullah";
					$message .= "\nAs you have requested to RESET your password for AMJ Reports.";
					$message .= "\n\nYour new temporary password is as follows: ";
					$message .= "\n$newpwd";
					$message .= "\n\nYou will be asked to change your password upon login!";
					$message .= "\n\nIF YOU DID NOT REQUEST TO RESET YOUR PASSWORD THEN PLEASE NOTIFY US IMMEDIATELY!";
					$message .= "\n\nWassalam";
					$message .= "\nGeneral Secretary Department";
					$message .= "\nreports@ahmadiyya.ca";
					mail($myEmail, "AMJ Reports - Password Reset", $message, "From:reports@ahmadiyya.ca");
					print "Your temporary password has been sent to your e-mail address.<br><a href='login.php'>Click here to Login</a>";
					exit();	
				} 
				else 
				{
					$Error = "Could not update user!";
					print "Update failed";
			
					//header("Location: list_reports.php");
					//include ("reports.php");
				}
			}
			else
		       	{
				$Error="Error";
				$Error_Msg = "Invalid User ID and Email combination";
			}
	}
	else
	{
		$Error="Error";
		$Error_Msg = "Invalid User ID and Email combination";
	}
} 
?> 
<html>
<head>
<title>
<? 
	if ($amjwho && $amjwho!="")	
		echo $amjwho; 
	else
	{
?>
		Ahmadiyya Muslim Jama`at
<? 
	}	
?>
</title>
<link href="../style.css" rel="stylesheet" type="text/css">
<?php include '../incl/headscript.inc'; ?>
<script language="JavaScript" type="text/JavaScript">
<!--
function get_user_id() {
	var s = document.userForm.user_type1.value;
	document.userForm.user_id1.value=s.toLowerCase() + document.userForm.branch_code1.value;;
}
function submit_onclick() {
	var s = document.userForm.user_email.value;
	var len = s.length-1;
	if (document.userForm.user_id1.value == "" || document.userForm.user_id1.value == " "){
		alert('You are missing user id!');
		document.userForm.user_id1.focus();
		return false;
	}
	if (document.userForm.user_type1.value == "P" && document.userForm.branch_code1.value == "CA"){
		alert('Invalid branch code!');
		document.userForm.branch_code1.focus();
		return false;
	}
	if (document.userForm.user_email.value == "" || document.userForm.user_email.value == " "){
		alert('You are missing E-mail!');
		document.userForm.user_email.focus();
		return false;
	}
	else if ((s.indexOf('@')<0) || (s.indexOf('.')<0) || (s.indexOf(' ')>0)){
		alert('Please provide a valid E-mail!');
		document.userForm.user_email.focus();
		return false;
	}
	else if ((s.charAt(0) == '.') || (s.charAt(0) == '@') || (s.charAt(len) == '.') || (s.charAt(len) == '@')){
		alert('Please provide a valid E-mail!');
		document.userForm.user_email.focus();
		return false;
	}
}
//-->
</script>
</head>
<body bgcolor="#ffffff">
<?php include '../incl/topbar.inc'; ?>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="right" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="100" valign=top >
        <?php include 'menu.php'; ?></td>
          <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">

              <tr valign="top">
                <td valign="top"><center>
				<?php $resetpwd='Y';
				 include 'admin_header.php'; ?><br>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
						<td align="center">
							<!--<center>-->
							<table  border="0">
								<? if ($Error == "Error") {?>
									<tr>
										<td colspan="2" align="center">
										<font color=darkred><? echo $Error_Msg;?></font>
										</td>
									</tr>
								<? }?>
							<tr>
							<td>
							  <form name="userForm" method="post" action="resetpwd.php">
							  <table width="350" border="0" class=newstyletable> 
								<tr>
										<th colspan=2><span class="">Password Reset</span></th>
								</tr>
								<tr>
								  <td colspan="2" align="center">&nbsp;</td>
								</tr>
								<tr>
								  <td width="100"><p class="newstyletxt">User ID:</p></td>
								  <td width="280">&nbsp;<input name="user_id" maxlength="50" class="newstyletxtinput" type="text" id="user_id"></td>
								</tr>
								<tr>
                                                                  <td width="100"><p class="newstyletxt">Pin:</p></td>
                                                                  <td width="280">&nbsp;<input name="pin" type="password" class="newstyletxtinput" id="pin"></td>
                                                                </tr>
								<tr>
								  <td width="100"><p class="newstyletxt">Email:</p></td>
								  <td width="280"><input name="user_email" maxlength="255" size="25" class="newstyletxtinput" type="text" id="user_email"></td>
								</tr>

								<tr><td colspan="2" align="center">&nbsp;<span class="">Note: All fields are required</span></td></tr>
								<tr>
								  <td colspan="2" align="center">
								  	<input name="attempt_no" type="hidden" id="attempt_no" value="<? echo $attempt_no;?>">
								  	<input type="submit" name="Submit" value="Submit" class="newstylebutton" onclick="return submit_onclick()">&nbsp;&nbsp;
								  </td>
								</tr>
							  </table>
								<script language="JavaScript">
									document.userForm.user_id.focus();
								</script>
							</form>
							</td>
							</tr>
							</table>
							<!--</center>-->
						</td>
					  </tr>
					</table>
				</td>
                <td width="160">
                  <?php $pwd_reminder_php='Y'; include 'incl/rightbar.inc'; ?>
                </td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
<?php include 'incl/bottombar.inc'; ?>
</body>
</html>
