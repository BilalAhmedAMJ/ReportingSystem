<?php include ("protected.php") ?>
<?php include ("../incl/dbcon.php"); ?>
<?php
if ($user_type=='E') {
        header("Location: elections.php");
}
$showreportmenu=1;
$listreportsview=1;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title><?php echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">
<?php include '../incl/headscript.inc'; ?>
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
				<?php //include 'admin_header.php'; ?>
				 <table class="newstyletable">
					<tr>
					<th colspan="3"> Reports</th>
					</tr>
					<tr><td>
					<table >
						<tr><td align="center" colspan="10">
						<?php if ($last_page_message!="") print "$last_page_message"; ?>
						</td></tr>
				 	<?php
					///////////////////////////////////////////////////////////////
					// retrieve criteria from session if not supplied by form post
					session_start();
					$myary = $_SESSION['list_reports'];
					
					if ($month=="")
					{
						$month = $myary['month'];
					}
					if ($year=="")
					{
						$year = $myary['year'];
						//echo "<h1>Year:" . $year . "</h1>";
					}
					if ($year=="") 
					{
						$year = date('Y');
					}
					if ($status=="")
					{
						$status = $myary['status'];
					}
					if(is_null($help_requested) )
					{	
						$help_requested="off"; 
					}
					else if (isset($help_requested))
					{	
						$help_requested="on"; 
					}
					else 
					{
						$help_requested= $myary['help_requested'];
					}
					if ($user_dept =="")
					{
						$user_dept = $myary['user_dept'];
					}
					if ($report_code =="")
					{
						$report_code = $myary['report_code'];
					}
					if ($branch=="")
					{
						$branch = $myary['branch'];
					}
					
					//echo "<h1>Year:" . $year . "</h1>";
					
					$_SESSION['list_reports'] = array ("month" => $month,
									"year" => $year,
									"status" => $status,
									"user_dept" => $user_dept,
									"report_code" => $report_code,
									"branch" => $branch,
									"help_requested" => $help_requested);
					///////////////////////////////////////////////////////////////


						$query = "SELECT a.month,a.year, a.rid,b.report_name,a.date_posted,
						c.branch_name,a.status,a.report_code,d.month_desc 
						FROM ami_all_reports a, ami_reports b, ami_branches c, months d
						WHERE a.branch_code = c.branch_code and a.report_code = b.report_code and a.month = d.month_num ";
						if ($user_level!="N"){
							$query .= " and c.branch_code='$branch_code' ";
						}
						if ($user_dept!="All"){
							$query .= " and a.report_code='$user_dept' ";
						}
						if (($user_dept=="All") && ($report_code!="all") && ($report_code!="")){
							$query .= " and a.report_code='$report_code' ";
						}
						if (($month!="all") && ($month!="")){
							$query .= " and a.month='$month' ";
						}
						if (($user_level=="N") && ($branch!="all") && ($branch!="")){
							$query .= " and c.branch_code='$branch' ";
						}
						else if (($user_level=="N") && ($branch_code!="all") && ($branch_code!="")){
							if ($user_id=="Admin")
							{}
							else if ($branch_code==$nat_branch)
								$query .= " and (c.region_code='$branch_code' OR c.branch_code='$branch_code' OR region_code LIKE 'R%') ";
							else
								$query .= " and (c.region_code='$branch_code' OR c.branch_code='$branch_code') ";
						}

						if ($year!=""){
							$query .= " and a.year='$year' ";
						} else {
							//pick up non-archived reports for the new term
							$query .= " and a.archived_ind='0' ";
						}
/*						if (($user_level=="N") && ($branch!="all") && ($branch!="")){
							$query .= " and c.branch_code='$branch' ";
						}
*/
						if (($user_level=="N") && ($help_requested=="on")) {
							$query .= " and LENGTH(a.help)>3 ";
						}
						if (($status!="all") && ($status!="")){
							$query .= " and a.status='$status' ";
						}

						$query .= " ORDER BY a.year desc, a.month desc, c.branch_name asc, b.report_name asc";

