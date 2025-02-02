<? include ("protected.php");
 include("../incl/dbcon.php");
 ?>
<?php
$Error="";
$Error_Msg="";
$PRINTED = 0;
if (!(($user_type=='GS') || ($user_type=='P') || ($user_level=='N')))
{
	print "Restricted Access. ($user_type,$user_level)";
	exit();
}

if ($r_custom_mesg == "Yes")
{
        $rCustomMesg = trim($_REQUEST['r_custom_mesg']);
        $rMesg= trim($_REQUEST['r_mesg']);
        $rSubj= trim($_REQUEST['r_subj']);
	if (($rSubj=='')  || ($rMesg=='') )
		$Error_Msg .= "Email Subject or Message MUST NOT be blank<br>";
}

if (($Error_Msg=='') && ($r_month!='')) {

        $rMonth= trim($_REQUEST['r_month']);
	$rMonthName = date("F", mktime(0, 0, 0, $rMonth, 10)); 
        $rDept= trim($_REQUEST['report_code']);
        $rBranch= trim($_REQUEST['branch']);
        $rLevel= trim($_REQUEST['r_level']);
        $rOutstanding= trim($_REQUEST['r_outstanding']);

	if ($rOutstanding=="3")	// Departments head users to be e-mailed - Only works with Level 'I' and 'N' 
	{
		$depts2receive="";
		$Year = Date('Y');
		if ($rMonth>Date('m'))
			$Year = $Year-1;
		//$query = "select distinct b.report_code from ami_all_reports b where b.year='$Year' and b.month='$rMonth' ";
		//$query = "select distinct b.report_code from ami_all_reports b  left JOIN ami_branches a on a.branch_code=b.branch_code  where b.year='$Year' and b.month='$rMonth' ";
		$query = "select distinct user_id from ami_all_reports b  left JOIN ami_branches a on a.branch_code=b.branch_code LEFT JOIN ami_users c ON b.report_code = c.user_type where b.year='$Year' and b.month='$rMonth' ";
		$query .= " and b.status=2";
		if ($rLevel == 'I') 
			$query .= " and (a.region_code='CAL' or a.region_code LIKE 'I%') and a.branch_code!='$nat_branch' and c.branch_code=a.region_code";
		else if ($rLevel == 'N') 
			$query .= " and a.region_code!='CAL' and a.region_code NOT LIKE 'I%' and a.branch_code!='$nat_branch' and c.branch_code='$nat_branch'";

		$query .= " order by b.report_code asc";
		if (($rLevel == 'I') || ($rLevel == 'N'))
		{
			$result = @mysql_db_query($dbname,$query);
			while ($row = mysql_fetch_array($result)) {
				if (($depts2receive=="") && ($row['user_id']!=""))
					$depts2receive="'". $row['user_id']."'" ;
				else if ($row['user_id']!="")
					$depts2receive.=",'". $row['user_id']."'" ;
			}
			// free	
			mysql_free_result($result);
		}
		else {
			$Error .= "This option Does Not work for 'All' or 'Local' Level. Please select different Level!<br>";
		}
	//print $query."\n";
	}
	else if ($rOutstanding=="2")	// Presidents
	{
		$presidentofbranches="";
		$Year = Date('Y');
		if ($rMonth>Date('m'))
			$Year = $Year-1;
		//$query = "select a.branch_code from ami_all_reports a where a.year='$Year' and a.month='$rMonth' ";
		$query = "select distinct b.branch_code from ami_all_reports b left join ami_users a on a.user_type=b.report_code and a.branch_code=b.branch_code where b.year='$Year' and b.month='$rMonth' ";
		$query .= " and b.status=1";
		if (($rBranch != '') && ($rBranch != 'all')) 
			$query .= " and b.branch_code = '$rBranch'";
		if ($rLevel == 'I') 
			$query .= " and a.user_level='N' and a.branch_code!='$nat_branch'";
		else if ($rLevel == 'N') 
			$query .= " and a.user_level='N' and a.branch_code='$nat_branch'";
		else if (($rLevel!= '') && ($rLevel != 'all'))
			$query .= " and a.user_level='$rLevel'";
		if (($rOutstanding!="2") && ($rDept!= '') && ($rDept!= 'all'))
			$query .= " and b.report_code='$rDept'";

		$query .= " order by b.report_code asc";
		$result = @mysql_db_query($dbname,$query);
		while ($row = mysql_fetch_array($result)) {
			if (($presidentofbranches=="") && ($row['branch_code']!=""))
				$presidentofbranches="'". $row['branch_code']."'" ;
			else if ($row['branch_code']!="")
				$presidentofbranches.=",'". $row['branch_code']."'" ;
		}
		// free	
		mysql_free_result($result);
	}
	else if ($rOutstanding>"0")	// those who havent completed. so here we setup exclusde_users list of those who have submitted already
	{
		$exclude_users = "";
		$Year = Date('Y');
		if ($rMonth>Date('m'))
			$Year = $Year-1;
		$query = "select a.user_id from ami_all_reports b left join ami_users a on a.user_type=b.report_code and a.branch_code=b.branch_code where b.year='$Year' and b.month='$rMonth' ";
		if ($rOutstanding=="1")	// dissregards Branch, Dept and Level
			$query .= " and b.status!='0'";
		else if ($rOutstanding=="2")	// won't happen as this case is already handled
			$query .= " and b.status!=3 and b.status>1";

		if (($rBranch != '') && ($rBranch != 'all')) 
			$query .= " and b.branch_code = '$rBranch'";
		if ($rLevel == 'I') 
			$query .= " and a.user_level='N' and a.branch_code!='$nat_branch'";
		else if ($rLevel == 'N') 
			$query .= " and a.user_level='N' and a.branch_code='$nat_branch'";
		else if (($rLevel!= '') && ($rLevel != 'all'))
			$query .= " and a.user_level='$rLevel'";
		if (($rOutstanding!="2") && ($rDept!= '') && ($rDept!= 'all'))
			$query .= " and b.report_code='$rDept'";

		$query .= " order by b.report_code asc";

		$result = @mysql_db_query($dbname,$query);
		while ($row = mysql_fetch_array($result)) {
			if (($exclude_users=="") && ($row['user_id']!=""))
				$exclude_users = "'". $row['user_id']."'" ;
			else if ($row['user_id']!="")
				$exclude_users .= ",'".$row['user_id']."'";
		}
		// free	
		mysql_free_result($result);
	}

	// Get list of regions (imarat) for each branch
	$query = "SELECT branch_code, branch_name, region_code FROM ami_branches where status!=0"; 
	$result = @mysql_db_query($dbname,$query);
	while ($row = mysql_fetch_array($result)) {
		$branchArray[$row['branch_code']] = $row['region_code']; 
	  	$branchNameArray[$row['branch_code']] = $row['branch_name'];
	}

	// Get list of departments 
	$query = "SELECT report_code, report_name FROM ami_reports"; 
	$result = @mysql_db_query($dbname,$query);
	while ($row = mysql_fetch_array($result)) {
		$departmentArray[$row['report_code']] = $row['report_name']; 
	}

	$subj = "AMJ Report Due";
	$gmessage = "\n\nAssalamo alaikum wa Rahmatullah!";
	if ($rOutstanding=="3")	// users matching user-ids of those national/imarat users who have pending reports.
	{
		$subj = "ACTION REQUIRED: Secretary Report pending acknowledgement";
		//$query = "select distinct a.* from ami_users a where a.status='1' ";
		$query = "select distinct a.*, b.report_name from ami_users a left join ami_reports b on a.user_type=b.report_code where a.status='1'";
		$query .= " and a.user_level='N' and a.user_id in (". $depts2receive .") ";
		$gmessage .= "\n\nThere are reports pending your review and feedback for the month of $rMonthName.";
		$gmessage .= "\n\nYou are requested to login to the reporting system and review these pending reports and acknowledge them by providing your feedback and changing their status to \"Received\". You may click here to proceed: https://report.ahmadiyya.ca";
	}
	else if ($rOutstanding=="2")	// Presidents only
	{
		$subj = "ACTION REQUIRED: Secretary Report pending Verification";
		$query = "select distinct a.* from ami_users a where a.user_type='P' and a.status='1' and a.user_email!='' ";
		$query .= " and a.branch_code in (". $presidentofbranches.") ";
		$gmessage .= "\n\nThere are reports pending your verification for the month of $rMonthName.";
		$gmessage .= "\n\nYou are requested to login to the reporting system and review the pending reports and verify them right away. You may click here to proceed: https://report.ahmadiyya.ca";
		if ($user_level!='N')
			$gmessage .= "\nIt appears that the respective secretary(s) have completed their reports and awaiting your verification. Please visit http://report.ahmadiyya.ca to verify them.";
	}
	else	// all or only those who haven't submitted report 
	{
		// following only selects those users who have previously submitted at least one report 
		$query = "select a.*, b.report_name from ami_users a left join ami_reports b on a.user_type=b.report_code where a.status='1'";
		if ($exclude_users!="")
			$query .= " and a.user_id not in (". $exclude_users .") ";
		if (($rBranch != '') && ($rBranch != 'all')) 
			$query .= " and a.branch_code = '$rBranch'";
		if ($rLevel == 'I') 
			$query .= " and a.user_level='N' and a.branch_code!='$nat_branch'";
		else if ($rLevel == 'N') 
			$query .= " and a.user_level='N' and a.branch_code='$nat_branch'";
		else if (($rLevel!= '') && ($rLevel != 'all'))
			$query .= " and a.user_level='$rLevel'";
		if (($rDept!= '') && ($rDept!= 'all'))
			$query .= " and a.user_type='$rDept'";
		$query .= " order by b.report_code asc";

		$gmessage .= "\n\nYou are requested to submit your monthly report for the month of $rMonthName. Please visit http://report.ahmadiyya.ca to submit your report.";
		if ($user_level!='N')
			$gmessage .= "\nOnce you have completed the report, kindly request your President to verify the reports.";
	}
	$gmessage .= "\n\nJazakumullah!\nWassalam!";

	if (($user_level == 'N') && ($branch_code == $nat_branch) && ($user_type == 'GS'))
		$gmessage .= "\nNational General Secretary";
	else if (($user_level == 'N') && ($branch_code == $nat_branch))
		$gmessage .= "\nNational Secretary $departmentArray[$user_type]";
	else if (($user_level == 'N') && ($user_type!='GS') && (($branch_code=="CAL") || (substr($branch_code,0,1)=="I")))
                $gmessage .= "\nSecretary $departmentArray[$user_type] - $branchNameArray[$branch_code]";
	else if ($rBranch != "all")
		$gmessage .= "\nGeneral Secretary - $rBranch";
	else 
		$gmessage .= "\nGeneral Secretary - $branchNameArray[$branch_code]";
	$sndr = ($user_email!='')?$user_email:"reports@ahmadiyya.ca";

	if ($rCustomMesg == "Yes")
	{
		$subj = $rSubj;
		$message = $rMesg;
	}

        $result = @mysql_db_query($dbname,$query);
	$Error_Msg = "<u><b>Email Reminder Results</u></b><br><br>";
        while ($row = mysql_fetch_array($result)) {
                 $theUser= $row['user_id'];
                 $theEmail= $row['user_email'];
                 $theExpiry_date = $row['expiry_date'];
                 $theName = $row['user_name'];
                 $theType= $row['user_type'];
                 $theDeptName= $row['report_name'];
                 $theBranch = $row['branch_code'];
                 $theRegion= $branchArray[$row['branch_code']];
                 $theBranchName = $branchNameArray[$row['branch_code']];

		// ignore if branch_code != theBranch or branch_code != theRegion
		if (($user_level == 'N') && ($branch_code == $nat_branch))
		{
			if (($theBranch != $branch_code) && ($theRegion != $branch_code) && (substr($theRegion,0,1)!="R"))
				continue;
		} 
		else if (($theBranch != $branch_code) && ($theRegion != $branch_code))
			continue;
////// BEGIN TEST CODE ////
//		print $theBranch.'['.$theRegion.']';
////// END TEST CODE ////

		if ($rCustomMesg == "Yes") { }
		else if ($theType=='GS')
			$message = "Dear General Secretary [$theBranchName] - [User ID: $theUser],".$gmessage;
		else if (($theType=='P') && (($theBranch=="CAL") || (substr($theBranch,0,1)=="I")))
			$message = "Respected $theName, Local Ameer Jama`at [$theBranchName] - [User ID: $theUser],".$gmessage;
		else if ($theType=='P')
			$message = "Respected $theName, Sadr Jama`at [$theBranchName] - [User ID: $theUser],".$gmessage;
		else if ($theBranch == "CA")
			$message = "Respected National Secretary $theDeptName,".$gmessage;
		else
			$message = "Dear Secretary $theDeptName [$theBranchName],".$gmessage;

		if ( (($theType!='P') || ($rOutstanding=='2') || ($rOutstanding=='3')) && 
			($theEmail!="") && ($theExpiry_date!="")) {	// user found
			$pdt = date("Y-m-d");
			if ($theExpiry_date<$pdt){	// account expired
				$Error_Msg .= "'$theUser' account for '$theDeptName' of '$theBranchName' has been expired (Re-registration required)<br>";
			} 
			else if (($theUser) && ($theEmail))
			{
				//mail($theEmail, $subj, $message, "From:$sndr");
		////// BEGIN TEST CODE ////
				if ($PRINTED!=1) {
					print "<br>".$subj. "<br>" .$message."<br>";
					$PRINTED=1;
					mail("8340720@gmail.com", $subj, $message, "From:$sndr");
				}
				//$Error_Msg .= "Reminder sent to '$theUser' '$theDept' successfully.<br>";	// change password enforced
		////// END TEST CODE ////
				if (($theType=='P') && (($theBranch=="CAL") || (substr($theBranch,0,1)=="I")))
                                        $Error_Msg .= "<font color=green>Reminder sent to '$theUser', Local Ameer of $theBranchName successfully.</font><br>";
                                else if ($theType=='P')
                                        $Error_Msg .= "<font color=green>Reminder sent to '$theUser', President of $theBranchName successfully.</font><br>";
                                else if ($theType=='GS')
                                        $Error_Msg .= "<font color=green>Reminder sent to '$theUser', General Secretary of $theBranchName successfully.</font><br>";
                                else 
                                        $Error_Msg .= "<font color=green>Reminder sent to '$theUser', Secretary $theDeptName of $theBranchName successfully.</font><br>";

			} 
			else 
			{
				$Error_Msg .= "Could not send e-mail to '$theUser', $theDeptName of $theBranchName.<br>";
			}
		}
	}

}
if ($r_month=='')  {
	if (Date('m') == 1)
		$r_month=12;
	else
		$r_month=Date('m')-1;
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
    <td align="right" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="100" valign=top bgcolor=black>
        <?php include 'menu.php'; ?></td>
          <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">

              <tr valign="top">
                <td valign="top">
				<?php $reminders='Y';?>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
						<td align="center">
							<!--<center>-->
							<table class="BoxCSS" border="0">
							<tr>
							<td>
							  <form name="userForm" method="post" action="reminders_dev.php">
							  <table width="350" border="0">
								<tr>
										<th colspan=2 bgcolor="#F2FAFB"><span class="pageheader">Email Reminder<br></span></th>
								</tr>
								<tr>
										<td colspan=2 bgcolor="#F2FAFB">Send an e-mail reminder to users to complete report for the following month.<br></td>
								</tr>
<tr>
                                                                                <td></td>
                                                                                <td>
                                                                                  <select name="r_month" size=1>
                                                                                        <option <? if ($r_month==1) print selected; ?> value="01">January</option>
                                                                                        <option <? if ($r_month==2) print selected; ?> value="02">February</option>
                                                                                        <option <? if ($r_month==3) print selected; ?> value="03">March</option>
                                                                                        <option <? if ($r_month==4) print selected; ?> value="04">April</option>
                                                                                        <option <? if ($r_month==5) print selected; ?> value="05">May</option>
                                                                                        <option <? if ($r_month==6) print selected; ?> value="06">June</option>
                                                                                        <option <? if ($r_month==7) print selected; ?> value="07">July</option>
                                                                                        <option <? if ($r_month==8) print selected; ?> value="08">August</option>
                                                                                        <option <? if ($r_month==9) print selected; ?> value="09">September</option>
                                                                                        <option <? if ($r_month==10) print selected; ?> value="10">October</option>
                                                                                        <option <? if ($r_month==11) print selected; ?> value="11">November</option>
                                                                                        <option <? if ($r_month==12) print selected; ?> value="12">December</option>
                                                                                  </select>
                                                                                </td>
                                                                        </tr>
									<? if ($user_dept=="All") { ?>
								<tr>
										<td colspan=2 bgcolor="#F2FAFB"><br>E-mail will be sent to users based on the following selection (To send reminder to all office bearers choose 'All'). <br></td>
								</tr>

  <tr>                                                                  
										<td>Report:</td>
										<td>
										<?//Get all Reports
										$query3 = "SELECT report_name, report_code FROM ami_reports where report_code!='AR' and report_code!='QR' and report_code!='TR' order by report_name";
										$result3 = @mysql_db_query($dbname,$query3);?>
										<select name="report_code">
										<option value="all" selected>All</option>
										<?php 
										  while ($row3 = mysql_fetch_array($result3)) { 
										
											$val = $row3['report_code'];
											$des = $row3['report_name'];
											if ($report_code == $val) {?>
												<option value=<? print "\"$val\"";  ?> selected><? print "$des";  ?></option>
                                                                                        <?} else {?>
                                                                                                <option value=<? print "\"$val\"";  ?>><? print "$des";  ?></option>
                                                                                        <? }
                                                                                  }?>
                                                                                 </select>
                                                                                </td>
                                                                        </tr>
                                                                        <? } else {?>
                                                                        	<input type="hidden" name="report_code" value="<? echo $user_dept ?>">
                                                                        <? }?>
                                                                        <? if ($user_level=="N") { ?>
                                                                         <tr>
										<td>Branch:</td>
                                                                                        <td>
                                                                                        <?
                                                                                         //Get the branches
											if ($user_id=="Admin")
												$query3 = "SELECT * FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%')  order by branch_name";
										 	else if ($branch_code==$nat_branch)
												 $query3 = "SELECT * FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%')  order by branch_name";
											else
                                                                                        	$query3 = "SELECT * FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code')  order by branch_name";
                                                                                         $result3 = @mysql_db_query($dbname,$query3);?>
                                                                                         <select name="branch">
                                                                                         <option value="all" selected>All</option>
                                                                                         <?php while ($row3 = mysql_fetch_array($result3)) { ?>
                                                                                          <?
                                                                                                        $val = $row3['branch_code'];
                                                                                                        $des = $row3['branch_name'];

                                                                                                if ($branch == $val) {?>
                                                                                                        <option value=<? print "\"$val\"";  ?> selected><? print "$des";  ?></option>
                                                                                                <?} else {?>
                                                                                                        <option value=<? print "\"$val\"";  ?>><? print "$des";  ?></option>
                                                                                                <? }
                                                                                        } ?>
                                                                                                </select>
                                                                                          </td>
                                                                                  </tr>
									<? } else {?>
                                                                                            <input type="hidden" name="branch" value="<? echo $branch_code?>">
                                                                                <? }?>
                                                                        <? if (($user_type=="P") || ($user_type=="GS") || ($user_level=="N")) { ?>
									<tr>
                                                                                <td>Send to:</td>
                                                                                <td>
                                                                                  <select name="r_outstanding" size=1>
                                                                                         <option value="0">All</option>
                                                                                        <option <? if ($rOutstanding=="1") print selected; ?> value="1">Those who have NOT completed their monthly report.</option>
										<? if (($user_type=="GS") && ($user_level=="N")) { ?>
                                                                                        <option <? if ($rOutstanding=="2") print selected; ?> value="2">Branch Presidents who have NOT verified their monthly reports.</option>
											<? if ($branch_code==$nat_branch)  { ?>
												<option <? if ($rOutstanding=="3") print selected; ?> value="3">National or Imarat Secretaries who have NOT Received their reports.</option>
											<? } else { ?>
												<option <? if ($rOutstanding=="3") print selected; ?> value="3">Imarat Secretaries who have NOT Received their reports.</option>
											<? } ?>
										<? } ?>
                                                                                 </select>
										  </td>
									  </tr>
                                                                        <? } ?>
                                                                        <? if ($user_level=="N") { ?>
										<? if ($user_type=="GS") { ?>
										<tr>
											<td>Level:</td>
											<td>
											  <select name="r_level" size=1>
												 <option value="all" selected>All</option>
										<? if ($branch_code==$nat_branch)  { ?>
												<option <? if ($rLevel=="L") print selected; ?> value="L">Local (excluding halqas)</option>
										<? } else  { ?>
												<option <? if ($rLevel=="L") print selected; ?> value="L">Local (including halqas)</option>
										<? } ?>
												<option <? if ($rLevel=="I") print selected; ?> value="I">Imarat Amila</option>
                                                                        <? if ($branch_code==$nat_branch) { ?>
												<option <? if ($rLevel=="N") print selected; ?> value="N">National Amila</option>
										<? } ?>
											 </select>
											  </td>
										  </tr>
										<? } ?>
									<tr><td colspan=2 >&nbsp;</td>
									</tr>
									<tr bgcolor="#DKBDBA"><td>Custom:</td>
                                                                        <td valign="middle"><input type="checkbox" <? echo ($rCustomMesg=="Yes")?checked:unchecked; ?> name="r_custom_mesg" value="Yes"/>&nbsp;(Send Following Message)</td>
									</tr>
									<tr bgcolor="#DKBDBA">
                                                                                <td>Subject:</td>
										 <td><input class="BoxCSS1" name="r_subj" <? echo $read_only; ?> size="50" maxlength="150" value="<? echo $rSubj; ?>">
										  </td>
									</tr>
									<tr bgcolor="#DKBDBA">
										 <td>Message:</td>
										<td><textarea class="BoxCSS1" name="r_mesg" <? echo $read_only; ?> cols="50" rows="4"><? echo $rMesg; ?></textarea>
										  </td>
									  </tr>

									<? } else {?>
									<input type="hidden" name="r_level" value="L">
									<? }?>

                                                                        <tr>
                                                                                <td colspan="2" align="center"><br><input type="submit" name="Submit" value="Send" class="ButtonCSS"></td>
                                                                        </tr>
                                                                        </form>



								<tr>
								  <td>&nbsp;<br></td>
								</tr>
								<? if ($Error_Msg != '') {?>
									<tr>
										<td colspan="2" align="center">
										<span class="pagetextAltColorSmall"><? echo $Error_Msg;?></span>
										</td>
									</tr>
								<? }?>
								<? if ($Error != '') {?>
									<tr>
										<td colspan="2" align="center">
										<span class="pagetextAltColorSmall"><? echo $Error;?></span>
										</td>
									</tr>
								<? }?>
							  </table>
								<script language="JavaScript">
									document.userForm.r_month.focus();
								</script>
							</td>
							</tr>
							</table>
							<!--</center>-->
						</td>
					  </tr>

					</table>
				</td>
                <td width="160" bgcolor="#F3F3F3">
                  <?php $reminders_php='Y'; include 'incl/rightbar.inc'; ?>
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
