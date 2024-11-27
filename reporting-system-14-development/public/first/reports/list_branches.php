<? include ("protected.php") ?>
<? include ("../incl/dbcon.php");
if ($user_id!="Admin"){
	header("Location: list_reports.php");
}
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
					<th colspan="3" >Branches</th>
					</tr>
					<tr><td>
					<table >
				 	<?
					///////////////////////////////////////////////////////////////
					// retrieve criteria from session if not supplied by form post
					session_start();
					$myary = $_SESSION['list_branches'];
					
					if ($region=="")
					{
						$region= $myary['region'];
					}
					if ($status=="")
					{
						$status = $myary['status'];
					}
					
					$_SESSION['list_branches'] = array ("region" => $region,
									"status" => $status);
					///////////////////////////////////////////////////////////////


						$query = "SELECT b.*,r.region_name from ami_branches b left join ami_regions r on b.region_code=r.region_code";
						if (($status!="all") && ($status!="")){
							$query .= " where b.status='$status' ";
							if (($region!="all") && ($region!="")){
								$query .= " and b.region_code='$region' ";
							}
						}
						else if (($region!="all") && ($region!="")){
							$query .= " where b.region_code='$region' ";
						}

						$query .= " ORDER BY branch_name" ;

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
							<td colspan=5 >
								<table border="0" width="100%" cellspacing=0 cellpadding=0>
									<form name="list_branch" method="post" action="list_branches.php">
									<tr>
										<td>&nbsp;Region</td>
										<td>&nbsp;Status</td>
										<td rowspan=2 width=50% align="center" valign=bottom><input type="submit" name="Submit" value="ApplyFilter" class="newstylesmallbutton"></td>

									</tr>
									<tr>
										<td>
										<?
										 //Get the regions 
										 $query3 = "SELECT * FROM ami_regions where status=1 order by region_name";
										 $result3 = @mysql_db_query($dbname,$query3);?>
										 <select name="region">
										 <option value="all" selected>All</option>
										 <?php while ($row3 = mysql_fetch_array($result3)) { ?>
										  <?
												$val = $row3['region_code'];
												$des = $row3['region_name'];

											if ($region == $val) {?>
												<option value=<? print "\"$val\"";  ?> selected><? print "$des";  ?></option>
											<?} else {?>
												<option value=<? print "\"$val\"";  ?>><? print "$des";  ?></option>
											<? }
										} ?>
											</select>
										 </td>
										<td>
										<select name="status">
											<option <? if ($status=="all") print "selected" ?> value="all">All</option>
											<option <? if ($status=="0") print "selected" ?> value="0">Inactive</option>
											<option <? if ($status=="1") print "selected" ?> value="1">Active</option>
										</select>
									</td>
									</tr>
									</form>
								</table>
							</td>
						</tr>
						<tr>
							<td align="center" colspan="10">
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
				 <tr align="center" >
					<td class=newstylecolumnheader width="50"><font color=""><b>Code</b></font></td>
					<td class=newstylecolumnheader width="150"><font color=""><b>Branch Name</b></font></td>
					<td class=newstylecolumnheader width="200"><font color=""><b>Region Name</b></font></td>
					<td class=newstylecolumnheader width="50"><font color=""><b>Status</b></font></td>
					<td class=newstylecolumnheader width="100"><font color=""><b>Action</b></font></td>
				</tr>
				 <?
							$currow = 1;
							 while ($row = mysql_fetch_array($result2))
							 {

									if ($currow % 2 == 0) {
									   $bgcolor= "#F7F2F4";
									} else {
									   $bgcolor= "#FFFFFF";
									}

									print "<tr bgcolor=\"$bgcolor\">\n";
									print "<td style=\"padding:2px;\">" . $row["branch_code"] . "</td>\n";
									print "<td style=\"padding:2px;\">" . $row["branch_name"] . "</td>\n";
									print "<td style=\"padding:2px;\">" . $row["region_name"]  . "</td>\n";

									$status1 = $row["status"];
									if($status1 == "0")
									{
										print "<td align=\"center\" style=\"padding:2px;\"><font color=\"red\">Inactive</td>\n";
									}
									else
									{
										print "<td align=\"center\" style=\"padding:2px;\">Active</td>\n";
									}

									print "<td align=\"center\"><a class=newstylesmallbutton href=\"add_branch.php?id=" . $row["bid"] . "\">Edit</a>";
									$currow++;
								 }
										 mysql_free_result($result);

							   ?>
	   <? if ($numrows > $limit) { ?>
                                                <tr><td colspan=5>
                                                <hr>

						<p align="center">
						<?

						if (($status!="all") && ($status!="")){
							if ($search!="") {
								$search .= "&status=$status";
							} else {
								$search = "status=$status";
							}
						}
						if (($region!="all") && ($region!="")){
							if ($search!="") {
								$search .= "&region=$region";
							} else {
								$search = "region=$region";
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
						</table>

				</td>
                <td width="160">
                  <?php $list_branches_php='Y'; include 'incl/rightbar.inc'; ?>
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