//					    print "$query";
					    $result = @mysql_db_query($dbname,$query);
					    
					    #echo "<h1>Result:" . $result . "</h1>";
					    
						$numrows = mysql_num_rows( $result );
							//echo "<font size=2>Total:" . $numrows . "</font>";
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
							<td colspan=6 class=newstylefilterbox>
								<table border="0" width="100%" cellspacing=0 cellpadding=0> 
									<form name="list_report" method="post" action="list_reports.php">
									<tr>
										<td>&nbsp;Month</td>
										<td>&nbsp;Year</td>
										<?php if ($user_dept=="All") { ?>
											<td>&nbsp;Report</td>
										<?php } 
										 if ($user_level=="N") { ?> 
										<td>&nbsp;Branch</td>
										<?php } ?>
										<td>&nbsp;Status</td>
							<? if ($user_level=="N") { ?> 
										<td><span title="Show those reports where help is requested from markaz">Help</span></td>
							<? } ?>
										 <td rowspan=2 valign=bottom width=25% align="center"><input type="submit" name="Submit" value="Apply Filter" class="newstylesmallbutton"></td>
									</tr>
									<tr>
										<td>
										  <select name="month" size=1>
											<option <? if ($month=="all") print selected; ?> value="all" selected>All</option>
											<option <? if ($month=="01") print selected; ?> value="01">January</option>
											<option <? if ($month=="02") print selected; ?> value="02">February</option>
											<option <? if ($month=="03") print selected; ?> value="03">March</option>
											<option <? if ($month=="04") print selected; ?> value="04">April</option>
											<option <? if ($month=="05") print selected; ?> value="05">May</option>
											<option <? if ($month=="06") print selected; ?> value="06">June</option>
											<option <? if ($month=="07") print selected; ?> value="07">July</option>
											<option <? if ($month=="08") print selected; ?> value="08">August</option>
											<option <? if ($month=="09") print selected; ?> value="09">September</option>
											<option <? if ($month=="10") print selected; ?> value="10">October</option>
											<option <? if ($month=="11") print selected; ?> value="11">November</option>
											<option <? if ($month=="12") print selected; ?> value="12">December</option>
										  </select>
										</td>
										<td><input name="year" size="4" maxlength="4" type="text" id="year" value="<? echo $year; ?>"></td>
										<?php
										
											 if ($user_dept=="All") { 
											 
										 ?>
										<td>
											<?php
											 //Get all Reports
											 $query3 = "SELECT report_name, report_code FROM ami_reports order by report_name";
											 $result3 = @mysql_db_query($dbname,$query3);?>
											 <select name="report_code">
											 <option value="all" selected>All</option>
											 <?php while ($row3 = mysql_fetch_array($result3)) { ?>
											  <?php
													$val = $row3['report_code'];
													$des = $row3['report_name'];
											
												if ($report_code == $val) {?>
													<option value=<? print "\"$val\"";  ?> selected><? print "$des";  ?></option>
												<?php
												} else {
													
												?>
													<option value=<? print "\"$val\"";  ?>><? print "$des";  ?></option>
												<?php }
											  }
											  ?>
											 </select>
										</td>
										<?php } else {?>
											<input type="hidden" name="report_code" value="<? echo $user_dept ?>">
										<?php }?>
										<?php if ($user_level=="N") { ?>
											<td>
											<?
											 //Get the branches	
										if ($user_id=="Admin")
											 $query3 = "SELECT * FROM ami_branches where status=1 order by branch_name";
										else if ($branch_code==$nat_branch)
											 $query3 = "SELECT * FROM ami_branches where status=1 and (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%') order by branch_name";
										else
											 $query3 = "SELECT * FROM ami_branches where status=1 and (region_code='$branch_code' OR branch_code='$branch_code') order by branch_name";
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
										<? }?>
										<td>
										<select name="status">
											<option <? if ($status=="all") print "selected"; ?> value="all">All</option>
											<option <? if ($status=="0") print "selected"; ?> value="0">Draft</option>
											<option <? if ($status=="1") print "selected"; ?> value="1">Complete</option>
											<option <? if ($status=="2") print "selected"; ?> value="2">Verified</option>
											<option <? if ($status=="3") print "selected"; ?> value="3">Received</option>
										</select>
										</td>
							<? if ($user_level=="N") { ?> 
										<td>&nbsp;<input type=checkbox name=help_requested <? if ($help_requested=="on") echo "checked";?> >
										</td>
							<? } ?>
									</tr>
									</form>
								</table>
							</td>
						</tr>
						<tr>
<!-- HIDE SUB MENU AS SHOW ON MENU.PHP
							<? if ($HideNew!="Y") {?>
								<form name="new" method="post" action="reports.php">
							<td align="left" colspan="1">
									<input type="submit" name="new_report" value="New Report" class="newstylesubbutton">
							</td>
								</form>
							<? } else { ?>
							<td align="left" colspan="1">
								&nbsp;
							</td>
							<? }?>
	 <?             if ( (($user_type=="GS") || ($user_type=="P") || ($user_level=="N")) && ($user_type!="E") )  {?>
                                        <form name="new" method="post" action="reminders.php">
                        <td align="left">
                                       <input type="submit" name="rems" value="Reminder" class="newstylesubbutton">
                        </td>
                                        </form>
			<td align="right" colspan="8">
                        <? }else {?>
							<td align="right" colspan="9">
                        <? }?>
