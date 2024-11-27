<? include ("protected.php");
 include("../incl/dbcon.php");
 ?>
<?php
//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                                                                                                         // always modified
//header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
//header("Cache-Control: post-check=0, pre-check=0", false);
//header("Pragma: no-cache");                          // HTTP/1.0
$Error="";
$Error_Msg="";
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

	if ($rOutstanding=="2")
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
			$query .= " and a.user_level='N' and a.branch_code!='CA'";
		else if ($rLevel == 'N') 
			$query .= " and a.user_level='N' and a.branch_code='CA'";
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
	else if ($rOutstanding>"0")
	{
		$exclude_users = "";
		$Year = Date('Y');
		if ($rMonth>Date('m'))
			$Year = $Year-1;
		$query = "select a.user_id from ami_all_reports b left join ami_users a on a.user_type=b.report_code and a.branch_code=b.branch_code where b.year='$Year' and b.month='$rMonth' ";
		if ($rOutstanding=="1")	// dissregards Branch, Dept and Level
			$query .= " and b.status!='0'";
		else if ($rOutstanding=="2")
			$query .= " and b.status!=3 and b.status>1";

		if (($rBranch != '') && ($rBranch != 'all')) 
			$query .= " and b.branch_code = '$rBranch'";
		if ($rLevel == 'I') 
			$query .= " and a.user_level='N' and a.branch_code!='CA'";
		else if ($rLevel == 'N') 
			$query .= " and a.user_level='N' and a.branch_code='CA'";
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
	$query = "SELECT branch_code, region_code FROM ami_branches where status!=0"; 
	$result = @mysql_db_query($dbname,$query);
	while ($row = mysql_fetch_array($result)) {
		$branchArray[$row['branch_code']] = $row['region_code']; 
		//print $row['branch_code'].' '.$row['region_code']; 
	}

	$gmessage = "\n\nAssalam o alaikum wa Rahmatullah";
	if ($rOutstanding=="2")	// Presidents only
	{
		$query = "select distinct a.* from ami_users a where a.user_type='P' and a.status='1' and a.user_email!='' ";
		$query .= " and a.branch_code in (". $presidentofbranches.") ";
		$gmessage .= "\n\nYou are requested to verify the monthly reports for the month of $rMonthName.";
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
			$query .= " and a.user_level='N' and a.branch_code!='CA'";
		else if ($rLevel == 'N') 
			$query .= " and a.user_level='N' and a.branch_code='CA'";
		else if (($rLevel!= '') && ($rLevel != 'all'))
			$query .= " and a.user_level='$rLevel'";
		if (($rDept!= '') && ($rDept!= 'all'))
			$query .= " and a.user_type='$rDept'";
		$query .= " order by b.report_code asc";

		$gmessage .= "\n\nYou are requested to submit your monthly report for the month of $rMonthName. Please visit http://report.ahmadiyya.ca to submit your report.";
		if ($user_level!='N')
			$gmessage .= "\nOnce you have completed the report, kindly request your President to verify the reports.";
	}
	$gmessage .= "\n\nWassalam";
	if ($user_level=='N')
		$gmessage .= "\nOffice of National General Secretary";
	else
		$gmessage .= "\nGeneral Secretary - $rBranch";
	$sndr = ($user_email!='')?$user_email:"reports@ahmadiyya.ca";

	$subj = "AMJ Report Due";
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

		// ignore if branch_code != theBranch or branch_code != theRegion
		if (($user_level == 'N') && ($branch_code == "CA"))
		{
			if (($theBranch != $branch_code) && ($theRegion != $branch_code) && (substr($theRegion,0,1)!="R"))
				continue;
		} 
		else if (($theBranch != $branch_code) && ($theRegion != $branch_code))
			continue;
//		print $theBranch.'['.$theRegion.']';

		if ($rCustomMesg == "Yes") { }
		else if ($theType=='GS')
			$message = "Dear General Secretary [$theBranch] - [User ID: $theUser],".$gmessage;
		else
			$message = "Dear Secretary $theDeptName [$theBranch],".$gmessage;

		if ( (($theType!='P') || ($rOutstanding=='2')) && 
			($theEmail!="") && ($theExpiry_date!="")) {	// user found
			$pdt = date("Y-m-d");
			if ($theExpiry_date<$pdt){	// account expired
				$Error_Msg .= "'$theUser' account has been expired (Re-registration required)<br>";
			} 
			else if (($theUser) && ($theEmail))
			{
				mail($theEmail, $subj, $message, "From:$sndr");
				$Error_Msg .= "Reminder sent to '$theUser' successfully.<br>";	// change password enforced
			} 
			else 
			{
				$Error_Msg .= "Could not send e-mail to '$theUser'.<br>";
			}
		}
	}
//	print "'$Error'<br><a href='reminders.php'>Click here to return</a>";
//	exit();	
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
							  <form name="userForm" method="post" action="reminders.php">
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
										 	else if ($branch_code=="CA")
												 $query3 = "SELECT * FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%')  order by branch_name";
											else
                                                                                        	$query3 = "SELECT * FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code')  order by branch_name";
                                                                                        	//$query3 = "SELECT * FROM ami_branches where status=1 and branch_code!='CA' order by branch_name";
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
                                                                                        <option selected value="1">Those who have NOT completed their monthly report.</option>
                                                                                        <!--option <? if ($rOutstanding=="1") print selected; ?> value="1">Those who have NOT completed their monthly report.</option-->
										<? if (($user_type=="GS") && ($user_level=="N")) { ?>
                                                                                        <option <? if ($rOutstanding=="2") print selected; ?> value="2">Branch Presidents who have NOT verified their monthly reports.</option>
										<? } ?>
                                                                                 </select>
										  </td>
									  </tr>
                                                                        <? } ?>
	<? //if (($branch_code=="CA") && ($user_type=="GS") && ($user_level=="N")) { ?>
                                                                        <? if ($user_level=="N") { ?>
										<? if ($user_type=="GS") { ?>
										<tr>
											<td>Level:</td>
											<td>
											  <select name="r_level" size=1>
												 <option value="all" selected>All</option>
										<? if ($branch_code=="CA")  { ?>
                                                                                                <option <? if ($rLevel=="L") print selected; ?> value="L">Local (excluding halqas)</option>
                                                                                <? } else  { ?>
                                                                                                <option <? if ($rLevel=="L") print selected; ?> value="L">Local (including halqas)</option>
                                                                                <? } ?>

												<option <? if ($rLevel=="I") print selected; ?> value="I">Imarat Amila</option>
                                                                        <? if ($branch_code=="CA") { ?>
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