<?php include ("protected.php");
include ("../incl/dbcon.php");
if (($user_type!="P") && ($user_type!="GS") && ($user_id!="Admin")) {
	header("Location: list_reports.php");
}
$user_id_error = false;
$today = date("Y-m-d");
if (date("m")>=7)
	$def_expiry = date("Y-m-d",mktime(0, 0, 0, 06,   30,   date("Y")+3));
else
	$def_expiry= date("Y-m-d",mktime(0, 0, 0, 06,   30,   date("Y")));

// In case Local president or GS is editing or adding user
if ($user_level == "L")
{
	$status = '0';
	$branch_code1 = $branch_code;
	$user_level1 = $user_level;
}
if (($user_id=="Admin") && ($activate_id!="")) {
	
	$insert_data = "UPDATE ami_users SET status = '$status' where u_id='$activate_id'";
	$result=@mysql_db_query($dbname,$insert_data,$id_link);
	if ($result == "1"){
		$Error = "User updated successfully!";
		//$subject = "$Field11 - reg success";
		//mail("$Field8", "Online Registration", $smsg, "From:8340720@gmail.com");
		header("Location: list_users.php?success=1");
	} else {
		$Error = "Could not update user!";
		header("Location: list_users.php?failed=1");
	}
	exit();
}

if ($user_id1!=""){
	$Error="";
	if ($user_dept1=="Own") {
		$user_dept1=$user_type1;
	}
	if ($id!=""){

		 $query = "SELECT user_id FROM ami_users where user_id='$user_id1' and u_id!='$id'";
		 $result3 = @mysql_db_query($dbname,$query);
		 if (mysql_fetch_array($result3)) { 
			// user_id already exists;
			$user_id_error = true;
			$Error = "User ID <b>$user_id1</b> is already in use. Please enter different user ID.";
		 }
		 else
		 {
			//print "update";
			if ($user_pass=="") {
				//print "update1";
				$insert_data = "UPDATE ami_users SET
							user_name = '$user_name',
							user_id = '$user_id1',
							user_type = '$user_type1',
							user_level = '$user_level1',
							branch_code = '$branch_code1',
							user_dept = '$user_dept1',
							user_phone = '$user_phone',
							mem_code = '$mem_code',
							user_email = '$user_email1',
							change_pw = '$change_pw',
							expiry_date = '$expiry_date',
							status = '$status'
							WHERE u_id	='$id'";
			} else {
				//print "update2";
				$insert_data = "UPDATE ami_users SET
							user_name = '$user_name',
							user_id = '$user_id1',
							user_pass = old_password('$user_pass'),
							user_type = '$user_type1',
							user_level = '$user_level1',
							branch_code = '$branch_code1',
							user_dept = '$user_dept1',
							mem_code = '$mem_code',
							user_phone = '$user_phone',
							user_email = '$user_email1',
							change_pw = '$change_pw',
							expiry_date = '$expiry_date',
							status = '$status'
							WHERE u_id	='$id'";
			}
			//print "$insert_data";
			$result=@mysql_db_query($dbname,$insert_data,$id_link);
			if ($result == "1"){
				$Error = "User updated successfully!";
				//$subject = "$Field11 - reg success";
				//mail("$Field8", "Online Registration", $smsg, "From:8340720@gmail.com");
			} else {
				$Error = "Could not update user!";
			}
		}
		mysql_free_result($result3);
	} else {	// NEW USER REQUEST
		// first check if this user_id is new and non-existent

		 $query = "SELECT user_id FROM ami_users where user_id='$user_id1'";
		 $result3 = @mysql_db_query($dbname,$query);
		 if (mysql_fetch_array($result3)) { 
			// user_id already exists;
			$user_id_error = true;
			$Error = "User ID <b>$user_id1</b> is already in use. Please enter different user ID.";
		 }
		 else
		{
			//print "insert";
			$insert_data = "insert into ami_users
							values ('', '$user_name', '$user_id1', old_password('$user_pass'),
									'$user_type1', '$user_level1', '$branch_code1',
									'$user_dept1', '$user_phone', '$user_email1',
									'$change_pw', '$expiry_date', '$status','','$mem_code','');";
			//print "$insert_data<br>";
			$result=@mysql_db_query($dbname,$insert_data,$id_link);
			if ($result == "1"){
				$Error = "User added successfully!";
				//$subject = "$Field11 - reg success";
				//mail("$Field8", "Online Registration", $smsg, "From:mansooranasir@gmail.com");
			} else {
				$Error = "Could not add user!";
			}
		}
		mysql_free_result($result3);
	}
	if ($Error=="") {
		//header("Location: list_users.php");
		//include("list_users.php");
	}
	$id ="";
	$user_pass = "";
/*	$user_name = $user_name;
	$user_type1 = $user_type1;
	$user_level1 = $user_level1;
	$branch_code1 = $branch_code1;
	$user_dept1 = $user_dept1;
	$user_phone = $user_phone;
	$user_email1 = $user_email1;
	$change_pw = $change_pw;
	$expiry_date = $expiry_date;
	$status = $status; */
}
if ($id !="") {

	$query = "SELECT * FROM ami_users WHERE u_id = '$id'"; //ami_users
	$result = @mysql_db_query($dbname,$query);

	//print "$result";
	if ($result) {
		$row = mysql_fetch_array($result);
		$user_name = $row['user_name'];
		$user_id1 = $row['user_id'];
		$user_type1 = $row['user_type'];
		$user_level1 = $row['user_level'];
		$branch_code1 = $row['branch_code'];
		$user_dept1 = $row['user_dept'];
		$user_phone = $row['user_phone'];
		$mem_code = $row['mem_code'];
		$user_email1 = $row['user_email'];
		$change_pw = $row['change_pw'];
		$expiry_date = $row['expiry_date'];
		$status = $row['status'];
	}
}
?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">

