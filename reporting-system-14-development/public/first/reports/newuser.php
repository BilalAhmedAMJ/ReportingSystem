<?php
include("../incl/ssl.inc");

?>

<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#ffffff">
<?php include '../incl/headscript.inc'; ?>
<script language="JavaScript" type="text/JavaScript">
<!--
function submit_onclick() {
	var s = document.frmRegistration.email.value;
	var len = s.length-1;
	if (document.frmRegistration.first_name.value == "" || document.frmRegistration.first_name.value == " "){
		alert('You are missing First Name!');
		document.frmRegistration.first_name.focus();
		return false;
	}
	if (document.frmRegistration.last_name.value == "" || document.frmRegistration.last_name.value == " "){
		alert('You are missing Last Name!');
		document.frmRegistration.last_name.focus();
		return false;
	}
	if (document.frmRegistration.email.value == "" || document.frmRegistration.email.value == " "){
		alert('You are missing E-mail!');
		document.frmRegistration.email.focus();
		return false;
	}
	else if ((s.indexOf('@')<0) || (s.indexOf('.')<0) || (s.indexOf(' ')>0)){
		alert('Please provide a valid E-mail!');
		document.frmRegistration.email.focus();
		return false;
	}
	else if ((s.charAt(0) == '.') || (s.charAt(0) == '@') || (s.charAt(len) == '.') || (s.charAt(len) == '@')){
		alert('Please provide a valid E-mail!');
		document.frmRegistration.email.focus();
		return false;
	}
	if (document.frmRegistration.phone.value == "" || document.frmRegistration.phone.value == " "){
		alert('You are missing Phone#!');
		document.frmRegistration.phone.focus();
		return false;
	}
	if (document.frmRegistration.branch.value == "" || document.frmRegistration.branch.value == " "){
		alert('You are missing Branch!');
		document.frmRegistration.branch.focus();
		return false;
	}
	if (document.frmRegistration.department.value == "" || document.frmRegistration.department.value == " "){
		alert('You are missing department!');
		document.frmRegistration.department.focus();
		return false;
	}
	if (document.frmRegistration.request.value == ""){
		alert('Please select a request type!');
		document.frmRegistration.request.focus();
		return false;
	}
}
//-->
</script>
<p>

