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


						$start_date = sprintf("%d-01-01",$year);
						$end_date = sprintf("%d-12-31",$year);

						$query = "select e.dept_code, e.branch_code, b.branch_name, v.m_code, v.name, v.votes 
						from ami_elections e 
						left join ami_election_votes v on e.e_id = v.e_id 
						left join ami_branches b on e.branch_code=b.branch_code 
						where e.date_held >='$start_date' and e.date_held <='$end_date' ";


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

						$query .= " order by e.branch_code, v.m_code";

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
                                                <form name="list_elections" method="post" action="elections_crosstab.php">
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

				$deptArray = array('P','VP','GS','TH','TT','TM','IT', 'FE', 
					  	'SM','AN', 'DT', 'JD', 'MB', 'RA', 'SB', 'ST',
						'TJ', 'UA', 'UK', 'WJ', 'WS', 'WN',
						'WA', 'TI', 'IA', ZT);
				$deptNameArray = array('President','Vice President','General Secretary','Tabligh','Tarbiyyat','Ta`lim','Isha`at', 'Finance',
							'Additional Secretary Mal', 'Amin', 'Diyafat', 'Ja`idad', 'Muhasib', 'Rishta Nata' , 'Sam`i Wa Basri', 'San`at-o-Tijarat',
							'Tahrik Jadid', 'Umur Amma', 'Umur Kharijiyya', 'Waqf Jadid', 'Waqf Jaid Nau Mubaeen', 'Waqf Nau',
							'Wasayya', 'Talimul Quraan', 'Internal Auditor', 'Zira`at');
				$arrayCount = 26;
				 ?>
				 </tr></table>
					<table border=0 cellspacing="1" cellpadding="3" bgcolor="#c0c0c0" width="100%">
				 <tr align="center" bgcolor="#000000">
					<td width=180 align=center bgcolor="#F2FAFB"><span class="topmenutext">Branch</span></td>
					<td widht=200 align=center bgcolor="#F2FAFB"><span class="topmenutext">Proposed Name</span></td> 
					<? 
					$csv_output .= '"Branch",';
					$csv_output .= '"Proposed Name",';
					for ($i = 0; $i < $arrayCount; $i++) { ?>
						<td align=center bgcolor="#F2FAFB"><span class="topmenutext"><? print $deptNameArray[$i]; ?></span></td> 
					<?
						$csv_output .= '"'.$deptNameArray[$i].'",';
					 } ?>
				</tr>
				 <?
					$csv_output .= "\n";
							$currow = 1;
							$last_branch= '0';
							$ppcode= '0';
							$voted= 0;
							 while ($row = mysql_fetch_array($result2))
							 {
									if ($ppcode == $row["m_code"])  {
										$currow++;
										continue;
									}
									if ($row["branch_code"] != $last_branch) {
										// switch color	
										if ($bgcolor== "#F7F2F4")
										   $bgcolor= "#F2F8F1";
										 else 
										   $bgcolor= "#F7F2F4";
									}
									$last_branch = $row["branch_code"];	

									print "<tr bgcolor=\"$bgcolor\">\n";
									print "<td style=\"padding:2px;\"><b>".  $row["branch_name"] ."</b></td>\n";
									print "<td style=\"padding:2px;\">" .  $row["name"] . "</td>\n";
									$csv_output .= '"'.$row["branch_name"].'",';
									$csv_output .= '"'.$row["name"].'",';
									$ppcode= $row["m_code"];	
									for ($i = 0; $i < $arrayCount; $i++) 
									{
										$voted = 0;
										mysql_data_seek($result2,$currow-1); // reset row back to ppname
										mysql_field_seek($result2,0); // reset row back to ppname
										while ($row = mysql_fetch_array($result2))
										{ // for each row check if this name exist
										   if (($ppcode==$row["m_code"]) &&
											($row["dept_code"] == $deptArray[$i])) {
												print "<td style=\"padding:2px;\">" . $row["votes"] . "</td>\n";
												$csv_output .= '"'.$row["votes"].'",';
												$voted = 1;
												break;
										    }
										    else if ($ppcode!=$row["m_code"]) break;
										}
										if ($voted!=1) {
											print "<td style=\"padding:2px;\">&nbsp;</td>\n";
											$csv_output .= '"",';
										}
									}
									mysql_data_seek($result2,$currow-1); // reset row back to ppname
									$row = mysql_fetch_array($result2);
									print "</tr>\n";
									$csv_output .= "\n";

									$currow++;
								 }

//////////////// EXPORT CODE ////////
        // Open file export.csv.
        $f = fopen ('export.csv','w');
        // Put all values from $out to export.csv.
        fputs($f, $csv_output);
        fclose($f);
////////////////////////////////////
							 mysql_free_result($result);
						 if ($currow==1)
                                                { ?>
                                                        <tr><td colspan=<? echo $arrayCount+2; ?> bgcolor="#F2F8F1">No Results Found</td></tr>
                                                <? } else {?>
					 <form name="userForm" method="post" action="elections_crosstab.php">
					 <tr bgcolor="#000000">
                                                <input type="hidden" name="export_query" value="<? echo $query?>">
                                                <td colspan=<? echo $arrayCount+2; ?> bgcolor="white" align="right">
                                                <input type="submit" name="Submit" value="Export Results" class="ButtonCSS2">&nbsp;&nbsp;
                                                </td>
				</tr>
                                                </form>
                                                <? }?>
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

/*
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
        <tr>
          <td colspan="3" bgcolor="#666666"><?php// include '../incl/navwidth.inc'; ?></td>
        </tr>
      </table></td>
  </tr>
</table>
<?php include 'incl/bottombar.inc'; ?>
</body>
</html>
