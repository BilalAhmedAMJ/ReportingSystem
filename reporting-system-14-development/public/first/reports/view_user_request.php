<?php include ("protected.php");
include ("../incl/dbcon.php");
//if ($user_id!="Admin") {
if (($user_id != "Admin") && ($user_type != "P")) {

	header("Location: list_reports.php");
}
$req_sent = "";
$create_user_id = ""; 
if ($reqid == "")
{
//print "test";
	header("Location: list_user_requests.php");
}
else
{
	if (($req_dept == 'UA') || ($req_dept == 'FE')) 
	{
		//print "Email Request to Verify";
                $query0 = "select user_email from ami_users where user_type='$req_dept' and user_level='N' and status='1'";
		$message= "Dear National Secretary sahib,\n\n";
		$message.="Kindly review the request for approval for appointment by visiting http://report.ahmadiyya.ca and confirm the status of Chanda payer/Umur Amma record.\n\n";
		$message.="If the status is satisfactory, please click verify.\n\n";
		$message.="JazakAllah\n\n";
		$message.="National General Secretary";
		$result0 = @mysql_db_query($dbname,$query0);
		if ($result0) {
			$row0 = mysql_fetch_array($result0);
			$theEmail= $row0['user_email'];
			$headers = "From: reports@ahmadiyya.ca" . "\r\n" .  "CC: reports@ahmadiyya.ca";	
			mail($theEmail, "AMJ User Verification Request" , $message, $headers);
			//mail("8340720@gmail.com", "AMJ User Verification Request" , $message, $headers);
			// modify status to pending
			if ($req_dept == 'UA')
				$insert_data = "UPDATE ami_office_request SET status='5', uma_verified='0' where reqid='$reqid'";
			else if ($req_dept == 'FE')
				$insert_data = "UPDATE ami_office_request SET status='5', fin_verified='0' where reqid='$reqid'";
			$result1=@mysql_db_query($dbname,$insert_data,$id_link);
			if ($result1 == "1"){
				$req_sent = $req_dept;
			}
		}

	}

	$query = "SELECT a.*, case a.dept_code when 'P' then 'President' else b.report_name end as report_name,c.branch_name, a.status
			FROM ami_office_request a left join ami_reports b on a.dept_code=b.report_code,  ami_branches c
			WHERE a.branch_code = c.branch_code and a.reqid='$reqid'";


        $result = @mysql_db_query($dbname,$query);

        //print "$result";
        if ($result) {
                $row = mysql_fetch_array($result);
		$branch_name = $row['branch_name'];
		$report_name = $row['report_name'];
                $user_name = $row['user_name'];
                $mem_code= $row['mem_code'];
                $dept_code= $row['dept_code'];
                $branch_code1 = $row['branch_code'];
                $user_phone = $row['user_phone'];
                $user_email1 = $row['user_email'];
                $reason= $row['reason'];
                $by_birth= $row['by_birth'];
                $mem_since= $row['mem_since'];
                $curr_dept= $row['curr_dept'];
                $past_depts= $row['past_depts'];
                $age= $row['age'];
                $moosi= $row['moosi'];
                $beard= $row['beard'];
                $vehicle= $row['vehicle'];
                $chanda_report= $row['chanda_report'];
                $tazeer= $row['tazeer'];
                $family= $row['family'];
                $health= $row['health'];
                $rel_knowedge= $row['rel_knowedge'];
                $date_submitted= $row['date_submitted'];
                $submitted_by= $row['submitted_by'];
                $approved_date= $row['approved_date'];
                $approved_by= $row['approved_by'];
                $comments= $row['comments'];
                $status = $row['status'];
		$fin_verified = $row['fin_verified'];
                $fin_verified_by = $row['fin_verified_by'];
                $fin_verified_date = $row['fin_verified_date'];
                $uma_verified = $row['uma_verified'];
                $uma_verified_by = $row['uma_verified_by'];
                $uma_verified_date = $row['uma_verified_date'];
                $decision_comment= $row['decision_comment'];


		$query2 = "SELECT * FROM ami_users WHERE user_type='$dept_code' and branch_code='$branch_code1'"; //ami_office_request
		$result2 = @mysql_db_query($dbname,$query2);

		//print "$result2";
		if ($result2) {
			$row2 = mysql_fetch_array($result2);
			$e_user_id= $row2['user_id'];
			$e_user_name= $row2['user_name'];
			$e_user_email= $row2['user_email'];
			$e_user_phone= $row2['user_phone'];
			$e_user_type= $row2['user_type'];
			$e_user_expiry= $row2['expiry_date'];
			$e_user_status= $row2['status'];
			$e_branch_code = $row2['branch_code'];
		}

        }



}
?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">

