<? include ("protected.php") ?>
<? include ("../incl/dbcon.php");
if (($user_type!="P") && ($user_type!="GS") && ($user_id!="Admin")) {
	header("Location: list_reports.php");
}
$showusermenu=1;
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
				<table width=95% align=center class="newstyletable">

					<tr >
					<th colspan=3>Users</th>
					</tr>
					<tr><td >
					<table border=0 align=center width=100%>
				 	<?

					///////////////////////////////////////////////////////////////
					// retrieve criteria from session if not supplied by form post
					session_start();
					$myary = $_SESSION['list_users'];
					
					if ($status=="")
					{
						$status = $myary['status'];
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

					
					$_SESSION['list_users'] = array ("status" => $status,
									"user_dept" => $user_dept,
									"report_code1" => $report_code1,
									"adv_filter" => $adv_filter,
									"adv_expression" => $adv_expression,
									"branch" => $branch);
					///////////////////////////////////////////////////////////////
						

						$query = 	"SELECT a.u_id,a.user_name, a.user_id, a.user_type, case a.user_level when 'N' then 'National' else 'Local' end as user_level
										, case a.user_type when 'P' then 'President' else b.report_name end as report_name, a.branch_code, c.branch_name, a.status
										FROM ami_users a left join ami_reports b on a.user_type=b.report_code,  ami_branches c
										WHERE a.branch_code = c.branch_code";
						if ($user_level=="L"){
							$query .= " and c.branch_code='$branch_code' ";
						}
						//if ($user_dept!="All"){
						//	$query .= " and a.report_code='$user_dept' ";
						//}
						if (($user_dept=="All") && ($report_code1!="all") && ($report_code1!="")){
							$query .= " and a.user_type='$report_code1' ";
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

						if (($status!="all") && ($status!="")){
							$query .= " and a.status='$status' ";
						}

						if (($adv_filter!="none") && ($adv_expression!="")){
							$query .= " and a.$adv_filter LIKE '%$adv_expression%' ";
						}

						$query .= " ORDER BY c.branch_name, b.report_name, a.user_name ";

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
							<td colspan="7" class=newstylefilterbox>
								<table border="0" cellspacing=0 cellpadding=0 width="100%"> 
									<form name="list_report" method="post" action="list_users.php">
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
										<td rowspan=2 width=25% align="center" valign=bottom><input type="submit" name="Submit" value="Apply Filter" class="newstylesmallbutton"></td>
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
											 <option value="E">Election</option>
											 </select>
										<? } else {?>
												&nbsp;<input type="hidden" name="report_code" value="<? echo $user_dept ?>">
										<? }?>
										</td>
										<? if ($user_level=="N") { ?>
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
											}?>
												</select>
											  </td>
											<td>
												<select name="adv_filter">
													<option selected value="none">-None-</option>
													<option <? if ($adv_filter=="user_name") print "selected"; ?>  value="user_name">Name</option>
													<option <? if ($adv_filter=="user_id") print "selected"; ?>  value="user_id">User ID</option>
													<option <? if ($adv_filter=="expiry_date") print "selected"; ?>  value="expiry_date">Expiry Date</option>
												</select>
											</td>
											<td>
												<input type="text" name="adv_expression" value="<? print $adv_expression ?>" >
											</td>
										<? }?>
										<td>
										<select name="status">
											<option <? if ($status=="all") print "selected"; ?>  value="all">All</option>
											<option <? if ($status=="0") print "selected"; ?> value="0">Inactive</option>
											<option <? if ($status=="1") print "selected"; ?> value="1">Active</option>
										</select>
										</td>
									</tr>
									</form>
								</table>

				</td>
				 </tr>
						<tr>
							<!--form name="new" method="post" action="add_user.php"-->
								<!--<input type="submit" name="add_user" value="New User" class="ButtonCSS">-->
 <!--SUBMENU	
							<td align="left" colspan="2">
								&nbsp;<a href="new_user_request.php" class="newstylesubbutton">New Approval</a>&nbsp;
								&nbsp;&nbsp;&nbsp;<a href="add_user.php" class="newstylesubbutton">New User</a>&nbsp;
								<? if ($user_id=="Admin") { ?>
								&nbsp;&nbsp;&nbsp;<a href="list_users_log.php" class="newstylesubbutton">Login Log</a>&nbsp;
								<? } ?>
							</td>
