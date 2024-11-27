<? if ($gs=="Y") {
	//print "$branch_code $report_code $month $year<br>";
//	print "$branch_code1 $report_code $month $year<br>";
	//Check if report already exist
	if (($report_code) && ($month) && ($year)) {
		if (($user_level=="N") && (!$rid)) {
		  $gsquery0 = "SELECT rid FROM ami_all_gs_reports WHERE branch_code = '$branch' and month = '$month' and year = '$year'";
		} else if ($user_level=="N") {
			 $gsquery0 = "SELECT rid FROM ami_all_gs_reports WHERE branch_code = '$branch_code1' and month = '$month' and year = '$year'";
			//$gsquery0 = "SELECT branch_name,region_code FROM ami_branches WHERE branch_code = '$branch_code1'";
		} else {
		  $gsquery0 = "SELECT rid FROM ami_all_gs_reports WHERE branch_code = '$branch_code' and month = '$month' and year = '$year'";
		}

		//print "gsquery0:$gsquery0<br>";
		$gsresult0 = @mysql_db_query($dbname,$gsquery0,$id_link);
		if ($gsresult0){
			$gsrow0 = mysql_fetch_array($gsresult0);
			$gsrid = $gsrow0['rid'];
		}
	}
	//print "gsrid=$gsrid<br>";
	//New report or existing report
	if (($report_code) || ($gsrid)) {
	  if ($gsrid){
		  $query0 = "SELECT * FROM ami_all_gs_reports WHERE rid = '$gsrid'";
		  //print "query0:$query0<br>";
		  $result0 = @mysql_db_query($dbname,$query0,$id_link);
		  if ($result0){
			$row0 = mysql_fetch_array($result0);
			  $gsbranch_code = $row0['branch_code'];
			  $gsmonth = $row0['month'];
			  $gsyear = $row0['year'];
			  $amila_meeting1 = $row0['amila_meeting1'];
			  $meeting1_date = $row0['meeting1_date'];
			  $meeting1_attendance = $row0['meeting1_attendance'];
			  $meeting1_minutes = $row0['meeting1_minutes'];
			  $meeting1_attachment = $row0['meeting1_attachment'];
			  $amila_meeting2 = $row0['amila_meeting2'];
			  $meeting2_date = $row0['meeting2_date'];
			  $meeting2_attendance = $row0['meeting2_attendance'];
			  $meeting2_minutes = $row0['meeting2_minutes'];
			  $meeting2_attachment = $row0['meeting2_attachment'];
			  $gbody_meeting = $row0['gbody_meeting'];
			  $gbody_meeting_date = $row0['gbody_meeting_date'];
			  $gbody_meeting_location = $row0['gbody_meeting_location'];
			  $gents = $row0['gents'];
			  $ladies = $row0['ladies'];
			  $children = $row0['children'];
			  $total_adults = $row0['total_adults'];
			  $not_attend_last_year = $row0['not_attend_last_year'];
			  $action_taken = $row0['action_taken'];
			  $gbody_brief_report = $row0['gbody_brief_report'];
			  $gbody_meeting_attachment = $row0['gbody_meeting_attachment'];
			  $total_households = $row0['total_households'];
			  $total_members = $row0['total_members'];
			  $total_moved_out = $row0['total_moved_out'];
			  $total_moved_in = $row0['total_moved_in'];
			  $tajneed_attachment = $row0['tajneed_attachment'];
			  $households_visit_by_p_gs = $row0['households_visit_by_p_gs'];
			  $visit1_comments = $row0['visit1_comments'];
			  $households_visit_by_amila = $row0['households_visit_by_amila'];
			  $visit2_comments = $row0['visit2_comments'];
			  $visit_attachment = $row0['visit_attachment'];
			  $saiq1 = $row0['saiq1'];
			  $saiq1_visit_in_person = $row0['saiq1_visit_in_person'];
			  $saiq1_contact_by_phone = $row0['saiq1_contact_by_phone'];
			  $saiq1_message = $row0['saiq1_message'];
			  $saiq2 = $row0['saiq2'];
			  $saiq2_visit_in_person = $row0['saiq2_visit_in_person'];
			  $saiq2_contact_by_phone = $row0['saiq2_contact_by_phone'];
			  $saiq2_message = $row0['saiq2_message'];
			  $saiq3 = $row0['saiq3'];
			  $saiq3_visit_in_person = $row0['saiq3_visit_in_person'];
			  $saiq3_contact_by_phone = $row0['saiq3_contact_by_phone'];
			  $saiq3_message = $row0['saiq3_message'];
			  $saiq4 = $row0['saiq4'];
			  $saiq4_visit_in_person = $row0['saiq4_visit_in_person'];
			  $saiq4_contact_by_phone = $row0['saiq4_contact_by_phone'];
			  $saiq4_message = $row0['saiq4_message'];
			  $saiq5 = $row0['saiq5'];
			  $saiq5_visit_in_person = $row0['saiq5_visit_in_person'];
			  $saiq5_contact_by_phone = $row0['saiq5_contact_by_phone'];
			  $saiq5_message = $row0['saiq5_message'];
			  $saiq6 = $row0['saiq6'];
			  $saiq6_visit_in_person = $row0['saiq6_visit_in_person'];
			  $saiq6_contact_by_phone = $row0['saiq6_contact_by_phone'];
			  $saiq6_message = $row0['saiq6_message'];
			  $saiq7 = $row0['saiq7'];
                          $saiq7_visit_in_person = $row0['saiq7_visit_in_person'];
                          $saiq7_contact_by_phone = $row0['saiq7_contact_by_phone'];
                          $saiq7_message = $row0['saiq7_message'];
                          $saiq8 = $row0['saiq8'];
                          $saiq8_visit_in_person = $row0['saiq8_visit_in_person'];
                          $saiq8_contact_by_phone = $row0['saiq8_contact_by_phone'];
                          $saiq8_message = $row0['saiq8_message'];
                          $saiq9 = $row0['saiq9'];
                          $saiq9_visit_in_person = $row0['saiq9_visit_in_person'];
                          $saiq9_contact_by_phone = $row0['saiq9_contact_by_phone'];
                          $saiq9_message = $row0['saiq9_message'];
                          $saiq10 = $row0['saiq10'];
                          $saiq10_visit_in_person = $row0['saiq10_visit_in_person'];
                          $saiq10_contact_by_phone = $row0['saiq10_contact_by_phone'];
                          $saiq10_message = $row0['saiq10_message'];
                          $saiq11 = $row0['saiq11'];
                          $saiq11_visit_in_person = $row0['saiq11_visit_in_person'];
                          $saiq11_contact_by_phone = $row0['saiq11_contact_by_phone'];
                          $saiq11_message = $row0['saiq11_message'];
                          $saiq12 = $row0['saiq12'];
                          $saiq12_visit_in_person = $row0['saiq12_visit_in_person'];
                          $saiq12_contact_by_phone = $row0['saiq12_contact_by_phone'];
                          $saiq12_message = $row0['saiq12_message'];

			  $gs = $row0['gs'];
			  $th = $row0['th'];
			  $tt = $row0['tt'];
			  $tm = $row0['tm'];
			  $it = $row0['it'];
			  $sb = $row0['sb'];
			  $ra = $row0['ra'];
			  $uk = $row0['uk'];
			  $ua = $row0['ua'];
			  $fe = $row0['fe'];
			  $dt = $row0['dt'];
			  $wa = $row0['wa'];
			  $tj = $row0['tj'];
			  $wj = $row0['wj'];
			  $jd = $row0['jd'];
			  $wn = $row0['wn'];
			  $zt = $row0['zt'];
			  $st = $row0['st'];
			  $mb = $row0['mb'];
			  $an = $row0['an'];
			  $ws = $row0['ws'];
			  $sm = $row0['sm'];
			  $ti = $row0['ti'];
		  } else {
			print "Error: invalid report id!";
			exit();
		  }
	  } ?>
		<table border="0" width="580">
		<? // Amila Meeting1?>
		<tr><td bgcolor="#000000"><span class="pageheader"><font color="white">1- Majlis `Amila Meeting</font></span><td></tr>
		<tr><td>
			<table border="0" width="580" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0" width="150">Meeting held<br>(Please check)</td>
					<td>
						<? if ($amila_meeting1=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="amila_meeting1" class="boxCSS1">
						<? } else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="amila_meeting1" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0" width="150">Date<br>(YYYY-MM-DD)</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="10" size="10" name="meeting1_date" value="<? echo $meeting1_date; ?>" class="boxCSS1"></td>
					<td bgcolor="#c0c0c0">Attendance</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="meeting1_attendance" value="<? echo $meeting1_attendance; ?>" class="boxCSS1"></td>
					<td bgcolor="#c0c0c0" width="150">Meeting attached<br>(Please check)</td>
					<td>
						<? if ($meeting1_attachment !="") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="meeting1_checkbox" class="boxCSS1">
						<? } else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="meeting1_checkbox" class="boxCSS1">
						<? }?>
					</td>
				</tr>
				<tr><td colspan="8">&nbsp;</td></tr>
				<tr><td colspan="8" bgcolor="#c0c0c0">Minutes of meeting 
				<? if (($read_only=="") && ($allow_attachments=="1")) {?>
				or attach meeting minutes below
				<? } ?>
				<td></tr>
				<tr><td colspan="8"><textarea class="BoxCSS1" name="meeting1_minutes" <? echo $read_only; ?> cols="68" rows="4"><? echo $meeting1_minutes; ?></textarea></td></tr>
				<tr><td colspan="8">&nbsp;</td></tr>
				<? if (($read_only=="") && ($allow_attachments=="1")) {?>
				<tr><td colspan="8" bgcolor="#c0c0c0">
					Meeting Minutes:&nbsp;
					<input type="file" name="meeting1_attachment" class="boxCSS1">
				</td></tr>
				<? }?>
				<tr><td colspan="8">
					<? if (($gsrid) && ($meeting1_attachment)) {?>
						<a href="attachments/<? print "$meeting1_attachment\"";?> target="_blank"><? print "$meeting1_attachment";?></a>
					<? } ?> &nbsp;
				</td></tr>
			</table>
		</td><tr>
		<? // Amila Meeting2?>
		<tr><td bgcolor="#000000"><span class="pageheader"><font color="white">2- Majlis `Amila Meeting</font></span><td></tr>
		<tr><td>
			<table border="0" width="580" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0" width="150">Meeting held<br>(Please check)</td>
					<td>
						<? if ($amila_meeting2=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="amila_meeting2" class="boxCSS1">
						<? } else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="amila_meeting2" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0" width="150">Date<br>(YYYY-MM-DD)</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="10" size="10" name="meeting2_date" value="<? echo $meeting2_date; ?>" class="boxCSS1"></td>
					<td bgcolor="#c0c0c0">Attendance</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="meeting2_attendance" value="<? echo $meeting2_attendance; ?>" class="boxCSS1"></td>
					<td bgcolor="#c0c0c0" width="150">Meeting attached<br>(Please check)</td>
					<td>
						<? if ($meeting2_attachment !="") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="meeting2_checkbox" class="boxCSS1">
						<? } else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="meeting2_checkbox" class="boxCSS1">
						<? }?>
					</td>
				</tr>
				<tr><td colspan="8">&nbsp;</td></tr>
				<tr><td colspan="8" bgcolor="#c0c0c0">Minutes of meeting
				<? if (($read_only=="") && ($allow_attachments=="1")) {?>
				 or attach meeting minutes below
				<? }?>
				<td></tr>
				<tr><td colspan="8"><textarea class="BoxCSS1" name="meeting2_minutes" <? echo $read_only; ?> cols="68" rows="4"><? echo $meeting2_minutes; ?></textarea></td></tr>
				<tr><td colspan="8">&nbsp;</td></tr>
				<? if (($read_only=="") && ($allow_attachments=="1")) {?>
				<tr><td colspan="8" bgcolor="#c0c0c0">
					Meeting Minutes:&nbsp;
					<input type="file" name="meeting2_attachment" class="boxCSS1">
				</td></tr>
				<? }?>
				<tr><td colspan="8">
					<? if (($gsrid) && ($meeting2_attachment)) {?>
						<a href="attachments/<? print "$meeting2_attachment\"";?> target="_blank"><? print "$meeting2_attachment";?></a>
					<? } ?> &nbsp;
				</td></tr>
			</table>
		</td><tr>
		<? // General body Meeting?>
		<tr><td bgcolor="#000000"><span class="pageheader"><font color="white">General Body Meeting</font></span><td></tr>
		<tr><td>
			<table border="0" width="580" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0" width="150">Meeting held<br>(Please check)</td>
					<td>
						<? if ($gbody_meeting =="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="gbody_meeting" class="boxCSS1">
						<? } else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="gbody_meeting" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0" width="150">Date<br>(YYYY-MM-DD)</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="10" size="10" name="gbody_meeting_date" value="<? echo $gbody_meeting_date; ?>" class="boxCSS1"></td>
					<td bgcolor="#c0c0c0">Location:</td>
					<td colspan="3"><input type="text" <? echo $read_only; ?>  maxlength="255" size="20" name="gbody_meeting_location" value="<? echo $gbody_meeting_location; ?>" class="boxCSS1"></td>
				</tr>
				<tr><td colspan="8">&nbsp;</td></tr>
				<tr>
					<td bgcolor="#c0c0c0" width="150">Total Attendance:</td>
					<? $gbody_total_attendance = $gents + $ladies + $children;?>
					<td><input type="text" readonly title="Read only" maxlength="3" size="3" name="gbody_total_attendance" value="<? echo $gbody_total_attendance; ?>" class="boxCSS1"></td>
					<td bgcolor="#c0c0c0" width="150">Gents:</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="gents" value="<? echo $gents; ?>" class="boxCSS1" onblur="getTotal();"></td>
					<td bgcolor="#c0c0c0">Ladies:</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="ladies" value="<? echo $ladies; ?>" class="boxCSS1" onblur="getTotal();"></td>
					<td bgcolor="#c0c0c0">Children:</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="children" value="<? echo $children; ?>" class="boxCSS1" onblur="getTotal();"></td>
				</tr>
				<tr><td colspan="8">&nbsp;</td></tr>
				<tr>
					<td bgcolor="#c0c0c0" width="150">Total Adult<br>Members</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="total_adults" value="<? echo $total_adults; ?>" class="boxCSS1"></td>
					<td colspan="5" bgcolor="#c0c0c0">No. of members who did not attend any meeting in last 1 year</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="not_attend_last_year" value="<? echo $not_attend_last_year; ?>" class="boxCSS1"></td>
				</tr>
				<tr><td colspan="8">&nbsp;</td></tr>
				<tr><td colspan="8" bgcolor="#c0c0c0">Actions taken to motivate such members to attend<td></tr>
				<tr><td colspan="8"><textarea class="BoxCSS1" name="action_taken" <? echo $read_only; ?> cols="68" rows="4"><? echo $action_taken; ?></textarea></td></tr>
				<tr><td colspan="8">&nbsp;</td></tr>
				<tr><td colspan="8" bgcolor="#c0c0c0">Brief report of the meeting 
				<? if (($read_only=="") && ($allow_attachments=="1")) {?>
				or attach report below
				<? }?>
				<td></tr>
				<tr><td colspan="8"><textarea class="BoxCSS1" name="gbody_brief_report" <? echo $read_only; ?> cols="68" rows="4"><? echo $gbody_brief_report; ?></textarea></td></tr>
				<tr><td>&nbsp;</td></tr>
				<? if (($read_only=="") && ($allow_attachments=="1")) {?>
				<tr><td colspan="8" bgcolor="#c0c0c0">
					Meeting report:&nbsp;
					<input type="file" name="gbody_meeting_attachment" class="boxCSS1">
				</td></tr>
				<? }?>
				<tr><td colspan="8">
					<? if (($gsrid) && ($gbody_meeting_attachment)) {?>
						<a href="attachments/<? print "$gbody_meeting_attachment\"";?> target="_blank"><? print "$gbody_meeting_attachment";?></a>
					<? } ?> &nbsp;
				</td></tr>
			</table>
		</td><tr>
		<? // Tajnid?>
		<tr><td bgcolor="#000000"><span class="pageheader"><font color="white">Tajnid (Attach details & the change of address form)</font></span><td></tr>
		<tr><td>
			<table border="0" width="580" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0" width="140">Total Households<br>in Jama`at:</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="total_households" value="<? echo $total_households; ?>" class="boxCSS1"></td>
					<td bgcolor="#c0c0c0" <? echo $read_only; ?>  width="140">Total Members:</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="4" size="4" name="total_members" value="<? echo $total_members; ?>" class="boxCSS1"></td>
					<td bgcolor="#c0c0c0" width="150">No. of Members<br>Moved Out:</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="total_moved_out" value="<? echo $total_moved_out; ?>" class="boxCSS1"></td>
					<td bgcolor="#c0c0c0" width="150">No. of Members<br>Moved In:</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="total_moved_in" value="<? echo $total_moved_in; ?>" class="boxCSS1"></td>
				</tr>
				<tr><td colspan="8">&nbsp;</td></tr>
				<? if (($read_only=="") && ($allow_attachments=="1")) {?>
				<tr><td colspan="8" bgcolor="#c0c0c0">
					Change of address form:&nbsp;
					<input type="file" name="tajneed_attachment" class="boxCSS1">
				</td></tr>
				<? }?>
				<tr><td colspan="8">
					<? if (($gsrid) && ($tajneed_attachment)) {?>
						<a href="attachments/<? print "$tajneed_attachment\"";?> target="_blank"><? print "$tajneed_attachment";?></a>
					<? } ?> &nbsp;
				</td></tr>
			</table>
		</td><tr>
		<? // Visits?>
		<tr><td bgcolor="#000000"><span class="pageheader"><font color="white">Visits (Attach report if required)</font></span><td></tr>
		<tr><td>
			<table border="0" width="580" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0">No. Households visited by President / General Secretary this month:</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="households_visit_by_p_gs" value="<? echo $households_visit_by_p_gs; ?>" class="boxCSS1"></td>
					<td bgcolor="#c0c0c0">Comments:</td>
					<td><input type="text" <? echo $read_only; ?>  size="10" name="visit1_comments" value="<? echo $visit1_comments; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0">No. Households visited by Members of Majlis `Amila this month:</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="households_visit_by_amila" value="<? echo $households_visit_by_amila; ?>" class="boxCSS1"></td>
					<td bgcolor="#c0c0c0">Comments:</td>
					<td><input type="text" <? echo $read_only; ?>  size="10" name="visit2_comments" value="<? echo $visit2_comments; ?>" class="boxCSS1"></td>
				</tr>
				<tr><td colspan="8">&nbsp;</td></tr>
				<? if (($read_only=="") && ($allow_attachments=="1")) {?>
				<tr><td colspan="8" bgcolor="#c0c0c0">
					Attach report:&nbsp;
					<input type="file" name="visit_attachment" class="boxCSS1">
				</td></tr>
				<? }?>
				<tr><td colspan="8">
					<? if (($gsrid) && ($visit_attachment)) {?>
						<a href="attachments/<? print "$visit_attachment\"";?> target="_blank"><? print "$visit_attachment";?></a>
					<? } ?> &nbsp;
				</td></tr>
			</table>
		</td><tr>
		<? // Saiqin?>
		<tr><td bgcolor="#000000"><span class="pageheader"><font color="white">Report of Sa'iqin (One Sa'iq should have approximately 10 households in his Hizb)</font></span><td></tr>
		<tr><td>
			<table border="0" width="580" class="BoxCSS1">
				<tr bgcolor="#c0c0c0">
					<td width="20">No.</td>
					<td bgcolor="#c0c0c0" width="140">Name of Sa'iq</td>
					<td bgcolor="#c0c0c0" width="140">No. of households<br>visited in person</td>
					<td bgcolor="#c0c0c0" width="140">No. of households<br>contacted by phone</td>
					<td bgcolor="#c0c0c0" width="140">No. of households<br>messages conveyed</td>
				</tr>
				<tr>
					<td>1-</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="50" size="20" name="saiq1" value="<? echo $saiq1; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq1_visit_in_person" value="<? echo $saiq1_visit_in_person; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq1_contact_by_phone" value="<? echo $saiq1_contact_by_phone; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq1_message" value="<? echo $saiq1_message; ?>" class="boxCSS1"></td>
				</tr>
				<tr bgcolor="#F2F8F1">
					<td>2-</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="50" size="20" name="saiq2" value="<? echo $saiq2; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq2_visit_in_person" value="<? echo $saiq2_visit_in_person; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq2_contact_by_phone" value="<? echo $saiq2_contact_by_phone; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq2_message" value="<? echo $saiq2_message; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>3-</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="50" size="20" name="saiq3" value="<? echo $saiq3; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq3_visit_in_person" value="<? echo $saiq3_visit_in_person; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq3_contact_by_phone" value="<? echo $saiq3_contact_by_phone; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq3_message" value="<? echo $saiq3_message; ?>" class="boxCSS1"></td>
				</tr>
				<tr bgcolor="#F2F8F1">
					<td>4-</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="50" size="20" name="saiq4" value="<? echo $saiq4; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq4_visit_in_person" value="<? echo $saiq4_visit_in_person; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq4_contact_by_phone" value="<? echo $saiq4_contact_by_phone; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq4_message" value="<? echo $saiq4_message; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>5-</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="50" size="20" name="saiq5" value="<? echo $saiq5; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq5_visit_in_person" value="<? echo $saiq5_visit_in_person; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq5_contact_by_phone" value="<? echo $saiq5_contact_by_phone; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq5_message" value="<? echo $saiq5_message; ?>" class="boxCSS1"></td>
				</tr>
				<tr bgcolor="#F2F8F1">
					<td>6-</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="50" size="20" name="saiq6" value="<? echo $saiq6; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq6_visit_in_person" value="<? echo $saiq6_visit_in_person; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq6_contact_by_phone" value="<? echo $saiq6_contact_by_phone; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq6_message" value="<? echo $saiq6_message; ?>" class="boxCSS1"></td>
				</tr>
                                <tr bgcolor="#F2F8F1">
                                        <td>7-</td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="50" size="20" name="saiq7" value="<? echo $saiq7; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq7_visit_in_person" value="<? echo $saiq7_visit_in_person; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq7_contact_by_phone" value="<? echo $saiq7_contact_by_phone; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq7_message" value="<? echo $saiq7_message; ?>" class="boxCSS1"></td>
                                </tr>
                                <tr bgcolor="#F2F8F1">
                                        <td>8-</td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="50" size="20" name="saiq8" value="<? echo $saiq8; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq8_visit_in_person" value="<? echo $saiq8_visit_in_person; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq8_contact_by_phone" value="<? echo $saiq8_contact_by_phone; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq8_message" value="<? echo $saiq8_message; ?>" class="boxCSS1"></td>
                                </tr>
                                <tr bgcolor="#F2F8F1">
                                        <td>9-</td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="50" size="20" name="saiq9" value="<? echo $saiq9; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq9_visit_in_person" value="<? echo $saiq9_visit_in_person; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq9_contact_by_phone" value="<? echo $saiq9_contact_by_phone; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq9_message" value="<? echo $saiq9_message; ?>" class="boxCSS1"></td>
                                </tr>
                                <tr bgcolor="#F2F8F1">
                                        <td>10-</td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="50" size="20" name="saiq10" value="<? echo $saiq10; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq10_visit_in_person" value="<? echo $saiq10_visit_in_person; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq10_contact_by_phone" value="<? echo $saiq10_contact_by_phone; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq10_message" value="<? echo $saiq10_message; ?>" class="boxCSS1"></td>
                                </tr>
                                <tr bgcolor="#F2F8F1">
                                        <td>11-</td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="50" size="20" name="saiq11" value="<? echo $saiq11; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq11_visit_in_person" value="<? echo $saiq11_visit_in_person; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq11_contact_by_phone" value="<? echo $saiq11_contact_by_phone; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq11_message" value="<? echo $saiq11_message; ?>" class="boxCSS1"></td>
                                </tr>
                                <tr bgcolor="#F2F8F1">
                                        <td>12-</td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="50" size="20" name="saiq12" value="<? echo $saiq12; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq12_visit_in_person" value="<? echo $saiq12_visit_in_person; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq12_contact_by_phone" value="<? echo $saiq12_contact_by_phone; ?>" class="boxCSS1"></td>
                                        <td><input type="text" <? echo $read_only; ?>  maxlength="3" size="3" name="saiq12_message" value="<? echo $saiq12_message; ?>" class="boxCSS1"></td>
                                </tr>

			</table>
		</td></tr>


<!-- /////// EXCLUDE FOLLOWING DEPARTMENT REPORT SUBMITTED QUESTIONS ////// 
		<? // Submitted Reports?>
		<tr><td bgcolor="#000000"><span class="pageheader"><font color="white">Monthly Reports Submitted. (Please check)</font></span><td></tr>
		<tr><td>
			<table border="0" width="580" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0">1-</td>
					<td bgcolor="#c0c0c0">General Secretary</td>
					<td >
						<? if ($gs=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="gs" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="gs" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0">8-</td>
					<td bgcolor="#c0c0c0">Umur Kharijiyya</td>
					<td >
						<? if ($uk=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="uk" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="uk" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0">15-</td>
					<td bgcolor="#c0c0c0">Ja`idad</td>
					<td >
						<? if ($jd=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="jd" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="jd" class="boxCSS1">
						<? }?>
					</td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0">2-</td>
					<td bgcolor="#c0c0c0">Tabligh</td>
					<td >
						<? if ($th=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="th" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="th" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0">9-</td>
					<td bgcolor="#c0c0c0">Umur Amma</td>
					<td >
						<? if ($ua=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="ua" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="ua" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0">16-</td>
					<td bgcolor="#c0c0c0">Waqf Nau</td>
					<td >
						<? if ($wn=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="wn" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="wn" class="boxCSS1">
						<? }?>
					</td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0">3-</td>
					<td bgcolor="#c0c0c0">Tarbiyat</td>
					<td >
						<? if ($tt=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="tt" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="tt" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0">10-</td>
					<td bgcolor="#c0c0c0">Finance</td>
					<td >
						<? if ($fe=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="fe" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="fe" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0">17-</td>
					<td bgcolor="#c0c0c0">Zira`at</td>
					<td >
						<? if ($zt=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="zt" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="zt" class="boxCSS1">
						<? }?>
					</td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0">4-</td>
					<td bgcolor="#c0c0c0">Ta`lim</td>
					<td >
						<? if ($tm=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="tm" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="tm" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0">11-</td>
					<td bgcolor="#c0c0c0">Diyafat</td>
					<td >
						<? if ($dt=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="dt" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="dt" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0">18-</td>
					<td bgcolor="#c0c0c0">San`at-o-Tijarat</td>
					<td >
						<? if ($st=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="st" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="st" class="boxCSS1">
						<? }?>
					</td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0">5-</td>
					<td bgcolor="#c0c0c0">Isha`at</td>
					<td >
						<? if ($it=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="it" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="it" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0">12-</td>
					<td bgcolor="#c0c0c0">Wasaya</td>
					<td >
						<? if ($wa=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="wa" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="wa" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0">19-</td>
					<td bgcolor="#c0c0c0">Muhasib</td>
					<td >
						<? if ($mb=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="mb" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="mb" class="boxCSS1">
						<? }?>
					</td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0">6-</td>
					<td bgcolor="#c0c0c0">Sam`i wa Basri</td>
					<td >
						<? if ($sb=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="sb" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="sb" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0">13-</td>
					<td bgcolor="#c0c0c0">Tahrik Jadid</td>
					<td >
						<? if ($tj=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="tj" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="tj" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0">20-</td>
					<td bgcolor="#c0c0c0">Amin</td>
					<td >
						<? if ($an=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="an" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="an" class="boxCSS1">
						<? }?>
					</td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0">7-</td>
					<td bgcolor="#c0c0c0">Rishta Nata</td>
					<td >
						<? if ($ra=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="ra" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="ra" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0">14-</td>
					<td bgcolor="#c0c0c0">Waqf Jadid</td>
					<td >
						<? if ($wj=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="wj" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="wj" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0">21-</td>
					<td bgcolor="#c0c0c0">Additional Waqf Jadid</td>
					<td >
						<? if ($ws=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="ws" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="ws" class="boxCSS1">
						<? }?>
					</td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0">22-</td>
					<td bgcolor="#c0c0c0">Ta’limul Qur’an Waqf`Ardi</td>
					<td >
						<? if ($ti=="1") {?>
							<input type="checkbox" <? echo $read_only; ?>  checked name="ti" class="boxCSS1">
						<?} else {?>
							<input type="checkbox" <? echo $read_only; ?>  name="ti" class="boxCSS1">
						<? }?>
					</td>
					<td bgcolor="#c0c0c0" colspan="6">&nbsp;</td>
				</tr>
			</table>
		</td></tr>

/////// EXCLUDE ABOVE DEPARTMENT REPORT SUBMITTED QUESTION ////// -->

		</table>
				<input type="hidden" name="gsrid" value=<? print "\"$gsrid\""; ?>>
				<input type="hidden" name="fmeeting1_attachment" value=<? print "\"$meeting1_attachment\""; ?>>
				<input type="hidden" name="fmeeting2_attachment" value=<? print "\"$meeting2_attachment\""; ?>>
				<input type="hidden" name="fgbody_meeting_attachment" value=<? print "\"$gbody_meeting_attachment\""; ?>>
				<input type="hidden" name="ftajneed_attachment" value=<? print "\"$tajneed_attachment\""; ?>>
				<input type="hidden" name="fvisit_attachment" value=<? print "\"$visit_attachment\""; ?>>
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