<?php include 'incl/topbar.inc'; ?>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="right" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="100" valign=top bgcolor=black>
        <?php include 'menu.php'; ?></td>
          <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr valign="top">
                <td valign="top"><center>
					<?php
					$newuser="Y";
					include 'admin_header.php'; ?><br>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                    	<td>
						  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="SpacerTable">
							<tr>
							  <td><img src="../images/spacer.gif" width="100%" height="1"></td>
							</tr>
						  </table>
                          <table width="100%" border="0" cellspacing="0" cellpadding="3">
                          <tr valign="top">
                            <td >
                            <center>
                            	<? if ($first_name != "") {
                            		$smsg = "First Name: $first_name\n";
                            		$smsg .= "Last Name: $last_name\n";
                            		$smsg .= "Email: $email\n";
                            		$smsg .= "Phone: $phone\n";
                            		$smsg .= "Branch: $branch\n";
                            		$smsg .= "Department: $department\n";
                            		$smsg .= "Request Type: $request\n";
									#mail("mansooranasir@yahoo.com", "AMI Reports new user request", $smsg, "From:reports@ahmadiyya.ca");
									mail("reports@ahmadiyya.ca", "AMI Reports new user request", $smsg, "From:reports@ahmadiyya.ca");
									if ($email){
										$message = "\n\nAssslam o Alaikum wa Rahmatullah";
										$message .= "\nJazakum Allah and thank you for requesting user id for AMI Reports.";
										$message .= "\nSomeone will be contecting you with login information soon.";
										$message .= "\n\nWassalam";
										$message .= "\nGeneral Secretary Department";
										$message .= "\nreports@ahmadiyya.ca";
										mail($email, "AMI reports new user request", $message, "From:reports@ahmadiyya.ca");
									}
                            	?>
									<table border="0" cellpadding="3" cellspacing="2">
									  <tr valign="top">
										<td align="center" colspan="2">
										<span class="pageheader">&nbsp;</span>
										</td>
									  </tr>
									  <tr valign="top">
										<td align="center" colspan="2">
										Thank you very much for sending new user request.<br>
										We have received your request and someone will <br>
										be contecting you soon.<br>
										</td>
									  </tr>
                            		</table>
                            	<? } else {?>
									<form action="newuser.php" method="post" name="frmRegistration">
									<table border="0" cellpadding="3" cellspacing="2">
									  <tr valign="top">
										<td nowrap bgcolor="#000000" align="center" colspan="2">
										<span class="pageheader"><font color="white">New User</font></span>
										</td>
									  </tr>
									  <tr>
										<td bgcolor="#F2F8F1">First Name:</td>
										<td bgcolor="#F2F8F1"><input name="first_name" type="text" maxlength="25" class="BoxCSS" id="first_name">&nbsp;*
										</td>
									  </tr>
									  <tr>
										<td bgcolor="#FCF8FA">Last Name:</td>
										<td bgcolor="#FCF8FA"><input name="last_name" type="text" maxlength="25" class="BoxCSS" id="last_name">&nbsp;*
										</td>
									  </tr>
									  <tr bgcolor="#F2F8F1">
										<td width="100">Email Address:</td>
										<td><input name="email" type="text" class="BoxCSS" maxlength="100" id="email">&nbsp;*</td>
									  </tr>
									  <tr>
										<td bgcolor="#FCF8FA">Phone:</td>
										<td bgcolor="#FCF8FA"><input name="phone" type="text" maxlength="20" class="BoxCSS" id="phone">*</td>
									  </tr>
									  <tr bgcolor="#F2F8F1">
										<td>Branch:</td>
										<td><input name="branch" type="text" class="BoxCSS" maxlength="60" id="branch" title="The name of the Jama`at where you reside. e.g. Central Toronto, Peace Village, Calgary etc.">&nbsp;*</td>
									  </tr>
									  <tr>
										<td bgcolor="#FCF8FA">Department:</td>
										<td bgcolor="#FCF8FA"><input name="department" type="text" maxlength="50" class="BoxCSS" id="department">*</td>
									  </tr>
									  <tr bgcolor="#F2F8F1">
										<td>Request type:</td>
										<td>
										<select name=request>
										<option value="" selected>None</option>
										<option value="Account creation">Account creation</option>
										<option value="Account changes">Account changes</option>
										<option value="Password reset">Password reset</option>
										</select>
										</td>
									  </tr>
									  <tr>
										<td bgcolor="#FCF8FA">&nbsp;</td>
										<td bgcolor="#FCF8FA"><input type="submit" name="Submit" value="Submit" class="ButtonCSS" onclick="return submit_onclick()"></td>
									  </tr>
									</table>
								  </form>
								  <script>
								  document.frmRegistration.first_name.focus();
								  </script>
                              <? }?>
                              <p>&nbsp;</p></td>
                          </tr>
                        </table></td>
                    </tr>
                  </table>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="87" valign="top">&nbsp;</td>
                      <td valign="top">&nbsp;</td>
                    </tr>
                  </table></td>
                <td width="1" bgcolor="#666666">
                  <?php include '../incl/navheight.inc'; ?>
                </td>
                <td width="160" bgcolor="#F3F3F3">
                  <?php include 'incl/rightbar.inc'; ?>
                </td>
              </tr>
            </table></td>
          <td bgcolor="#666666"><img src="../images/spacer.gif" width="1" height="1"></td>
        </tr>
        <tr>
          <td colspan="3" bgcolor="#666666"><?php include '../incl/navwidth.inc'; ?></td>
        </tr>
      </table></td>
  </tr>
</table>
<?php include '../incl/bottombar.inc'; ?>
<?php include '../incl/preload.inc'; ?>
</body>
</html>
