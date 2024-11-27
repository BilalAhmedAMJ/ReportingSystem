<? include ("protected.php");
include("../incl/dbcon.php");
//if (($user_level!="N") {
if (($user_level!="N") && ($user_type!="GS") && ($user_type!="P")){

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
          <td width="100" valign=top>
        <?php include 'menu.php'; ?></td>
          <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0"> 
              <tr valign="top">
                <td valign="top" align="center">
					<table border=0 width=100%>
								<tr>
									<th><span >Report Analysis</span><br><br></th>
								</tr>
						<tr>
							<td align=center>
							<table border="0" width=80% class=newstyletable>
							<tr>
							<td align=center>
							<?
							$today = getdate();
							$month = $today['mon'];
							$year = $today['year'];
							if ($month < 7)
								$year = $year - 1;
							$last_year = $year -1;
					///////////////////////////////////////////////////////////////
                                        // retrieve criteria from session if not supplied by form post
                                        session_start();
                                        $myary = $_SESSION['gs_reports'];

                                        if ($myary['year']!="")
                                        {
                                                $year= $myary['year'];
                                        }
                                        if ($RA_status=="")
                                        {
                                                $RA_status = $myary['status'];
                                        }
                                        if ($RA_office=="")
                                        {
                                                $RA_office= $myary['office'];
                                        }
                                        if ($RA_branch=="")
                                        {
                                                $RA_branch = $myary['branch'];
                                        }
                                        if ($RA_show_office=="")
                                        {
                                                $RA_show_office= $myary['show_office'];
                                        }
                                        if ($RA_saiq_report=="")
                                        {
                                                $RA_saiq_report= $myary['saiq_report'];
                                        }

                                        ///////////////////////////////////////////////////////////////
							?>
							<form name="loginForm" method="post" action="gen_report.php">
							  <table width="100%" border="0" cellspacing=5 cellpadding=3 class=newstyletable >
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
								  <td align=right >Office:</td>
								  <td >
									<?
									 //Get all Reports
									 $query3 = "SELECT report_name, report_code FROM ami_reports order by report_name";
									 $result3 = @mysql_db_query($dbname,$query3);?>
									 <select name="report_code">
								<?	if (($user_type=='P') || ($user_type=='GS')) { ?>
									 <option value="all">All</option>
								<? } ?>
									 <?php while ($row3 = mysql_fetch_array($result3)) { ?>
									  <?
										$val = $row3['report_code'];
										$des = $row3['report_name'];
										if (($user_type=='P') || ($user_type=='GS') || ($user_type==$val)) 
										{
										?>
											<option 
											<? if ($RA_office == $val) print "selected"; ?>
											value=<? print "\"$val\"";  ?>><? print "$des";  ?></option>
										 <? 	
										}
									 }?>
									 </select>
								  </td>
								</tr>
								<tr>
								  <td align=right >Year:</td>
								  <td bgcolor=><input type="text" class="BoxCSS1" maxlength="4" size="7" name="year" value="<? echo $year ?>"></td>
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

								<tr><td align=right ><p class="normaltxt">Branch:</p></td>
									<td bgcolor="">
									<?
									 //Get the branches
									 //$query3 = "SELECT * FROM ami_branches where status=1 order by branch_name";
								//	 $query3 = "SELECT * FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code')  order by branch_name";

									if ($user_id=="Admin")
                                                                       		$query3 = "SELECT * FROM ami_branches where status=1 order by branch_name";
                                                                       	else if ($branch_code==$nat_branch)
										$query3 = "SELECT * FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%')  order by branch_name";
									else
                                                                       		$query3 = "SELECT * FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code')  order by branch_name";




									 $result3 = @mysql_db_query($dbname,$query3);?>
									 <select name="branch">
									<?if ($user_id=="Admin") { ?>
										 <option value="all">All (Including Halqas)</option>
									<? } else if ($user_level=='N') { ?>
										<option value="all">All</option>
									<? } ?>
									 <?php while ($row3 = mysql_fetch_array($result3)) { ?>
									  <?
											$val = $row3['branch_code'];
											$des = $row3['branch_name'];
										?>
											<option 
											<? if ($RA_branch==$val) print selected; ?>
											value=<? print "\"$val\"";  ?>><? print "$des";  ?></option>
										<? }?>
										</select>
									  </td>
								</tr>

								<tr>
									<td align=right >Status:</td>
									<td >
										<select name="status">
											<option value="all" <? if ($RA_status=="all") print selected; ?>>All (excluding Draft)</option>
											<option value="vf-rc" <? if ($RA_status=="vf-rc") print selected; ?>>Verified or Received</option>
											<option value="0" <? if ($RA_status=="0") print selected; ?>>Draft</option>
											<option value="1" <? if ($RA_status=="1") print selected; ?>>Complete</option>
											<option value="2" <? if ($RA_status=="2") print selected; ?>>Verified</option>
											<option value="3" <? if ($RA_status=="3") print selected; ?>>Received</option>
										</select>
									</td>
								</tr>
<!--								<tr>
									<td align=right colspan=2 bgcolor="#F2F8F1">Based on GS report: &nbsp;
										<input type="checkbox" name="gs_report"><br><i>[Shows all offices]</i>
									</td>
								</tr>
								<tr><td colspan="2" align="center">&nbsp;</td></tr>
-->
								<?	if (($user_type=='P') || ($user_type=='GS')) { ?>
								<tr>
									<td  align=right >Display Office Names:&nbsp;
									</td>
									<td  bgcolor=""><input type="checkbox" name="show_office" <? if ($RA_show_office) print checked; ?>>
									</td>
								</tr>
								
								<? } ?>
								<tr>
									<td align=right >Show Sa`iqin Report: &nbsp;
									</td>
									<td bgcolor="" >
										<input type="checkbox" name="saiq_report" <? if ($RA_saiq_report) print checked; ?>>&nbsp;<i>[Branch must be selected]</i>
									</td>
								</tr>
							  </table>
							</td>
							</tr>
							</table>
						</td>
						</tr>
								<tr>
								  <td align="center"><br><br><input type="submit" name="Submit" value="Submit" class="newstylemenubutton"></td>
								</tr>
					</table>

							</form>
				</td>
                <td width="160"> 
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
