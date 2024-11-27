<? if ($tr=="Y") {?>
<script language="JavaScript" type="text/JavaScript">
<!--
/*
function sumTotal(obj) {
	var x=document.getElementById(obj).value
	if (isNaN(x){
		document.getElementById(obj).value = 0;
	}
	alert (document.getElementById(obj).value);
	alert(document.getElementById(obj).name);
	//b.value = a.value + obj.value;
}
*/
//-->
</script>

	<? //print "$branch_code $report_code $month $year<br>";
	//print "$branch $report_code $month $year<br>";
	//Check if report already exist
	if (($report_code) && ($month) && ($year)) {
		if (($user_level=="N") && (!$rid)) {
		  $gsquery0 = "SELECT rid FROM ami_tabshir_reports WHERE branch_code = '$branch' and month = '$month' and year = '$year'";
		} else if ($user_level=="N") {
		  $gsquery0 = "SELECT rid FROM ami_tabshir_reports WHERE branch_code = '$branch_code1' and month = '$month' and year = '$year'";
		} else {
		  $gsquery0 = "SELECT rid FROM ami_tabshir_reports WHERE branch_code = '$branch_code' and month = '$month' and year = '$year'";
		}

		//print "gsquery0:$gsquery0<br>";
		$gsresult0 = @mysql_db_query($dbname,$gsquery0,$id_link);
		if ($gsresult0){
			$gsrow0 = mysql_fetch_array($gsresult0);
			$gsrid = $gsrow0['rid'];
		}
		
		if ($user_level=="N") {
			//Branches sum
			$cnt = "SELECT sum(daeen_this_month) daeen_this_montha,
				 sum(daeen_total) daeen_totala,
				 sum(people_intro_islam) people_intro_islama,
				 sum(hq_sold_this_month) hq_sold_this_montha,
				 sum(hq_dist_this_month) hq_dist_this_montha,
				 sum(tc_this_month) tc_this_montha,
				 sum(participation) participationa,
				 sum(bait_this_month) bait_this_montha,
				 sum(joined_nat) joined_nata,
				 sum(exhib_this_month) exhib_this_montha,
				 sum(non_saw_exhib) non_saw_exhiba,
				 sum(stall_this_month) stall_this_montha,
				 sum(non_visited_stall) non_visited_stalla,
				 sum(tv_prog_this_month) tv_prog_this_montha,
				 sum(total_hours) total_hoursa,
				 sum(rc_msg_this_month) rc_msg_this_montha,
				 sum(news_paper) news_papera,
				 sum(our_news_pub) our_news_puba
				 FROM ami_tabshir_reports 
				 WHERE branch_code != '$branch_code' and month = '$month' 
				 and year = '$year'";
			$sumresult = @mysql_db_query($dbname,$cnt,$id_link);
			if ($sumresult){
				$sumrow = mysql_fetch_array($sumresult);
			}
			//Total sum
			$cnt = "SELECT sum(daeen_this_month) daeen_this_monthb,
				 sum(daeen_total) daeen_totalb,
				 sum(people_intro_islam) people_intro_islamb,
				 sum(hq_sold_this_month) hq_sold_this_monthb,
				 sum(hq_dist_this_month) hq_dist_this_monthb,
				 sum(tc_this_month) tc_this_monthb,
				 sum(participation) participationb,
				 sum(bait_this_month) bait_this_monthb,
				 sum(joined_nat) joined_natb,
				 sum(exhib_this_month) exhib_this_monthb,
				 sum(non_saw_exhib) non_saw_exhibb,
				 sum(stall_this_month) stall_this_monthb,
				 sum(non_visited_stall) non_visited_stallb,
				 sum(tv_prog_this_month) tv_prog_this_monthb,
				 sum(total_hours) total_hoursb,
				 sum(rc_msg_this_month) rc_msg_this_monthb,
				 sum(news_paper) news_paperb,
				 sum(our_news_pub) our_news_pubb
				 FROM ami_tabshir_reports 
				 WHERE month = '$month' 
				 and year = '$year'";
			$sumallresult = @mysql_db_query($dbname,$cnt,$id_link);
			if ($sumallresult){
				$sumallrow = mysql_fetch_array($sumallresult);
			}
		}
	}
	//print "gsrid=$gsrid<br>";
	//New report or existing report
	if (($report_code) || ($gsrid)) {
	  if ($gsrid){
		  $query0 = "SELECT * FROM ami_tabshir_reports WHERE rid = '$gsrid'";
		  //print "query0:$query0<br>";
		  $result0 = @mysql_db_query($dbname,$query0,$id_link);
		  if ($result0){
			$row0 = mysql_fetch_array($result0);
			  $gsbranch_code = $row0['branch_code'];
			  $gsmonth = $row0['month'];
			  $gsyear = $row0['year'];
			  
			  $daeen_this_month = $row0['daeen_this_month'];
			  $daeen_total = $row0['daeen_total'];
			  $people_intro_islam = $row0['people_intro_islam'];

			  $hq_sold_this_month = $row0['hq_sold_this_month'];
			  $hq_dist_this_month = $row0['hq_dist_this_month'];
			  $intro_dig = $row0['intro_dig'];
			  $trans_remark = $row0['trans_remark'];
			  $pub_material_attachment = $row0['pub_material_attachment'];

			  $tc_this_month = $row0['tc_this_month'];
			  $participation = $row0['participation'];
			  $train = $row0['train'];
			  $desc_program = $row0['desc_program'];

			  $bait_this_month = $row0['bait_this_month'];
			  $joined_nat = $row0['joined_nat'];
			  $name_nat = $row0['name_nat'];

			  $gen_pub = $row0['gen_pub'];

			  $exhib_this_month = $row0['exhib_this_month'];
			  $non_saw_exhib = $row0['non_saw_exhib'];
			  $exhib_details = $row0['exhib_details'];
			  $hq_trans_remark = $row0['hq_trans_remark'];
			  $pub_hq_material_attachment = $row0['pub_hq_material_attachment'];

			  $stall_this_month = $row0['stall_this_month'];
			  $non_visited_stall = $row0['non_visited_stall'];
			  $stall_detail = $row0['stall_detail'];

			  $tv_prog_this_month = $row0['tv_prog_this_month'];
			  $total_hours = $row0['total_hours'];
			  $rc_msg_this_month = $row0['rc_msg_this_month'];
			  $news_paper = $row0['news_paper'];
			  $our_news_pub = $row0['our_news_pub'];
			  $imp_of_news = $row0['imp_of_news'];
			  $people_res_to_news = $row0['people_res_to_news'];

			  $imp_person_statement = $row0['imp_person_statement'];
			  $new_area = $row0['new_area'];

			  $med_assist = $row0['med_assist'];
			  $waqf_aarzi = $row0['waqf_aarzi'];
			  $assist_hospitals = $row0['assist_hospitals'];

		  } else {
			print "Error: invalid report id!";
			exit();
		  }
	  } ?>
		<table border="0" width="600">
		<? // ?>
		<!--<tr><td ><span class="pageheader"><font color="red">Report is underconstruction, please try later . . .</font></span><td></tr>-->
		<? // if ($user_level=="N") {?>
		<tr><td bgcolor=""><span class="pageheader"><font color="">1- Daeen Ilallah:</font></span><td></tr>
		<tr><td>
			<table border="0" width="600" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0" >a. Number of Daeen during this month.</td>
					<td bgcolor="#c0c0c0" nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="daeen_this_month" value="<? echo $daeen_this_month; ?>" class="boxCSS1" ><!--onblur="sumTotal('daeen_this_month')"-->
					<? if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="daeen_this_montha" value="<? echo $sumrow["daeen_this_montha"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="daeen_this_monthb" value="<? echo $sumallrow["daeen_this_monthb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td >b. Total Daeen at present time.</td>
					<td  nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="daeen_total" value="<? echo $daeen_total; ?>" class="boxCSS1">
					<? if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="daeen_totala" value="<? echo $sumrow["daeen_totala"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="daeen_totalb" value="<? echo $sumallrow["daeen_totalb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0">c. How many people were introduced to Islam during this period?</td>
					<td bgcolor="#c0c0c0" nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="people_intro_islam" value="<? echo $people_intro_islam; ?>" class="boxCSS1">
					<?if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="people_intro_islama" value="<? echo $sumrow["people_intro_islama"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="people_intro_islamb" value="<? echo $sumallrow["people_intro_islamb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
			</table>
		</td><tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td bgcolor=""><span class="pageheader"><font color="">2. Publication and distribution of the Holy Quran:</font></span><td></tr>
		<tr><td>
			<table border="0" width="600" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0"  >a. How many copies of the Holy Quran were sold during the month?</td>
					<td bgcolor="#c0c0c0" nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="hq_sold_this_month" value="<? echo $hq_sold_this_month; ?>" class="boxCSS1">
					<?if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="hq_sold_this_montha" value="<? echo $sumrow["hq_sold_this_montha"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="hq_sold_this_monthb" value="<? echo $sumallrow["hq_sold_this_monthb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td >b. How many copies of the Holy Qur’an were distributed during the month?</td>
					<td  nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="hq_dist_this_month" value="<? echo $hq_dist_this_month; ?>" class="boxCSS1">
					<?if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="hq_dist_this_montha" value="<? echo $sumrow["hq_dist_this_montha"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="hq_dist_this_monthb" value="<? echo $sumallrow["hq_dist_this_monthb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0" colspan=2>c. Names and brief introduction of the dignitaries to whom the Holy Qur’an was presented as gift.</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="intro_dig" <? echo $read_only; ?> cols="74" rows="4"><? echo $intro_dig; ?></textarea></td></tr>
				<tr>
					<td bgcolor="#c0c0c0" colspan=2>d. Any remarks made by the people about the translation of the Holy Qur’an. Please describe in their own words. (If remarks were published, please provide the published material)</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="trans_remark" <? echo $read_only; ?> cols="74" rows="4"><? echo $trans_remark; ?></textarea></td></tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<? if (($read_only=="") && ($allow_attachments=="1")) {?>

				<tr><td colspan="2" bgcolor="#c0c0c0">
					Attach Published Material:&nbsp;
					<input type="file" name="pub_material_attachment" class="boxCSS1">
				</td></tr>
				<? }?>
				<tr><td colspan="2">
					<? if (($gsrid) && ($pub_material_attachment)) {?>
						<a href="attachments/<? print "$pub_material_attachment\"";?> target="_blank"><? print "$pub_material_attachment";?></a>
					<? } ?> &nbsp;
				</td></tr>
			</table>
		</td><tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td bgcolor=""><span class="pageheader"><font color="">3. Tarbiyyati classes and/or refresher courses:</font></span><td></tr>
		<tr><td>
			<table border="0" width="600" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0"  >a. How Many Tarbiyyati Classes and Refresher courses were arranged during the month?</td>
					<td bgcolor="#c0c0c0" nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="tc_this_month" value="<? echo $tc_this_month; ?>" class="boxCSS1">
					<? if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="tc_this_montha" value="<? echo $sumrow["tc_this_montha"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="tc_this_monthb" value="<? echo $sumallrow["tc_this_monthb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td >b. How many Ahmadies participated in these Events?</td>
					<td  nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="participation" value="<? echo $participation; ?>" class="boxCSS1">
					<? if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="participationa" value="<? echo $sumrow["participationa"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="participationb" value="<? echo $sumallrow["participationb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0" colspan=2>c. How many train the trainer classes were held during the year? Give details.</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="train" <? echo $read_only; ?> cols="74" rows="4"><? echo $train; ?></textarea></td></tr>
				<tr>
					<td bgcolor="#c0c0c0" colspan=2>d. Please give a brief description of these programs. Explaining the response and fruits received from these and any other.</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="desc_program" <? echo $read_only; ?> cols="74" rows="4"><? echo $desc_program; ?></textarea></td></tr>
			</table>
		</td><tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td bgcolor=""><span class="pageheader"><font color="">4. Bait:</font></span><td></tr>
		<tr><td>
			<table border="0" width="600" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0"  >a. Total number of Baits during this month.</td>
					<td bgcolor="#c0c0c0" nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="bait_this_month" value="<? echo $bait_this_month; ?>" class="boxCSS1">
					<? if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="bait_this_montha" value="<? echo $sumrow["bait_this_montha"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="bait_this_monthb" value="<? echo $sumallrow["bait_this_monthb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td >b. Total number of Nationalities who joined Ahmadiyyat during the year.</td>
					<td  nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="joined_nat" value="<? echo $joined_nat; ?>" class="boxCSS1">
					<? if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="joined_nata" value="<? echo $sumrow["joined_nata"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="joined_natb" value="<? echo $sumallrow["joined_natb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0" colspan=2>c. List names of these Nationalities.</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="name_nat" <? echo $read_only; ?> cols="74" rows="4"><? echo $name_nat; ?></textarea></td></tr>
			</table>
		</td><tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td bgcolor=""><span class="pageheader"><font color="">5. Assistance to the Destitute, Widows or Orphans:</font></span><td></tr>
		<tr><td>
			<table border="0" width="600" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0" colspan=2>a. The work during this month maybe explained in details with the impressions of the general public about these efforts.</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="gen_pub" <? echo $read_only; ?> cols="74" rows="4"><? echo $gen_pub; ?></textarea></td></tr>
			</table>
		</td><tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td bgcolor=""><span class="pageheader"><font color="">6. Exhibitions:</font></span><td></tr>
		<tr><td>
			<table border="0" width="600" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0"  >a. How many exhibitions were held during the month?</td>
					<td bgcolor="#c0c0c0" nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="exhib_this_month" value="<? echo $exhib_this_month; ?>" class="boxCSS1">
					<? if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="exhib_this_montha" value="<? echo $sumrow["exhib_this_montha"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="exhib_this_monthb" value="<? echo $sumallrow["exhib_this_monthb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td >b. How many Non-Ahmadies saw these exhibitions?</td>
					<td  nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="non_saw_exhib" value="<? echo $non_saw_exhib; ?>" class="boxCSS1">
					<? if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="non_saw_exhiba" value="<? echo $sumrow["non_saw_exhiba"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="non_saw_exhibb" value="<? echo $sumallrow["non_saw_exhibb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0" colspan=2>c. Please give a brief description of these exhibitions explaining the response and fruits received from these exhibitions and any other faith enhancing events?</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="exhib_details" <? echo $read_only; ?> cols="74" rows="4"><? echo $exhib_details; ?></textarea></td></tr>
				<tr>
					<td bgcolor="#c0c0c0" colspan=2>d. Any remarks made by the people about the translation of the Holy Qur’an. Please describe in their own words. (If remarks were published, please provide the published material)</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="hq_trans_remark" <? echo $read_only; ?> cols="74" rows="4"><? echo $hq_trans_remark; ?></textarea></td></tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<? if (($read_only=="") && ($allow_attachments=="1")) {?>
				<tr><td colspan="2" bgcolor="#c0c0c0">
					Attach Published Material:&nbsp;
					<input type="file" name="pub_hq_material_attachment" class="boxCSS1">
				</td></tr>
				<? }?>
				<tr><td colspan="2">
					<? if (($gsrid) && ($pub_hq_material_attachment)) {?>
						<a href="attachments/<? print "$pub_hq_material_attachment\"";?> target="_blank"><? print "$pub_hq_material_attachment";?></a>
					<? } ?> &nbsp;
				</td></tr>
			</table>
		</td><tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td bgcolor=""><span class="pageheader"><font color="">7. Book Stalls For Tabligh:</font></span><td></tr>
		<tr><td>
			<table border="0" width="600" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0"  >a. How Many bookstalls were managed during the month?</td>
					<td bgcolor="#c0c0c0" nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="stall_this_month" value="<? echo $stall_this_month; ?>" class="boxCSS1">
					<? if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="stall_this_montha" value="<? echo $sumrow["stall_this_montha"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="stall_this_monthb" value="<? echo $sumallrow["stall_this_monthb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td >b. How many Non Ahmadies visited these Bookstalls?</td>
					<td  nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="non_visited_stall" value="<? echo $non_visited_stall; ?>" class="boxCSS1">
					<? if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="non_visited_stalla" value="<? echo $sumrow["non_visited_stalla"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="non_visited_stallb" value="<? echo $sumallrow["non_visited_stallb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0" colspan=2>c. Please give a brief description of these Bookstalls explaining the response and fruits received from these Bookstalls and any other Faith enhancing events.</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="stall_detail" <? echo $read_only; ?> cols="74" rows="4"><? echo $stall_detail; ?></textarea></td></tr>
			</table>
		</td><tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td bgcolor=""><span class="pageheader"><font color="">8. Press And Media:</font></span><td></tr>
		<tr><td>
			<table border="0" width="600" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0"  >a. Total TV programs broadcasted during the month.</td>
					<td bgcolor="#c0c0c0" nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="tv_prog_this_month" value="<? echo $tv_prog_this_month; ?>" class="boxCSS1">
					<? if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="tv_prog_this_montha" value="<? echo $sumrow["tv_prog_this_montha"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="tv_prog_this_monthb" value="<? echo $sumallrow["tv_prog_this_monthb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td >b. Total Hours?</td>
					<td  nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="total_hours" value="<? echo $total_hours; ?>" class="boxCSS1">
					<? if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="total_hoursa" value="<? echo $sumrow["total_hoursa"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="total_hoursb" value="<? echo $sumallrow["total_hoursb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0"  >c. How many people received message of truth through these programs?</td>
					<td bgcolor="#c0c0c0" nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="rc_msg_this_month" value="<? echo $rc_msg_this_month; ?>" class="boxCSS1">
					<? if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="rc_msg_this_montha" value="<? echo $sumrow["rc_msg_this_montha"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="rc_msg_this_monthb" value="<? echo $sumallrow["rc_msg_this_monthb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td >d. Total number of Newspapers, which published our News during the month.</td>
					<td  nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="news_paper" value="<? echo $news_paper; ?>" class="boxCSS1">
					<? if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="news_papera" value="<? echo $sumrow["news_papera"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="news_paperb" value="<? echo $sumallrow["news_paperb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td bgcolor="#c0c0c0"  >e. How many of our news, articles, editorials were published during this period?</td>
					<td bgcolor="#c0c0c0" nowrap><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="our_news_pub" value="<? echo $our_news_pub; ?>" class="boxCSS1">
					<? if ($user_level=="N") {?>
						&nbsp;+&nbsp;<input type="text" readonly  maxlength="3" size="4" name="our_news_puba" value="<? echo $sumrow["our_news_puba"]; ?>" class="boxCSS1">
						&nbsp;=&nbsp;<input type="text" readonly  maxlength="3" size="4" name="our_news_pubb" value="<? echo $sumallrow["our_news_pubb"]; ?>" class="boxCSS1">
					<? }?>
					</td>
				</tr>
				<tr>
					<td  colspan=2>f. What were the impressions of these press and media items?</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="imp_of_news" <? echo $read_only; ?> cols="74" rows="4"><? echo $imp_of_news; ?></textarea></td></tr>
				<tr>
					<td bgcolor="#c0c0c0" colspan=2>g. What did people write or say in response to these news items? Note: Please give details with the circulation of each media source.</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="people_res_to_news" <? echo $read_only; ?> cols="74" rows="4"><? echo $people_res_to_news; ?></textarea></td></tr>
			</table>
		</td><tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td bgcolor=""><span class="pageheader"><font color="">9. Impressions of Important Personalities:</font></span><td></tr>
		<tr><td>
			<table border="0" width="600" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0" colspan=2>a. If any important personalities have issued a statement during this month praising Jama’at for any activities. The full text of speech or Author’s statement should be sent with all related details.</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="imp_person_statement" <? echo $read_only; ?> cols="74" rows="4"><? echo $imp_person_statement; ?></textarea></td></tr>
			</table>
		</td><tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td bgcolor=""><span class="pageheader"><font color="">10. Ahmadiyyat in New Areas:</font></span><td></tr>
		<tr><td>
			<table border="0" width="600" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0" colspan=2>a. Please give the details of first time the plant of Ahmadiyyat is born?</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="new_area" <? echo $read_only; ?> cols="74" rows="4"><? echo $new_area; ?></textarea></td></tr>
			</table>
		</td><tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td bgcolor=""><span class="pageheader"><font color="">11. Hospitals:</font></span><td></tr>
		<tr><td>
			<table border="0" width="600" class="BoxCSS1">
				<tr>
					<td bgcolor="#c0c0c0" colspan=2>a. Medical assistance provided by Ahmadi Doctors and Personals during the month.</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="med_assist" <? echo $read_only; ?> cols="74" rows="4"><? echo $med_assist; ?></textarea></td></tr>
				<tr>
					<td bgcolor="#c0c0c0" colspan=2>b. Any Waqf Aarzi work done by Ahmadi Doctors Personals.</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="waqf_aarzi" <? echo $read_only; ?> cols="74" rows="4"><? echo $waqf_aarzi; ?></textarea></td></tr>
				<tr>
					<td bgcolor="#c0c0c0" colspan=2>c. Any Assistance provided to Ahmadiyya hospitals (like Fazal-e-Omar Hospital Rabwah) in the form of equipment, construction or monetary help.</td>
				</tr>
				<tr><td colspan="2"><textarea class="BoxCSS1" name="assist_hospitals" <? echo $read_only; ?> cols="74" rows="4"><? echo $assist_hospitals; ?></textarea></td></tr>
			</table>
		</td><tr>
		<tr><td>
			<input type="hidden" name="gsrid" value=<? print "\"$gsrid\""; ?>>
			<input type="hidden" name="fpub_material_attachment" value=<? print "\"$pub_material_attachment\""; ?>>
			<input type="hidden" name="fpub_hq_material_attachment" value=<? print "\"$pub_hq_material_attachment\""; ?>>
		</td><tr>
		</table>
	<? } else { ?>
		<table width="100%" border="0" class="BoxCSS">
		<tr>
		<td>
		  <table width="600" border="0">
			<tr><td>The selected report is not available at this time. Please try later.</td></tr>
		  </table>
		 </td>
		 </tr>
		 </table>
	<? }
 } else {
 	include("login.php");
 }?>
