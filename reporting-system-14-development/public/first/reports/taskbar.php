<script>
function resizeText(multiplier) {
  if (document.body.style.fontSize == "") {
    document.body.style.fontSize = "1.0em";
  }
  document.body.style.fontSize = parseFloat(document.body.style.fontSize) + (multiplier * 0.2) + "em";
}
</script>
<center><font size=5 color=#336699 face=Times><i>Reporting Portal</font></i></center><br>

<?
 if ($user_id=="") {  ?>
	<table border=0 valign=top cellspacing=0 cellpadding=0 width=200 class=newstylemaintable> 

	<tr>
	<td valign=top><center><br><a href="login.php"><font color="white">Home</font></a></br>
	</td>
	</tr>
	</table>
<? } else { ?>
	<table border=0 valign=top cellspacing=3 cellpadding=2 width=200 bgcolor="white" height="100%" class=newstylemaintable>
	<?
		if ( (($user_dept!="") && ($user_type!="")) || ($user_level!="") )
		{
		/////////////// FIND OUT IF USER IS IMARAT USER /////////////
											  //Get the branch name
											  $imarat_user = 0;
											  $query1 = "SELECT branch_name,region_code FROM ami_branches WHERE branch_code = '$branch_code'";
											  $result1 = @mysql_db_query($dbname,$query1);
											  if ($result1){
												$row1 = mysql_fetch_array($result1);
												$branch_name=$row1['branch_name'];
												$region_code=$row1['region_code'];
												if ($region_code==$nat_branch)
													$imarat_user = 1;
											  } else {
												$branch_name = $branch_code;
											  }
		////////////////////////////////////////////////////////////
		}
	?>
	<tr><td align=center>
<!--img id="plustext" alt="Increase text size" src="images/arrow.png" onclick="resizeText(1)" />
<img id="minustext" alt="Decrease text size" src="images/arrow.png" onclick="resizeText(-1)" /-->
					<!--form name="logout" method="post" action="logout.php">
						<input type="submit" name="logout" value="Logout" class="newstylelogoutbutton"><br>
					</form-->
					<a href="change_password.php" class="newstylesmallbutton"> <span title="Click here to modify your password">Change Password</span></a>&nbsp;&nbsp;
					<a href="logout.php" class="newstylelogoutbutton">Logout</a><br>
					<br><font color="#808080">Date:</font>&nbsp;<font color="black"><? echo date("d-M-Y"); ?> 
					<br><font color="#808080">User:</font>&nbsp;<font color="black"><? echo $user_id; ?>
					<br><font color="#808080">Dept:</font>&nbsp;<font color="black"><? echo $user_type; ?>
					<br><font color="#808080">Branch:</font>&nbsp;<font color="black"><? echo $branch_name; ?>
					<br><br><a href="Guide_AMJ_Reports_2014v1.3.pdf" class="newstylesmallbutton" target=_blank>User Guide</a>
	<br>
	</td>
	</tr>
	</table><br>
	<?

	 if ($listreportsview && ($user_level=="N") && ($branch_code==$nat_branch)) { 
		// get month and year for last reporting period
		$today = getdate();
               	$month = $today['mon']-1;
               	$year = $today['year'];
               	$last_year = $year -1;
               	if ($month == 0)
               	{
               		$month = 12;
                       	$year = $last_year;
			$last_year = $year -1;
		}

		$query1 = "SELECT count(*) as verified FROM ami_all_reports a, ami_branches b WHERE a.branch_code=b.branch_code and a.month=$month and  a.year=$year and a.report_code='$user_type' and a.status>=2 and (b.region_code='$nat_branch' or b.region_code like 'R%')";
		$result1 = @mysql_db_query($dbname,$query1,$id_link);
		if ($result1){
			$row1 = mysql_fetch_array($result1);
			$verified_cnt = $row1['verified'];
		}
		$query1 = "SELECT count(*) as received FROM `ami_all_reports` a, ami_branches b WHERE a.branch_code=b.branch_code and a.month=$month and  a.year=$year and a.report_code='$user_type' and a.status>2 and (b.region_code='$nat_branch' or b.region_code like 'R%')";
		$result1 = @mysql_db_query($dbname,$query1,$id_link);
		if ($result1){
			$row1 = mysql_fetch_array($result1);
			$received_cnt = $row1['received'];
		}
	?>
		<table border=0 valign=top cellspacing=3 cellpadding=2 width=200 bgcolor="white" height="100%" class=newstylemaintable>
		<tr><td colspan="3" align="center">Reports Stats for <? echo date("M",mktime(0,0,0,$month,1,2011)); ?>, <? echo $year; ?> </td>
		</tr>
		<tr>
			<td colspan="3" align="left" nowrap><b>Reports Verified:&nbsp;</b><? echo $verified_cnt; ?></td>
		</tr>
		<tr>
			<td colspan="3" align="left" nowrap><b>Reviewed by<br>National Secretary:&nbsp;</b><? echo $received_cnt;  ?></td>
		</tr>
		</table><br>
	<?
		// current selected year and current selected month
		$cur_year =  $_SESSION['list_reports']['year'];
		$cur_month =  $_SESSION['list_reports']['month'];
		if (($cur_month == 'All')  || ($cur_month == 'all'))
			$cur_month = ''; 
		else
			$cur_month_name = date("M",mktime(0,0,0,$cur_month,1,2011));
		if ( ($cur_month != $month) || ($cur_year != $year))
		{
			if ($cur_month=='')
				$query0 = "SELECT count(*) as verified FROM ami_all_reports a, ami_branches b WHERE a.branch_code=b.branch_code and  a.year=$cur_year and a.report_code='$user_type' and a.status>=2 and (b.region_code='$nat_branch' or b.region_code like 'R%')";
			else
				$query0 = "SELECT count(*) as verified FROM ami_all_reports a, ami_branches b WHERE a.branch_code=b.branch_code and a.month=$cur_month and  a.year=$cur_year and a.report_code='$user_type' and a.status>=2 and (b.region_code='$nat_branch' or b.region_code like 'R%')";
			$result0 = @mysql_db_query($dbname,$query0,$id_link);
			if ($result0){
				$row0 = mysql_fetch_array($result0);
				$verified_cnt = $row0['verified'];
			}
			if ($cur_month=='')
				$query0 = "SELECT count(*) as received FROM `ami_all_reports` a, ami_branches b WHERE a.branch_code=b.branch_code and  a.year=$cur_year and a.report_code='$user_type' and a.status>2 and (b.region_code='$nat_branch' or b.region_code like 'R%')";
			else
				$query0 = "SELECT count(*) as received FROM `ami_all_reports` a, ami_branches b WHERE a.branch_code=b.branch_code and a.month=$cur_month and  a.year=$cur_year and a.report_code='$user_type' and a.status>2 and (b.region_code='$nat_branch' or b.region_code like 'R%')";
			$result0 = @mysql_db_query($dbname,$query0,$id_link);
			if ($result0){
				$row0 = mysql_fetch_array($result0);
				$received_cnt = $row0['received'];
			}
		?>
			<table border=0 valign=top cellspacing=3 cellpadding=2 width=200 bgcolor="white" height="100%" class=newstyletable>
			<tr><td colspan="3" align="center">Reports Stats for <? if ($cur_month!='') { echo $cur_month_name.', ';  } echo $cur_year; ?> </td>
			</tr>
			<tr>
				<td colspan="3" align="left" nowrap><b>Reports Verified:&nbsp;</b><? echo $verified_cnt; ?></td>
			</tr>
			<tr>
				<td colspan="3" align="left" nowrap><b>Reviewed by<br>National Secretary:&nbsp;</b><? echo $received_cnt;  ?></td>
			</tr>
			</table><br>
	<? 	}
	} 

	 if ($report_help != "") { ?>
		<table border=0 valign=top cellspacing=3 cellpadding=2 width=200 bgcolor="white" height="100%" class=newstylemaintable>
		<tr><td align=left><p align=center class=pageheader>Base your report on the following activities</p><hr color=#cccccc>
		<? print $report_help; ?> 
		<hr color=#cccccc> </td> </tr>
		</table><br><br>
		<table border=0 valign=top cellspacing=3 cellpadding=2 width=200 bgcolor="white" height="100%" class=newstylemaintable>
		<tr><td align=left><p align=center class=pageheader>Guide</p><hr color=#cccccc>
		The <b>Status</b> option provides three options to mark the status of report before submission.
		<br><br>Select <b>Draft</b> if you have not completed your report yet and you want to come back later to complete the report. The report with status <b>Draft</b> will not be submitted for review by the National Secretary.
		<br><br>Select <b>Complete</b> if you have completed the report and are ready for submission.
		<br><br><b>Verified by President</b> option is only enabled for the Presidents of Jama`at. A president has to
		mark <b>Verified</b> once he has reviewed and verified the report.
		<hr color=#cccccc> </td> </tr> </table>
			<?
	}
}
?>
