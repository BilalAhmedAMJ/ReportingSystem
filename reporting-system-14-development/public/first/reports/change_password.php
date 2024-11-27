<? include ("protected.php") ?>

<?php
$success=0;
$Error="";
if (($userId != "") && ($userId != $user_id)) {

	$Error="Invalid User ID!";

} else if ($userId != "") {

	$myUser = trim($_REQUEST['userId']);
	$myPass = trim($_REQUEST['old_passwd']);
	$myNPass = trim($_REQUEST['new_passwd']);
	$myCPass = trim($_REQUEST['confirm_passwd']);

	
	if ($myNPass!=""){
		if ($myNPass == $myCPass){
                        if (preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{6,12}$/',$myNPass)) {

				//check pin #
				$filename='.pin';
				if (file_exists($filename)) {
					if (!$fp = fopen($filename, 'r')) {
						 //print "Error0";
						 $Error="Error";
					}
					else{
						$fsize = filesize($filename);
						$contents = fread ($fp, filesize($filename));
						if ($contents == $pin){
							include("../incl/dbcon.php");

							$query = "SELECT * FROM ami_users WHERE user_id = '$myUser' and user_pass = old_password('$myPass') and status = '1'"; //ami_users
							$result = @mysql_db_query($dbname,$query);

							//print "$result";
							if ($result) {
							   $row = mysql_fetch_array($result);
							   $today = getdate();
							   $month = $today['mon'];
							   $year = $today['year'];
							   $myID = $row['user_id'];
							   if ($myID==$myUser)
							   {
								 $myType = $row['user_type'];
								 $myBCode = $row['branch_code'];
								 $myLevel = $row['user_level'];
								 $myEmail1= $row['user_email'];
								 $myDept = $row['user_dept'];
								 $myChange_pw = $row['change_pw'];
								 $myExpiry_date = $row['expiry_date'];
								if ($month>9){
									$month = ($month + 3) - 12;
									$year = $year + 1;
								} else {
									$month = $month + 3;
								}
								$ndt = $year . "-" . $month . "-01";
								//print "$ndt";
								$query ="update ami_users set user_pass=old_password('$myNPass'), change_pw = '$ndt' where user_id ='$myUser' and status = '1'";
								$result = @mysql_db_query($dbname,$query);
								if ($result=="1") {
									 $myChange_pw = $ndt;
									//print "$query";
									//session_start();
									//session_unset();
									//session_destroy();
                                        				//exit();
									//session_start();

									$_SESSION['login'] = array ("login" => $myUser,
                                                                                                "ID" => session_id(),
                                                                                                "user_id" => $myID,
                                                                                                "user_type" => $myType,
                                                                                                "user_level" => $myLevel,
                                                                                                "user_dept" => $myDept,
                                                                                                "user_email" => $myEmail1,
                                                                                                "branch_code" => $myBCode);

//									print "<center>Your password has been changed successfully.<br><a href='list_reports.php'>Click here to Continue</a></center>";
//									exit();
									$success=1;
									//header("Location: list_reports.php");
									//include("list_reports.php");

									//include("login.php");
									//header("Location: login.php");
								} else {
									//print "Error0";
									$Error="Failed to update password, please try later!";
								}
							   } else {
								//print "Error3";
								$Error="Invalid user information!";
							   }
							} else {
								//print "Error3";
								$Error="Invalid user information!";
							}
						} else {
							//print "Error4";
							$Error="Invalid pin # provided!";
						}
					}
				} else{
					//print "Error5";
					$Error="Could not validate the pin #!";
				}
				fclose($fp);
			} else{
				//print "Error4";
				$Error="Minimum requirements for password:<br>must be at least 6 characters long<br>must contain at least one number<br>must contain at least one letter.";
			}
		} else{
			//print "Error5";
			$Error="New password does not match with confirmation password!";
		}
	} else{
		//print "Error6";
		$Error="New password is empty!";
	}
}
else
{
	$userId = $user_id;
}
?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">
<?php include '../incl/headscript.inc'; ?>
<script language="JavaScript" type="text/JavaScript">
<!--
function submit_onclick() {
	if (document.loginForm.userId.value == "" || document.loginForm.userId.value == " "){
		alert('You are missing user id!');
		document.loginForm.userId.focus();
		return false;
	}
	if (document.loginForm.old_passwd.value == "" || document.loginForm.old_passwd.value == " "){
		alert('You are missing old password!');
		document.loginForm.old_passwd.focus();
		return false;
	}
	if (document.loginForm.new_passwd.length < 6) {
		alert('Passowrd must be at lease 6 characters long!');
		document.loginForm.new_passwd.focus();
		return false;
	}
	if (document.loginForm.new_passwd.value == "" || document.loginForm.new_passwd.value == " "){
		alert('You are missing new password!');
		document.loginForm.new_passwd.focus();
		return false;
	}
	if (document.loginForm.confirm_passwd.value == "" || document.loginForm.confirm_passwd.value == " "){
		alert('You are missing confirmation password!');
		document.loginForm.confirm_passwd.focus();
		return false;
	}
	if (document.loginForm.new_passwd.value != document.loginForm.confirm_passwd.value){
		alert('New password and confirmation password does not match!');
		document.loginForm.new_passwd.focus();
		return false;
	}
	if (document.loginForm.pin.value == "" || document.loginForm.pin.value == " "){
		alert('You are missing pin!');
		document.loginForm.pin.focus();
		return false;
	}
}
//-->
</script>
</head>
<body bgcolor="#ffffff">
<?php include 'incl/topbar.inc'; ?>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="right" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="100" valign=top> 
        <?php include 'menu.php'; ?></td>
          <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">

          <td colspan="2" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr valign="top">
                <td valign="top">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
								<tr>
						<?
							if ( (($user_dept=="") || ($user_type=="")) || ($user_level="") )  
							{
						?>
									<th ><span >Change Password</span></th>
						<?	} else { ?>
									<th ><span >Change Password</span></th>

						<?	} ?>
								</tr>
						<td align="center">
							<!--<center>-->
							<br><br>
							<?php if ($Error!="") {
								print "<font color=red><span  align='center'>$Error</span></font>";
							} ?>
							<table class="newstyletable" border="0">
							<tr>
							<td>


			<? if ($success==1) { ?>
							<center <p class="normaltxt">Your password has been changed.<br>
							<a href="list_reports.php">Click here to View Reports</a>	</p></center>

			<? } else { ?>

							  <form name="loginForm" method="post" action="change_password.php">
							  <table width="350" border="0">
								<tr>
								  <td bgcolor="" width="200"><p class="normaltxt">User ID:</p></td>
								  <td bgcolor="" width="280"> 
<? print $user_id; ?>
								<input name="userId" class="BoxCSS" type="hidden" value="<? print $user_id; ?>" id="userId">
<!--
								<input name="userId" class="BoxCSS" type="text" value="<? print $user_id; ?>" id="userId">*
-->
									</td>
								</tr>
								<tr>
								  <td bgcolor=""><p class="normaltxt">Old password:</p></td>
								  <td bgcolor=""><input name="old_passwd" type="password" class="BoxCSS" id="old_passwd">*</td>
								</tr>
								<tr>
								  <td bgcolor=""><p class="normaltxt">New password:</p></td>
								  <td bgcolor=""><input name="new_passwd" type="password" class="BoxCSS" id="new_passwd">*</td>
								</tr>
								<tr>
								  <td bgcolor=""><p class="normaltxt">Confirm Password:</p></td>
								  <td bgcolor=""><input name="confirm_passwd" type="password" class="BoxCSS" id="confirm_passwd">*</td>
								</tr>
								<tr>
								  <td bgcolor=""><p class="normaltxt">Pin:</p></td>
								  <td bgcolor=""><input name="pin" type="password" class="BoxCSS" id="pin">*</td>
								</tr>
								<tr><td colspan="2" align="center">&nbsp;<span >Note:
                                All fields with (*) are required</span></td></tr>
							  </table>
								<script language="JavaScript">
									document.loginForm.userId.focus();
								</script>
			<? } ?>
							</td>
							</tr>
								<tr>
								  <td align="center">
								  	<input type="submit" name="Submit" value="Submit" class="ButtonCSS" onclick="return submit_onclick()">
								  </td>
								</tr>
							</table>
								</form>
							<!--</center>-->
						</td>
					  </tr>
					</table>
				</td>
                <td width="160" >
                  <?php $change_password_php; include 'incl/rightbar.inc'; ?>
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
