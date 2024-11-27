<?
include("../incl/dbcon.php");
include ("protected.php") ;

$today = getdate();
$current_month = $today['mon'];
$current_year = $today['year'];
?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">
<?php include '../incl/headscript.inc'; ?>

<script language="JavaScript" type="text/JavaScript">
<!--
function getTotal() {
	if (isNaN(parseInt(document.report.gents.value))){
		document.report.gents.value = 0;
	}
	if (isNaN(parseInt(document.report.ladies.value))){
		document.report.ladies.value = 0;
	}
	if (isNaN(parseInt(document.report.children.value))){
		document.report.children.value = 0;
	}
	if (!isNaN(document.report.gents.value)){
		if (!isNaN(document.report.ladies.value)){
			if (!isNaN(document.report.children.value)){
				document.report.gbody_total_attendance.value = parseInt(document.report.gents.value) + parseInt(document.report.ladies.value) + parseInt(document.report.children.value);
			} else {
				document.report.gbody_total_attendance.value = parseInt(document.report.gents.value) + parseInt(document.report.ladies.value);
			}
		} else {
			document.report.gbody_total_attendance.value = parseInt(document.report.gents.value);
		}
	} else {
		if (!isNaN(document.report.ladies.value)){
			if (!isNaN(document.report.children.value)){
				document.report.gbody_total_attendance.value = parseInt(document.report.ladies.value) + parseInt(document.report.children.value);
			} else {
				document.report.gbody_total_attendance.value = parseInt(document.report.ladies.value);
			}
		} else {
			if (!isNaN(document.report.children.value)){
				document.report.gbody_total_attendance.value = parseInt(document.report.children.value);
			}
		}
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
                <td valign="top" align="center">
					<table border="0">
						<tr>
							<td>
								<?php
								$date_posted="";
								$prepared_by="";
								//print "$branch_code|$branch|$report_code $month $year<br>";
								//print "$branch $report_code $month $year<br>";
								//Check if report already exist
								if ($report_code=="QR") {
									if ($user_level=="N") {
									  $query0 = "SELECT month FROM ami_quarterly_reports WHERE branch_code = '$branch' and quarter= '$quarter' and year = '$year'";
									} else {
									  $query0 = "SELECT month FROM ami_quarterly_reports WHERE branch_code = '$branch_code' and quarter= '$quarter' and year = '$year'";
									}
									//print "$query0<br>";
									$result0 = @mysql_db_query($dbname,$query0,$id_link);
									if ($result0){
										$row0 = mysql_fetch_array($result0);
										$qmonth = $row0['month'];
									}
									if ($qmonth!="") {
										$month = $qmonth;
										//print "$qmonth<br>";
									}
									//if report already there then get quarter
									if ($rid!="") {
									  $qrq0 = "SELECT quarter FROM ami_quarterly_reports WHERE branch_code = '$branch' and month = '$month' and year = '$year'";
									} else {
									  $qrq0 = "SELECT quarter FROM ami_quarterly_reports WHERE branch_code = '$branch_code' and month = '$month' and year = '$year'";
									}
							
									//print "qrquery0:$gsquery0<br>";
									$qresult0 = @mysql_db_query($dbname,$qrq0,$id_link);
									if ($qresult0){
										$qrow0 = mysql_fetch_array($qresult0);
										$quarter = $qrow0['quarter'];
									}

								}
								if ($report_code=="TR") {
									if ($user_level=="N") {
									  $query0 = "SELECT month FROM ami_tabshir_reports WHERE branch_code = '$branch' and quarter= '$quarter' and year = '$year'";
									} else {
									  $query0 = "SELECT month FROM ami_tabshir_reports WHERE branch_code = '$branch_code' and quarter= '$quarter' and year = '$year'";
									}
									//print "$query0<br>";
									$result0 = @mysql_db_query($dbname,$query0,$id_link);
									if ($result0){
										$row0 = mysql_fetch_array($result0);
										$qmonth = $row0['month'];
									}
									if ($qmonth!="") {
										$month = $qmonth;
										//print "$qmonth<br>";
									}
								}
								if ($report_code=="AR") {
									if ($user_level=="N") {
									  $query0 = "SELECT month FROM ami_annual_reports WHERE branch_code = '$branch' and year = '$year'";
									} else {
									  $query0 = "SELECT month FROM ami_annual_reports WHERE branch_code = '$branch_code' and year = '$year'";
									}
									//print "$query0<br>";
									$result0 = @mysql_db_query($dbname,$query0,$id_link);
									if ($result0){
										$row0 = mysql_fetch_array($result0);
										$qmonth = $row0['month'];
									}
									if ($qmonth!="") {
										$month = $qmonth;
										//print "$qmonth<br>";
									} else {
										$month=12;
										//print "$qmonth<br>";
									}
								}
								if (($report_code) && ($month) && ($year)) {
									if ($user_level=="N") {
									  $query0 = "SELECT rid FROM ami_all_reports WHERE branch_code = '$branch' and report_code= '$report_code' and month = '$month' and year = '$year'";
									} else {
									  $query0 = "SELECT rid FROM ami_all_reports WHERE branch_code = '$branch_code' and report_code= '$report_code' and month = '$month' and year = '$year'";
									}

									//print "$query0<br>";
									$result0 = @mysql_db_query($dbname,$query0,$id_link);
									if ($result0){
										$row0 = mysql_fetch_array($result0);
										$rid = $row0['rid'];
									}
								}
								//print "rid=$rid<br>";
								//New report or existing report
								if (($report_code) || ($rid)) {
								  if ($rid){
									  $query0 = "SELECT * FROM ami_all_reports WHERE rid = '$rid'";
									   if ( ($user_id == 'Admin') ||
									   	(($user_level == 'N') && ($user_dept == 'All')) )
										$query0 .= "";	
									   else if ($user_level == 'N')
										$query0 .= " AND report_code='$user_dept'";
									   else if (($user_level == 'L') && ($user_dept == 'All'))
										$query0 .= " AND branch_code='$branch_code'";
									   else
										$query0 .= " AND report_code='$user_dept' AND branch_code='$branch_code'";

									  //print "rid:$query0<br>";
									  $result0 = @mysql_db_query($dbname,$query0,$id_link);
									  if ($result0){
										$row0 = mysql_fetch_array($result0);
										if ( !$row0 ) {
											print "Error: invalid report id!";
											exit();
										}
										$date_posted = $row0['date_posted'];
										$report_code = $row0['report_code'];
										$branch_code1 = $row0['branch_code'];
										$prepared_by = $row0['prepared_by'];
										$month = $row0['month'];
										$month1 = sprintf("%02d",$row0['month']);
										$year = $row0['year'];
										$activities_this_month = $row0['activities_this_month'];
										$help = $row0['help'];
										$problems = $row0['problems'];
										$activities_next_month = $row0['activities_next_month'];
										$comments = $row0['comments'];
										$remarks_national_sec = $row0['remarks_national_sec'];
										$remarks_amir = $row0['remarks_amir'];
										$attachment = $row0['attachment'];
										$status = $row0['status'];
									  } else {
									  	print "Error: invalid report id!";
									  	exit();
									  }
								  }
								  else if (  (($year == $current_year) && ($month>$current_month))
									  	|| ($year > $current_year) )			// new report
								  {
									print "<center><font color=red>Error: Invalid reporting period specified (Must be current or previous month)</font><br> 
									<form name='new' method='post' action='reports.php'> 
                                                                        <input type='submit' name='new_report' value='Back' class='ButtonCSS'> 
									</form></center>";

									exit();
								  }

							
								  $query = "SELECT * FROM ami_reports WHERE report_code = '$report_code'";
								  $result = @mysql_db_query($dbname,$query);
								  if ($result){
									 $row = mysql_fetch_array($result);
									  //Get the branch name
									  if (($user_level=="N") && (!$rid)) {
									  	$query1 = "SELECT branch_name,region_code FROM ami_branches WHERE branch_code = '$branch'";
									  } else if ($user_level=="N") {
									  	$query1 = "SELECT branch_name,region_code FROM ami_branches WHERE branch_code = '$branch_code1'";
									  } else {
									  	$query1 = "SELECT branch_name,region_code FROM ami_branches WHERE branch_code = '$branch_code'";
									  }
									  $result1 = @mysql_db_query($dbname,$query1);
									  if ($result1){
									  	$row1 = mysql_fetch_array($result1);
									  	$branch_name=$row1['branch_name'];
									  	$region_code=$row1['region_code'];
									  } else {
									  	$branch_name = $branch_code;
									  }?>
										<table width="100%" border="0">
										<tr>
										<td>
										<? if ($report_code == "QR") {?>
											<form name="report" enctype="multipart/form-data" method="post" action="save_quarterly_report.php">
										<? } else if ($report_code == "TR") {?>
											<form name="report" enctype="multipart/form-data" method="post" action="save_tabshir_report.php">
										<? } else if ($report_code == "AR") {?>
											<form name="report" enctype="multipart/form-data" method="post" action="save_annual_report.php">
										<? } else {?>
											<form name="report" enctype="multipart/form-data" method="post" action="save_report.php">
											<!--<form name="report" method="post" action="save_report.php">-->
										<? }?>
										  <table width="580" border="0">
											 <?
											  //Get the user name
											  $query2 = "SELECT * FROM ami_users WHERE user_id = '$user_id'";
											  $result2 = @mysql_db_query($dbname,$query2);
											  if ($result2){
												$row2 = mysql_fetch_array($result2);
												$user_name=$row2['user_name'];
											  } else {
												$user_name = $user_id;
											  }
											  //Get the verify user name
											  if ($user_name=="") {
											  	if ($row2['user_type']=="P") {
											  			$user_name='President';
											  	} else {
													  $query3 = "SELECT * FROM ami_reports WHERE report_code = '" . $row2['user_type'] . "'";
													  $result3 = @mysql_db_query($dbname,$query3);
													  if ($result3){
														$row3 = mysql_fetch_array($result3);
														$user_name=$row3['report_name'];
														if (($row2['user_type']!="SM") && ($row2['user_type']!="GS"))
															$user_name= 'Sec. ' . $user_name;
														if ($row2['user_level']=="N") 
															$user_name= 'National ' . $user_name;
													  } else {
														$user_name = $user_id;
													  }
												}
											  }
											  ?>
											<tr>
												<td >
													<table border="0" width="100%" >
														<tr>
															<td align="left"><b>Prepared by:&nbsp;</b>
																<? if ($prepared_by=="") {
																		echo $user_name;
																	} else {
																		echo $prepared_by;
																	}?>
															</td>
															<td align="center"><b>Date posted:&nbsp;</b>
																<? if ($date_posted=="") {
																		echo date("d-M-Y");
																	} else {
																		echo date("d-M-Y",strtotime($date_posted));
																	}?>
															</td>
															<td align="right">
															<? if ($rid) {?>
																<b>Status:&nbsp;</b>
																<? if ($status =='0') {?>
																	Draft
																<? } else if ($status =='1') {?>
																	Complete
																<? } else if ($status =='2') {?>
																	Verified
																<?   if (($user_level=="L") && ($user_type!="P")) {
																		$read_only = "Readonly";?>
																		&nbsp;<font color="red">(Readonly)</font>
																	<? }
																   } else if ($status =='3') {?>
																	Received
																<?   if ($user_level=="L") {
																		$read_only = "Readonly";?>
																		&nbsp;<font color="red">(Readonly)</font>
																	<? }
																   } else {?>
																	Unknown
																<? }?>

															<? } else {?>
																<b>Status:&nbsp;</b>Draft
															<? }?>
															</td>
														</tr>
													<? if (($user_level=="N") && ($branch_code==$nat_branch) && ($branch_code1==$nat_branch)) { ?>


<?
					$query0 = "SELECT count(*) as verified FROM ami_all_reports a, ami_branches b WHERE a.branch_code=b.branch_code and a.month=$month and a.year=$year and a.report_code='$report_code' and a.status>=2 and (b.region_code='$nat_branch' or b.region_code like 'R%')";
					$result0 = @mysql_db_query($dbname,$query0,$id_link);
					if ($result0){
						$row0 = mysql_fetch_array($result0);
						$verified_cnt = $row0['verified'];
					}
					$query0 = "SELECT count(*) as received FROM `ami_all_reports` a, ami_branches b WHERE a.branch_code=b.branch_code and a.month=$month and a.year=$year and a.report_code='$report_code' and a.status>2 and (b.region_code='$nat_branch' or b.region_code like 'R%')";
					$result0 = @mysql_db_query($dbname,$query0,$id_link);
					if ($result0){
						$row0 = mysql_fetch_array($result0);
						$received_cnt = $row0['received'];
					}


?>
														<tr>

															<td colspan="3" align="left" nowrap><b>Number of reports Verified:&nbsp;</b><? echo $verified_cnt;  ?></td>

														</tr>
														<tr>
															<td colspan="3" align="left" nowrap><b>Number of reports reviewed by National Secretary:&nbsp;</b><? echo $received_cnt;  ?></td>
														</tr>
													<? } ?>
													<!--/table>
												</td>
											</tr-->
											<?if ($month1=="") $month1=$month;?>
											<!--tr >
											  <td >
												  <table border="0" class="newstylemaintable"-->
												  <tr><td colspan=3>
													  <table border="0" width="100%" class=newstyletable cellspacing=6>
													  <? if ($report_code == "QR") {?>
														  <tr>
															<th colspan="6"><span class=""><? echo $row['report_name']; ?> - 
															<? if ($quarter == "1") {?>
																Q1 - Jan-Mar
															<?  } else if ($quarter == "2") {?>
																Q2 - Apr-Jun
															<?  } else if ($quarter == "3") {?>
																Q3 - Jul-Sep
															<?  } else if ($quarter == "4") {?>
																Q4 - Oct-Dec
															<?  }?>
															</span></th>
														  </tr>
													  <? } else if ($report_code == "AR") {?>
														  <tr>
															<th colspan="6"><span class="">Annual Report</span></th>
														  </tr>
													  <? } else {?>
														  <tr>
															<th colspan="6"><span class="">Department of <? echo $row['report_name']; ?> - Monthly Report</span></th>
														  </tr>
													  <? }?>
													  <tr >
														<td align="center"><span class="">Jama`at</span></td>
														<td width="200" bgcolor=white><span class=""><? echo $branch_name; ?></span></td>
													  <? if ($report_code != "AR") {?>
	 													<td align="center"><span class="">Month</span></td>
														<td width="80" bgcolor="white"><span class=""><? echo $months["$month1"]; ?>
													   <? } else {?>
	 													<td colspan=2 align="center"><span class=""></span></td>
													   <? } ?>
														<td align="center"><span class="">Year</span></td>
														<td width="40" bgcolor="white"><span class=""><? echo $year; ?></span></td>
													  </tr>
													  </table>
													</td><tr>
													</table>
											  </td>
											</tr>
											<?// Include additional General Secretary report
											if ($report_code == "GS") {?>
												<tr>
												  <td >
														<? $gs="Y";
														include("gs_report.php");
														?>
												  </td>
												</tr>
											<?  }
											if ($report_code == "QR") {?>
												<tr>
												  <td >
														<? $qr="Y";
														include("quarterly_report.php");
														?>
												  </td>
												</tr>
											<?  }
											if ($report_code == "TR") {?>
												<tr>
												  <td >
														<? $tr="Y";
														include("tabshir_report.php");
														?>
												  </td>
												</tr>
											<?  }
											if ($report_code == "AR") {?>
												<tr>
												  <td >
														<? $ar="Y";
														include("annual_report.php");
														?>
												  </td>
												</tr>
											<?  }?>
											<tr>
											  <td  >&nbsp;</td>
											</tr>
											<tr>
											  <? if (($report_code != "QR") && ($report_code != "TR") && 
													($report_code != "AR")) {?>
<!-- //////// REPORT HIGHLIGTS ////
											  <td valign="top">
											  	<table border="0" class="BoxCSS">
											  		<tr bgcolor="#003366"><td align="center"><span class="pageheader"><font color="white">Report Highlights</font></span></td><tr>
											  		<tr bgcolor="#003366"><td align="center"><span class="pageheader"><font color="white">Base your report on the following activities</font></span></td><tr>
											  		<tr><td><? echo $row['report_desc'];?></td><tr>
											  	</table>
											  </td>
 //////////////////////////// --> 

<?
$report_help = $row['report_desc'];		// USED BY TASKBAR on right side 

if ( (substr($region_code,0,1)=='R') || ($region_code == "") || ($region_code == $nat_branch))
	$level_str = "National";
else
	$level_str = "Imarat";

?>
											  <td valign="top" >
											  	<table border="0" width=100%>
											  	<tr><td class="newstylefieldheader">List all departmental activities & achievements for this month. 
                                                                          <? if (($read_only=="") && ($allow_attachments=="1")) {?>
											(Attach details)
									<? } ?>
												<td></tr>
											  	<tr><td><textarea class="newstyletxtarea" name="activities_this_month" <? echo $read_only; ?> cols="70" rows="10"><? echo $activities_this_month; ?></textarea></td></tr>
												<? if (($read_only=="") && ($allow_attachments=="1")) {?>
												<tr><td class=newstylefieldheader>
													Attachment:&nbsp;
													<input type="file" name="attachment" class="boxCSS1">
												</td></tr>
												<? }?>
												<tr><td>
													<? if (($rid) && ($attachment)) {?>
														<a href="attachments/<? print "$attachment\"";?> target="_blank"><? print "$attachment";?></a>
													<? } ?> &nbsp; 
												</td></tr>
												<tr><td class="newstylefieldheader">Problems encountered in carrying out above activities.<td></tr>
											  	<tr><td><textarea class="newstyletxtarea" name="problems" <? echo $read_only; ?> cols="70" rows="6"><? echo $problems; ?></textarea></td></tr>
												<tr><td>&nbsp;</td></tr>
												<tr><td class="newstylefieldheader">Any help required from <?echo $level_str; ?><!--National--> Markaz for your department.<td></tr>
											  	<tr><td><textarea class="newstyletxtarea" name="help" <? echo $read_only; ?> cols="70" rows="6"><? echo $help; ?></textarea></td></tr>
												<tr><td>&nbsp;</td></tr>
												<tr><td class="newstylefieldheader">Activities planned for next month.<td></tr>
											  	<tr><td><textarea class="newstyletxtarea" name="activities_next_month" <? echo $read_only; ?> cols="70" rows="6"><? echo $activities_next_month; ?></textarea></td></tr>
											  	<tr><td>&nbsp;</td></tr>
											  	<tr><td class="newstylefieldheader">Comments:<td></tr>
											  	<tr><td><textarea class="newstyletxtarea" name="comments" <? echo $read_only; ?> cols="70" rows="6"><? echo $comments; ?></textarea></td></tr>
											<? } else {?>
												<tr><td>
												<table border="0">
												<tr><td>&nbsp;</td></tr>
											<? }?>
												<tr><td class=newstylenatfieldheader>Comments by Department <?echo $level_str; ?> Secretary:<td></tr>
												<? 
													$report_branch = $branch_code1;
													$user_branch = $branch_code;
													$report_region= $region_code;
												?>
												<? if (($user_id!="Admin") && (($user_level=="L") || ($user_branch==$report_branch))) { ?>
													<tr><td><textarea class="newstyletxtareaRO" readonly name="remarks_national_sec" cols="70" rows="6"><? echo $remarks_national_sec; ?></textarea></td></tr>
												<? } else {?>
													<tr><td><textarea class="newstyletxtarea" name="remarks_national_sec" cols="70" rows="6"><? echo $remarks_national_sec; ?></textarea></td></tr>
												<? }?>
												<tr><td>&nbsp;</td></tr>
												<tr><td class="newstylenatfieldheader">Comments by <? echo $level_str; ?> Markaz:<td></tr>

												<? 	// Either a National GS/P or Imarat GS/P on it's halqas report
													if ( ($user_level=="N") && ($user_dept=="All") &&
													( ($user_branch==$nat_branch) || ($user_branch==$report_region) ) ) { ?>
                                                                                                        <tr><td><textarea class="newstyletxtarea" name="remarks_amir" cols="70" rows="6"><? echo $remarks_amir; ?></textarea></td></tr>
                                                                                                <? } else {?>
                                                                                                        <tr><td><textarea class="newstyletxtareaRO" readonly name="remarks_amir" cols="70" rows="6"><? echo $remarks_amir; ?></textarea></td></tr>
                                                                                                <? }?>


											  	</table>
											  </td>
											</tr>
											<tr><td  align="center">&nbsp;</td></tr>
											<tr>
											  <td align="center">

											  	<? if ($read_only =="") {?>
<!--
											  	<tr><td class="newstylenatfieldheader">
-->Status:&nbsp;&nbsp;&nbsp;&nbsp;
											  		<select name="status">
											  			<? if (($user_level=="L") &&($user_type!="P")) {?>
											  					<? if ($status =='0') {?>
																	<option value="0" selected>Draft</option>
																	<option value="1">Complete</option>
																<?} else if ($status=='1') { ?>
																	<option value="0">Draft</option>
																	<option value="1" selected>Complete</option>
																<?} else {?>
																	<option value="0">Draft</option>
																	<option value="1">Complete</option>
																<? }?>
											  			<? } else if (($user_level=="L") &&($user_type=="P")) {?>
											  				<? if ($status =='0') {?>
																<option value="0" selected>Draft</option>
																<option value="1">Complete</option>
																<option value="2">Verified</option>
															<?} else if ($status=='1') { ?>
																<option value="0">Draft</option>
																<option value="1" selected>Complete</option>
																<option value="2">Verified</option>
															<?} else if ($status=='2') { ?>
																<option value="0">Draft</option>
																<option value="1">Complete</option>
																<option value="2" selected>Verified</option>
															<?} else {?>
																<option value="0">Draft</option>
																<option value="1">Complete</option>
																<option value="2">Verified</option>
															<? }?>
											  			<? } else if ($user_level=="N") {?>
											  				<? if ($status =="0") {  ?>
																<option value="0" selected>Draft</option>
																<option value="1">Complete</option>
																<? if (($user_type=="P") && ($branch_code == $branch_code1))  {// presidents own branch ?>
																<option value="2">Verified</option>
																<? } else if ($branch_code != $branch_code1)  {// my own branch?>
																<option value="3">Received</option>
																<? } ?>	
															<?} else if ($status=="1") { ?>
																<option value="0">Draft</option>
																<option value="1" selected>Complete</option>
																<? if (($user_type=="P") && ($branch_code == $branch_code1))  {// presidents own branch ?>
																<option value="2">Verified</option>
																<? } else if ($branch_code != $branch_code1)  {// my own branch?>
																<option value="3">Received</option>
																<? } ?>	
															<?} else if ($status=="2") { ?>
																<option value="0">Draft</option>
																<option value="1">Complete</option>
																<option value="2" selected>Verified</option>
																<?  if ($branch_code != $branch_code1)  {// my own branch?>
																<option value="3">Received</option>
																<? } ?>	
															<?} else if ($status=="3") { ?>
																<option value="0">Draft</option>
																<option value="1">Complete</option>
																<? if (($user_type=="P") && ($branch_code == $branch_code1))  {// presidents own branch ?>
																<option value="2">Verified</option>
																<? } else if ($branch_code != $branch_code1)  {// my own branch?>
																<option value="3" selected>Received</option>
																<? } ?>	
															<?} else {?>
																<option value="0">Draft</option>
																<option value="1">Complete</option>
																<? if (($user_type=="P") && ($branch_code == $branch_code1))  {// presidents own branch ?>
																<option value="2">Verified</option>
																<? } else if ($branch_code != $branch_code1)  {// my own branch?>
																<option value="3">Received</option>
																<? } ?>	
															<? } ?>
											  			<? }?>
											  		</select>
											  	<!--/td></tr-->
											  	<? } //read only ?>
												  <input type="hidden" name="report_code" value=<? print "\"$report_code\""; ?>>
												  <input type="hidden" name="month" value=<? print "\"$month\""; ?>>
												  <input type="hidden" name="year" value=<? print "\"$year\""; ?>>
												  <input type="hidden" name="quarter" value=<? print "\"$quarter\""; ?>>
												  <input type="hidden" name="user_name" value=<? print "\"$user_name\""; ?>>
												  <? if ($rid) {?>
													  <input type="hidden" name="rid" value=<? print "\"$rid\""; ?>>
													  <input type="hidden" name="file_nm" value=<? print "\"$attachment\""; ?>>
												  <? }?>
												  <? if (($user_level=="N") && (!$rid)) {?>
												  	<input type="hidden" name="branch" value=<? print "\"$branch\""; ?>>
												  <? } else if ($user_level=="N") {?>
												  	<input type="hidden" name="branch" value=<? print "\"$branch_code1\""; ?>>
												  <? } else {?>
												  	<input type="hidden" name="branch" value=<? print "\"$branch_code\""; ?>>
												  <? }
												  if ($read_only=="") {
												  ?>
												  	<input type="submit" name="Submit" value="Submit" class="newstylesubbutton">
												  	<!--<input type="reset" name="Reset" value="Reset" class="ButtonCSS">-->
												  <? }?>

											  </td>
											</tr>
										  </table>
										</form>
										</td>
										</tr>
										</table>
										<script language="JavaScript">
											<? if ($report_code == "QR") { ?>
											document.report.number_of_amila_meetings.focus();
											<? } else if ($report_code == "TR") { ?>
											document.report.daeen_this_month.focus();
											<? } else if ($report_code == "AR") { ?>
											document.report.total_families.focus();
											<? } else {?>
											document.report.activities_this_month.focus();
											<? }?>
										</script>
								<?	}
									else { ?>
										<table width="100%" border="0" class="BoxCSS">
										<tr>
										<td>
										  <table width="580" border="0">
											<tr><td>The selected report is not available at this time. Please try later.</td></tr>
										  </table>
										 </td>
										 </tr>
										 </table>
								<?	}
								}
								?>
							</td>
						</tr>
					</table>

				</td>
                <td width="160" >
                  <?php $reports='Y'; include 'incl/rightbar.inc'; ?>
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
