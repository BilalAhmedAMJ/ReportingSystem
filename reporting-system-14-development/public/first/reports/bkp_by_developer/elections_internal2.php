<? include ("protected.php") ?>
<? include ("../incl/dbcon.php"); 
if (!(($user_type=='P') || ($user_level=='N')))
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


						$query = "SELECT e.*,r.report_name, r.office_code, b.branch_name, 
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

						$query .= " group by e.e_id, v.m_code order by b.branch_code";

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
                                                <form name="list_elections" method="post" action="elections_internal.php">
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
                                                                                         $query3 = "SELECT * FROM ami_branches where status=1 and branch_code!='CA' order by branch_name";
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
				 <tr align="center" bgcolor="#000000">
					<td width=20 align=center bgcolor="#F2FAFB"><span class="topmenutext">fldApprovalsID</span></td>
                        <? $csv_output .= '"fldApprovalsID",'; ?>
					<td width=20 align=center bgcolor="#F2FAFB"><span class="topmenutext">Duration</span></td>
                        <? $csv_output .= '"Duration",'; ?>
					<td width=80 align=center bgcolor="#F2FAFB"><span class="topmenutext">Department Level</span></td>
                        <? $csv_output .= '"Department Level",'; ?>
					<td width=50 align=center bgcolor="#F2FAFB"><span class="topmenutext">fldOFCDE</span></td>
                        <? $csv_output .= '"fldOFCDE",'; ?>
					<td width=100 align=center bgcolor="#F2FAFB"><span class="topmenutext">Election Date</span></td>
                        <? $csv_output .= '"Election Date",'; ?>
					<td width=50 align=center bgcolor="#F2FAFB"><span class="topmenutext">Total Eligible</span></td>
                        <? $csv_output .= '"Total Eligible",'; ?>
					<td width=50 align=center bgcolor="#F2FAFB"><span class="topmenutext">Total Present</span></td>
                        <? $csv_output .= '"Total Present",'; ?>
					<td width=50 align=center bgcolor="#F2FAFB"><span class="topmenutext">M.Code</span></td> 
                        <? $csv_output .= '"M.Code",'; ?>
					<td align=center bgcolor="#F2FAFB"><span class="topmenutext">Beard?</span></td> 
                        <? $csv_output .= '"Beard?",'; ?>
					<td align=center bgcolor="#F2FAFB"><span class="topmenutext">Jama`at</span></td> 
                        <? $csv_output .= '"Jama`at",'; ?>
					<td align=center bgcolor="#F2FAFB"><span class="topmenutext">Proposed By</span></td> 
                        <? $csv_output .= '"Proposed By",'; ?>
					<td align=center bgcolor="#F2FAFB"><span class="topmenutext">Seconded By</span></td> 
                        <? $csv_output .= '"Seconded By",'; ?>
					<td align=center bgcolor="#F2FAFB"><span class="topmenutext">Votes</span></td> 
                        <? $csv_output .= '"Votes",'; ?>
					<td align=center bgcolor="#F2FAFB"><span class="topmenutext">Approval Status</span></td> 
                        <? $csv_output .= '"Approval Status",'; ?>
				</tr>
				 <?
                         $csv_output .= "\n";
							$currow = 1;
							$last_e_id = 0;
							$percentage = "";
							$bold = 0;
							 while ($row = mysql_fetch_array($result2))
							 {
									//if ($currow % 2 == 0) {
									if ($row["e_id"] != $last_e_id) {
										// switch color	
										if ($bgcolor== "#F7F2F4")
										   $bgcolor= "#F2F8F1";
										 else 
										   $bgcolor= "#F7F2F4";
									}
									$last_e_id = $row["e_id"];	
									$bold = false;//($row['approved_m_code']==$row["m_code"]);	// approved or ZERO
									print "<tr bgcolor=\"$bgcolor\">\n";
									print "<td align=center style=\"padding:2px;\">". ($bold?'<b>':'') . ($currow + 5999) . "</td>\n";
									$csv_output .= '"'. ($currow+5999) .'",';
									print "<td align=center style=\"padding:2px;\">". ($bold?'<b>':'') . "7</td>\n";
									$csv_output .= '"7",';
									print "<td style=\"padding:2px;\">". ($bold?'<b>':'') . "Jamaat</td>\n";
									$csv_output .= '"Jamaat",';
									print "<td style=\"padding:2px;\">". ($bold?'<b>':'') . $row['office_code'] . "</td>\n";
									$csv_output .= '"'.$row["office_code"].'",';
									print "<td align=center style=\"padding:2px;\">". ($bold?'<b>':'') . $row["date_held"] ."</td>\n";
									$csv_output .= '"'.$row["date_held"].'",';
									print "<td align=center style=\"padding:2px;\">". ($bold?'<b>':'') . $row["total_eligible"] ."</td>\n";
									$csv_output .= '"'.$row["total_eligible"].'",';
									print "<td align=center style=\"padding:2px;\">". ($bold?'<b>':'') . $row["participants"] ."</td>\n";
									$csv_output .= '"'.$row["participants"].'",';
									print "<td style=\"padding:2px;\">" . ($bold?'<b>':'') . $row["m_code"] . "</td>\n";
									$csv_output .= '"'.$row["m_code"].'",';
									if ($row["has_beard"]=="1") {
										print "<td style=\"padding:2px;\">". ($bold?'<b>':'')."Yes</td>\n";
										$csv_output .= '"Yes",';
									} else {
										print "<td style=\"padding:2px;\">". ($bold?'<b>':'')."No</td>\n";
										$csv_output .= '"No",';
									}
									print "<td style=\"padding:2px;\">" . ($bold?'<b>':'') . $row["branch_code"] . "</td>\n";
									$csv_output .= '"'.$row["branch_code"].'",';
									print "<td style=\"padding:2px;\">" . ($bold?'<b>':''). $row["proposed_by"] . "</td>\n";
									$csv_output .= '"'.$row["proposed_by"].'",';
									print "<td style=\"padding:2px;\">" . ($bold?'<b>':''). $row["seconded_by"] . "</td>\n";
									$csv_output .= '"'.$row["seconded_by"].'",';
									print "<td style=\"padding:2px;\">" . ($bold?'<b>':'') . $row["votes"] . "</td>\n";
									$csv_output .= '"'.$row["votes"].'",';
									print "<td style=\"padding:2px;\">PEN</td>\n";
									$csv_output .= '"PEN",';

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
////////////////////////////////////
							 mysql_free_result($result);
						 if ($currow==1)
                                                { ?>
                                                        <tr><td colspan=14 bgcolor="#F2F8F1">No Results Found</td></tr>
                                                <? } else {?>
					 <form name="userForm" method="post" action="elections_internal.php">
					 <tr bgcolor="#000000">
                                                <input type="hidden" name="export_query" value="Y">
                                                <td colspan=14 bgcolor="white" align="right">
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

						?>
						</td>
						</tr>
						</table>

				</td>
              </tr>
            </table></td>
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
<?
if ($export_query == 'Y')
{
        // Open file export.csv.
        $f = fopen ('export.csv','w');
        // Put all values from $out to export.csv.
        fputs($f, $csv_output);
        fclose($f);

        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename="export.csv"');
        readfile('export.csv');

        $f = fopen ('export.csv','w');
        fputs($f, "");
        fclose($f);
        exit();
}
?>
