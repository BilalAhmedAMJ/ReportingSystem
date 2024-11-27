<?php include ("protected.php");
include ("../incl/dbcon.php");
if (($user_type!="P") && ($user_type!="GS") && ($user_id!="Admin")) {
	header("Location: list_reports.php");
}
$_SESSION['request_submitted'] = 0;
?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">

<?php include '../incl/headscript.inc'; ?>
<script language="JavaScript" type="text/JavaScript">
<!--
function submit_onclick() {
	var s = document.userForm.user_email1.value;
	var len = s.length-1;
	if (document.userForm.user_name.value == "" || document.userForm.user_name.value == " "){
		alert('You are missing user name!');
		document.userForm.user_name.focus();
		return false;
	}
	if (document.userForm.mem_code.value == "" || document.userForm.mem_code.value == " "){
		alert('You are missing Member Code!');
		document.userForm.mem_code.focus();
		return false;
	}
	if (document.userForm.user_phone.value == "" || document.userForm.user_phone.value == " "){
		alert('You are missing Phone#!');
		document.userForm.user_phone.focus();
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
	if (document.userForm.reason.value == "" || document.userForm.reason.value == " "){
		alert('You must provide Reason For Appointment!');
		document.userForm.mem_code.focus();
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
    <td align="left" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="100" valign=top >
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
                    <tr><td valign="top">
<form id="userForm" action="new_user_requested.php" method="post" name="userForm">
<table width="100%" border="0" class=newstyletable cellspacing="1" cellpadding="4">
									<tr>
<tr> <th colspan=2 >Request for Appointment of an Office Bearer<br>
		<font size=1>All questions must be answered. Incomplete form will not be processed.</font><br><br></th>
									</tr>
<tr><td bgcolor="#dddddd" width = "150"><div align="left"><label for="branch_code1">Branch</label></div></td>
<td bgcolor="#dddddd">
 <?//Get all branches

if ($user_id=="Admin") {
                                                           $query3 = "SELECT * FROM ami_branches where status=1 order by branch_name";
                                                                         $result3 = @mysql_db_query($dbname,$query3);?>
                                                                         <select name="branch_code1">
                                                                         <?php while ($row3 = mysql_fetch_array($result3)) { ?>
                                                                          <?
                                                                                        $val = $row3['branch_code'];
                                                                                        $des = $row3['branch_name'];
											if ($val == '$nat_branch') continue;
                                                                                        if ($branch_code1 == $branch_code) {?>
                                                                                                <option value=<? print "\"$val\"";  ?> selected><? print "$des - $val";  ?></option>
                                                                                        <? } else {?>
                                                                                                <option value=<? print "\"$val\"";  ?>><? print "$des - $val";  ?></option>
                                                                                        <? }?>
                                                                                <? }?>
                                                                                </select>

<?}
else
{
?>

<input id="branch_code1" name="branch_code1" type="hidden" value="<? print $branch_code; ?>" >
<?
print "[".$branch_code."]";
}
?>
</td></tr>
<tr><td bgcolor="#dddddd" width = "150"><div align="left"><label for="dept_code">Office</label></div></td>
<td bgcolor="#dddddd">
  <?//Get all Reports
                                                                                 $query3 = "SELECT report_name, report_code FROM ami_reports where office_code!='' order by report_name";
                                                                                 $result3 = @mysql_db_query($dbname,$query3);?>
                                                                                 <select name="dept_code">
                                                                                 <?php while ($row3 = mysql_fetch_array($result3)) { ?>
                                                                                  <?
                                                                                                $val = $row3['report_code'];
                                                                                                $des = $row3['report_name'];
										?>
                                                                                                <option value=<? print "\"$val\"";  ?>><? print "$des - $val";  ?></option>
                                                                                 <? }?>
                                                                                  <option value="P">President - P</option>
</select>

</td></tr>
<tr><td bgcolor="#dddddd" width = "150"><div align="left"><label for="mem_code">Member Code</label></div></td>
<td bgcolor=#dddddd><input id="mem_code" name="mem_code" type="text" size="10" value="" maxlength="6"></td></tr>
<tr><td bgcolor="#dddddd" width = "150"><div align="left"><label for="user_name">Name Proposed</label></div></td>
<td bgcolor=#dddddd><input id="user_name" name="user_name" type="text" size="50" value="" maxlength="50"></td></tr>
<tr><td bgcolor="#dddddd" width = "150"><div align="left"><label for="user_phone">Phone No.</label></div></td>
<td bgcolor=#dddddd><input id="user_phone" name="user_phone" type="text" size="25" value="" maxlength="25"></td></tr>
<tr><td bgcolor="#dddddd" width = "150"><div align="left"><label for="user_email">E-mail</label></div></td>
<td bgcolor=#dddddd><input id="user_email1" name="user_email1" type="text" size="50" value="" maxlength="255"></td></tr>
<tr><td bgcolor=#dddddd width = "150"><div align="left"><label for="reason">Reason for Request</label></div></td>
<td bgcolor=#dddddd><input id="reason" name="reason" type="text" size="100" value="" maxlength="250"></td></tr>
<tr><td bgcolor=#dddddd width = "150"><div align="left"><label for="by_birth">Ahmadi By Birth</label></div></td>
<td bgcolor=#dddddd><input id="by_birth" name="by_birth" type="checkbox" value="1">
<label for="mem_since"> -or-   Ahmadi Since</label>
<input id="mem_since" name="mem_since" type="text" size="25" value="" maxlength="255"></td></tr>
<tr><td bgcolor=#dddddd width = "150"><div align="left"><label for="curr_dept">Current Office(s) held</label></div></td>
<td bgcolor=#dddddd>
<input id="curr_dept" name="curr_dept" type="text" size="80" value="" maxlength="250">
</td>
</tr>
<tr><td bgcolor=#dddddd width = "150"><div align="left"><label for="past_depts">Past Office(s) held in Jama`at / Auxiliary Organizations</label></div></td>
<td bgcolor=#dddddd><input id="past_depts" name="past_depts" type="text" size="80" value="" maxlength="250"></td></tr>
<tr><td bgcolor=#dddddd width = "150"><div align="left"><label for="age">Age</label></div></td>
<td bgcolor=#dddddd><input id="age" name="age" type="text" size="5" value="" maxlength="2">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label for="moosi">Moosi?</label>
<input id="moosi" name="moosi" type="checkbox" value="1">
<label for="beard">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Beard?</label>
<input id="beard" name="beard" type="checkbox" value="1">
<label for="vehicle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vehicle?</label>
<input id="vehicle" name="vehicle" type="checkbox" value="1"></td></tr>
<tr><td bgcolor=#dddddd width = "150"><div align="left"><label for="chanda_report">Chanda Report</label></div></td>
<td bgcolor=#dddddd><select id="chanda_report" name="chanda_report" size="1">
<option value="0">Regular</option>
<option value="1">Ba` Sharah</option>
<option value="2">Other</option>
</select></td></tr>
<tr><td bgcolor=#dddddd width = "150"><div align="left"><label for="family">Family Background</label></div></td>
<td bgcolor=#dddddd><input id="family" name="family" type="text" size="100" value="" maxlength="250"></td></tr>
<tr><td bgcolor=#dddddd width = "150"><div align="left"><label for="tazeer">Tazeer (Disciplinary Action)?, If Any</label></div></td>
<td bgcolor=#dddddd><input id="tazeer" name="tazeer" type="text" size="100" value="" maxlength="250"></td></tr>
<tr><td bgcolor=#dddddd width = "150"><div align="left"><label for="health">Health & Mobility</label></div></td>
<td bgcolor=#dddddd><input id="health" name="health" type="text" size="100" value="" maxlength="250"></td></tr>
<tr><td bgcolor=#dddddd width = "150"><div align="left"><label for="rel_knowedge">Level of Religious Knowledge</label></div></td>
<td bgcolor=#dddddd><input id="rel_knowedge" name="rel_knowedge" type="text" size="100" value="" maxlength="250"></td></tr>
<tr><td bgcolor=#dddddd width = "150"><div align="left"><label for="comments">Comments</label></div></td>
<td bgcolor=#dddddd><textarea id="comments" name="comments" rows="4" cols="40"></textarea></td></tr>

<tr><td width="150"></td><td><br>
<input type="submit" name="Submit" value="Submit" class="ButtonCSS" onclick="return submit_onclick()">&nbsp;&nbsp;
                                                                        <input type="reset" name="Reset" value="Reset" class="ButtonCSS">


</td>

<script language="JavaScript">
	document.userForm.user_name.focus();
</script>

</tr></table></form>



</td>
                <td width="160"  valign=top>
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