<?php include '../incl/headscript.inc'; ?>
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
                    <tr><td valign="top"><br>
<table width="100%" bgcolor=white border="0" cellspacing="2" cellpadding="0">
<tr> <th bgcolor=white colspan=2 ><span class="pageheader"><font size=4 >Request for New Appointment of Office Bearer</font></span><br><br></th> </tr>

<tr><td width=50% bgcolor=#eeeeee><div align="left"><font size=3><label for="branch_code1">Branch</label>:&nbsp;&nbsp; <b>
<?
print $branch_name;
?>
</b></div></font></td>
<td width=50% bgcolor=#eeeeee><div align="left"><font size="3"><label for="dept_code">Office</label>:&nbsp;&nbsp;
<b>
<? print $report_name; ?></b></span>
</div></td></tr>



<tr><td colspan=2><br></td></tr>
<tr> <th bgcolor=black><span class="header"><font color=white size=2 >New Appointment</font></span></th>
<th bgcolor=black><span class="header"><font color=white size=2 >Current User</font></span></th> 
 </tr>
<tr><td bgcolor=white><!-- FIRST COLUMN -->
<table width="100%" border="0" cellspacing="2" cellpadding="2">
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="mem_code">Member Code</label></div></td>
<td> <? print $mem_code; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="user_name">Member Name</label></div></td>
<td> <? print $user_name; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="user_email">E-mail</label></div></td>
<td> <? print $user_email1; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="user_phone">Phone No.</label></div></td>
<td> <? print $user_phone; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="reason">Reason for Appointment</label></div></td>
<td> <? print $reason; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="by_birth">Ahmadi By Birth</label></div></td>
<td> <? if ($by_birth) print "Yes";
	else
		print "Ahmadi Since ".$mem_since;
	  ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="curr_dept">Current Office Held</label></div></td>
<td> <? print $curr_dept; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="past_depts">Previously Held Office</label></div></td>
<td> <? print $past_depts; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="age">Age</label></div></td>
<td> <? print $age; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="moosi">Moosi</label></div></td>
<td> <? if ($moosi) print "Yes"; else print "No";  ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="beard">Beard</label></div></td>
<td> <? if ($beard) print "Yes"; else print "No";  ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="vehicle">Vehicle</label></div></td>
<td> <? if ($vehicle) print "Yes"; else print "No";  ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="chanda_report">Chanda Report</label></div></td>
<td> <? if ($chanda_report == 0)
			print "Regular"; 
	else if ($chanda_report == 1)
			print "Ba`Shara";
	else
			print "Other"; 
		?></td></tr>

<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="family">Family History</label></div></td>
<td> <? print $family; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="tazeer">Tazeer</label></div></td>
<td> <? print $tazeer; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="health">Health Condition</label></div></td>
<td> <? print $health; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="rel_knowedge">Religious Knowledge</label></div></td>
<td> <? print $rel_knowedge; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="comments">Comments</label></div></td>
<td> <? print $comments; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="date_submitted">Date Submitted</label></div></td>
<td> <? print $date_submitted; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="submitted_by">Submitted By</label></div></td>
<td> <? print $submitted_by; ?></td></tr>

</table>
</td>
<td valign=top bgcolor=white><!-- SECOND COLUMN -->