SUBMENU-->
							<td align="center" colspan="7">
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
							<? if ($success==1) { ?>
								<font color=green><b>Success!</b></font>
							<? } else if ($failed==1) { ?>
								<font color=red><b>Failed!</b></font>
							<? }  ?>
							</td>
							<!--/form-->
				 </tr>
				 <tr align="center">
					<td width="160" class=newstylecolumnheader><font ><b>Name</b></font></td>
					<td width="80" class=newstylecolumnheader><font ><b>User ID</b></font></td>
					<td width="150" class=newstylecolumnheader><font ><b>Branch Name</b></font></td>
					<td width="120" class=newstylecolumnheader><font ><b>Department</b></font></td>
					<td width="50" class=newstylecolumnheader><font ><b>Level</b></font></td>
					<td width="50" class=newstylecolumnheader><font ><b>Status</b></font></td>
					<td width="160" class=newstylecolumnheader><font ><b>Action</b></font></td>
				</tr>
				 <?
							$currow = 1;
							 while ($row = mysql_fetch_array($result2))
							 {

									if ($currow % 2 == 0) {
									   $bgcolor= "#EBFAFF";
									} else {
									   $bgcolor= "#FFFFFF";
									}

									print "<tr bgcolor=\"$bgcolor\">\n";
									print "<td style=\"padding:2px;\">" . $row["user_name"] . "</td>\n";
									print "<td style=\"padding:2px;\">" . $row["user_id"] . "</td>\n";
									print "<td style=\"padding:2px;\">" . $row["branch_name"] . "</td>\n";
									print "<td style=\"padding:2px;\">" . $row["report_name"] . "</td>\n";

								if (($row["user_level"] == 'National') && ($row["branch_code"]!=$nat_branch))
									print "<td style=\"padding:2px;\">Imarat</td>\n";
								else
									print "<td style=\"padding:2px;\">" . $row["user_level"] . "</td>\n";

									$status1 = $row["status"];
									if($status1 == "0")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"red\">Inactive</td>\n";
									}
									else
									{
										print "<td align=\"center\" style=\"padding:2px;\">Active</td>\n";
									}
									print "<td align=\"center\">";
									if ((($user_level == "N") && ($branch_code==$nat_branch)) || (($user_id != $row["user_id"]) && ($row["user_type"]!="P")))
									{
										if (($row["status"]=='0') && ($user_id=="Admin"))
											print "<a class=newstylesmallbutton href=\"add_user.php?activate_id=" . $row["u_id"] . "&status=1\">Activate&nbsp;</a>";
										else if (($row["status"]!='0') && ($user_id=="Admin"))
											print "<a class=newstylesmallbutton href=\"add_user.php?activate_id=" . $row["u_id"] . "&status=0\">Disable&nbsp;</a>";
										print "<a class=newstylesmallbutton href=\"add_user.php?id=" . $row["u_id"] . "\">Edit&nbsp;</a>";
										if (($status1 == "0") || ($user_id=="Admin"))
											print "<a class=newstylesmallbutton href=\"del_user.php?id=". $row["u_id"] . "\"><font color=darkred>delete</font></a>\n";
									}
									print "</td></tr>\n";
									$currow++;
								 }
										 mysql_free_result($result);

							   ?>
	  <? if ($numrows > $limit) { ?>
						<tr>
						<td colspan=7><br>
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
						  	print "&#160;<a class=newstylesmallbutton href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$newoffset&$search\">Next</a>  ";
						  } else {
						  	print "&#160;<a class=newstylesmallbutton href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$newoffset\">Next</a>  ";
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
						<tr>
							<td>
							</td>
						</tr>
						</table>

				</td>
                <td width="160" >
                  <?php $list_users_php='Y'; include 'incl/rightbar.inc'; ?>
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
