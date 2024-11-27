<? include ("protected.php") ?>
<? include ("../incl/dbcon.php");
/*if  ($user_level != "N") {
	header("Location: list_reports.php");
}*/
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
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
          <td width="100" valign=top >
        <?php include 'menu.php'; ?></td>
          <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">

              <tr valign="top">
                <td valign="top" align="center">
				 <table class="newstyletable">
					<tr >
					<th colspan="3" >New Office Bearer Requests</th>
					</tr>
					<tr><td>
					<table >
				 	<?
					///////////////////////////////////////////////////////////////
					// retrieve criteria from session if not supplied by form post
					session_start();
					$myary = $_SESSION['list_new_user_requests'];
					
					if ($status=="")
					{
						$status = $myary['status'];
					}
					if ($do_show_all=="")
					{
						$do_show_all = $myary['do_show_all'];
					}
					if ($user_dept =="")
					{
						$user_dept = $myary['user_dept'];
					}
					if ($report_code1 =="")
					{
						$report_code1 = $myary['report_code1'];
					}
					if ($branch=="")
					{
						$branch = $myary['branch'];
					}
					if ($adv_filter=="")
					{
						$adv_filter= $myary['adv_filter'];
					}
					if ($adv_filter=="none")
					{
						$adv_expression="";
					}
					else if ($adv_expression=="")
					{
						$adv_expression= $myary['adv_expression'];
					}

					
					$_SESSION['list_new_user_requests'] = array ("status" => $status,
									"user_dept" => $user_dept,
									"do_show_all" => $do_show_all,
									"report_code1" => $report_code1,
									"adv_filter" => $adv_filter,
									"adv_expression" => $adv_expression,
									"branch" => $branch);
					///////////////////////////////////////////////////////////////
						
					if ($user_id=="Admin") 
					{
						$query = "SELECT a.reqid,a.user_name, a.dept_code, a.mem_code, a.beard, a.date_submitted, a.fin_verified, a.uma_verified,
							case a.dept_code when 'P' then 'President' else b.report_name end as report_name,c.branch_name, a.status
							FROM ami_office_request a left join ami_reports b on a.dept_code=b.report_code,  ami_branches c
							WHERE a.branch_code = c.branch_code";

						if (($user_level=="N") && ($branch!="all") && ($branch!="")){
							$query .= " and a.branch_code='$branch' ";
						}
						if (($user_dept=="All") && ($report_code1!="all") && ($report_code1!="")){
							$query .= " and a.dept_code='$report_code1' ";
						}
					}
					else if (( $user_level=='N') && ($user_type=="FE")) 
					{
						$query = "SELECT a.reqid,a.user_name, a.dept_code, a.mem_code, a.beard, a.date_submitted, a.fin_verified, a.uma_verified,
							case a.dept_code when 'P' then 'President' else b.report_name end as report_name,c.branch_name, a.status
							FROM ami_office_request a left join ami_reports b on a.dept_code=b.report_code,  ami_branches c
							WHERE a.branch_code = c.branch_code and a.fin_verified!='1'";

						if (($user_level=="N") && ($branch!="all") && ($branch!="")){
							$query .= " and a.branch_code='$branch' ";
						}
						if (($user_dept=="All") && ($report_code1!="all") && ($report_code1!="")){
							$query .= " and a.dept_code='$report_code1' ";
						}
					}
					else if (( $user_level=='N') && ($user_type=="UA")) 
					{
						$query = "SELECT a.reqid,a.user_name, a.dept_code, a.mem_code, a.beard, a.date_submitted, a.fin_verified, a.uma_verified,
							case a.dept_code when 'P' then 'President' else b.report_name end as report_name,c.branch_name, a.status
							FROM ami_office_request a left join ami_reports b on a.dept_code=b.report_code,  ami_branches c
							WHERE a.branch_code = c.branch_code and a.uma_verified!='1'";

						if (($user_level=="N") && ($branch!="all") && ($branch!="")){
							$query .= " and a.branch_code='$branch' ";
						}
						if (($user_dept=="All") && ($report_code1!="all") && ($report_code1!="")){
							$query .= " and a.dept_code='$report_code1' ";
						}
					}
					else if ($user_level == 'L')
					{
						$query = "SELECT a.reqid,a.user_name, a.dept_code, a.mem_code, a.beard, a.date_submitted, a.fin_verified, a.uma_verified,
							case a.dept_code when 'P' then 'President' else b.report_name end as report_name,c.branch_name, a.status
							FROM ami_office_request a left join ami_reports b on a.dept_code=b.report_code,  ami_branches c
							WHERE a.branch_code = c.branch_code and a.branch_code='$branch_code'";
						if (($user_dept=="All") && ($report_code1!="all") && ($report_code1!="")){
							$query .= " and a.dept_code='$report_code1' ";
						}
					}
					else
					{
						$query = "SELECT a.reqid,a.user_name, a.dept_code, a.mem_code, a.beard, a.date_submitted, a.fin_verified, a.uma_verified,
							case a.dept_code when 'P' then 'President' else b.report_name end as report_name,c.branch_name, a.status
							FROM ami_office_request a left join ami_reports b on a.dept_code=b.report_code,  ami_branches c
							WHERE a.branch_code = c.branch_code and a.dept_code='$user_type'";

						if (($user_level=="N") && ($branch!="all") && ($branch!="")){
							$query .= " and a.branch_code='$branch' ";
						}
					}
