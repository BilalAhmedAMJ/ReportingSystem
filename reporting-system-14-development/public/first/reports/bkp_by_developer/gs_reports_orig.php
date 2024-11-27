<? include ("protected.php");
include("../incl/dbcon.php");
if (($user_level!="N") && ($user_type!="GS")){
	header("Location: list_reports.php");
}
?>
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
					<br><br>
					<table>
						<tr>
							<td>
							<table class="BoxCSS" border="0">
							<tr>
							<td>
							<?
							$today = getdate();
							$month = $today['mon'];
							$year = $today['year'];
							$last_year = $year -1;
							?>
							<form name="loginForm" method="post" action="gen_report.php">
							  <table width="250" border="0">
								<tr>
									<th colspan=2 bgcolor="#000000"><span class="pageheader"><font color="white">Select Report</font></span></th>
								</tr>
								<!--<tr>
								  <td bgcolor="#F2F8F1"><p class="normaltxt">Report:</p></td>
								  <td bgcolor="#F2F8F1">
									  <select name="report_type" size=1>
									  <option value="M">Reports by month</option>
									  <option value="Y">Reports by year</option>
									  </select>
									</td>
								</tr>-->
								<tr>
								  <td bgcolor="#FCF8FA" width="25%"><p class="normaltxt">Office:</p></td>
								  <td bgcolor="#FCF8FA" width="75%">
									<?
									 //Get all Reports
									 $query3 = "SELECT report_name, report_code FROM ami_reports order by report_name";
									 $result3 = @mysql_db_query($dbname,$query3);?>
									 <select name="report_code">
									 <option value="all">All</option>
									 <?php while ($row3 = mysql_fetch_array($result3)) { ?>
									  <?
											$val = $row3['report_code'];
											$des = $row3['report_name'];
										?>
											<option value=<? print "\"$val\"";  ?>><? print "$des";  ?></option>
									 <? }?>
									 </select>
								  </td>
								</tr>
								<tr>
								  <td bgcolor="#F2F8F1"><p class="normaltxt">Year:</p></td>
								  <td bgcolor="#F2F8F1"><input type="text" class="BoxCSS1" maxlength="4" size="4" name="year" value="<? echo $year ?>"></td>
								</tr>
								<!--<tr>
								  <td bgcolor="#FCF8FA"><p class="normaltxt">Month:</p></td>
								  <td bgcolor="#FCF8FA">
									  <select name="month" size=1>
									  <option selected value="all">All</option>
									  </select>
								  </td>
								</tr>-->
								<input type="hidden" name="month" value="all"> 
							<!--	<input type="hidden" name="branch" value="all"> -->

								<tr><td bgcolor="#F2F8F1"><p class="normaltxt">Branch:</p></td>
									<td bgcolor="#F2F8F1">
									<?
									 //Get the branches
									 //$query3 = "SELECT * FROM ami_branches where status=1 order by branch_name";
								//	 $query3 = "SELECT * FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code')  order by branch_name";

									if ($user_id=="Admin")
										$query3 = "SELECT * FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%')  order by branch_name";
                                                                       	else if ($branch_code=="CA")
                                                                       		$query3 = "SELECT * FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%')  order by branch_name";
									else
                                                                       		$query3 = "SELECT * FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code')  order by branch_name";





									 $result3 = @mysql_db_query($dbname,$query3);?>
									 <select name="branch">
									 <option value="all">All</option>
									 <?php while ($row3 = mysql_fetch_array($result3)) { ?>
									  <?
											$val = $row3['branch_code'];
											$des = $row3['branch_name'];
										?>
											<option value=<? print "\"$val\"";  ?>><? print "$des";  ?></option>
										<? }?>
										</select>
									  </td>
								</tr>

								<tr>
									<td bgcolor="#FCF8FA">Status:</td>
									<td bgcolor="#FCF8FA">
										<select name="status">
											<option value="all" selected>All</option>
											<option value="0">Draft</option>
											<option value="1">Complete</option>
											<option value="2">Verified by President</option>
											<option value="3">Received</option>
										</select>
									</td>
								</tr>
								<tr>
									<td bgcolor="#F2F8F1">Based on GS report:</td>
									<td bgcolor="#F2F8F1">
										<input type="checkbox" name="gs_report">
									</td>
								</tr>
								<tr><td colspan="2" align="center">&nbsp;</td></tr>
								<tr>
								  <td bgcolor="#F2F8F1" colspan="2" align="center"><input type="submit" name="Submit" value="Submit" class="ButtonCSS"></td>
								</tr>
							  </table>
							</form>
							</td>
							</tr>
							</table>
						</td>
						</tr>
					</table>

				</td>
                <td width="160" bgcolor="#F3F3F3">
                  <?php $gs_reports_php='Y'; include 'incl/rightbar.inc'; ?>
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