HIDE SUB MENU -->
							<td align="center" colspan="9">
							  <p style="font-size:8pt;">
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
				 </tr>
				 <tr align="center">
					<td width="100" class=newstylecolumnheader><font color=""><b>Month & Year</b></font></td>
					<td width="200" class=newstylecolumnheader><font color=""><b>Branch Name</b></font></td>
					<td width="150" class=newstylecolumnheader><font color=""><b>Report Name</b></font></td>
					<td width="100" class=newstylecolumnheader><font color=""><b>Date posted</b></font></td>
					<td width="120" class=newstylecolumnheader><font color=""><b>Status</b></font></td>
					<td width="150" class=newstylecolumnheader><font color=""><b>Action</b></font></td>
				</tr>
				 <?
							$currow = 1;
							 while ($row = mysql_fetch_array($result2))
							 {

									if ($currow % 2 == 0) {
									   $bgcolor= "#F9FFFFF";
									} else {
									   $bgcolor= "#FFFFFF";
									}

									print "<tr bgcolor=\"$bgcolor\">\n";
									print "<td style=\"padding:2px;\">" . substr($row["month_desc"],0,3) . "-" . $row["year"] . "</td>\n";
									print "<td style=\"padding:2px;\">" . $row["branch_name"] . "</td>\n";
									print "<td style=\"padding:2px;\">" . $row["report_name"] . "</td>\n";
									print "<td style=\"padding:2px;\">"	. $row["date_posted"] . "</td>\n";

									$status1 = $row["status"];
									if($status1 == "0")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"#800000\">Draft</font></td>\n";
									}
									else if($status1 == "1")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"black\"><i>Complete</i></font></td>\n";
									}
									else if($status1 == "2")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"black\">Verified</font></td>\n";
									}
									else if($status1 == "3")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"grey\">Received</font></td>\n";
									}
									else
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"red\">Unknown</font></td>\n";
									}

 									print "<td align=\"center\"><a class=newstylesmallbutton href=\"sel_report.php?rid=" . $row["rid"] . "\">Edit</a>&nbsp;";
								if ($user_dept=="All")
									print "<a class=newstylesmallbutton href=\"pr_report_all.php?rid=" . $row["rid"] . "\" target=\"_blank\">ALL</a>&nbsp;";

									print "<a class=newstylesmallbutton href=\"pr_report.php?rid=" . $row["rid"] . "\" target=\"_blank\">View</a></td></tr>\n";
									$currow++;
								 }
										 mysql_free_result($result);

							   ?>
  <? if ($numrows > $limit) { ?>

						<tr><td colspan=6><br>
						<p align="center">
						<?

						if (($month!="all") && ($month!="")) {
							$search = "month=$month";
						}
						if ($year!="") {
							if ($search!="") {
								$search .= "&year=$year";
							} else {
								$search = "year=$year";
							}
						}
						if (($user_dept=="All") && ($report_code!="all") && ($report_code!="")){
							if ($search!="") {
								$search .= "&report_code=$report_code";
							} else {
								$search = "report_code=$report_code";
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
						  	print "<a class=newstylesmallbutton href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=0&$search\">First</a> ";
						  } else {
						  	print "<a class=newstylesmallbutton href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=0\">First</a> ";
						  }
						} else {
						  print "First | ";
								}

						if ($skip>=1) {
							$prevoffset=$skip-$limit;
						  if ($search) {
						  	print "<a class=newstylesmallbutton href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$prevoffset&$search\">Previous</a> ";
						  } else {
						  	print "<a class=newstylesmallbutton href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$prevoffset\">Previous</a> ";
						  }
						} else {
						  print "Previous | ";
						}

						if ((($skip/$limit)!= $pages) && $pages!=0) {
							  $newoffset=$skip+$limit;
						  if ($search) {
						  	print "&#160;<a class=newstylesmallbutton href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$newoffset&$search\">Next</a> ";
						  } else {
						  	print "&#160;<a class=newstylesmallbutton href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$newoffset\">Next</a> ";
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
							print "<a class=newstylesmallbutton href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$lastoffset&$search\">Last</a> ";
						  } else {
						  	print "<a class=newstylesmallbutton href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$lastoffset\">Last</a> ";
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
						</table>

				</td>
                <td width="160" calss=newstylemaintable>
                  <?php 
			 include 'incl/rightbar.inc'; ?>
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
