<link href="../style.css" rel="stylesheet" type="text/css">
<? if ($ar=="Y") {
	//print "$branch_code $report_code $month $year<br>";
	//print "$branch $report_code $month $year<br>";
	//Check if report already exist
	if (($report_code) && ($month) && ($year)) {
		if (($user_level=="N") && (!$rid)) {
		  $arquery0 = "SELECT rid FROM ami_annual_reports WHERE branch_code = '$branch' and year = '$year'";
		} else if ($user_level=="N") {
		  $arquery0 = "SELECT rid FROM ami_annual_reports WHERE branch_code = '$branch_code1' and year = '$year'";
		} else {
		  $arquery0 = "SELECT rid FROM ami_annual_reports WHERE branch_code = '$branch_code' and year = '$year'";
		}

		//print "arquery0:$gsquery0<br>";
		$qrresult0 = @mysql_db_query($dbname,$arquery0,$id_link);
		if ($qrresult0){
			$qrrow0 = mysql_fetch_array($qrresult0);
			$arrid = $qrrow0['rid'];
		}
	}
	//print "arrid=$arrid<br>";
	//New report or existing report
	if (($report_code) || ($arrid)) {
	  if ($arrid){
		  $query0 = "SELECT * FROM ami_annual_reports WHERE rid = '$arrid'";
		  //print "query0:$query0<br>";
		  $result0 = @mysql_db_query($dbname,$query0,$id_link);
		  if ($result0){
			$row0 = mysql_fetch_array($result0);
			  $qrbranch_code = $row0['branch_code'];
//			  $qrmonth = $row0['month'];	// default is 12
			  $qryear = $row0['year'];

			  $total_families = $row0['total_families'];
			  $total_members = $row0['total_members'];
			  $active_families= $row0['active_families'];
			  $active_members= $row0['active_members'];
			  $new_active_members= $row0['new_active_members'];
			  $new_active_families= $row0['new_active_families'];
			  $num_general_meetings= $row0['num_general_meetings'];
			  $average_attendance= $row0['average_attendance'];
			  $num_amila_meetings = $row0['num_amila_meetings'];
			  $num_reports_sent = $row0['num_reports_sent'];
			  $num_tabligh_events = $row0['num_tabligh_events'];
			  $new_active_tabligh_members = $row0['new_active_tabligh_members'];
			  $num_prayer_centers= $row0['num_prayer_centers'];
			  $num_salat_offered= $row0['num_salat_offered'];
			  $attendance_prayer_centers = $row0['attendance_prayer_centers'];
			  $average_jumma_attendance= $row0['average_jumma_attendance'];
			  $regular_quran_class_held= $row0['regular_quran_class_held'];
			  $attendance_regular_quran_class= $row0['attendance_regular_quran_class'];
			  $num_paying_ba_sharaa= $row0['num_paying_ba_sharaa'];
			  $started_paying_ba_sharaa= $row0['started_paying_ba_sharaa'];
			  $comments = $row0['comments'];
		  } else {
			print "Error: invalid report id!";
			exit();
		  }
	  } ?>
		<table border="0" width="580">
		<? // Majlis Amila ?>
		<tr><td>
			<table border="0" width="580" class="BoxCSS1">
				<tr>
					<td>Total Tajnid</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="total_members" value="<? echo $total_members; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>Total number of family units</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="total_families" value="<? echo $total_families; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of Active Members</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="active_members" value="<? echo $active_members; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of Active Families</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="active_families" value="<? echo $active_families; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of members who have become active during the year due to Jama`at efforts</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="new_active_members" value="<? echo $new_active_members; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of families who have become active during the year due to Jama`at efforts</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="new_active_families" value="<? echo $new_active_families; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>How many General Body Meetings were held</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="num_general_meetings" value="<? echo $num_general_meetings; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>What was the average attendance in those meetings</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="average_attendance" value="<? echo $average_attendance; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>How many Majlis `Amila meetings were held during the year</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="num_amila_meetings" value="<? echo $num_amila_meetings; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>How many Monthly Activity Reports were submitted by your Jama`at during this year</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="num_reports_sent" value="<? echo $num_reports_sent; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>How many Tabligh events were held during the year</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="num_tabligh_events" value="<? echo $num_tabligh_events; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>How many members were specifically approached to make them active in Tabligh</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="new_active_tabligh_members" value="<? echo $new_active_tabligh_members; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>How many Prayer Centers (in addition to Juma Centers) are there in your Jama`at</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="num_prayer_centers" value="<? echo $num_prayer_centers; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>How many salat (1-5) are regularly offered at the Mosque/Namaz centre</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="num_salat_offered" value="<? echo $num_salat_offered; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>How many Jama`at members regularly attend Congregational prayers at the Mosque/Namaz centre</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="attendance_prayer_centers" value="<? echo $attendance_prayer_centers; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>What is the average attendance in Friday prayers for members from your Jama`at</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="average_jumma_attendance" value="<? echo $average_jumma_attendance; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>How many regular classes are held monthly to teach Holy Qur'an to children/Jama`at members in your Jama`at</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="regular_quran_class_held" value="<? echo $regular_quran_class_held; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>what percentage of Jama`at members attend these classes e.g. 10%</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="attendance_regular_quran_class" value="<? echo $attendance_regular_quran_class; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>How many of the earning members of your Jama`at pay Chanda according to the prescribed rates</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="num_paying_ba_sharaa" value="<? echo $num_paying_ba_sharaa; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>How many Jama`at members have started to pay Chanda according to the prescribed rates during the year as a result of efforts by the Office bearers of Jama`at</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="5" name="started_paying_ba_sharaa" value="<? echo $started_paying_ba_sharaa; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
                                        <td colspan="2">Comments:<br><textarea class="BoxCSS1" name="comments" <? echo $read_only; ?> cols="60" rows="6"><? echo $comments; ?></textarea></td>
				</tr>
			</table>
		</td>

<tr>
		<? // Submitted Reports?>
		<tr><td ><span class="pageheader"><font color="white">&nbsp;</font></span><td></tr>
			<input type="hidden" name="arrid" value=<? print "\"$arrid\""; ?>>   
		</table>
	<? } else { ?>
		<table width="100%" border="0" class="BoxCSS">
		<tr>
		<td>
		  <table width="580" border="0">
			<tr><td>The selected report is not available at this time. Please try later.</td></tr>
		  </table>
		 </td>
		 </tr>
		 </table>
	<? }
 } else {
 	include("login.php");
 }?>
