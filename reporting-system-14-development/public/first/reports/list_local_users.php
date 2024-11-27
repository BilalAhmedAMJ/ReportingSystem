<? include ("protected.php") ?>
<? include ("../incl/dbcon.php");

if (!(($user_type=='GS') || ($user_type=='P') || ($user_level=='N')))
{
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
<?php include '../incl/topbar.inc'; ?>
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
					<tr bgcolor="#F7F2F4">
					<th colspan="3" bgcolor="#000000"><font color="white">Users (Office Bearers)</font></th>
					</tr>
					<tr><td>
					<table >
				 	<?

						$query = 	"SELECT a.u_id,a.user_name, a.user_id, case a.user_level when 'N' then 'National' else 'Local' end as user_level
										, case a.user_type when 'P' then 'President' else b.report_name end as report_name,c.branch_name, a.status
										FROM ami_users a left join ami_reports b on a.user_type=b.report_code,  ami_branches c
										WHERE a.branch_code = c.branch_code and a.branch_code='$branch_code'";

						//if ($user_level=="L"){
						//	$query .= " and c.branch_code='$branch_code' ";
						//}
						//if ($user_dept!="All"){
						//	$query .= " and a.report_code='$user_dept' ";
						//}
						//if (($user_level=="N") && ($branch!="all") && ($branch!="")){
						//	$query .= " and a.branch_code='$user_branch' ";
						//}
						$query .= " ORDER BY  b.report_name, a.user_name, c.branch_name ";

					    //print "$query";
					    $result = @mysql_db_query($dbname,$query);
						$numrows = mysql_num_rows( $result );

						$limit = 30;
						$skip = $_REQUEST["skip"];
						if (empty($skip)) {
						  $skip = 0;
						}

						$query2 = $query . " limit $skip,$limit";
					    $result2 = @mysql_db_query($dbname,$query2);
						$pages=intval($numrows/$limit);
					?>
						<tr>
							
							<td align="left" colspan="1">
						<!--	<form name="new" method="post" action="add_user.php">
								<input type="submit" name="add_user" value="New User" class="ButtonCSS3">
							</form>
						-->
							</td>
							<td align="right" colspan="9">
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
				 <tr align="center" bgcolor="#000000">
					<td width="200"><font color="white"><b>Department</b></font></td>
					<td width="50"><font color="white"><b>User ID</b></font></td>
					<td width="250"><font color="white"><b>Name</b></font></td>
					<!--<td width="150"><font color="white"><b>Branch Name</b></font></td>-->
					<td width="50"><font color="white"><b>Level</b></font></td>
					<td width="50"><font color="white"><b>Status</b></font></td>
					<!--<td width="100"><font color="white"><b>Action</b></font></td>-->
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
									print "<td style=\"padding:2px;\">" . $row["report_name"] . "</td>\n";
									print "<td style=\"padding:2px;\">" . $row["user_id"] . "</td>\n";
									print "<td style=\"padding:2px;\">" . $row["user_name"] . "</td>\n";
									//print "<td style=\"padding:2px;\">" . $row["branch_name"] . "</td>\n";
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

									//print "<td align=\"center\"><a href=\"add_user.php?id=" . $row["u_id"] . "\">Edit</a>&nbsp;&nbsp;&nbsp;";
									//print "<a href=\"del_user.php?id=". $row["u_id"] . "\">delete</a></td></tr>\n";
									$currow++;
								 }
										 mysql_free_result($result);

							   ?>
	  <? if ($numrows > $limit) { ?>
						<tr><td colspan=7>
						<hr>
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

					if ($numrows > $limit) 
					{

						if ($skip >=1) {
						  if ($search) {
						  	print "<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=0&$search\">First</a> | ";
						  } else {
						  	print "<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=0\">First</a> | ";
						  }
						} else {
						  if ($numrows > $limit) print "First | ";
								}

						if ($skip>=1) {
							  $prevoffset=$skip-$limit;
						  if ($search) {
						  	print "<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$prevoffset&$search\">Previous</a> | ";
						  } else {
						  	print "<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$prevoffset\">Previous</a> | ";
						  }
						} else {
						 if ($numrows > $limit)  print "Previous | ";
						}

						if ((($skip/$limit)!= $pages) && $pages!=0) {
							  $newoffset=$skip+$limit;
						  if ($search) {
						  	print "&#160;<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$newoffset&$search\">Next</a> | ";
						  } else {
						  	print "&#160;<a href=\"".$HTTP_SERVER_VARS['PHP_SELF']."?skip=$newoffset\">Next</a> | ";
						  }
						} else {
						  if ($numrows > $limit) print "Next | ";
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
						  if ($numrows > $limit) print "Last ";
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
                <td width="1" bgcolor="#666666">
                  <?php include '../incl/navheight.inc'; ?>
                </td>
                <td width="160" bgcolor="#F3F3F3">
                  <?php include 'incl/rightbar.inc'; ?>
                </td>
              </tr>
            </table></td>
          <td bgcolor="#666666"><img src="../images/spacer.gif" width="1" height="1"></td>
        </tr>
        <tr>
          <td colspan="3" bgcolor="#666666"><?php include '../incl/navwidth.inc'; ?></td>
        </tr>
      </table></td>
  </tr>
</table>
<?php include '../incl/bottombar.inc'; ?>
<?php include '../incl/preload.inc'; ?>
</body>
</html>
