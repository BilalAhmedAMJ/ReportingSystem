<?php include ("protected.php");
include ("../incl/dbcon.php");
if ($user_id!="Admin")  {
	header("Location: list_user_requests.php");
}
$today = date("Y-m-d");

$Error = "";


if ($reqid == "") 
{
	header("Location: list_user_requests.php");
}
else 
{
    if ($revokerejection_req=="1") 
    {
	///// MARK AS NEW 	// i.e. status 1
	$insert_data_reject = "UPDATE ami_office_request SET status='0', approved_by='', decision_comment='', approved_date='' where reqid='$reqid'";
	$result_reject=@mysql_db_query($dbname,$insert_data_reject,$id_link);
    }
    else if ($delete_req=="1") 
    {
	///// Delete record i.e. status 2
	$insert_data_delete = "UPDATE ami_office_request SET status='2', approved_by='', decision_comment='', approved_date='' where reqid='$reqid'";
	$result_delete=@mysql_db_query($dbname,$insert_data_delete,$id_link);
	header("Location: list_user_requests.php");
    }
    else if ($letterissued_req=="1") 
    {
	///// MARK AS LETTER SENT  ie.. status 8
	$insert_data_reject = "UPDATE ami_office_request SET status='8', approved_by='', approved_date='' where reqid='$reqid'";
	$result_reject=@mysql_db_query($dbname,$insert_data_reject,$id_link);
    }
    else if ($reject_req=="1") 
    {
	///// MARK AS REJECTED	- i.e. status 9
	$insert_data_reject = "UPDATE ami_office_request SET status='9', decision_comment='$decision_comment', approved_by='', approved_date='' where reqid='$reqid'";
	$result_reject=@mysql_db_query($dbname,$insert_data_reject,$id_link);
	// send e-mail to president 

	$query_r = "select user_email from ami_users where (user_type='P' and branch_code='$e_branch_code') and status='1'";
	$message_r = "Assalam-o-Alaikum Respected Office Bearer,\n\nNew office bearer request for the following member has been Rejected because.";
	$message_r .= $decision_comment;
	$message_r .= ".\n\n";
	$message_r .= "Member Code: ".$new_user_mcode. "\n";
	$message_r .= "Member Name: ".$new_user_name . "\n";
	$message_r .= "Office: ".$e_dept_name. "\n";
	$message_r .= "Branch: ".$e_branch_name. "\n\n";
	$message_r .= "Wasalaam\nNational General Secretary\n".$amjwho.".";
	$result_r = @mysql_db_query($dbname,$query_r);
	if ($result_r) {
		while ($row_r = mysql_fetch_array($result_r)) {
			$theEmail= $row_r['user_email'];
			//print $theEmail;
			mail($theEmail.", reports@ahmadiyya.ca", "AMJ User Verification Request" , $message_r, "From:reports@ahmadiyya.ca");
			//mail("samer@microcan.net", "TEST - AMJ User Verification Request" , $message_r, "From:reports@ahmadiyya.ca");
		}
	}

    }
    else if (!$e_user_id || $e_user_id == "")
    {
	if (($new_user_name=="") || ($new_user_email=="") || ($new_user_phone==""))
	{
		print "User name, email or phone missing";
		exit();
	}
	else if (!$create_user_id || $create_user_id=="") {
		header("Location: list_user_requests.php");
	} else {
		///////// UPDATE STATUS OF REQUEST TO BE APPROVED
		$insert_data = "UPDATE ami_office_request SET status='1', approved_by='$user_id', approved_date='$today', fin_verified='1', uma_verified='1' where reqid='$reqid'";
		$result4=@mysql_db_query($dbname,$insert_data,$id_link);
		if ($result4 == "1"){
			//$Error = "User updated successfully!";
			///////// CREATE NEW USER ACCOUNT 
			$theexpiry = date("Y-m-d",mktime(0, 0, 0, 06,   30,   date("Y")+$new_user_expiry));
			$insert_data1 = "insert into ami_users
               			values ('', '$new_user_name', '$create_user_id', old_password('$1001!001'),
					'$dept_code', 'L', '$e_branch_code',
					'$dept_code', '$new_user_phone', '$new_user_email',
					'$change_pw', '$theexpiry', '1','','$new_user_mcode','');";
			//print "$insert_data<br>";
			$result1=@mysql_db_query($dbname,$insert_data1,$id_link);
                        if ($result1 == "1"){
                                //$Error = "User added successfully!";
                                //$subject = "$Field11 - reg success";
				//print 'Approved';	
				// send e-mail to president and national dept head

				$query00 = "select user_email from ami_users where (user_type='$dept_code' and user_level='N') || (user_type='P' and branch_code='$e_branch_code') and status='1'";
				$message0 = "Assalam-o-Alaikum Respected Office Bearer,\n\nNew office bearer request for the following member has been approved.\n\n";
				$message0 .= "Member Code: ".$new_user_mcode. "\n";
				$message0 .= "Member Name: ".$new_user_name . "\n";
				$message0 .= "Office: ".$e_dept_name. "\n";
				$message0 .= "Branch: ".$e_branch_name. "\n\n";
				$message0 .= "Wasalaam\nNational General Secretary\n".$amjwho.".";
				//print $query00.'<BR>';
				$result00 = @mysql_db_query($dbname,$query00);
				if ($result00) {
					while ($row00 = mysql_fetch_array($result00)) {
						$theEmail= $row00['user_email'];
						//print $theEmail;
						mail($theEmail.", reports@ahmadiyya.ca", "AMJ User Verification Request" , $message0, "From:reports@ahmadiyya.ca");
						//mail("8340720@gmail.com", "AMJ User Verification Request" , $message.$theEmail, "From:reports@ahmadiyya.ca");
					}
				}
                        } else {
                                //$Error = "Could not add user!";
				///////// UNDO REQUEST STATUS CHANGE
				// undo approval as couldnt create user account
				$insert_data3 = "UPDATE ami_office_request SET status='4', approved_by='', approved_date='' where reqid='$reqid'";
				$result3=@mysql_db_query($dbname,$insert_data3,$id_link);
                        }
		}
		else 
		{
			//print $insert_data;
			$Error = "<br>Error: Could not Approve Request!";
		}
	}
    }
    else
    {
	if (($new_user_name=="") || ($new_user_email=="") || ($new_user_phone==""))
	{
		print "User name, email or phone missing";
		exit();
	}
	else	// APPROVE
	{
		// approve, also ensure verifications are also marked as verified
		$insert_data = "UPDATE ami_office_request SET status='1', approved_by='$user_id', approved_date='$today', fin_verified='1', uma_verified='1' where reqid='$reqid'";
		$result=@mysql_db_query($dbname,$insert_data,$id_link);
		if ($result == "1"){
			//$Error = "User updated successfully!";
			//$subject = "$Field11 - reg success";
			//mail("$Field8", "Online Registration", $smsg, "From:8340720@gmail.com");
			//header("Location: list_users.php?success=1");

			$theexpiry = date("Y-m-d",mktime(0, 0, 0, 06,   30,   date("Y")+$new_user_expiry));
			// now copy user info to ami_user;
			$insert_data2 = "UPDATE ami_users SET user_name='$new_user_name', user_pass=old_password('1001!001'), user_email='$new_user_email', user_phone='$new_user_phone', expiry_date='$theexpiry', mem_code='$new_user_mcode', status='1' where user_id='$e_user_id'";
			//print $insert_data2;
			$result2=@mysql_db_query($dbname,$insert_data2,$id_link);
			if ($result2 == "1"){
				//print 'Approved';	
				// send e-mail to president and national dept head

				$query0 = "select user_email from ami_users where (user_type='$dept_code' and user_level='N') || (user_type='P' and branch_code='$e_branch_code') and status='1'";
				$message = "Assalam-o-Alaikum Respected Office Bearer,\n\nNew office bearer request for the following member has been approved.\n\n";
				$message .= "Member Code: ".$new_user_mcode. "\n";
				$message .= "Member Name: ".$new_user_name . "\n";
				$message .= "Office: ".$e_dept_name. "\n";
				$message .= "Branch: ".$e_branch_name. "\n\n";
				$message .= "Wasalaam\nNational General Secretary\n".$amjwho.".";
				//print $query0.'<BR>';
				$result0 = @mysql_db_query($dbname,$query0);
				if ($result0) {
					while ($row0 = mysql_fetch_array($result0)) {
						$theEmail= $row0['user_email'];
						//print $theEmail;
						mail($theEmail.", reports@ahmadiyya.ca", "AMJ User Verification Request" , $message, "From:reports@ahmadiyya.ca");
						//mail("8340720@gmail.com", "AMJ User Verification Request" , $message.$theEmail, "From:reports@ahmadiyya.ca");
					}
				}

			}
			else
			{	
				// undo approval as couldnt replace user
				$insert_data3 = "UPDATE ami_office_request SET status='4', approved_by='', approved_date='' where reqid='$reqid'";
				$result3=@mysql_db_query($dbname,$insert_data3,$id_link);
			}	
			

		} else {
			//print $insert_data;
			$Error = "<br>Error: Could not Approve Request!";
			//header("Location: list_users.php?failed=1");
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
                $comments= $row['comments'];
                $status = $row['status'];
                $fin_verified = $row['fin_verified'];
                $fin_verified_by = $row['fin_verified_by'];
                $fin_verified_date = $row['fin_verified_date'];
                $uma_verified = $row['uma_verified'];
                $uma_verified_by = $row['uma_verified_by'];
                $uma_verified_date = $row['uma_verified_date'];
                $approved_date = $row['approved_date'];
                $approved_by= $row['approved_by'];

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
</table>
</td></tr>

<tr><td colspan=2><br><center>
<? 
if ($status == 1)
	print "Request Has Been <font size=3 color=green><b>Approved</b></font><br>";
else if ($status == 8)
	print "Request Completed (It will not appear in the list)<br>";
else if ($status == 5)
	print "Request is <font size=3 color=blue><b>Pending Verification</b></font><br>";
else 
{
if ($fin_verified == '1')
	print "Request Has Been <font size=3 color=green><b>Verified by Finance</b></font><br>";
else if ($uma_verified == '1')
	print "Request Has Been <font size=3 color=green><b>Verified by Umoor-e-Aama</b></font><br>";
} 

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

