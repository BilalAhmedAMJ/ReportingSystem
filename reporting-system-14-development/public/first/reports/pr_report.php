<?
include("../incl/dbcon.php");
include ("protected.php") ?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">
<body >
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" class="BoxCSS">
  <tr>
   <td valign="top" align="center">
		<table border="0">
			<tr>
				<td>
					<?php
					$date_posted="";
					$prepared_by="";
					//print "$branch_code $report_code $month $year<br>";
					//print "$branch $report_code $month $year<br>";
					//Check if report already exist
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
						  //print "rid:$query0<br>";
						 if ( ($user_id == 'Admin') ||
							(($user_level == 'N') && ($user_dept == 'All')) )
							$query0 .= "";
						   else if ($user_level == 'N')
							$query0 .= " AND report_code='$user_dept'"; 
						   else if (($user_level == 'L') && ($user_dept == 'All'))
							$query0 .= " AND branch_code='$branch_code'";
						   else
							$query0 .= " AND report_code='$user_dept' AND branch_code='$branch_code'";

						  $result0 = @mysql_db_query($dbname,$query0,$id_link);
						  if ($result0){
							$row0 = mysql_fetch_array($result0);
							if (!$row0) {
								print "Error: invalid report id!";
								exit();
							}
							$date_posted = $row0['date_posted'];
							$report_code = $row0['report_code'];
							$branch_code = $row0['branch_code'];
							$prepared_by = $row0['prepared_by'];
							$month = sprintf("%02d",$row0['month']);
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
					  $query = "SELECT * FROM ami_reports WHERE report_code = '$report_code'";
					  $result = @mysql_db_query($dbname,$query);
					  if ($result){
						 $row = mysql_fetch_array($result);
						  //Get the branch name
						  if (($user_level=="N") && (!$rid)) {
							$query1 = "SELECT branch_name FROM ami_branches WHERE branch_code = '$branch'";
						  } else {
							$query1 = "SELECT branch_name FROM ami_branches WHERE branch_code = '$branch_code'";
						  }
						  $result1 = @mysql_db_query($dbname,$query1);
						  if ($result1){
							$row1 = mysql_fetch_array($result1);
							$branch_name=$row1['branch_name'];
						  } else {
							$branch_name = $branch_code;
						  }?>
							<table width="100%" border="0">
							<tr>
							<td>
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
									<td colspan="2">
										<table border="0" width="100%">
											<tr>
<td align="left" >
            <img name="topleft" src="../images/topbar-left.png" alt="Ahmadiyya Muslim Jamaat" height="45" border="0">
    </td>
<td align="right">&nbsp;
    </td>

											</tr>
											<tr><td colspan="3"><hr width="100%"></td></tr>
											<tr>
												<td align="left" nowrap><b>Date:&nbsp;</b><? echo date("Y-m-d"); ?></td>
												<td align="right" colspan="2"><b>Login user:&nbsp;</b><? echo $user_name ?></td>
											</tr>
											<tr><td>&nbsp;</td></tr>
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
															echo date("Y-m-d");
														} else {
															echo $date_posted;
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
											<tr><td>&nbsp;</td></tr>
										</table>
									</td>
								</tr>
								<tr bgcolor="#000000">
								  <td colspan="2">
									  <table border="0" class="BoxCSS">
									  <tr><td>
										  <table border="0" width="582" bgcolor="#000000">
										  <? if ($report_code == "QR") {?>
											  <tr>
												<th colspan="6"><span class=""><font color="white"><? echo $row['report_name']; ?> - 
												<? if ($quarter == "1") {?>
													Q1 - Jan-Mar
												<?  } else if ($quarter == "2") {?>
													Q2 - Apr-Jun
												<?  } else if ($quarter == "3") {?>
													Q3 - Jul-Sep
												<?  } else if ($quarter == "4") {?>
													Q4 - Oct-Dec
												<?  }?>
												</font></span></th>
											  </tr>
										  <? } else if ($report_code == "AR") {?>
											  <tr>
												<th colspan="6"><span class=""><font color="white">Annual Report</font></span></th>
											  </tr>
										  <? } else {?>
											  <tr>
												<th colspan="6"><span class=""><font color="white">Department of <? echo $row['report_name']; ?> - Monthly Report</font></span></th>
											  </tr>
										  <? }?>
										  <tr >
											<td align="center"><span class=""><font color="white">Jama`at</font></span></td>
											<td width="200" bgcolor="white"><span class=""><font color="black"><? echo $branch_name; ?></font></span></td>
										  <? if ($report_code == "AR") {?>
											<td align="center" colspan=2><span class=""></span></td>
										  <? } else {?>
											<td align="center"><span class=""><font color="white">Month</font></span></td>
											<td width="80" bgcolor="white"><span class=""><font color="black"><? echo $months["$month"]; ?></font></span></td>
										  <? }?>
											<td align="center"><span class=""><font color="white">Year</font></span></td>
											<td width="40" bgcolor="white"><span class=""><font color="black"><? echo $year; ?></font></span></td>
										  </tr>
										  </table>
										</td><tr>
										</table>
								  </td>
								</tr>
								<tr>
								  <td colspan="2">&nbsp;</td>
								</tr>
								<?// Include additional General Secretary report
								if ($report_code == "GS") {?>
									<tr>
									  <td colspan="2">
											<? $gs="Y";
											include("pr_gs_report.php");
											?>
									  </td>
									</tr>
								<?  }?>
								<?// Include quarterly report
								if ($report_code == "QR") {?>
									<tr>
									  <td colspan="2">
											<? $qr="Y";
											include("pr_qr_report.php");
											?>
									  </td>
									</tr>
								<?  }
								if ($report_code == "TR") {?>
									<tr>
									  <td colspan="2">
											<? $tr="Y";
											include("tabshir_report.php");
											?>
									  </td>
									</tr>
								<?  }
								if ($report_code == "AR") {?>
									<tr>
									  <td colspan="2">
											<? $ar="Y";
											include("annual_report.php");
											?>
									  </td>
									</tr>
								<?  }?>
								<tr>
								  <td colspan="2">&nbsp;</td>
								</tr>
								<? if (($report_code != "QR") && ($report_code != "TR")
										&& ($report_code != 'AR')) {?>
								<tr>
								  <td valign="top">
									<table border="0" class="BoxCSS" width="100%">
										<tr bgcolor="#000000"><td align="center"><span class=""><font color="white">Report Highlights</font></span></td><tr>
										<tr bgcolor="#000000"><td align="center"><span class=""><font color="white">Base your report on the following activities</font></span></td><tr>
										<tr><td><? echo $row['report_desc'];?>&nbsp;</td><tr>
									</table>
								  </td>
								  <td valign="top">
									<table border="0">
									<tr><td bgcolor="#000000"><span class=""><font color="white">List all departmental activities & achievements for this month.</font></span><td></tr>
									<tr><td>
										<table border="0" class="BoxCSS1" width="100%">
										<tr><td><? echo $activities_this_month; ?>&nbsp;</td></tr>
										</table>
									</td></tr>
									<tr><td>&nbsp;</td></tr>
									<tr><td bgcolor="#000000"><span class=""><font color="white">Problems encountered in carrying out above activities.</font></span><td></tr>
									<tr><td>
										<table border="0" class="BoxCSS1" width="100%">
										<tr><td><? echo $problems; ?>&nbsp;</td></tr>
										</table>
									</td></tr>
									<tr><td>&nbsp;</td></tr>
									<tr><td bgcolor="#000000"><span class=""><font color="white">Any help required from National Markaz for your department.</font></span><td></tr>
									<tr><td>
										<table border="0" class="BoxCSS1" width="100%">
										<tr><td><? echo $help; ?>&nbsp;</td></tr>
										</table>
									</td></tr>
									<tr><td>&nbsp;</td></tr>
									<tr><td bgcolor="#000000"><span class=""><font color="white">Activities planned for next month.</font></span><td></tr>
									<tr><td>
										<table border="0" class="BoxCSS1" width="100%">
										<tr><td><? echo $activities_next_month; ?>&nbsp;</td></tr>
										</table>
									</td></tr>
									<tr><td>&nbsp;</td></tr>
									<tr><td bgcolor="#000000"><span class=""><font color="white">Comments:</font></span><td></tr>
									<tr><td>
										<table border="0" class="BoxCSS1" width="100%">
										<tr><td><? echo $comments; ?>&nbsp;</td></tr>
										</table>
									</td></tr>
								<? } else {?>
									<tr><td>
									<table border="0">
									<tr><td>&nbsp;</td></tr>
								<? }?>
									<tr><td>&nbsp;</td></tr>
									<? if ($remarks_national_sec !="") {?>
									<tr><td bgcolor="#8b4513"><span class=""><font color="white">Comments by Department National Secretary:</font></span><td></tr>
									<tr><td>
										<table border="0" class="BoxCSS1" width="100%">
										<tr><td><? echo $remarks_national_sec; ?>&nbsp;</td></tr>
										</table>
									</td></tr>
									<tr><td>&nbsp;</td></tr>
									<?}?>
									<? if ($remarks_amir !="") {?>
									<tr><td bgcolor="#6b8e23"><span class=""><font color="white">Comments by National Markaz:</font></span><td></tr>
									<tr><td>
										<table border="0" class="BoxCSS1" width="100%">
										<tr><td><? echo $remarks_amir; ?>&nbsp;</td></tr>
										</table>
									</td></tr>
									<tr><td>&nbsp;</td></tr>
									<? }?>
									<tr ><td align="center">
										<input type="button" class="noPrint ButtonCSS" name="Print" value="Print" onclick="javascript:window.print();">
										&nbsp;&nbsp;&nbsp;<input type="button" name="Close" value="Close" class="noPrint ButtonCSS" onclick="javascript:window.close();">
									</td></tr>
									</table>
								  </td>
								</tr>
							  </table>
							</form>
							</td>
							</tr>
							</table>
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
  </tr>
<script language="JavaScript">
	javascript:window.menubar=0;
</script>
</table>
</body>
</html>
