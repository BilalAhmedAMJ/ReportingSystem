<?php include ("protected.php");
include ("../incl/dbcon.php");
if ($user_level!="N")  {
	header("Location: list_user_requests.php");
}
$today = date("Y-m-d");

$Error = "";
if (($reqid == "") || (($user_type!="FE") && ($user_type!="UA") && ($user_type!="P")))
{
	header("Location: list_user_requests.php");
}
else
{
	if ((($user_type=="FE") || ($user_type=="UA")) && ($new_status=="1"))	// UPDATE TEH REQUEST STATUS
	{

		$_fin_verified = '';
		$_uma_verified = '';
		$query0 = "SELECT * FROM ami_office_request WHERE reqid='$reqid'";
		$result0 = @mysql_db_query($dbname,$query0);
		if ($result0) {
			$row0 = mysql_fetch_array($result0);
			$_fin_verified= $row0['fin_verified'];
			$_uma_verified= $row0['uma_verified'];
		}
		if ($user_type=="FE") 
		{
			$insert_data = "UPDATE ami_office_request SET fin_verified='$new_status', fin_verified_by='$user_id', fin_verified_date='$today' ";
			$_fin_verified= $new_status;
		}
		else if ($user_type=="UA") 
		{
			$insert_data = "UPDATE ami_office_request SET uma_verified='$new_status', uma_verified_by='$user_id', uma_verified_date='$today' ";
			$_uma_verified= $new_status;
		}
		if ($_uma_verified=='1' && $_fin_verified=='1')
			$insert_data .= ", status='4' ";	// VERIFIED

		$insert_data .= " where reqid='$reqid'";
		$result=@mysql_db_query($dbname,$insert_data,$id_link);
		if ($result == "1"){
			//print $insert_data;
			//$Error = "User updated successfully!";
			//$subject = "$Field11 - reg success";
			//mail("$Field8", "Online Registration", $smsg, "From:8340720@gmail.com");
			//header("Location: list_users.php?success=1");
		} else {
			print $insert_data;
			$Error = "<br>Error: Could not Verify Request!";
			//header("Location: list_users.php?failed=1");
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
                $comments= $row['comments'];
                $status = $row['status'];
                $fin_verified = $row['fin_verified'];
                $fin_verified_by = $row['fin_verified_by'];
                $fin_verified_date = $row['fin_verified_date'];
                $uma_verified = $row['uma_verified'];
                $uma_verified_by = $row['uma_verified_by'];
                $uma_verified_date = $row['uma_verified_date'];

		$query2 = "SELECT * FROM ami_users WHERE user_type='$dept_code' and branch_code='$branch_code1'"; //ami_office_request
		$result2 = @mysql_db_query($dbname,$query2);

		//print "$result";
		if ($result2) {
			$row2 = mysql_fetch_array($result2);
			$e_user_id= $row2['user_id'];
			$e_user_name= $row2['user_name'];
			$e_user_email= $row2['user_email'];
			$e_user_phone= $row2['user_phone'];
			$e_user_type= $row2['user_type'];
			$e_user_expiry= $row2['expiry_date'];
			$e_user_status= $row2['status'];
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
          <td width="100" valign=top bgcolor=black>
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
<tr><td bgcolor=#eeeeee><div align="left"><font size=3><label for="branch_code1">Branch</label>:&nbsp;&nbsp; <b>
<?
print $branch_name;
?>
</b></div></font></td>
<td bgcolor=#eeeeee><div align="left"><font size="3"><label for="dept_code">Office</label>:&nbsp;&nbsp;
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
{?>
	<tr><td>No Existing Account</td></tr>
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
</table>
</td></tr>

<tr><td colspan=2><br><center>
<? 
if ($fin_verified == '1')
	print "Request Has Been <font size=3 color=green><b>Verified by Finance</b></font><br>";
if ($uma_verified == '1')
	print "Request Has Been <font size=3 color=green><b>Verified by Umoor-e-Aama</b></font><br>";
 
if ((($fin_verified!='1') && ($user_type=="FE")) || 
	(($uma_verified!='1') && ($user_type=="UA"))) 	// UPDATE TEH REQUEST STATUS 
{ ?>

<form name="userForm" method="post" action="verify_user_request.php"> 
 <input name="reqid" type="hidden" id="reqid" value="<? echo $reqid;?>">
 <input name="new_status" type="hidden" id="new_status" value="1">
 <input name="dept_code" type="hidden" id="dept_code" value="<?echo $dept_code;?>">
<font size=2>Once you have verified the above information click <b>Verify</b> to approve this request!<br></font>
<br>
 <input  type="submit" name="Submit" value="Verify" class="ButtonCSS">
</form>

<? } 

?>
<br><br>
<a class="ButtonCSS3"  href="list_user_requests.php"> Back To List </a>
</td></tr>
</table>
</td>
                <td width="160" bgcolor="#F3F3F3">
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

