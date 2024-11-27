<? include ("protected.php") ?>
<? include ("../incl/dbcon.php"); 
if (!(($user_type=='P') || ($user_level=='N')))
{
        print "Restricted Access. ($user_type,$user_level)";
        exit();
}

if ($export_query != '')
{
	header("Cache-Control: must-revalidate");
        header("Pragma: must-revalidate");
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename="export.csv"');
        readfile('export.csv');

        $f = fopen ('export.csv','w');
        fputs($f, "");
        fclose($f);
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
				<?php include 'admin_header.php'; ?>
				 <table class="TableCSS">
					<tr bgcolor="#F7F2F4">
					<th colspan="3" bgcolor="#000000"><font color="white">Elections</font></th>
					</tr>
					<tr><td>
					<table border=0 cellspacing="1" cellpadding="1" width="100%">
				 	<?
					///////////////////////////////////////////////////////////////
					// retrieve criteria from session if not supplied by form post
					session_start();
					$myary = $_SESSION['elections_report'];
					
					if ($year=="")
					{
						$year = $myary['year'];
					}
					if ($year=="")
					{
						$year = Date('Y');
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
					
					$_SESSION['elections_report'] = array (
									"year" => $year,
									"status" => $status,
									"dept_code" => $dept_code,
									"branch" => $branch);
					///////////////////////////////////////////////////////////////


						$query = "SELECT e.*,r.report_name, b.branch_name, 
						v.m_code,v.name,v.proposed_by, v.seconded_by, v.has_beard, v.votes
						FROM ami_elections e, ami_reports r, ami_branches b, ami_election_votes v
						where e.branch_code = b.branch_code && (e.dept_code=r.report_code || e.dept_code='P' || e.dept_code='') 
						&& e.e_id=v.e_id";

						if ($dept_code=="shura"){
							$query = "SELECT e.*,b.branch_name, v.*
							FROM ami_elections e, ami_branches b, ami_election_votes v
							WHERE e.branch_code = b.branch_code
							and e.dept_code='' and e.e_id=v.e_id";
						}

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
							if ($status=="3")	// results 
								$query .= " and e.approved_m_code=v.m_code";
							else if ($status=="2")
								$query .= " and e.approved_m_code!='0'";
							else
								$query .= " and e.status='$status' and e.approved_m_code='0'";
						}

						$query .= " group by e.e_id, v.m_code order by date_held desc, b.branch_name";

					    //print "$query";
					    $result = @mysql_db_query($dbname,$query);
						$numrows = mysql_num_rows( $result );

						$limit = 1000000;
						$skip = $_REQUEST["skip"];
						if (empty($skip)) {
						  $skip = 0;
						}

						$query2 = $query . " limit $skip,$limit";
					    $result2 = @mysql_db_query($dbname,$query2);
						$pages=intval($numrows/$limit);
					?>
					 <tr><td colspan="3" bgcolor=lightgreen>
                                        <table border="0" width="100%" bgcolor=lightgreen cellspacing=0 cellpadding=0>
                                                <form name="list_elections" method="post" action="elections_report.php">
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
                                                                                <option <? if ($status=="3") print "selected"; ?> value="3">Results</option>
                                                                        </select>
                                                                        </td>
                                                                        </tr>
                                                                        </form>
                                                                </table>
                                                        </td>
                                                </tr>


						<tr bgcolor="white">
							<td align="left">
								&nbsp;
							</td>
							<td align="right">
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
						 <?if ($currow==1)
                                                { ?>
                                                        <tr><td colspan=15 bgcolor="#F2F8F1">No Results Found</td></tr>
                                                <? } else {?>
					 <form name="userForm" method="post" action="elections_report.php">
					 <tr bgcolor="#000000">
                                                <input type="hidden" name="export_query" value="<? echo $query?>">
                                                <td colspan=15 bgcolor="white" align="center">
                                                <input type="submit" name="Submit" value="Export Results" class="ButtonCSS2">&nbsp;&nbsp;
                                                </td>
				</tr>
                                                </form>
                                                <? }?>
				 <tr align="center" bgcolor="#000000">
					<td width=130 align=center bgcolor="#F2FAFB"><span class="topmenutext">BRANCH</span></td>
                        <? $csv_output .= '"BRANCH",'; ?>
					<td width=100 align=center bgcolor="#F2FAFB"><span class="topmenutext">DEPARTMENT</span></td>
                        <? $csv_output .= '"DEPARTMENT",'; ?>
					<td align=center bgcolor="#F2FAFB"><span class="topmenutext">PROPOSED CODE</span></td> 
                        <? $csv_output .= '"PROPOSED CODE",'; ?>
					<td align=center bgcolor="#F2FAFB"><span class="topmenutext">PROPOSED NAME</span></td> 
                        <? $csv_output .= '"PROPOSED NAME",'; ?>
					<td align=center bgcolor="#F2FAFB"><span class="topmenutext">HAS BEARD</span></td> 
                        <? $csv_output .= '"HAS BEARD",'; ?>
					<td align=center bgcolor="#F2FAFB"><span class="topmenutext">VOTES</span></td> 
                        <? $csv_output .= '"VOTES",'; ?>
                                    	<td align=center bgcolor="#F2FAFB"><span class="topmenutext">APPROVED</span></td>
                        <? $csv_output .= '"APPROVED",'; ?>
					<td width=80 align=center bgcolor="#F2FAFB"><span class="topmenutext">DATE HELD</span></td>
                        <? $csv_output .= '"DATE HELD",'; ?>
					<td width=100 align=center bgcolor="#F2FAFB"><span class="topmenutext">HELD BY</span></td> 
                        <? $csv_output .= '"HELD BY",'; ?>
					<td align=center bgcolor="#F2FAFB"><span class="topmenutext">PROPOSED BY</span></td> 
                        <? $csv_output .= '"PROPOSED BY",'; ?>
					<td align=center bgcolor="#F2FAFB"><span class="topmenutext">SECONDED BY</span></td> 
                        <? $csv_output .= '"SECONDED BY",'; ?>
					<td align=center bgcolor="#F2FAFB"><span class="topmenutext">ATTENDANCE</span></td> 
                        <? $csv_output .= '"ATTENDANCE",'; ?>
					<td align=center bgcolor="#F2FAFB"><span class="topmenutext">TOTAL</span></td> 
                        <? $csv_output .= '"TOTAL",'; ?>
					<td width=20 align=center bgcolor="#F2FAFB"><span class="topmenutext">ID</span></td>
                        <? $csv_output .= '"ID",'; ?>
					<td align=center width=70 bgcolor="#F2FAFB"><span class="topmenutext">STATUS</span></td>
                        <? $csv_output .= '"STATUS",'; ?>
				</tr>
				 <?
                         $csv_output .= "\n";
							$currow = 1;
							$last_e_id = 0;
							$percentage = "";
							$bold = 0;
							 while ($row = mysql_fetch_array($result2))
							 {
								if ($row['participants']>0)
									$percentage = sprintf("%d%%",($row['participants']/$row['total_eligible'])*100);
								else
									$percentage = "";

									//if ($currow % 2 == 0) {
									if ($row["e_id"] != $last_e_id) {
										// switch color	
										if ($bgcolor== "#F7F2F4")
										   $bgcolor= "#F2F8F1";
										 else 
										   $bgcolor= "#F7F2F4";
									}
									$last_e_id = $row["e_id"];	
									$bold = ($row['approved_m_code']==$row["m_code"]);	// approved or ZERO
									if ($row["m_code"] < 1000)
										print "<tr bgcolor=\"yellow\">\n";
									else
										print "<tr bgcolor=\"$bgcolor\">\n";
									print "<td style=\"padding:2px;\">". ($bold?'<b>':'') . $row["branch_name"] . "</td>\n";
									$csv_output .= '"'.$row["branch_name"].'",';
									if ($dept_code=="shura") {
										print "<td style=\"padding:2px;\">". ($bold?'<b>':'')."Shura</td>\n";
										$csv_output .= '"Shura",';
									} else if ($row["dept_code"]=="P") {
										print "<td style=\"padding:2px;\">". ($bold?'<b>':'')."President</td>\n";
										$csv_output .= '"President",';
									} else {
										print "<td style=\"padding:2px;\">". ($bold?'<b>':'') . $row['report_name'] . "</td>\n";
										$csv_output .= '"'.$row["report_name"].'",';
									}
									if ($row["m_code"] < 1000)
										print "<td style=\"padding:2px;\"><font color=red>" . ($bold?'<b>':'') . $row["m_code"] . "</font></td>\n";
									else
										print "<td style=\"padding:2px;\">" . ($bold?'<b>':'') . $row["m_code"] . "</td>\n";
									$csv_output .= '"'.$row["m_code"].'",';
									print "<td style=\"padding:2px;\">" . ($bold?'<b>':'') . $row["name"] . "</td>\n";
									$csv_output .= '"'.$row["name"].'",';
									if ($row["has_beard"]=="1") {
										print "<td style=\"padding:2px;\">". ($bold?'<b>':'')."Yes</td>\n";
										$csv_output .= '"Yes",';
									} else {
										print "<td style=\"padding:2px;\">". ($bold?'<b>':'')."No</td>\n";
										$csv_output .= '"No",';
									}
									print "<td style=\"padding:2px;\">" . ($bold?'<b>':'') . $row["votes"] . "</td>\n";
									$csv_output .= '"'.$row["votes"].'",';
									if ($row['approved_m_code']=="0") {
										print "<td align=center style=\"padding:2px;\">&nbsp;</td>\n";
										$csv_output .= '"",';
									} else if ($row['approved_m_code']==$row["m_code"]) {
										print "<td align=center style=\"padding:2px;\"><b>Yes</b></td>\n";
										$csv_output .= '"Yes",';
									} else {
										print "<td align=center style=\"padding:2px;\">No</td>\n";
										$csv_output .= '"No",';
									}


									print "<td align=center style=\"padding:2px;\">". ($bold?'<b>':'') . $row["date_held"] . "</td>\n";
									$csv_output .= '"'.$row["date_held"].'",';
									print "<td style=\"padding:2px;\">". ($bold?'<b>':'') . $row["held_by"] . "</td>\n";
									$csv_output .= '"'.$row["held_by"].'",';
									print "<td style=\"padding:2px;\">" . ($bold?'<b>':''). $row["proposed_by"] . "</td>\n";
									$csv_output .= '"'.$row["proposed_by"].'",';
									print "<td style=\"padding:2px;\">" . ($bold?'<b>':''). $row["seconded_by"] . "</td>\n";
									$csv_output .= '"'.$row["seconded_by"].'",';

//									print "<td align=center style=\"padding:2px;\">" . $percentage. "</td>\n";
//									$csv_output .= '"'.$percentage.'",';

									print "<td style=\"padding:2px;\">" . ($bold?'<b>':'') . $row["participants"] . "</td>\n";
									$csv_output .= '"'.$row["participants"].'",';
									print "<td style=\"padding:2px;\">". ($bold?'<b>':'') . $row["total_eligible"] . "</td>\n";
									$csv_output .= '"'.$row["total_eligible"].'",';

									print "<td align=center style=\"padding:2px;\">". ($bold?'<b>':'') . $row["e_id"] . "</td>\n";
									$csv_output .= '"'.$row["e_id"].'",';

									$status1 = $row["status"];
									if ($row['approved_m_code']!=0)
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"grey\"><i>Approved</i></font></td>\n";
										$csv_output .= '"Approved",';
									}
									else if($status1 == "0")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"blue\">Draft</font></td>\n";
										$csv_output .= '"Draft",';
									}
									else if ($status1 == "1")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"green\">Complete</font></td>\n";
										$csv_output .= '"Complete",';
									}
									print "</tr>\n";

									$currow++;
									$csv_output .= "\n";
								 }

//////////////// EXPORT CODE ////////
/*	mysql_field_seek($result,0);
	mysql_data_seek($result,0);
	$i = 0;
        $fcount= mysql_num_fields($result); 
        if ($fcount > 0) {
                while ( ($i<$fcount) && ($meta=mysql_fetch_field($result,$i)) ) {
                        $csv_output .= '"'.$meta->name.'",';
                        $i++;
                }
        }
        $csv_output .= "\n";
        while ($rowr = mysql_fetch_row($result)) {
                for ($j=0;$j<$i;$j++) {
                        $csv_output .= '"'.$rowr[$j].'",';
                }
                $csv_output .= "\n";
        }
*/
        // Open file export.csv.
        $f = fopen ('export.csv','w');
        // Put all values from $out to export.csv.
        fputs($f, $csv_output);
        fclose($f);
////////////////////////////////////
							 mysql_free_result($result);?>
						</table>

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


/*						if ($skip >=1) {
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