<table border=0 cellspacing=2 cellpadding=2 width=100%>
<? if (!$e_user_id || $e_user_id == '')
{
	$create_user_id = strtolower($dept_code) . $branch_code1;
?>
        <tr><td>NEW USER ACCOUNT: <b>
<? print $create_user_id; ?></b>
</td></tr>

<? } else { ?>

<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="e_user_id">User ID</label></div></td>
<td> <? print $e_user_id; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="e_user_name">User Name</label></div></td>
<td> <? print $e_user_name; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="e_user_email">User Email</label></div></td>
<td> <? print $e_user_email; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="e_user_phone">User Phone</label></div></td>
<td> <? print $e_user_phone; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="e_user_expiry">Expiry Date</label></div></td>
<td> <? print $e_user_expiry; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="e_user_status">Status</label></div></td>
<td> <? print $e_user_status; ?></td></tr>
<? } ?>
<tr><td colspan=2><hr></td></tr>
<? if ($user_id=='Admin') {?>
<tr><td colspan=2><b>FINANCE</b></td></tr>
<? if (($user_id=='Admin')  && ($status!=1)) {?>
<tr><td colspan=2><form  name="VerifyRequest" method="post" action="view_user_request.php">
<input name="reqid" type="hidden" id="reqid" value="<? echo $reqid;?>">
<input name="req_dept" type="hidden" id="req_dept" value="FE">
<input  type="submit" name="Submit" value="Request Verify" class="ButtonCSS2">&nbsp; E-mail Reminder <? if ($req_sent=='FE') echo "<font color=green>SENT</font>"  ?></form></td>
</tr>
<? } ?>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="fin_verified">FIN Verification</label></div></td>
<td> <? if ($fin_verified) print "Yes"; else print "No";  ?>  
</td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="fin_verified">FIN Verification By</label></div></td>
<td> <? print $fin_verified_by; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="fin_verified">FIN Verification Date</label></div></td>
<td> <? print $fin_verified_date; ?></td></tr>
<tr><td colspan=2>&nbsp;</td></tr>
<tr><td colspan=2><b>UMOOR-E-AAMA</b></td></tr>
<? if (($user_id=='Admin')  && ($status!=1)) {?>
<tr><td colspan=2><form  name="VerifyRequest" method="post" action="view_user_request.php">
<input name="reqid" type="hidden" id="reqid" value="<? echo $reqid;?>">
<input name="req_dept" type="hidden" id="req_dept" value="UA">
<input  type="submit" name="Submit" value="Request Verify" class="ButtonCSS2">&nbsp; E-mail Reminder <? if ($req_sent=='UA') echo "<font color=green>SENT</font>"  ?></form></td>
</tr>
<? } ?>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="fin_verified">UMA Verification</label></div></td>
<td> <? if ($uma_verified) print "Yes"; else print "No";  ?>
</td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="fin_verified">UMA Verification By</label></div></td>
<td> <? print $uma_verified_by; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="fin_verified">UMA Verification Date</label></div></td>
<td> <? print $uma_verified_date; ?></td></tr>
<? } ?>
<? if ($status == 1) {?>
<tr><td colspan=2>&nbsp;</td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="approved_by">Approved By</label></div></td>
<td> <? print $approved_by; ?></td></tr>
<tr><td bgcolor=#eeeeee width = "150"><div align="left"><label for="approved_date">Approved Date</label></div></td>
<td> <? print $approved_date; ?></td></tr>
<? } ?>
<?//////////// SHOW STATUS /////////?>
<tr><td colspan=2>&nbsp;</td></tr>
<tr>
<td bgcolor=#eeeeee width = "150"><div align="left"><label for="status">Status</label></div></td>
<?if($status == "0")
{
	print "<td align=\"left\" style=\"padding:2px;\"><font color=\"blue\">New</font></td>\n";
}
else if ($status == "1")
{
	print "<td align=\"left\" style=\"padding:2px;\"><font color=\"green\">Approved</font></td>\n";
}
else if ($status == "5")
{
	print "<td align=\"left\" style=\"padding:2px;\"><font color=\"blue\">Pending ".$verified_str." Verification</font></td>\n";
}
else if ($status == "4")
{
	print "<td align=\"left\" style=\"padding:2px;\"><font color=\"blue\">Verified</font></td>\n";
}
else if ($status == "8")
{
	print "<td align=\"left\" style=\"padding:2px;\"><font color=\"black\">Letter Issued</font></td>\n";
}
else if ($status == "9")
{
	print "<td align=\"left\" style=\"padding:2px;\"><font color=\"grey\">Rejected</font><br>[".$decision_comment."]</td>\n";
}
else
{
	print "<td align=\"left\" style=\"padding:2px;\"><font color=\"red\">Unknown</font></td>\n";
}?>
</tr>

