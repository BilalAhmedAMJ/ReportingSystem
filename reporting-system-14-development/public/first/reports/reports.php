<? include ("protected.php");
include("../incl/dbcon.php");

?>
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
          <td width="100" valign=top>
        <?php include 'menu.php'; ?></td>
          <td valign="top" align=center><table width="100%" border="0" cellpadding="0" cellspacing="0">

              <tr valign="top">
                <td valign="top" align="center">
				<?php
				$HideNew="Y";
				 ?>
					<table width=100% border=0 >
						<tr>
							<th ><span >Select Report</span><br><br></th>
						</tr>
						<tr>
							<td align=center>
                                                        <table border="0" width=80% class=newstyletable>
							<tr>
							<td align=center>
							<?
							$today = getdate();
							$month = $today['mon']-1;
							$year = $today['year'];
							$last_year = $year -1;
							$curr_year = $year;
							if ($month == 0)
							{
								$month = 12;
								$year = $last_year;
								$last_year = $year -1;
							}
							?>
							<form name="loginForm" method="post" action="sel_report.php">
                                                        <table align=center border="0" width=80% cellspacing=5 cellpadding=3 class=newstyletable>
								<? if ($user_dept=="All") { ?>
								<tr>
								  <td align=right >Report:</td>
								  <td >
									<?
									 //Get all Reports
									 $query3 = "SELECT report_name, report_code FROM ami_reports order by report_name";
									 $result3 = @mysql_db_query($dbname,$query3);?>
									 <select name="report_code">
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
								<? } else {?>
									<input type="hidden" name="report_code" value="<? echo $user_dept ?>">
								<? }?>
								<tr>
								  <td  align=right>Year:</td>
								  <td> 
								  	<select name="year">
								  		<option value=<? print "\"$last_year\"";?>><? print "$last_year";?></option>
								  		<option value=<? print "\"$year\""; ?> selected><? print "$year"; ?></option>
										<? if ($curr_year != $year) { ?>
								  		<option value=<? print "\"$curr_year\""; ?>><? print "$curr_year"; ?></option>
										<? } ?>
									</select>
								   </td>
								</tr>
								<tr>
								  <td align=right >Month:</td>
								  <td >
									  <select name="month" size=1>
										<? if ($month=="01") {?>
											<option value="01" selected>January</option>
										<? } else {?>
											<option value="01">January</option>
										<? }?>
										<? if ($month=="02") {?>
											<option value="02" selected>February</option>
										<? } else {?>
											<option value="02">February</option>
										<? }?>
										<? if ($month=="03") {?>
											<option value="03" selected>March</option>
										<? } else {?>
											<option value="03">March</option>
										<? }?>
										<? if ($month=="04") {?>
											<option value="04" selected>April</option>
										<? } else {?>
											<option value="04">April</option>
										<? }?>
										<? if ($month=="05") {?>
											<option value="05" selected>May</option>
										<? } else {?>
											<option value="05">May</option>
										<? }?>
										<? if ($month=="06") {?>
											<option value="06" selected>June</option>
										<? } else {?>
											<option value="06">June</option>
										<? }?>
										<? if ($month=="07") {?>
											<option value="07" selected>July</option>
										<? } else {?>
											<option value="07">July</option>
										<? }?>
										<? if ($month=="08") {?>
											<option value="08" selected>August</option>
										<? } else {?>
											<option value="08">August</option>
										<? }?>
										<? if ($month=="09") {?>
											<option value="09" selected>September</option>
										<? } else {?>
											<option value="09">September</option>
										<? }?>
										<? if ($month=="10") {?>
											<option value="10" selected>October</option>
										<? } else {?>
											<option value="10">October</option>
										<? }?>
										<? if ($month=="11") {?>
											<option value="11" selected>November</option>
										<? } else {?>
											<option value="11">November</option>
										<? }?>
										<? if ($month=="12") {?>
											<option value="12" selected>December</option>
										<? } else {?>
											<option value="12">December</option>
									  	<? }?>
									  </select>
								  </td>
								</tr>
								<? if ($user_level=="N") { ?>
									<tr><td align=right>Branch:</td>
									    <td >
									    <?
										 //Get the branches
									if ($branch_code==$nat_branch)
										 $query3 = "SELECT * FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%')  order by branch_name";
									else
										 $query3 = "SELECT * FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code')  order by branch_name";
										 $result3 = @mysql_db_query($dbname,$query3);?>
										 <select name="branch">
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
								<? }?>
								<tr><td  align=right >Quarter:</p></td>
								    <td >
									 <select name="quarter">
										<option value="" selected>None</option>
										<option value="1">Q1 - Jan-Mar</option>
										<option value="2">Q2 - Apr-Jun</option>
										<option value="3">Q3 - Jul-Sep</option>
										<option value="4">Q4 - Oct-Dec</option>
								  	 </select><br><font color=grey size=1>Applies to Quarterly report only</font>
								  	 </td>
								  </tr>
							  </table>
							</td>
							</tr>
							</table>
						</td>
						</tr>
						<tr>
						  <td align="center"><br><br><input class=newstylebutton type="submit" name="Submit" value="Start" class="ButtonCSS"></td>
						</tr>
					</table>
							</form>

				</td>
                <td width="160">
                  <?	$test_php='Y'; 
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