// DO NOT SHOW DELETED
						$query .= " and a.status!='2' ";

						if (($status!="all") && ($status!="")){
							$query .= " and a.status='$status' ";
						}
						else
						{
							if (($user_id!="Admin") 
								 || ($do_show_all != "1"))
							{
								$query .= " and a.status!='8' ";
								$query .= " and a.status!='9' ";
							}
						}

						if (($adv_filter!="none") && ($adv_expression!="")){
							$query .= " and a.$adv_filter LIKE '%$adv_expression%' ";
						}

						$query .= " ORDER BY c.branch_name, a.date_submitted desc, b.report_name, a.user_name ";

					    //print "$query";
					    $result = @mysql_db_query($dbname,$query);
						$numrows = mysql_num_rows( $result );

						$limit = 25;
						$skip = $_REQUEST["skip"];
						if (empty($skip)) {
						  $skip = 0;
						}

						$query2 = $query . " limit $skip,$limit";
					    $result2 = @mysql_db_query($dbname,$query2);
						$pages=intval($numrows/$limit);
					?>
				 <tr>
							<td colspan="7" >
								<table border="0" cellspacing=0 cellpadding=0 width="100%"> 
									<form name="list_user_requests" method="post" action="list_user_requests.php">
									<tr>
										<? if ($user_dept=="All") { ?>
										<td>&nbsp;Office</td>
										<? }?>
										<? if ($user_level=="N") { ?>
											<td>&nbsp;Branch</td>
											<td>&nbsp;Advanced</td>
											<td>&nbsp;Contains</td>
										<? }?>
										<td>&nbsp;Status</td>
										<td rowspan=2 valign=bottom width=25% align="center"><input type="submit" name="Submit" value="Apply Filter" class="newstylesmallbutton"></td>
									</tr>
									<tr>
										<? if ($user_dept=="All") { ?>
										<td>
											<?
											 //Get all Reports
											 $query3 = "SELECT report_name, report_code FROM ami_reports where office_code!='' order by report_name";
											 $result3 = @mysql_db_query($dbname,$query3);?>
											 <select name="report_code1">
											 <option value="all" selected>All</option>
											 <option value="P">President</option>
											 <?php while ($row3 = mysql_fetch_array($result3)) { ?>
											  <?
													$val = $row3['report_code'];
													$des = $row3['report_name'];
												
												if ($report_code1 == $val) {?>
													<option value=<? print "\"$val\"";  ?> selected><? print "$des";  ?></option>
												<?} else {?>
													<option value=<? print "\"$val\"";  ?>><? print "$des";  ?></option>
												<? }
											  }?>
											 </select>
										<? } else {?>
												&nbsp;<input type="hidden" name="report_code" value="<? echo $user_dept ?>">
										<? }?>
										</td>
										<? if ($user_level=="N") { ?>
											<td>
											<?
											 //Get the branches
											 $query3 = "SELECT * FROM ami_branches where status=1 order by branch_name";
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
											}?>
												</select>
											  </td>
											<td>
												<select name="adv_filter">
													<option selected value="none">-None-</option>
													<option <? if ($adv_filter=="user_name") print "selected"; ?>  value="user_name">Name</option>
												</select>
											</td>
											<td>
												<input type="text" name="adv_expression" value="<? print $adv_expression ?>">
											</td>
										<td><table cellspacing=0 cellpading=0 border=0><tr><td>
										<select name="status">
											<option <? if ($status=="all") print "selected"; ?>  value="all">All</option>
											<option <? if ($status=="0") print "selected"; ?> value="0">New</option>
											<option <? if ($status=="5") print "selected"; ?> value="5">Pending Verification</option>
											<option <? if ($status=="4") print "selected"; ?> value="4">Verified</option>
											<option <? if ($status=="1") print "selected"; ?> value="1">Approved</option>
											<option <? if ($status=="9") print "selected"; ?> value="9">Rejected</option>
										<? if ($user_id=="Admin") { ?>
											<option <? if ($status=="8") print "selected"; ?> value="8">Letter Sent</option>
										<? }?>
										</select>
										<? if ($user_id=="Admin") { ?>

										<!--select name="do_show_all">
											<option <? if ($do_show_all=="0") print "selected"; ?>  value="0">Active Only</option>
											<option <? if ($do_show_all=="1") print "selected"; ?> value="1">Show ALL</option>
										</select-->
											<!--td>Show_All:<input type="checkbox" id="do_show_all" name="do_show_all" value="1" <? echo (($do_show_all=="1")?"checked":"unchecked") ?>></td-->
										<? }?>
											</td></tr></table>
										</td>
										<? }?>
									</tr>
									</form>
								</table>

				</td>
				 </tr>
						<tr><td colspan=7></td>
						</tr>
						<tr>

                                                        <!--<td align="left" colspan="1">
							<?/* if (($user_type=="P") || ($user_type=="GS") || ($user_id=="Admin")) { ?>
                                                                <a href="new_user_request.php" class="ButtonCSS">New Approval</a>
							<? } */?>
                                                        </td-->
						

							<td align="center" colspan="7">
							<? if ($success==1) { ?>
								<font color=green><b>Success!</b></font>
							<? } else if ($failed==1) { ?>
								<font color=red><b>Failed!</b></font>
							<? }  ?>
					<?
							   print "Results ";
							   if ($numrows)
							   	print $skip + 1;
							   else
							   	print "0";
							   print " - ";
							   if ($skip + $limit > $numrows) {
								 print "$numrows";
							   } else {
								 print $skip + $limit;
							   }
							   print " of $numrows";
				 ?>
							</td>
				 </tr>
				 <tr align="center" class=newstylecolumnheader> 
					<td width="200"><font color=""><b>Branch Name</b></font></td>
					<td width="250"><font color=""><b>Name</b></font></td>
					<td width="50"><font color=""><b>Mem Code</b></font></td>
					<td width="180"><font color=""><b>Office</b></font></td>
					<td width="40"><font color=""><b>Beard?</b></font></td>
					<td width="85"><font color=""><b>Date Requested</b></font></td>
					<td width="50"><font color=""><b>Status</b></font></td>
					<td width="100"><font color=""><b>Action</b></font></td>
				</tr>
				 <?
							$currow = 1;
							 while ($row = mysql_fetch_array($result2))
							 {
								//if (($user_id!="Admin") && $row["dept_code"]!=$user_type) continue;

									if ($currow % 2 == 0) {
									   $bgcolor= "#F7F2F4";
									} else {
									   $bgcolor= "#FFFFFF";
									}

									$verified_str = '';
									print "<tr bgcolor=\"$bgcolor\">\n";
									print "<td style=\"padding:2px;\">" . $row["branch_name"] . "</td>\n";
									print "<td style=\"padding:2px;\">" . $row["user_name"] . "</td>\n";
									print "<td style=\"padding:2px;\">" . $row["mem_code"] . "</td>\n";
									print "<td style=\"padding:2px;\">" . $row["report_name"] . "</td>\n";
									if ($row["beard"])
										print "<td style=\"padding:2px;\">Yes</td>\n";
									else
										print "<td style=\"padding:2px;\">No</td>\n";
									print "<td style=\"padding:2px;\">" . $row["date_submitted"] . "</td>\n";
									if ($user_id=='Admin') 
									{
										if ($row["fin_verified"] != '1')
											$verified_str = '[F]';
										if ($row["uma_verified"] != '1')
											$verified_str .= ' [U]';
//										print "<td style=\"padding:2px;\"><font color=green><b>" . $verified_str . "</b></font></td>\n";
									}
									$status1 = $row["status"];
									if($status1 == "0")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"blue\">New</font></td>\n";
									}
									else if ($status1 == "1")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"green\">Approved</font></td>\n";
									}
									else if ($status1 == "8")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"black\">Letter Issued</font></td>\n";
									}
									else if ($status1 == "5")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"blue\">Pending ".$verified_str." Verification</font></td>\n";
									}
									else if ($status1 == "4")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"blue\">Verified</font></td>\n";
									}
									else if ($status1 == "9")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"grey\">Rejected</font></td>\n";
									}
									else
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"red\">Unknown</font></td>\n";
									}
									print "<td align=\"center\">";
									//	print "<a class=newstylesmallbutton href=\"verify_user_request.php?reqid=" . $row["reqid"] . "\">View</a>";
									if (($user_id == "Admin") || ($user_type == "P"))
										print "<a class=newstylesmallbutton href=\"view_user_request.php?reqid=" . $row["reqid"] . "\">View</a>";
									else if (($user_level=="N") && (($user_dept=="FE") || ($user_dept=="UA")))
										print "<a class=newstylesmallbutton href=\"verify_user_request.php?reqid=" . $row["reqid"] . "&dept_code=" . $row[dept_code]. "\">View</a>";


									print "</td></tr>\n";
									$currow++;
								 }
										 mysql_free_result($result);

							   ?>
	  <? if ($numrows > $limit) { ?>
						<tr>
						<td colspan=7>
						<p align="center">
						<?
						if (($user_dept=="All") && ($report_code1!="all") && ($report_code1!="")){
							if ($search!="") {
								$search .= "&report_code1=$report_code1";
							} else {
								$search = "report_code1=$report_code1";
							}
						}

						if (($status!="all") && ($status!="")){
							if ($search!="") {
								$search .= "&status=$status";
							} else {
								$search = "status=$status";
							}
						}

						if (($user_level=="N") && ($branch!="all") && ($branch!="")){
							if ($search!="") {
								$search .= "&branch=$branch";
							} else {
								$search = "branch=$branch";
							}
						}

						if ($skip >=1) {
						  if ($search) {
						  	print "<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=0&$search\">First</a> | ";
						  } else {
						  	print "<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=0\">First</a> | ";
						  }
						} else {
						  print "First | ";
								}

						if ($skip>=1) {
							  $prevoffset=$skip-$limit;
						  if ($search) {
						  	print "<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$prevoffset&$search\">Previous</a> | ";
						  } else {
						  	print "<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$prevoffset\">Previous</a> | ";
						  }
						} else {
						  print "Previous | ";
						}

						if ((($skip/$limit)!= $pages) && $pages!=0) {
							  $newoffset=$skip+$limit;
						  if ($search) {
						  	print "&#160;<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$newoffset&$search\">Next</a> | ";
						  } else {
						  	print "&#160;<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$newoffset\">Next</a> | ";
						  }
						} else {
						  print "Next | ";
						}
						 if ($numrows%$limit) {
						  $pages++;
						}

						$lastoffset=$limit*($pages-1);
						if ($skip<$lastoffset) {
						  if ($search) {
							print "<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$lastoffset&$search\">Last</a> ";
						  } else {
						  	print "<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$lastoffset\">Last</a> ";
						  }
						} else {
						  print "Last ";
						}
						?>
						<br>Pages:
						<?
					  for ($i=1;$i<=$pages;$i++) {
						  $newoffset=$limit*($i-1);
							  if ($newoffset == $skip) {
							print "<b>$i</b> &nbsp; \n";
						  } else {
							if ($search) {
								print "<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$newoffset&$search\">$i</a> &nbsp; \n";
							} else {
								print "<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$newoffset\">$i</a> &nbsp; \n";
							}
						  }
						}

						?>
						</td>
						</tr>
		<? } ?>
						</table>
						</td>
						</tr>
						<tr>
							<td>
							</td>
						</tr>
						</table>

				</td>
                <td width="160" >
                  <?php $list_user_requests_php='Y'; include 'incl/rightbar.inc'; ?>
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