<?////////////////////////////////?>
</table>
</td></tr>
<tr><td colspan=2><hr></td></tr>
<tr><td colspan=2><br><center>
<? if (($user_id=='Admin')  && ($status!=1) && ($status!=8) && ($status!=9)) {?>

<form  name="userApproveForm" method="post" action="approve_user_request.php">
Expires in&nbsp;
<select name="new_user_expiry">
<option selected value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
</select> year(s).&nbsp;&nbsp;
<input name="reqid" type="hidden" id="reqid" value="<? echo $reqid;?>">
<input name="e_user_id" type="hidden" id="e_user_id" value="<? echo $e_user_id;?>">
<? if ($create_user_id!="") { ?>
<input name="create_user_id" type="hidden" id="create_user_id" value="<? echo $create_user_id;?>">
<? } ?>
<input name="new_user_name" type="hidden" id="new_user_name" value="<? echo $user_name;?>">
<input name="new_user_email" type="hidden" id="new_user_email" value="<? echo $user_email1;?>">
<input name="new_user_phone" type="hidden" id="new_user_phone" value="<? echo $user_phone;?>">
<input name="new_user_mcode" type="hidden" id="new_user_mcode" value="<? echo $mem_code;?>">
<input name="e_branch_code" type="hidden" id="e_branch_code" value="<? echo $branch_code1;?>">
<input name="e_branch_name" type="hidden" id="e_branch_name" value="<? echo $branch_name;?>">
<input name="e_dept_name" type="hidden" id="e_dept_name" value="<? echo $report_name;?>">
<input name="dept_code" type="hidden" id="dept_code" value="<? echo $dept_code;?>">
<input  type="submit" name="Submit" value="Approve" class="ButtonCSS4"><br>
</form>
<? } 
if (($user_id=='Admin')  && ($status!=1) && ($status!=8) && ($status!=9)) {?>
<form  name="userRejectForm" method="post" action="approve_user_request.php">
<input name="reqid" type="hidden" id="reqid" value="<? echo $reqid;?>">
<input name="reject_req" type="hidden" id="reject_req" value="1">
<input name="new_user_name" type="hidden" id="new_user_name" value="<? echo $user_name;?>">
<input name="new_user_email" type="hidden" id="new_user_email" value="<? echo $user_email1;?>">
<input name="new_user_phone" type="hidden" id="new_user_phone" value="<? echo $user_phone;?>">
<input name="new_user_mcode" type="hidden" id="new_user_mcode" value="<? echo $mem_code;?>">
<input name="e_branch_code" type="hidden" id="e_branch_code" value="<? echo $branch_code1;?>">
<input name="e_branch_name" type="hidden" id="e_branch_name" value="<? echo $branch_name;?>">
<input name="e_dept_name" type="hidden" id="e_dept_name" value="<? echo $report_name;?>">
<input name="dept_code" type="hidden" id="dept_code" value="<? echo $dept_code;?>">
<input  type="submit" name="Submit" value="Reject" class="ButtonCSS1"><br>
<textarea id="decision_comment" name="decision_comment" rows="3" cols="40"></textarea>
</form>
<? } ?>
<? if (($user_id=='Admin')  && ($status==1)) {?>
<form  name="NotifiedApproval" method="post" action="approve_user_request.php">
<input name="letterissued_req" type="hidden" id="letterissued_req" value="1">
<input name="reqid" type="hidden" id="reqid" value="<? echo $reqid;?>">
<input  type="submit" name="Submit" value="Letter Issued" class="ButtonCSS2"></form>
<? } ?>
<? if (($user_id=='Admin')  && ($status==9)) {?>
<form  name="RevokeRejection" method="post" action="approve_user_request.php">
<input name="revokerejection_req" type="hidden" id="revokerejection_req" value="1">
<input name="reqid" type="hidden" id="reqid" value="<? echo $reqid;?>">
<input  type="submit" name="Submit" value="Revoke" class="ButtonCSS2"></form>
<? } ?>
<? if (($user_id=='Admin')  && ($status!=1) && ($status!=8) && ($status!=9)) {?>
<form  name="DeleteReq" method="post" action="approve_user_request.php">
<input name="delete_req" type="hidden" id="delete_req" value="1">
<input name="reqid" type="hidden" id="reqid" value="<? echo $reqid;?>">
<input  type="submit" name="Submit" value="Delete" class="ButtonCSS1"></form>
<? } ?>
<br><br>
<a class="ButtonCSS3"  href="list_user_requests.php"> Back To List </a>
</td></tr>
</table>
</td>
                <td width="160">
                  <?php $add_user_php='Y'; include 'incl/rightbar.php'; ?>
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

