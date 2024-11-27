<? include ("protected.php") ?>
<? include ("../incl/dbcon.php"); 
if (!(($user_type=='GS') && ($user_level=='N')))
{
        print "Restricted Access. ($user_type,$user_level)";
        exit();
}

/////////////////////////////////////////////////////////////
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
				 <table class="TableCSS">
				 	<?
					///////////////////////////////////////////////////////////////
					// retrieve criteria from session if not supplied by form post
					session_start();
					$myary = $_SESSION['elections_for_branch'];
					
					if ($elect_date=="")
					{
						$elect_date= $myary['elect_date'];
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
					
					$_SESSION['elections_for_branch'] = array (
									"elect_date" => $elect_date,
									"status" => $status,
									"dept_code" => $dept_code,
									"branch" => $branch);
?>
					<tr bgcolor="#F7F2F4">
					<th colspan="3" bgcolor="#000000"><font color="white">Elections for <? print $branch ?> held on <? print $elect_date ?></font></th>
					</tr>
					<tr><td colspan="3">
					<table border=0 cellspacing="1" cellpadding="1" width="100%"> 
<?
					///////////////////////////////////////////////////////////////


						$query = "SELECT e.*,r.report_name, b.branch_name, v.*
						FROM ami_elections e, ami_reports r, ami_branches b, ami_election_votes v
						where e.branch_code = b.branch_code && (e.dept_code=r.report_code || e.dept_code='P' || e.dept_code='') 
						&& e.e_id=v.e_id";

						if ($dept_code=="shura"){
							$query = "SELECT e.*,b.branch_name, v.*
							FROM ami_elections e, ami_branches b, ami_election_votes v
							WHERE e.branch_code = b.branch_code
							and e.dept_code='' and e.e_id=v.e_id";
						}

						$query .= " and e.branch_code='$branch' ";

						if ($elect_date!=""){
							$query .= " and e.date_held='$elect_date' ";
						}

						if (($status!="all") && ($status!="")) {
							if ($status=="2")
								$query .= " and e.approved_m_code!='0'";
							else
								$query .= " and e.status='$status' and e.approved_m_code='0'";
						}

						$query .= " group by e.e_id, v.m_code order by e.e_id, v.votes desc";

					    //print "$query";
					    $result = @mysql_db_query($dbname,$query);
						if (!$result)
							print ("invalid results:".$result);	
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
<!--						<tr bgcolor="white">
							<td align="left">
								&nbsp;
							</td>
							<td colspan=2 align="right">
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
						</p></td>
						 </tr>-->
						<tr bgcolor="white">
							<td align="left">
							<b>DATE HELD:</b> <? print $elect_date ?>
							</td>
							<td  colspan="2" align="left">
								<b>BRANCH:</b><? print $branch?>
							</td>
						 </tr>
				</td></tr></table>
				<tr><td colspan=3>
					
					<table border=0 cellspacing="1" cellpadding="3" bgcolor="white" ><tr valign=top align=left>
				 <?
							$currow = 0;
							$last_e_id = 0;
							$percentage = "";
							$bold = 0;
							$approved = '';
							$status1 = '0';
							$comments = '';	
							 while ($row = mysql_fetch_array($result2))
							 {
								if ($last_e_id!=$row["e_id"])
								{
									if ($currow > 0) 
									{
										print "</table>
										</td></tr>
										<tr><td bgcolor=\"".$bgcolor."\">".$comments."</td></tr>
										</table>";
										if ($approved =='0') {
											print "<p align=center><input type=\"submit\" name=\"Submit\" value=\"Approve\" class=\"ButtonCSS\"></p>
											</form>";
										} else if ($status1 != '0') {
											print "<form name=\"userForm\" method=\"post\" action=\"election_submit.php\">
											<input type=\"hidden\" name=\"elect_id\" value=\"". $last_e_id ."\">
											<input type=\"hidden\" name=\"sender_page\" value=\"elections_for_branch.php\">
											<input type=\"hidden\" name=\"undo_approved_mcode\" value=\"1\">
												<p align=center><input type=\"submit\" name=\"Submit\" value=\"Unapprove\" class=\"ButtonCSS1\"></p>
											</form>";
										}
									}
									if ($bgcolor== "#F7F2F4")
									 	$bgcolor= "#F2F8F1";
									else 
										$bgcolor= "#F7F2F4";
									print "<td width=180 bgcolor=\"".$bgcolor."\">";
									print "<table width=\"100%\" border=0 cellspacing=1 cellpadding=1 bgcolor=#e0e0e0>";
									//print "<tr><td bgcolor=\"".#c0c0c0."\">".$elect_date."</td></tr>\n";
									//if ($dept_code=="" || $dept_code=="shura")
									if ($dept_code=="shura")
										print "<tr><td align=center bgcolor=\"#c0c0c0\" style=\"padding:2px;\"><b>Shura</td></tr>\n";
									else if ($row["dept_code"]=="P")
										print "<tr><td align=center bgcolor=\"#c0c0c0\" style=\"padding:2px;\"><b>President</td></tr>\n";
									else
										print "<tr><td align=center bgcolor=\"#c0c0c0\" style=\"padding:2px;\"><b>".$row['report_name'] . "</td></tr>\n";

									/////////////////////////
									$status1 = $row["status"];
									$approved = $row['approved_m_code'];
									$comments = $row['recommendations'];
                                                                        if ($approved!=0)
                                                                        {
                                                                                print "<tr><td align=\"center\"><a href=\"election_submit.php?elect_id=" . $row['e_id']."&elect_view_only=1&sender_page=elections_for_branch.php\">View</a></td></tr>\n";
                                                                        }
                                                                        else if ($status1 == "0")
                                                                        {
                                                                                print "<tr><td align=\"center\"><a href=\"election_votes.php?select_elect_id=" . $row['e_id'] . "\">Edit</a></td></tr>\n";
                                                                        }
                                                                        else if ($status1 == "1")
                                                                        {
                                                                                print "<tr><td align=\"center\"><a href=\"election_submit.php?elect_id=" . $row['e_id']."&elect_view_only=1&sender_page=elections_for_branch.php\">View</a>";
                                                                                // allow National GS to change to draft
                                                                                if (($user_type=='GS') && ($user_level=='N') && ($approved=='0'))
                                                                                        print "&nbsp;|&nbsp;<a href=\"election_submit.php?elect_id=" . $row['e_id']. "&reopen=1\"><font color=red>Reopen</font></a>";

                                                                                print "</td></tr>\n";
                                                                        }

									/////////////////////////

									print "<tr><td bgcolor=\"".$bgcolor."\"style=\"padding:2px;\">" . $row["held_by"] . "</td></tr>\n";
									//print "<tr><td bgcolor=\"".$bgcolor."\"style=\"padding:2px;\">" . $row["total_eligible"] . "</td></tr>\n";
									//print "<tr><td bgcolor=\"".$bgcolor."\"style=\"padding:2px;\">" . $row["participants"] . "</td></tr>\n";
									if ($row['participants']>0)
										$percentage = sprintf("%d/%d [%d%%]",$row['participants'],$row['total_eligible'],($row['participants']/$row['total_eligible'])*100);
									else
										$percentage = "";
									print "<tr><td bgcolor=\"".$bgcolor."\"style=\"padding:2px;\">" . $percentage . "</td></tr>\n";

									if ($row['approved_m_code']!=0)
									{
										print "<tr><td bgcolor=\"".$bgcolor."\" align=\"center\" style=\"padding:2px;\"><font color=\"grey\"><i>Approved</i></font></td></tr>\n";
									}
									else if($status1 == "0")
									{
										$approved = '';
										print "<tr><td bgcolor=\"".$bgcolor."\" align=\"center\" style=\"padding:2px;\"><font color=\"blue\">Draft</font></td></tr>\n";
									}
									else if ($status1 == "1")
									{
										print "<tr><td bgcolor=\"".$bgcolor."\" align=\"center\" style=\"padding:2px;\"><font color=\"green\">Complete</font></td></tr>\n";
									}
									print "<tr><td><table width=\"100%\" border=0 cellspacing=1 cellpadding=1 bgcolor=#c0c0c0>";
									print "<tr><td align=center bgcolor=\"#d0d0d0\" style=\"padding:2px;\">Code</td>\n";
									print "<td align=center bgcolor=\"#d0d0d0\" style=\"padding:2px;\">Name</td>\n";
									print "<td align=center bgcolor=\"#d0d0d0\" style=\"padding:2px;\">Votes</td>\n</tr>";
									if ($approved == '0') print "<form name=\"userForm\" method=\"post\" action=\"election_submit.php\">";
								}
								$bold = ($row['approved_m_code']==$row["m_code"]);
								print "<tr>";
								if ($approved=='0')
									print "<td  bgcolor=\"".$bgcolor."\" style=\"padding:2px;\"><input type=\"radio\" Name=\"approved_mcode\" value=\"". $row["m_code"] ."\"> " . $row["m_code"] ."</td>\n";
								else 
									print "<td  bgcolor=\"".$bgcolor."\" style=\"padding:2px;\">" . ($bold?'<b>':''). $row["m_code"] ."</td>\n";
								print "<td  bgcolor=\"".$bgcolor."\" style=\"padding:2px;\">" . ($bold?'<b>':''). $row["name"]. "</td>\n";
								print "<td align=center bgcolor=\"".$bgcolor."\" style=\"padding:2px;\">" . ($bold?'<b>':'') . $row["votes"] . "</td>\n";
								print "</tr>";
								if ($approved=='0') {
									print "<input type=\"hidden\" name=\"elect_id\" value=\"". $row["e_id"]."\">";
									print "<input type=\"hidden\" name=\"sender_page\" value=\"elections_for_branch.php\">";//?branch=".$branch."&elect_date=".$elect_date."\">";
								}

								$last_e_id = $row["e_id"];	
								$currow++;

							//		($row["has_beard"]=="1")
								 }
							if ($currow == 0)
								print "</td></tr>";
							else {
								print "</table>
									</td></tr>
									<tr><td bgcolor=\"".$bgcolor."\">".$comments."</td></tr>
									</table>";
								if ($approved=='0') {
									print "<p align=center><input type=\"submit\" name=\"Submit\" value=\"Approve\" class=\"ButtonCSS\"></p>
									</form>";
								} else if ($status1 != '0') {
									print "<form name=\"userForm\" method=\"post\" action=\"election_submit.php\">
									<input type=\"hidden\" name=\"elect_id\" value=\"". $last_e_id ."\">
									<input type=\"hidden\" name=\"sender_page\" value=\"elections_for_branch.php\">
									<input type=\"hidden\" name=\"undo_approved_mcode\" value=\"1\">
										<p align=center><input type=\"submit\" name=\"Submit\" value=\"Unapprove\" class=\"ButtonCSS1\"></p>
									</form>";
								}
							}
						?>
							</table>
							 <?mysql_free_result($result);?>
				</td></tr>	
						</table>

						<p align="center">
						<?

						if ($elect_date!="") {
							if ($search!="") {
								$search .= "&elect_date=$elect_date";
							} else {
								$search = "elect_date=$elect_date";
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
?>
<!--                                        <span class="topmenutext">By clicking on the [Approve] button you will approved the selected candidate.<BR><font color="red">WARNING: THIS CANNOT BE UNDONE ONCE APPROVED</font></span> -->

<?/*

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

*/
						?>
						</td>
						</tr>
<!--						<tr>
							<td>
								<hr width="100%">
								<table border="0" width="100%">
									<form name="list_elections" method="post" action="elections_for_branch.php">
									<tr>
										<? if ($user_level=="N") { ?>
										<td>Department:</td>
										<td>
											 <select name="dept_code">
											 <option value="all" selected>All (Except Shura)</option>
											<? if ($dept_code== "shura") {?>
												 <option value="shura" selected >Shura</option>
												<?} else {?>
												 <option value="shura" >Shura</option>
												<?}?>
											 </select>
										<? } else {?>
											<td>&nbsp;</td>&nbsp;<input type="hidden" name="dept_code" value="<? echo $user_dept ?>">
										<? }?>
										</td>
										<td>Status:</td>
										<td>
										<select name="status">
											<option <? if ($status=="all") print "selected"; ?> value="all">All</option>
											<option <? if ($status=="0") print "selected"; ?> value="0">Draft</option>
											<option <? if ($status=="1") print "selected"; ?> value="1">Complete</option>
											<option <? if ($status=="2") print "selected"; ?> value="2">Approved</option>
										</select>
									</td>
									</tr>
									<? if ($user_level=="N") { ?>
										<tr><td>Branch:</td>
											<td>
											<?
											 //Get the branches
											 $query3 = "SELECT * FROM ami_branches where status=1 and branch_code!='$nat_branch' order by branch_name";
											 $result3 = @mysql_db_query($dbname,$query3);?>
											 <select name="branch">
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
										<td>Election Date:</td>
										<td><input name="elect_date" class="BoxCSS1" size="20" maxlength="10" type="text" id="elect_date" value="<? echo $elect_date; ?>">(YYYY-MM-DD)</td>
										 </tr>
									<? }?>
									<tr>
										<td colspan="4" align="center"><input type="submit" name="Submit" value="Search" class="ButtonCSS"></td>
									</tr>
									</form>
								</table>
							</td>
						</tr>
-->
						<tr> 
						<form name="list_elections" method="post" action="elections.php">
						<td colspan="2" align="center"><input type="submit" name="Submit" value="Back" class="ButtonCSS4"></td>
						</form>
						</tr>
						</table>

				</td>
                <!--<td width="1" bgcolor="#666666">
                  <?php //include '../incl/navheight.inc'; ?>
                </td>
                <td width="160" bgcolor="#F3F3F3">
                  <?php //include 'incl/rightbar.inc'; ?>
                </td>-->
              </tr>
            </table></td>
          <!--<td bgcolor="#666666"><img src="../images/spacer.gif" width="1" height="1"></td>-->
        </tr>
      </table></td>
  </tr>
</table>
<?php include 'incl/bottombar.inc'; ?>
</body>
</html>