<?php include '../incl/headscript.inc'; ?>
<script language="JavaScript" type="text/JavaScript">
<!--
function get_user_id() {
	var s = document.userForm.user_type1.value;
	document.userForm.user_id1.value=s.toLowerCase() + document.userForm.branch_code1.value;;
}
function submit_onclick() {
	var s = document.userForm.user_email1.value;
	var len = s.length-1;
	if (document.userForm.user_name.value == "" || document.userForm.user_name.value == " "){
		alert('You are missing user name!');
		document.userForm.user_name.focus();
		return false;
	}
	if (document.userForm.user_id1.value == "" || document.userForm.user_id1.value == " "){
		alert('You are missing user id!');
		document.userForm.user_id1.focus();
		return false;
	}
	if (document.userForm.id.value == ""){
		if (document.userForm.user_pass.value == "" || document.userForm.user_pass.value == " "){
			alert('You are missing password!');
			document.userForm.user_pass.focus();
			return false;
		}
		if (document.userForm.confirm_passwd.value == "" || document.userForm.confirm_passwd.value == " "){
			alert('You are missing confirmation password!');
			document.userForm.confirm_passwd.focus();
			return false;
		}
		if (document.userForm.user_pass.value != document.userForm.confirm_passwd.value){
			alert('Password and confirmation password does not match!');
			document.userForm.user_pass.focus();
			return false;
		}
	}
	if (document.userForm.user_type1.value == "E" && document.userForm.user_level1.value == "L"){
		alert('Invalid user level (Election coordinator be National level user)!');
		document.userForm.user_level1.focus();
		return false;
	}
/*	if (document.userForm.user_type1.value == "P" && document.userForm.user_level1.value == "N"){
		alert('Invalid user level!');
		document.userForm.user_level1.focus();
		return false;
	}*/
	if (document.userForm.user_type1.value == "P" && document.userForm.branch_code1.value == "<? echo $nat_branch; ?>"){
		alert('Invalid branch code!');
		document.userForm.branch_code1.focus();
		return false;
	}
	if (document.userForm.user_type1.value != "P" && document.userForm.user_type1.value != "GS" && document.userForm.user_dept1.value == "All"){
		alert('Invalid access type!');
		document.userForm.user_dept1.focus();
		return false;
	}
	if (document.userForm.user_phone.value == "" || document.userForm.user_phone.value == " "){
		alert('You are missing Phone#!');
		document.userForm.user_phone.focus();
		return false;
	}
	if (document.userForm.mem_code.value == "" || document.userForm.mem_code.value == " "){
		alert('You are missing Member code!');
		document.userForm.mem_code.focus();
		return false;
	}
	if (document.userForm.user_email1.value == "" || document.userForm.user_email1.value == " "){
		alert('You are missing E-mail!');
		document.userForm.user_email1.focus();
		return false;
	}
	else if ((s.indexOf('@')<0) || (s.indexOf('.')<0) || (s.indexOf(' ')>0)){
		alert('Please provide a valid E-mail!');
		document.userForm.user_email1.focus();
		return false;
	}
	else if ((s.charAt(0) == '.') || (s.charAt(0) == '@') || (s.charAt(len) == '.') || (s.charAt(len) == '@')){
		alert('Please provide a valid E-mail!');
		document.userForm.user_email1.focus();
		return false;
	}
		if (document.userForm.user_email1.value != document.userForm.user_email2.value){
			alert('Email and confirmation email does not match!');
			document.userForm.user_email1.focus();
			return false;
		}
	if (document.userForm.change_pw.value == "" || document.userForm.change_pw.value == " "){
		alert('You are missing next password change date!');
		document.userForm.change_pw.focus();
		return false;
	}
	if (document.userForm.expiry_date.value == "" || document.userForm.expiry_date.value == " "){
		alert('You are missing expiry date!');
		document.userForm.expiry_date.focus();
		return false;
	}
}
//-->
</script>
</head>
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
              <tr valign="top">
                <td valign="top"><center>
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
								<? if ($Error) {?>
									<tr>
										<td colspan="3" align="center"><br>
										<span class="pagetextAltColor"><? echo $Error;?></span><br><br>
										</td>
									</tr>
								<? }?>
							  <form name="userForm" method="post" action="add_user.php">
								<tr>
									<? if ($id!="") {?>
										<th colspan=3 >Edit user</th>
									<? } else {?>
										<th colspan=3 >Add user</th>
									<? }?>
								</tr>
								<tr><td colspan="3">&nbsp;</td>
								</tr>
                    <tr>
						<td align="center">
							<!--<center>-->
							<table class="newstylemaintable" cellspacing=2 border="0">
							<tr>
									<th colspan=3 bgcolor="">Credentials</th>
							</tr>
							<tr>
							<td>
							  <table width="350" border="0">
								<tr>
								  <td bgcolor=""><p class="normaltxt">Full Name:</p></td>
								  <td bgcolor=""><input name="user_name" maxlength="50" type="text" class="BoxCSS" id="user_name" value="<? echo $user_name;?>">*</td>
								</tr>
								<tr>
								  <td bgcolor="" width="200" valign=top>
								<? if (!$user_id_error) { ?>
							<p class="normaltxt">	User ID:</p>
								<? } else { ?>
							<p class="normaltxt">	<font color=red><b>User ID:</b></font></p>
								<? } ?>
								</td>
								  <td bgcolor="" width="280"><input name="user_id1" maxlength="50" class="BoxCSS" type="text" id="user_id" value="<? echo $user_id1;?>">*
								  <br><input type="Button" name="get_id" value="Create ID" class="ButtonCSS" onclick="get_user_id();"></td>
								</tr>
								<tr>
								  <td bgcolor=""><p class="normaltxt">Member code:</p></td>
								  <td bgcolor=""><input name="mem_code" maxlength="6" size="10" type="text" class="BoxCSS1" id="mem_code" value="<? echo $mem_code;?>">*</td>
								</tr>
								<tr>
								  <td bgcolor=""><p class="normaltxt">Password:</p></td>
								  <td bgcolor=""><input name="user_pass" maxlength="50" type="password" class="BoxCSS" id="user_pass">
								  <? if ($id=="") {?>*<? }?></td>
								</tr>
								<tr>
								  <td bgcolor=""><p class="normaltxt">Confirm password:</p></td>
								  <td bgcolor=""><input name="confirm_passwd" maxlength="50" type="password" class="BoxCSS" id="confirm_passwd">
								  <? if ($id=="") {?>*<? }?></td>
								</tr>
								<tr>
								  <td bgcolor=""><p class="normaltxt">Phone:</p></td>
								  <td bgcolor=""><input name="user_phone" maxlength="25" size="15" type="text" class="BoxCSS1" id="user_phone" value="<? echo $user_phone;?>">*</td>
								</tr>
								<tr>
								  <td bgcolor="" width="200"><p class="normaltxt">Email:</p></td>
								  <td bgcolor="" width="280"><input name="user_email1" maxlength="255" size="25" class="BoxCSS1" type="text" id="user_email1" value="<? echo $user_email1;?>">*</td>
								</tr>
								<tr>
								  <td bgcolor="" width="200"><p class="normaltxt">Confirm Email:</p></td>
								  <td bgcolor="" width="280"><input name="user_email2" maxlength="255" size="25" class="BoxCSS1" type="text" id="user_email2" value="<? echo $user_email1;?>">*</td>
								</tr>
							  </table>
								<script language="JavaScript">
									document.userForm.user_name.focus();
								</script>
							</td>
							</tr>
							</table>
							<!--</center>-->
						</td>
						<td>&nbsp;
						</td>
						<td>
							<!--<center>-->
							<table  border="0" class=newsytletable>
							<tr>
							<td>
							  <table width="350" border="0" class=newstylemaintable>
								<tr>
									<th colspan=2 bgcolor="">Privilages</th>
								</tr>
								<tr>
								  <td bgcolor=""><p class="normaltxt">Office:</p></td>
								  <td bgcolor="">
										<?//Get all Reports
										 $query3 = "SELECT report_name, report_code FROM ami_reports where office_code!='' order by report_name";
										 $result3 = @mysql_db_query($dbname,$query3);?>
										 <select name="user_type1">
									<? if ($user_level != "L") { ?>	
										 <? if ($user_type1=="P") {?>
										 	<option value="P" selected>President - P</option>
										 <? } else {?>
										 	<option value="P">President - P</option>
										 <? }?>
									 <? }?>
										 <?php while ($row3 = mysql_fetch_array($result3)) { ?>
										  <?
												$val = $row3['report_code'];
												$des = $row3['report_name'];
											if ($user_type1 == $val) {?>
												<option value=<? print "\"$val\"";  ?> selected><? print "$des - $val";  ?></option>
											<? } else {?>
												<option value=<? print "\"$val\"";  ?>><? print "$des - $val";  ?></option>
											<? }?>
										 <? }?>
									<? if ($user_level != "L") { ?>	
										 <? if ($user_type1=="E") {?>
										 	<option value="E" selected>Election - E</option>
										 <? } else {?>
										 	<option value="E">Election - E</option>
										 <? }?>
									 <? }?>
										 </select>
								  	*</td>
								</tr>
								<tr>
								  <td bgcolor=""><p class="normaltxt">Level:</p></td>
								  <td bgcolor="">
	<? 
	$levellabel="National";
	if (($user_level == "N") && ($branch_code!=$nat_branch)) { 
		$levellabel = "Imarat";
	}
	?>

							<? if ($user_level == "L") { ?>
									<input type="hidden" name="user_level1" value="L">Local
							<? } else { ?>	
								  	<select name="user_level1">
								  	<? if ($user_level=="") {?>
								  		<option value="L" selected>Local</option>
								  		<option value="N">National</option>
								  	<? } else {?>
										<? if ($user_level1=="L") {?>
											<option value="L" selected>Local</option>
											<option value="N"><? echo $levellabel;?></option>
										<? } else {?>
											<option value="L">Local</option>
											<option value="N" selected><? echo$levellabel;?></option>
										<? }?>
								  	<? }?>
								  	</select>*
							<? } ?>	
									</td>
								</tr>
								<tr><td bgcolor=""><p class="normaltxt">Branch:</p></td>
									<td bgcolor="">
							<? if ($user_level == "L") { ?>
									<input type="hidden" name="branch_code1" value="<? echo $branch_code; ?>">
									<? echo $branch_code; ?>
							<? } else { ?>	
									<?//Get all branches
										if ($user_id=="Admin")
									 		$query3 = "SELECT * FROM ami_branches where status=1 order by branch_name";
										else if ($branch_code==$nat_branch)
											 $query3 = "SELECT * FROM ami_branches where status=1 and (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%') order by branch_name";
										else
											 $query3 = "SELECT * FROM ami_branches where status=1 and (region_code='$branch_code' OR branch_code='$branch_code') order by branch_name";
									 $result3 = @mysql_db_query($dbname,$query3);?>
									 <select name="branch_code1">
									 <?php while ($row3 = mysql_fetch_array($result3)) { ?>
									  <?
											$val = $row3['branch_code'];
											$des = $row3['branch_name'];
											if ($branch_code1 == $val) {?>
												<option value=<? print "\"$val\"";  ?> selected><? print "$des - $val";  ?></option>
											<? } else {?>
												<option value=<? print "\"$val\"";  ?>><? print "$des - $val";  ?></option>
											<? }?>
										<? }?>
										</select>*
							<? } ?>	
									  </td>
								</tr>
								<tr>
								  <td bgcolor=""><p class="normaltxt">Access type:</p></td>
								  <td bgcolor="">
							<? if ($user_level == "L") { ?>
									<input type="hidden" name="user_dept1" value="Own">Own Office
							<? } else { ?>	
								  	<select name="user_dept1">
								  	<? if ($user_dept1=="All") {?>
								  		<option value="All" selected>All offices</option>
								  		<option value="Own">Own office</option>
								  	<? } else {?>
								  		<option value="All">All offices</option>
								  		<option value="Own" selected>Own office</option>
								  	<? }?>
								  	</select>*
							<? } ?>	
									</td>
								</tr>
								<tr>
								  <td bgcolor=""><p class="normaltxt">Next password change date:</p></td>
								  <td bgcolor="">
								  <? if ($change_pw) {?>
								  	<input name="change_pw" maxlength="10" size="10" type="text" class="BoxCSS1" id="change_pw" value="<? echo $change_pw; ?>">
								  <? } else {?>
								  	<input name="change_pw" maxlength="10" size="10" type="text" class="BoxCSS1" id="change_pw" value="<? echo $today; ?>">
								  <? }?>
								  *<br>(YYYY-MM-DD)</td>
								</tr>
								<tr>
								  <td bgcolor="" width="200"><p class="normaltxt">Expiry date:</p></td>
								  <? if ($expiry_date) {?>
								  <td bgcolor="" width="280"><input name="expiry_date" maxlength="10" size="10" class="BoxCSS1" type="text" id="expiry_date" value="<? echo $expiry_date;?>">*<br>(YYYY-MM-DD)</td>
								  <? } else {?>
								  <td bgcolor="" width="280"><input name="expiry_date" maxlength="10" size="10" class="BoxCSS1" type="text" id="expiry_date" value="<? echo $def_expiry;?>">*<br>(YYYY-MM-DD)</td>
								  <? }?>
								</tr>
								<tr>
								  <td bgcolor="" widht="200"><p align=center class="normaltxt">Status:</p></td>
								  <td bgcolor="" widht="200">
							<? if (($user_level == "L") || ($branch_code!=$nat_branch)) { ?>
									<input type="hidden" name="status" value="0">Inactive
							<? } else { ?>	
								  	<select name="status">
								  	<? if ($status=="1") {?>
								  		<option value="0">Inactive</option>
								  		<option value="1" selected>Active</option>
								  	<? } else {?>
								  		<option value="0" selected>Inactive</option>
								  		<option value="1">Active</option>
									<? }?>
								  	</select>*
								<? } ?>
									</td>
								</tr>
							  </table>
								<script language="JavaScript">
									document.userForm.user_name.focus();
								</script>
							</td>
							</tr>
							</table>
							<!--</center>-->
						</td>
					  </tr>
								<tr><td colspan="3" align="center"><br>&nbsp;<span class="pagetextAltColorSmall">Note:
                                All fields with (*) are required</span></td></tr>
								<tr>
								  <td colspan="3"><center>
								  	<input name="id" type="hidden" id="id" value="<? echo $id;?>">
								  	<input type="submit" name="Submit" value="Submit" class="ButtonCSS" onclick="return submit_onclick()">&nbsp;&nbsp;
								  	<input type="reset" name="Reset" value="Reset" class="ButtonCSS">
								  </td>
								</tr>
								<tr>
								  <td colspan=3><center><br>
								  	<a href="list_users.php" class="newstylesmallbutton">&nbsp;&nbsp; Back &nbsp;&nbsp;</a>
								  </td>
								</tr>
							</form>
					</table>
				</td>
                <td width="160">
                  <?php $add_user_php='Y'; include 'incl/rightbar.inc'; ?>
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
