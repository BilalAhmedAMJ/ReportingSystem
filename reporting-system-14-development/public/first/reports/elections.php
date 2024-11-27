<? include ("protected.php") ?>
<? include ("../incl/dbcon.php"); 

if (!(($user_type=='P') || ($user_level=='N')))
{
        print "Restricted Access. ($user_type,$user_level)";
        exit();
}
$elect_id = '';

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
          <td width="100" valign=top bgcolor=black>
        <?php include 'menu.php'; ?></td>
          <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">

              <tr valign="top">
                <td valign="top" align="center">
				<?php include 'admin_header.php'; ?>
				 <table class="TableCSS">
					<tr bgcolor="#F7F2F4">
					<th colspan="5" bgcolor="#000000"><font color="white">Elections</font></th>
					</tr>
					<tr><td>
					<table border=0 cellspacing="1" cellpadding="1" width="100%">
				 	<?
					///////////////////////////////////////////////////////////////
					// retrieve criteria from session if not supplied by form post
					session_start();
					$myary = $_SESSION['list_elections'];
					
					if ($year=="")
					{
						$year = $myary['year'];
					}  
					if ($year=="")
                                        {
                                                $year = date('Y');
                                        }

					if ($status=="")
					{
						$status = $myary['status'];
					}
					if ($dept_code=="")
					{
						$dept_code = $myary['dept_code'];
					}
					if ($branch=="")
					{
						$branch = $myary['branch'];
					}
					
					$_SESSION['list_elections'] = array (
									"year" => $year,
									"status" => $status,
									"dept_code" => $dept_code,
									"branch" => $branch);
					///////////////////////////////////////////////////////////////


						$query = "SELECT e.*,r.report_name, b.branch_name
						FROM ami_elections e, ami_reports r, ami_branches b
						where e.branch_code = b.branch_code && (e.dept_code=r.report_code || e.dept_code='P')";// || e.dept_code='')";
						//WHERE e.dept_code = r.report_code and e.branch_code = b.branch_code";

						if ($dept_code=="shura"){
							$query = "SELECT e.*,b.branch_name
							FROM ami_elections e, ami_branches b 
							WHERE e.branch_code = b.branch_code
							and e.dept_code=''";
						}
						/*if ($dept_code=="P"){
							$query = "SELECT e.*,b.branch_name
							FROM ami_elections e, ami_branches b 
							WHERE e.branch_code = b.branch_code
							and e.dept_code='P'";
						}*/
						if ($user_level=="L"){
							$query .= " and e.branch_code='$branch_code' ";
						}
						if (($dept_code!="all") && ($dept_code!="shura") && ($dept_code!="")){
							$query .= " and e.dept_code='$dept_code' ";
						}
						if ($year!=""){
							$start_date = sprintf("%d-01-01",$year);
							$end_date = sprintf("%d-12-31",$year);
							$query .= " and e.date_held >='$start_date' ";
							$query .= " and e.date_held <='$end_date' ";
						}
						if (($user_level=="N") && ($branch!="all") && ($branch!="")){
							$query .= " and e.branch_code='$branch' ";
						}

						if (($status!="all") && ($status!="")) {
							if ($status=="2")
								$query .= " and e.approved_m_code!='0'";
							else
								$query .= " and e.status='$status' and e.approved_m_code='0'";
						}

						$query .= " group by e_id order by date_held desc, b.branch_name";

					   // print "$query";
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


					<tr><td colspan="5" bgcolor=lightgreen>
					<table border="0" width="100%" bgcolor=lightgreen cellspacing=0 cellpadding=0>
						<form name="list_elections" method="post" action="elections.php">
							<tr>
								<td>&nbsp;Year</td>
								<? if ($user_level=="N") { ?>
								<td>&nbsp;Department</td>
								<td>&nbsp;Branch</td>
								<? }?>
								<td>&nbsp;Status</td>
								<td rowspan=2 width=25% align="center"><input type="submit" name="Submit" value="Filter" class="ButtonCSS4"></td>

							</tr>
							<tr>
									<td><input name="year" class="BoxCSS1" size="4" maxlength="4" type="text" id="year" value="<? echo $year; ?>"></td>
									<? if ($user_level=="N") { ?>
									<td>
											<?
											 //Get all Reports
											 $query3 = "SELECT report_name, report_code FROM ami_reports where report_code!='AR' and report_code!='QR' and report_code!='TR' order by report_name";
											 $result3 = @mysql_db_query($dbname,$query3);?>
											 <select name="dept_code">
											 <option value="all" selected>All (Except Shura)</option>
											<? if ($dept_code == "P") {?>
											 <option value="P" selected>President</option>
												<?} else {?>
											 <option value="P">President</option>
												<?}?>
											<? if ($dept_code== "shura") {?>
											 <option value="shura" selected >Shura</option>
												<?} else {?>
											 <option value="shura" >Shura</option>
												<?}?>
											 <?php while ($row3 = mysql_fetch_array($result3)) { ?>
											  <?
													$val = $row3['report_code'];
													$des = $row3['report_name'];
											
												if ($dept_code== $val) {?>
													<option value=<? print "\"$val\"";  ?> selected><? print "$des";  ?></option>
												<?} else {?>
													<option value=<? print "\"$val\"";  ?>><? print "$des";  ?></option>
												<? }
											  }?>
											 </select>
									<? } else {?>
										<input type="hidden" name="dept_code" value="<? echo $user_dept ?>">
									<? }?>
									</td>
									<? if ($user_level=="N") { ?>
									<td>
											<?
											 //Get the branches
											 $query3 = "SELECT * FROM ami_branches where status=1 and branch_code!='$nat_branch' order by branch_name";
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
									<?} ?>
									<td>
									<select name="status">
										<option <? if ($status=="all") print "selected"; ?> value="all">All</option>
										<option <? if ($status=="0") print "selected"; ?> value="0">Draft</option>
										<option <? if ($status=="1") print "selected"; ?> value="1">Complete</option>
										<option <? if ($status=="2") print "selected"; ?> value="2">Approved</option>
									</select>
									</td>
									</tr>
									</form>
								</table>
							</td>
						</tr>

						<tr bgcolor="white">
						<tr><td colspan="5"></td>
						</tr>
							<? if ($HideNew!="Y") {?>
								<form name="new" method="post" action="election_new.php">
								<td align="left">
								<input type="submit" name="new_report" value="Create" class="ButtonCSS">
								</td>
								</form>
							<? } else { ?>
								<td align="left">
									&nbsp;
								</td>
							<? }
							if (($user_type=='GS') && ($user_level=='N'))
							 {?>
								<form name="new" method="post" action="elections_report.php">
								<td align="left">
								<input type="submit" name="new_report" value="Detail View" class="ButtonCSS">
								</td>
								</form>
								<form name="new" method="post" action="elections_crosstab.php">
								<td align="left">
								<input type="submit" name="new_report" value="Cross Tab View" class="ButtonCSS5">
								</td>
								</form>
								<form name="new" method="post" action="elections_internal.php">
								<td align="left">
								<input type="submit" name="new_report" value="GS View" class="ButtonCSS5">
								</td>
								</form>
							<? } else { ?>
								<td align="left" colspan=3>
									&nbsp;
								</td>
							<? }?>
							<td width="80%" align="right">
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
				 </tr></table>
					<table border=0 cellspacing="1" cellpadding="3" bgcolor="#c0c0c0" >
				 <tr align="center" bgcolor="#000000">
					 <td width=80 align=center bgcolor="black"><span class="topmenutext"><font color=white>DATE HELD</font></span></td>
                                         <td width=180 align=center bgcolor="black"><span class="topmenutext"><font color=white>BRANCH</font></span></td>
                                         <td width=150 align=center bgcolor="black"><span class="topmenutext"><font color=white>DEPARTMENT</font></span></td>
                                     <td align=center bgcolor="black"><span class="topmenutext"><font color=white>ATTENDANCE</font></span></td> 
 <!--                                    	<td width=70 align=center bgcolor="#F2FAFB"><span class="topmenutext"><font color=white>APPROVED</font></span></td> -->
                                         <td align=center width=70 bgcolor="black"><span class="topmenutext"><font color=white>STATUS</font></span></td>
                                         <td align=center bgcolor="black"><span class="topmenutext"><font color=white>ACTION</font></span></td>

				</tr>
				 <?
							$currow = 1;
							$percentage = "";
							 while ($row = mysql_fetch_array($result2))
							 {
								if ($row['participants']>0)
									$percentage = sprintf("%d%%",($row['participants']/$row['total_eligible'])*100);
								else
									$percentage = "";

									if ($currow % 2 == 0) {
									   $bgcolor= "#F7F2F4";
									} else {
									   $bgcolor= "#F2F8F1";
									}

									print "<tr bgcolor=\"$bgcolor\">\n";
									print "<td align=center style=\"padding:2px;\">" . $row["date_held"] . "</td>\n";
//									print "<td style=\"padding:2px;\">" . $row["branch_name"] . "</td>\n";
								if (($user_type=='GS') && ($user_level=='N') &&  ($dept_code!="shura")) 
									print "<td align=\"center\"><a href=\"elections_for_branch.php?branch=".$row["branch_code"]."&elect_date=" . $row["date_held"] . "\">".$row["branch_name"] . "</a></td>\n";
								else	
									print "<td align=\"center\">" . $row["branch_name"] . "</a></td>\n";
									if ($dept_code=="shura")
										print "<td style=\"padding:2px;\">Shura</td>\n";
									else if ($row["dept_code"]=="P")
										print "<td style=\"padding:2px;\">President</td>\n";
									else
										print "<td style=\"padding:2px;\">" . $row['report_name'] . "</td>\n";

									print "<td align=center style=\"padding:2px;\">" . $percentage. "</td>\n";

/*									if ($row['approved_m_code']==0)
										print "<td align=center style=\"padding:2px;\"></td>\n";
									else
										print "<td align=center style=\"padding:2px;\">"	. $row['approved_m_code']. "</td>\n";
*/
									$status1 = $row["status"];
									if ($row['approved_m_code']!=0)
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"grey\"><i>Approved</i></font></td>\n";
										print "<td align=\"center\"><a href=\"election_submit.php?elect_id=" . $row['e_id']."&elect_view_only=1&sender_page=home\">View</a></td></tr>\n";
									}
									else if($status1 == "0")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"blue\">Draft</font></td>\n";
										print "<td align=\"center\"><a href=\"election_votes.php?select_elect_id=" . $row['e_id'] . "&sender_page=home\">Edit</a></td></tr>\n";
									}
									else if ($status1 == "1")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"green\">Complete</font></td>\n";
										print "<td align=\"center\"><a href=\"election_submit.php?elect_id=" . $row['e_id']."&elect_view_only=1&sender_page=home\">View</a>";
										// allow National GS to change to draft  
										if (($user_type=='GS') && ($user_level=='N') && ($row['approved_m_code']==0))
											print "&nbsp;|&nbsp;<a href=\"election_submit.php?elect_id=" . $row['e_id']. "&reopen=1&sender_page=home\"><font color=red>Reopen</font></a>";

										print "</td></tr>\n";
									}

									$currow++;
								 }
									 mysql_free_result($result);
						 if ($currow==1)
                                                { ?>
                                                        <tr><td colspan=6 bgcolor="#F2F8F1">No Reports Found</td></tr>
                                                <? } ?>

		<? if ($numrows > $limit) { ?>
						<tr><td colspan=6 bgcolor="white">

						<p align="center">
						<?

						if ($year!="") {
							if ($search!="") {
								$search .= "&year=$year";
							} else {
								$search = "year=$year";
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
						</td></tr>
		<? } ?>
						</table>
						</td>
						</tr>
						</table>

				</td>
                <td width="160" bgcolor="#F3F3F3">
                  <?php $elections_php='Y';
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
