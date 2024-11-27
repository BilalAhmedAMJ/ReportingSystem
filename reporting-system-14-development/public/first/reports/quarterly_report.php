<link href="../style.css" rel="stylesheet" type="text/css">
<? if ($qr=="Y") {
	//print "$branch_code $report_code $month $year<br>";
	//print "$branch $report_code $month $year<br>";
	//Check if report already exist
	if (($report_code) && ($month) && ($year)) {
		if (($user_level=="N") && (!$rid)) {
		  $qrquery0 = "SELECT rid FROM ami_quarterly_reports WHERE branch_code = '$branch' and month = '$month' and year = '$year'";
		} else {
		  $qrquery0 = "SELECT rid FROM ami_quarterly_reports WHERE branch_code = '$branch_code' and month = '$month' and year = '$year'";
		}

		//print "qrquery0:$gsquery0<br>";
		$qrresult0 = @mysql_db_query($dbname,$qrquery0,$id_link);
		if ($qrresult0){
			$qrrow0 = mysql_fetch_array($qrresult0);
			$qrrid = $qrrow0['rid'];
		}
	}
	//print "qrrid=$qrrid<br>";
	//New report or existing report
	if (($report_code) || ($qrrid)) {
	  if ($qrrid){
		  $query0 = "SELECT * FROM ami_quarterly_reports WHERE rid = '$qrrid'";
		  //print "query0:$query0<br>";
		  $result0 = @mysql_db_query($dbname,$query0,$id_link);
		  if ($result0){
			$row0 = mysql_fetch_array($result0);
			  $qrbranch_code = $row0['branch_code'];
			  $qrmonth = $row0['month'];
			  $qryear = $row0['year'];

			  $quarter = $row0['quarter'];
			  $number_of_amila_meetings = $row0['number_of_amila_meetings'];
			  $total_amila_members = $row0['total_amila_members'];
			  $amila_meeting1_date = $row0['amila_meeting1_date'];
			  $amila_meeting2_date = $row0['amila_meeting2_date'];
			  $amila_meeting3_date = $row0['amila_meeting3_date'];
			  $amila_meeting1_venue = $row0['amila_meeting1_venue'];
			  $amila_meeting2_venue = $row0['amila_meeting2_venue'];
			  $amila_meeting3_venue = $row0['amila_meeting3_venue'];
			  $amila_meeting1_attendance = $row0['amila_meeting1_attendance'];
			  $amila_meeting2_attendance = $row0['amila_meeting2_attendance'];
			  $amila_meeting3_attendance = $row0['amila_meeting3_attendance'];
			  $number_of_gbody_meetings = $row0['number_of_gbody_meetings'];
			  $total_adult_members = $row0['total_adult_members'];
			  $number_of_members_not_attended = $row0['number_of_members_not_attended'];
			  $action_taken = $row0['action_taken'];
			  $gbody_meeting1_date = $row0['gbody_meeting1_date'];
			  $gbody_meeting2_date = $row0['gbody_meeting2_date'];
			  $gbody_meeting3_date = $row0['gbody_meeting3_date'];
			  $gbody_meeting1_venue = $row0['gbody_meeting1_venue'];
			  $gbody_meeting2_venue = $row0['gbody_meeting2_venue'];
			  $gbody_meeting3_venue = $row0['gbody_meeting3_venue'];
			  $gbody_gents_in_meeting1 = $row0['gbody_gents_in_meeting1'];
			  $gbody_gents_in_meeting2 = $row0['gbody_gents_in_meeting2'];
			  $gbody_gents_in_meeting3 = $row0['gbody_gents_in_meeting3'];
			  $gbody_ladies_in_meeting1 = $row0['gbody_ladies_in_meeting1'];
			  $gbody_ladies_in_meeting2 = $row0['gbody_ladies_in_meeting2'];
			  $gbody_ladies_in_meeting3 = $row0['gbody_ladies_in_meeting3'];
			  $gbody_children_in_meeting1 = $row0['gbody_children_in_meeting1'];
			  $gbody_children_in_meeting2 = $row0['gbody_children_in_meeting2'];
			  $gbody_children_in_meeting3 = $row0['gbody_children_in_meeting3'];
			  $total_households = $row0['total_households'];
			  $total_members = $row0['total_members'];
			  $members_moved_out = $row0['members_moved_out'];
			  $members_moved_in = $row0['members_moved_in'];
			  $household_visited_by_P_GS1 = $row0['household_visited_by_P_GS1'];
			  $household_visited_by_P_GS2 = $row0['household_visited_by_P_GS2'];
			  $household_visited_by_P_GS3 = $row0['household_visited_by_P_GS3'];
			  $household_visited_by_amila1 = $row0['household_visited_by_amila1'];
			  $household_visited_by_amila2 = $row0['household_visited_by_amila2'];
			  $household_visited_by_amila3 = $row0['household_visited_by_amila3'];
			  $visits_attachment = $row0['visits_attachment'];
			  $highlights_of_main_activities = $row0['highlights_of_main_activities'];
			  $number_of_daeen_active = $row0['number_of_daeen_active'];
			  $number_of_daeen_inactive = $row0['number_of_daeen_inactive'];
			  $number_of_waqifeen_ardi = $row0['number_of_waqifeen_ardi'];
			  $days_of_waqf = $row0['days_of_waqf'];
			  $people_benefited_from_waqifeen_ardi = $row0['people_benefited_from_waqifeen_ardi'];
			  $baits_achieved = $row0['baits_achieved'];
			  $baits_inprogress = $row0['baits_inprogress'];
			  $baits_target_for_the_year = $row0['baits_target_for_the_year'];
			  $baits_target_for_the_quarter = $row0['baits_target_for_the_quarter'];
			  $letirature_distributed_last_quarter = $row0['letirature_distributed_last_quarter'];
			  $letirature_distributed_this_quarter = $row0['letirature_distributed_this_quarter'];
			  $tabligh_training_sessions_last_quarter = $row0['tabligh_training_sessions_last_quarter'];
			  $tabligh_training_sessions_this_quarter = $row0['tabligh_training_sessions_this_quarter'];
			  $new_contacts_last_quarter = $row0['new_contacts_last_quarter'];
			  $new_contacts_this_quarter = $row0['new_contacts_this_quarter'];
			  $members_offering_5_daily_prayers_this_quarter = $row0['members_offering_5_daily_prayers_this_quarter'];
			  $members_offering_5_daily_prayers_last_quarter = $row0['members_offering_5_daily_prayers_last_quarter'];
			  $members_offering_juma_this_quarter = $row0['members_offering_juma_this_quarter'];
			  $members_offering_juma_last_quarter = $row0['members_offering_juma_last_quarter'];
			  $members_offering_1_congregational_prayer = $row0['members_offering_1_congregational_prayer'];
			  $members_listen_hudurs_sermon = $row0['members_listen_hudurs_sermon'];
			  $members_watch_mta = $row0['members_watch_mta'];
			  $namaz_centers = $row0['namaz_centers'];
			  $unislamic_practice_eliminated = $row0['unislamic_practice_eliminated'];
			  $members_provided_counseling = $row0['members_provided_counseling'];
			  $members_know_salat_trans_below_15 = $row0['members_know_salat_trans_below_15'];
			  $members_know_salat_trans_above_15 = $row0['members_know_salat_trans_above_15'];
			  $members_know_salat_trans_last_quarter = $row0['members_know_salat_trans_last_quarter'];
			  $members_know_salat_trans_this_quarter = $row0['members_know_salat_trans_this_quarter'];
			  $members_know_salat_trans_target_quarter = $row0['members_know_salat_trans_target_quarter'];
			  $members_reading_books_of_PM_below_15 = $row0['members_reading_books_of_PM_below_15'];
			  $members_reading_books_of_PM_above_15 = $row0['members_reading_books_of_PM_above_15'];
			  $members_reading_books_of_PM_last_quarter = $row0['members_reading_books_of_PM_last_quarter'];
			  $members_reading_books_of_PM_this_quarter = $row0['members_reading_books_of_PM_this_quarter'];
			  $members_reading_books_of_PM_target_quarter = $row0['members_reading_books_of_PM_target_quarter'];
			  $members_weekly_religious_classes_below_15 = $row0['members_weekly_religious_classes_below_15'];
			  $members_weekly_religious_classes_above_15 = $row0['members_weekly_religious_classes_above_15'];
			  $members_weekly_religious_classes_last_quarter = $row0['members_weekly_religious_classes_last_quarter'];
			  $members_weekly_religious_classes_this_quarter = $row0['members_weekly_religious_classes_this_quarter'];
			  $members_weekly_religious_classes_target_quarter = $row0['members_weekly_religious_classes_target_quarter'];
			  $educational_sessions_last_quarter = $row0['educational_sessions_last_quarter'];
			  $educational_sessions_this_quarter = $row0['educational_sessions_this_quarter'];
			  $educational_sessions_target_quarter = $row0['educational_sessions_target_quarter'];
			  $classes_reading_quran_last_quarter = $row0['classes_reading_quran_last_quarter'];
			  $classes_reading_quran_this_quarter = $row0['classes_reading_quran_this_quarter'];
			  $classes_reading_quran_target_quarter = $row0['classes_reading_quran_target_quarter'];
			  $classes_attendance_quran_last_quarter = $row0['classes_attendance_quran_last_quarter'];
			  $classes_attendance_quran_this_quarter = $row0['classes_attendance_quran_this_quarter'];
			  $classes_trans_quran_last_quarter = $row0['classes_trans_quran_last_quarter'];
			  $classes_trans_quran_this_quarter = $row0['classes_trans_quran_this_quarter'];
			  $classes_trans_quran_target_quarter = $row0['classes_trans_quran_target_quarter'];
			  $classes_trans_attendance_quran_last_quarter = $row0['classes_trans_attendance_quran_last_quarter'];
			  $classes_trans_attendance_quran_this_quarter = $row0['classes_trans_attendance_quran_this_quarter'];
			  $classes_pron_quran_last_quarter = $row0['classes_pron_quran_last_quarter'];
			  $classes_pron_quran_this_quarter = $row0['classes_pron_quran_this_quarter'];
			  $classes_pron_quran_target_quarter = $row0['classes_pron_quran_target_quarter'];
			  $classes_pron_attendance_quran_last_quarter = $row0['classes_pron_attendance_quran_last_quarter'];
			  $classes_pron_attendance_quran_this_quarter = $row0['classes_pron_attendance_quran_this_quarter'];
			  $members_reading_quran_last_quarter = $row0['members_reading_quran_last_quarter'];
			  $members_reading_quran_this_quarter = $row0['members_reading_quran_this_quarter'];
			  $members_reading_quran_target_quarter = $row0['members_reading_quran_target_quarter'];
			  $members_trans_quran_last_quarter = $row0['members_trans_quran_last_quarter'];
			  $members_trans_quran_this_quarter = $row0['members_trans_quran_this_quarter'];
			  $members_trans_quran_target_quarter = $row0['members_trans_quran_target_quarter'];
			  $members_did_waqf_ardi_last_quarter = $row0['members_did_waqf_ardi_last_quarter'];
			  $members_did_waqf_ardi_this_quarter = $row0['members_did_waqf_ardi_this_quarter'];
			  $members_did_waqf_ardi_target_quarter = $row0['members_did_waqf_ardi_target_quarter'];
			  $members_pledged_waqf_ardi_last_quarter = $row0['members_pledged_waqf_ardi_last_quarter'];
			  $members_pledged_waqf_ardi_this_quarter = $row0['members_pledged_waqf_ardi_this_quarter'];
			  $members_pledged_waqf_ardi_target_quarter = $row0['members_pledged_waqf_ardi_target_quarter'];
			  $number_of_exhibition_last_quarter = $row0['number_of_exhibition_last_quarter'];
			  $number_of_exhibition_this_quarter = $row0['number_of_exhibition_this_quarter'];
			  $number_of_exhibition_target_quarter = $row0['number_of_exhibition_target_quarter'];
			  $number_of_articles_last_quarter = $row0['number_of_articles_last_quarter'];
			  $number_of_articles_this_quarter = $row0['number_of_articles_this_quarter'];
			  $number_of_articles_target_quarter = $row0['number_of_articles_target_quarter'];
			  $number_of_books_last_quarter = $row0['number_of_books_last_quarter'];
			  $number_of_books_this_quarter = $row0['number_of_books_this_quarter'];
			  $number_of_books_target_quarter = $row0['number_of_books_target_quarter'];
			  $number_of_books_sold_last_quarter = $row0['number_of_books_sold_last_quarter'];
			  $number_of_books_sold_this_quarter = $row0['number_of_books_sold_this_quarter'];
			  $number_of_books_sold_target_quarter = $row0['number_of_books_sold_target_quarter'];
			  $number_of_members_writing_speaking_last_quarter = $row0['number_of_members_writing_speaking_last_quarter'];
			  $number_of_members_writing_speaking_this_quarter = $row0['number_of_members_writing_speaking_this_quarter'];
			  $number_of_members_writing_speaking_target_quarter = $row0['number_of_members_writing_speaking_target_quarter'];
			  $number_of_members_subscribed_last_quarter = $row0['number_of_members_subscribed_last_quarter'];
			  $number_of_members_subscribed_this_quarter = $row0['number_of_members_subscribed_this_quarter'];
			  $number_of_members_subscribed_target_quarter = $row0['number_of_members_subscribed_target_quarter'];
			  $number_of_teams_formed_last_quarter = $row0['number_of_teams_formed_last_quarter'];
			  $number_of_teams_formed_this_quarter = $row0['number_of_teams_formed_this_quarter'];
			  $number_of_teams_formed_target_quarter = $row0['number_of_teams_formed_target_quarter'];
			  $mta_connection_this_quarter = $row0['mta_connection_this_quarter'];
			  $total_household_in_branch = $row0['total_household_in_branch'];
			  $household_without_mta = $row0['household_without_mta'];
			  $program_produced_for_mta = $row0['program_produced_for_mta'];
			  $program_telecasted = $row0['program_telecasted'];
			  $technical_members = $row0['technical_members'];
			  $av_added_to_local_library = $row0['av_added_to_local_library'];
			  $message_broadcast = $row0['message_broadcast'];
			  $trained_member_produced_mta_program = $row0['trained_member_produced_mta_program'];
			  $uk_details = $row0['uk_details'];
			  $marriageable_male = $row0['marriageable_male'];
			  $marriageable_female = $row0['marriageable_female'];
			  $matchmaking_completed = $row0['matchmaking_completed'];
			  $matchmaking_inprogress = $row0['matchmaking_inprogress'];
			  $counseling_sessions = $row0['counseling_sessions'];
			  $sessions_for_married_members = $row0['sessions_for_married_members'];
			  $number_of_gov_official_last_quarter = $row0['number_of_gov_official_last_quarter'];
			  $number_of_gov_official_this_quarter = $row0['number_of_gov_official_this_quarter'];
			  $number_of_gov_official_target_quarter = $row0['number_of_gov_official_target_quarter'];
			  $number_of_gov_official_rights_last_quarter = $row0['number_of_gov_official_rights_last_quarter'];
			  $number_of_gov_official_rights_this_quarter = $row0['number_of_gov_official_rights_this_quarter'];
			  $number_of_gov_official_rights_target_quarter = $row0['number_of_gov_official_rights_target_quarter'];
			  $number_of_gov_official_persecution_last_quarter = $row0['number_of_gov_official_persecution_last_quarter'];
			  $number_of_gov_official_persecution_this_quarter = $row0['number_of_gov_official_persecution_this_quarter'];
			  $number_of_gov_official_persecution_target_quarter = $row0['number_of_gov_official_persecution_target_quarter'];
			  $number_of_article_written_last_quarter = $row0['number_of_article_written_last_quarter'];
			  $number_of_article_written_this_quarter = $row0['number_of_article_written_this_quarter'];
			  $number_of_article_written_target_quarter = $row0['number_of_article_written_target_quarter'];
			  $number_of_com_understanding_last_quarter = $row0['number_of_com_understanding_last_quarter'];
			  $number_of_com_understanding_this_quarter = $row0['number_of_com_understanding_this_quarter'];
			  $number_of_com_understanding_target_quarter = $row0['number_of_com_understanding_target_quarter'];
			  $dispute_resolved = $row0['dispute_resolved'];
			  $dispute_outstanding = $row0['dispute_outstanding'];
			  $ijlas_e_aam = $row0['ijlas_e_aam'];
			  $ijlas_e_aam_attendance = $row0['ijlas_e_aam_attendance'];
			  $members_helped = $row0['members_helped'];
			  $members_settled_in_canada = $row0['members_settled_in_canada'];
			  $members_unemployed = $row0['members_unemployed'];
			  $training_for_trade = $row0['training_for_trade'];
			  $training_for_trade_attendance = $row0['training_for_trade_attendance'];
			  $sessions_for_emerging_market = $row0['sessions_for_emerging_market'];
			  $sessions_for_emerging_market_attendance = $row0['sessions_for_emerging_market_attendance'];
			  $media_propaganda_against_jamaat = $row0['media_propaganda_against_jamaat'];
			  $media_propaganda_against_jamaat_response = $row0['media_propaganda_against_jamaat_response'];
			  $ua_details = $row0['ua_details'];
			  $number_of_earning_members = $row0['number_of_earning_members'];
			  $members_pay_chanda_regulaly = $row0['members_pay_chanda_regulaly'];
			  $members_pay_chanda_irregulaly = $row0['members_pay_chanda_irregulaly'];
			  $members_pay_chanda_not_at_all = $row0['members_pay_chanda_not_at_all'];
			  $members_start_pay_chanda_this_quarter = $row0['members_start_pay_chanda_this_quarter'];
			  $efforts_made = $row0['efforts_made'];
			  $efforts_made_attachment = $row0['efforts_made_attachment'];
			  $chanda_aam_collected = $row0['chanda_aam_collected'];
			  $chanda_jalsa_salana_collected = $row0['chanda_jalsa_salana_collected'];
			  $chanda_wasiyyat_collected = $row0['chanda_wasiyyat_collected'];
			  $expense_this_quarter = $row0['expense_this_quarter'];
			  $expense_to_date = $row0['expense_to_date'];
			  $training_sessions_hospitality = $row0['training_sessions_hospitality'];
			  $training_hospitality_member = $row0['training_hospitality_member'];
			  $homes_ready_accommodation_to_guest  = $row0['homes_ready_accommodation_to_guest '];
			  $member_ready_diyafat_for_guest = $row0['member_ready_diyafat_for_guest'];
			  $diyafat_served_events = $row0['diyafat_served_events'];
			  $guest_served = $row0['guest_served'];
			  $sessions_for_wasiyyat_last_quarter = $row0['sessions_for_wasiyyat_last_quarter'];
			  $sessions_for_wasiyyat_this_quarter = $row0['sessions_for_wasiyyat_this_quarter'];
			  $sessions_for_wasiyyat_target_quarter = $row0['sessions_for_wasiyyat_target_quarter'];
			  $sessions_for_wasiyyat_condition_last_quarter = $row0['sessions_for_wasiyyat_condition_last_quarter'];
			  $sessions_for_wasiyyat_condition_this_quarter = $row0['sessions_for_wasiyyat_condition_this_quarter'];
			  $sessions_for_wasiyyat_condition_target_quarter = $row0['sessions_for_wasiyyat_condition_target_quarter'];
			  $sessions_for_musis_res_last_quarter = $row0['sessions_for_musis_res_last_quarter'];
			  $sessions_for_musis_res_this_quarter = $row0['sessions_for_musis_res_this_quarter'];
			  $sessions_for_musis_res_target_quarter = $row0['sessions_for_musis_res_target_quarter'];
			  $sessions_for_musis_edu_last_quarter = $row0['sessions_for_musis_edu_last_quarter'];
			  $sessions_for_musis_edu_this_quarter = $row0['sessions_for_musis_edu_this_quarter'];
			  $sessions_for_musis_edu_target_quarter = $row0['sessions_for_musis_edu_target_quarter'];
			  $member_become_musis_last_quarter = $row0['member_become_musis_last_quarter'];
			  $member_become_musis_this_quarter = $row0['member_become_musis_this_quarter'];
			  $member_become_musis_target_quarter = $row0['member_become_musis_target_quarter'];
			  $member_offered_teaching_quran = $row0['member_offered_teaching_quran'];
			  $member_edu_tahrik = $row0['member_edu_tahrik'];
			  $member_entered_tahrik = $row0['member_entered_tahrik'];
			  $member_approached_tahrik = $row0['member_approached_tahrik'];
			  $member_increased_tahrik_pledge = $row0['member_increased_tahrik_pledge'];
			  $member_edu_waqf_jadid = $row0['member_edu_waqf_jadid'];
			  $member_entered_waqf_jadid = $row0['member_entered_waqf_jadid'];
			  $waqf_jadid_collection_target = $row0['waqf_jadid_collection_target'];
			  $member_waqf_jadid_with_pledge = $row0['member_waqf_jadid_with_pledge'];
			  $number_receipt_processed = $row0['number_receipt_processed'];
			  $total_income = $row0['total_income'];
			  $total_expense = $row0['total_expense'];
			  $receipt_books_issued = $row0['receipt_books_issued'];
			  $receipt_books_returned = $row0['receipt_books_returned'];
			  $members_res_for_property_maintenance = $row0['members_res_for_property_maintenance'];
			  $members_res_for_property_security = $row0['members_res_for_property_security'];
			  $members_res_for_property_cleanliness = $row0['members_res_for_property_cleanliness'];
			  $members_trained_above_activities = $row0['members_trained_above_activities'];
			  $members_trained_above_participation = $row0['members_trained_above_participation'];
			  $muhasib_team_memebrs = $row0['muhasib_team_memebrs'];
			  $expense_for_the_activities = $row0['expense_for_the_activities'];
			  $expense_muhasib_to_date = $row0['expense_muhasib_to_date'];
			  $number_of_activities_for_future_needs = $row0['number_of_activities_for_future_needs'];
			  $total_number_of_waqifeen = $row0['total_number_of_waqifeen'];
			  $waqifeen_punctual_in_namaz = $row0['waqifeen_punctual_in_namaz'];
			  $waqifeen_punctual_in_tahajjud = $row0['waqifeen_punctual_in_tahajjud'];
			  $waqifeen_punctual_reading_quran = $row0['waqifeen_punctual_reading_quran'];
			  $waqifeen_made_punctual_in_namaz = $row0['waqifeen_made_punctual_in_namaz'];
			  $waqifeen_made_punctual_in_tahajjud = $row0['waqifeen_made_punctual_in_tahajjud'];
			  $waqifeen_made_punctual_reading_quran = $row0['waqifeen_made_punctual_reading_quran'];
			  $waqifeen_watch_mta = $row0['waqifeen_watch_mta'];
			  $waqifeen_percent_improved_mta = $row0['waqifeen_percent_improved_mta'];
			  $waqifeen_taught_namaz = $row0['waqifeen_taught_namaz'];
			  $waqifeen_taught_quran = $row0['waqifeen_taught_quran'];
			  $waqifeen_taught_trans = $row0['waqifeen_taught_trans'];
			  $waqifeen_book_assigned = $row0['waqifeen_book_assigned'];
			  $waqifeen_read_book_assigned = $row0['waqifeen_read_book_assigned'];
			  $waqifeen_taught_tabligh = $row0['waqifeen_taught_tabligh'];
			  $waqifeen_participated = $row0['waqifeen_participated'];
			  $waqifeen_sports_activities = $row0['waqifeen_sports_activities'];
			  $waqifeen_outdoor_trips = $row0['waqifeen_outdoor_trips'];
			  $waqifeen_parent_meetings = $row0['waqifeen_parent_meetings'];
			  $waqifeen_parent_attendance = $row0['waqifeen_parent_attendance'];
			  $waqifeen_taught_languages = $row0['waqifeen_taught_languages'];
			  $waqifeen_taught_name_of_languages = $row0['waqifeen_taught_name_of_languages'];
			  $members_with_agr_knowledge = $row0['members_with_agr_knowledge'];
			  $members_in_agr_field  = $row0['members_in_agr_field '];
			  $members_learn_agr_skills = $row0['members_learn_agr_skills'];
			  $sessions_for_agr_knowledge = $row0['sessions_for_agr_knowledge'];
			  $number_of_houses_with_kitchen_backyard = $row0['number_of_houses_with_kitchen_backyard'];
			  $number_of_houses_with_kitchen_garden = $row0['number_of_houses_with_kitchen_garden'];
			  $member_helped_to_find_job = $row0['member_helped_to_find_job'];
			  $member_still_unemployed = $row0['member_still_unemployed'];
			  $sessions_to_improve_job_hunt = $row0['sessions_to_improve_job_hunt'];
			  $sessions_to_improve_resume = $row0['sessions_to_improve_resume'];
			  $sessions_to_learn_new_trade = $row0['sessions_to_learn_new_trade'];
			  $teams_in_touch_new_trend = $row0['teams_in_touch_new_trend'];
			  $students_encouraged_for_university = $row0['students_encouraged_for_university'];
			  $number_of_receipt_recovered = $row0['number_of_receipt_recovered'];
			  $number_of_bank_deposits = $row0['number_of_bank_deposits'];
			  $total_deposits = $row0['total_deposits'];
			  $total_payments = $row0['total_payments'];
			  $total_new_ahmadis = $row0['total_new_ahmadis'];
			  $new_ahmadis_promot_in_tahrik = $row0['new_ahmadis_promot_in_tahrik'];
			  $sessions_to_improve_above = $row0['sessions_to_improve_above'];
			  $sessions_to_improve_above_attendance = $row0['sessions_to_improve_above_attendance'];
			  $member_start_pay_chanda = $row0['member_start_pay_chanda'];
			  $member_still_not_pay_chanda = $row0['member_still_not_pay_chanda'];
			  $member_start_pay_chanda_this_qurater = $row0['member_start_pay_chanda_this_qurater'];
			  $member_pay_chanda_irregularly = $row0['member_pay_chanda_irregularly'];
			  $member_pay_chanda_not_at_all = $row0['member_pay_chanda_not_at_all'];
			  $strategy_to_devise_above = $row0['strategy_to_devise_above'];
			  $strategy_to_devise_above_attachment = $row0['strategy_to_devise_above_attachment'];
			  $member_started_pre_auth_chanda_payment = $row0['member_started_pre_auth_chanda_payment'];
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
					<td colspan=10>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan="6"><b>MAJLIS `AMILA</b></td>
								<td align=right><b>Month</b></td>
								<td align=center width="80"><b>1</b></td>
								<td align=center width="80"><b>2</b></td>
								<td align=center width="80"><b>3</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td valign=top>No. of meetings held</td>
					<td valign=top>
						<input type="text" <? echo $read_only; ?> maxlength="1" size="2"   name="number_of_amila_meetings" value="<? echo $number_of_amila_meetings; ?>" class="boxCSS1">
					</td>
					<td valign=top>Date</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="10" size="10" name="amila_meeting1_date" value="<? echo $amila_meeting1_date; ?>" class="boxCSS1"><br>YYYY-MM-DD</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="10" size="10" name="amila_meeting2_date" value="<? echo $amila_meeting2_date; ?>" class="boxCSS1"><br>YYYY-MM-DD</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="10" size="10" name="amila_meeting3_date" value="<? echo $amila_meeting3_date; ?>" class="boxCSS1"><br>YYYY-MM-DD</td>
					<td valign=top>Venues</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="150" size="10" name="amila_meeting1_venue" value="<? echo $amila_meeting1_venue; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="150" size="10" name="amila_meeting2_venue" value="<? echo $amila_meeting2_venue; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="150" size="10" name="amila_meeting3_venue" value="<? echo $amila_meeting3_venue; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>Total Amila members</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="2" size="2" name="total_amila_members" value="<? echo $total_amila_members; ?>" class="boxCSS1"></td>
					<td colspan="5">Attendance in each `Amila meeting </td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="2" size="3" name="amila_meeting1_attendance" value="<? echo $amila_meeting1_attendance; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="2" size="3" name="amila_meeting2_attendance" value="<? echo $amila_meeting2_attendance; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="2" size="3" name="amila_meeting3_attendance" value="<? echo $amila_meeting3_attendance; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // General body Meeting?>
		<tr><td ><span class="pageheader"><font color="">&nbsp;</font></span><td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=8>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan="4"><b>GENERAL BODY MEETING</b></td>
								<td align=right><b>Month</b></td>
								<td align=center width="80"><b>1</b></td>
								<td align=center width="80"><b>2</b></td>
								<td align=center width="80"><b>3</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>No. of meetings held</td>
					<td valign=top>
						<input type="text" <? echo $read_only; ?> maxlength="1" size="2"   name="number_of_gbody_meetings" value="<? echo $number_of_gbody_meetings; ?>" class="boxCSS1">
					</td>
					<td>Total adult members</td>
					<td valign=top>
						<input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="total_adult_members" value="<? echo $total_adult_members; ?>" class="boxCSS1">
					</td>
					<td>Date</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="10" size="10" name="gbody_meeting1_date" value="<? echo $gbody_meeting1_date; ?>" class="boxCSS1"><br>YYYY-MM-DD</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="10" size="10" name="gbody_meeting2_date" value="<? echo $gbody_meeting2_date; ?>" class="boxCSS1"><br>YYYY-MM-DD</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="10" size="10" name="gbody_meeting3_date" value="<? echo $gbody_meeting3_date; ?>" class="boxCSS1"><br>YYYY-MM-DD</td>
				</tr>
				<tr>
					<td colspan=3>No. of members who did not attend any meeting in this quarter</td>
					<td valign=top>
						<input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_members_not_attended" value="<? echo $number_of_members_not_attended; ?>" class="boxCSS1">
					</td>
					<td>Venues</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="150" size="10" name="gbody_meeting1_venue" value="<? echo $gbody_meeting1_venue; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="150" size="10" name="gbody_meeting2_venue" value="<? echo $gbody_meeting2_venue; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="150" size="10" name="gbody_meeting3_venue" value="<? echo $gbody_meeting3_venue; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=4>Action taken to motivate such members to attend</td>
					<td>Gents</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="gbody_gents_in_meeting1" value="<? echo $gbody_gents_in_meeting1; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="gbody_gents_in_meeting2" value="<? echo $gbody_gents_in_meeting2; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="gbody_gents_in_meeting3" value="<? echo $gbody_gents_in_meeting3; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=4 rowspan=2><textarea class="BoxCSS1" name="action_taken" cols=44 rows=2 <? echo $read_only; ?>><? echo $action_taken; ?></textarea></td>
					<td>Ladies</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="gbody_ladies_in_meeting1" value="<? echo $gbody_ladies_in_meeting1; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="gbody_ladies_in_meeting2" value="<? echo $gbody_ladies_in_meeting2; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="gbody_ladies_in_meeting3" value="<? echo $gbody_ladies_in_meeting3; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>children</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="gbody_children_in_meeting1" value="<? echo $gbody_children_in_meeting1; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="gbody_children_in_meeting2" value="<? echo $gbody_children_in_meeting2; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="gbody_children_in_meeting3" value="<? echo $gbody_children_in_meeting3; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<tr><td ><span class="pageheader"><font color="">&nbsp;</font></span><td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=8>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan="8"><b>TAJNEED</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>Total households in Jama`at</td>
					<td valign=top>
						<input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="total_households" value="<? echo $total_households; ?>" class="boxCSS1">
					</td>
					<td>Total members</td>
					<td valign=top>
						<input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="total_members" value="<? echo $total_members; ?>" class="boxCSS1">
					</td>
					<td>Members moved <b>Out</b></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_moved_out" value="<? echo $members_moved_out; ?>" class="boxCSS1"></td>
					<td>Members moved <b>In</b></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_moved_in" value="<? echo $members_moved_in; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // Visits?>
		<tr><td ><span class="pageheader"><font color="">&nbsp;</font></span><td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=5>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td ><b>VISITS</b> 
		<? if (($read_only=="") && ($allow_attachments=="1")) {?>
	(attach report if required)
		<? } ?>
		</td>
								<td align=right><b>Month</b></td>
								<td align=center width="80"><b>1</b></td>
								<td align=center width="80"><b>2</b></td>
								<td align=center width="80"><b>3</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>Households visited by President or General Secretary this quarter</td>
					<td><input type="text" readonly  maxlength="3" size="8" name="total_household_visited_by_P_GS" value="<? echo $total_household_visited_by_P_GS; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?> maxlength="2" size="10"   name="household_visited_by_P_GS1" value="<? echo $household_visited_by_P_GS1; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?> maxlength="2" size="10"   name="household_visited_by_P_GS2" value="<? echo $household_visited_by_P_GS2; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="2" size="10" name="household_visited_by_P_GS3" value="<? echo $household_visited_by_P_GS3; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>Households visited by other Majlis Amila members this quarter</td>
					<td><input type="text" readonly  maxlength="3" size="8" name="total_household_visited_by_amila" value="" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?> maxlength="2" size="10"   name="household_visited_by_amila1" value="<? echo $household_visited_by_amila1; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?> maxlength="2" size="10"   name="household_visited_by_amila2" value="<? echo $household_visited_by_amila2; ?>" class="boxCSS1"></td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="2" size="10" name="household_visited_by_amila3" value="<? echo $household_visited_by_amila3; ?>" class="boxCSS1"></td>
				</tr>
				<tr><td colspan="5">&nbsp;</td></tr>
				<? if (($read_only=="") && ($allow_attachments=="1")) {?>

				<tr><td colspan="5" >
					Attached visit report:&nbsp;
					<input type="file" name="visits_attachment" class="boxCSS1">
				</td></tr>
				<? }?>
				<tr><td colspan="5">
					<? if (($qrrid) && ($visits_attachment)) {?>
						<a href="attachments/<? print "$visits_attachment\"";?> target="_blank"><? print "$visits_attachment";?></a>
					<? } ?> &nbsp;
				</td></tr>
			</table>
		</td><tr>
		<? // Activities highlights?>
		<tr><td ><span class="pageheader"><font color="">&nbsp;</font></span><td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td ><b>HIGHLIGHTS OF MAIN ACTIVITIES IN JAMA`AT</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan=4 rowspan=2><textarea class="BoxCSS1" name="highlights_of_main_activities" cols=82 rows=8 <? echo $read_only; ?>><? echo $highlights_of_main_activities; ?></textarea></td>
				</tr>
			</table>
		</td><tr>
		<? // Tabligh reports?>
		<tr><td ><b>Please provide results of your efforts in performing your departmental duties.
		<? if (($read_only=="") && ($allow_attachments=="1")) {?>
			 You may attach a detailed report. 
		<? } ?>
		</b><td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=7>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=7><b>TABLIGH DEPARTMENT </b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>No. of Da`een ilAllah in your Jama`at</td>
					<td>Active</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_daeen_active" value="<? echo $number_of_daeen_active; ?>" class="boxCSS1"></td>
					<td>Inactive</td>
					<td colspan=3><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="number_of_daeen_inactive" value="<? echo $number_of_daeen_inactive; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>Waqaf 'Aardi days for Tabligh offered</td>
					<td>No. of Waqifeen</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_waqifeen_ardi" value="<? echo $number_of_waqifeen_ardi; ?>" class="boxCSS1"></td>
					<td>No. of Days</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="days_of_waqf" value="<? echo $days_of_waqf; ?>" class="boxCSS1"></td>
					<td>No. of People Benefited</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="people_benefited_from_waqifeen_ardi" value="<? echo $people_benefited_from_waqifeen_ardi; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>Bai'ats</td>
					<td>Achieved</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="baits_achieved" value="<? echo $baits_achieved; ?>" class="boxCSS1"></td>
					<td>In progress</td>
					<td colspan=3><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="baits_inprogress" value="<? echo $baits_inprogress; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>Bai'ats target</td>
					<td>For the year</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="baits_target_for_the_year" value="<? echo $baits_target_for_the_year; ?>" class="boxCSS1"></td>
					<td>For the quarter</td>
					<td colspan=3><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="baits_target_for_the_quarter" value="<? echo $baits_target_for_the_quarter; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>Literature / Packages Distributed</td>
					<td>Last quarter</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="letirature_distributed_last_quarter" value="<? echo $letirature_distributed_last_quarter; ?>" class="boxCSS1"></td>
					<td>This quarter</td>
					<td colspan=3><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="letirature_distributed_this_quarter" value="<? echo $letirature_distributed_this_quarter; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>Tabligh training sessions organized</td>
					<td>Last quarter</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="tabligh_training_sessions_last_quarter" value="<? echo $tabligh_training_sessions_last_quarter; ?>" class="boxCSS1"></td>
					<td>This quarter</td>
					<td colspan=3><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="tabligh_training_sessions_this_quarter" value="<? echo $tabligh_training_sessions_this_quarter; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>New contacts established</td>
					<td>Last quarter</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="new_contacts_last_quarter" value="<? echo $new_contacts_last_quarter; ?>" class="boxCSS1"></td>
					<td>This quarter</td>
					<td colspan=3><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="new_contacts_this_quarter" value="<? echo $new_contacts_this_quarter; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // Tarbiyat report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=8>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=8><b>TARBIYAT DEPARTMENT</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>No. of members offering 5 prayers  regularly</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_offering_5_daily_prayers_this_quarter" value="<? echo $members_offering_5_daily_prayers_this_quarter; ?>" class="boxCSS1"></td>
					<td>Last Quarter</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_offering_5_daily_prayers_last_quarter" value="<? echo $members_offering_5_daily_prayers_last_quarter; ?>" class="boxCSS1"></td>
					<td>Juma (this qtr)</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_offering_juma_this_quarter" value="<? echo $members_offering_juma_this_quarter; ?>" class="boxCSS1"></td>
					<td>Last Quarter</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_offering_juma_last_quarter" value="<? echo $members_offering_juma_last_quarter; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>No. of members offering at least one congregational prayer daily</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_offering_1_congregational_prayer" value="<? echo $members_offering_1_congregational_prayer; ?>" class="boxCSS1"></td>
					<td colspan=3>No. of members listened to Hudur's  sermons</td>
					<td ><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_listen_hudurs_sermon" value="<? echo $members_listen_hudurs_sermon; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>No. of members who watch MTA regularly</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_watch_mta" value="<? echo $members_watch_mta; ?>" class="boxCSS1"></td>
					<td colspan=3>No. of Namaz Centers</td>
					<td ><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="namaz_centers" value="<? echo $namaz_centers; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=8>Which Un-Islamic practice was targeted this quarter to be eliminated from members</td>
				</tr>
				<tr>
					<td colspan=8><textarea class="BoxCSS1" name="unislamic_practice_eliminated" cols=82 rows=8 <? echo $read_only; ?>><? echo $unislamic_practice_eliminated; ?></textarea></td>
				</tr>
				<tr>
					<td colspan=7>No. of parents or children who were provided with counseling services to help cope with social challenges</td>
					<td colspan=3><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_provided_counseling" value="<? echo $members_provided_counseling; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // Talim report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=6>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td rowspan=2><b>TA`LIM DEPARTMENT</b></td>
								<td rowspan=2 width=80><b>Members below 15 years of age</b></td>
								<td rowspan=2 width=80><b>Members above 15 years of age</b></td>
								<td colspan=3 align=center><b>Quarter</b></td>
							</tr>
							<tr>
								<td width=60><b>Last</b></td>
								<td width=60><b>This</b></td>
								<td width=60><b>Target</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>No. of members who know Translation of Salat</td>
					<td width=80><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_know_salat_trans_below_15" value="<? echo $members_know_salat_trans_below_15; ?>" class="boxCSS1"></td>
					<td width=80><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_know_salat_trans_above_15" value="<? echo $members_know_salat_trans_above_15; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_know_salat_trans_last_quarter " value="<? echo $members_know_salat_trans_last_quarter ; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_know_salat_trans_this_quarter" value="<? echo $members_know_salat_trans_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_know_salat_trans_target_quarter" value="<? echo $members_know_salat_trans_target_quarter; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of members reading books of Promised Messiah (as)</td>
					<td width=80><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_reading_books_of_PM_below_15" value="<? echo $members_reading_books_of_PM_below_15; ?>" class="boxCSS1"></td>
					<td width=80><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_reading_books_of_PM_above_15" value="<? echo $members_reading_books_of_PM_above_15 ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_reading_books_of_PM_last_quarter " value="<? echo $members_reading_books_of_PM_last_quarter ; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_reading_books_of_PM_this_quarter" value="<? echo $members_reading_books_of_PM_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_reading_books_of_PM_target_quarter" value="<? echo $members_reading_books_of_PM_target_quarter; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of members attending the weekly Religious Classes</td>
					<td width=80><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_weekly_religious_classes_below_15" value="<? echo $members_weekly_religious_classes_below_15; ?>" class="boxCSS1"></td>
					<td width=80><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_weekly_religious_classes_above_15" value="<? echo $members_weekly_religious_classes_above_15 ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_weekly_religious_classes_last_quarter " value="<? echo $members_weekly_religious_classes_last_quarter ; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_weekly_religious_classes_this_quarter" value="<? echo $members_weekly_religious_classes_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_weekly_religious_classes_target_quarter" value="<? echo $members_weekly_religious_classes_target_quarter; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>No. of educational sessions held to uplift the religious and secular knowledge of members</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="educational_sessions_last_quarter " value="<? echo $educational_sessions_last_quarter ; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="educational_sessions_this_quarter" value="<? echo $educational_sessions_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="educational_sessions_target_quarter" value="<? echo $educational_sessions_target_quarter; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // Talim Quran waqf ardi report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=6>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td rowspan=2><b>TA`LIMUL QUR`AN WAQF `ARDI</b></td>
								<td colspan=3 align=center><b>Quarter</b></td>
								<td colspan=2><b>Attendance</b></td>
							</tr>
							<tr>
								<td width=60><b>Last</b></td>
								<td width=60><b>This</b></td>
								<td width=60><b>Target</b></td>
								<td width=60><b>Last</b></td>
								<td width=60><b>This</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>No. classes held to teach simple reading of the Holy Qur'an</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="classes_reading_quran_last_quarter" value="<? echo $classes_reading_quran_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="classes_reading_quran_this_quarter" value="<? echo $classes_reading_quran_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="classes_reading_quran_target_quarter " value="<? echo $classes_reading_quran_target_quarter ; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="classes_attendance_quran_last_quarter" value="<? echo $classes_attendance_quran_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="classes_attendance_quran_this_quarter" value="<? echo $classes_attendance_quran_this_quarter; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. classes held to teach commentary and the meaning of the Holy Qur`an</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="classes_trans_quran_last_quarter" value="<? echo $classes_trans_quran_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="classes_trans_quran_this_quarter" value="<? echo $classes_trans_quran_this_quarter ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="classes_trans_quran_target_quarter " value="<? echo $classes_trans_quran_target_quarter ; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="classes_trans_attendance_quran_last_quarter" value="<? echo $classes_trans_attendance_quran_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="classes_trans_attendance_quran_this_quarter" value="<? echo $classes_trans_attendance_quran_this_quarter; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. classes held to improve pronunciation of the Holy Qur`an </td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="classes_pron_quran_last_quarter" value="<? echo $classes_pron_quran_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="classes_pron_quran_this_quarter" value="<? echo $classes_pron_quran_this_quarter ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="classes_pron_quran_target_quarter " value="<? echo $classes_pron_quran_target_quarter ; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="classes_pron_attendance_quran_last_quarter" value="<? echo $classes_pron_attendance_quran_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="classes_pron_attendance_quran_this_quarter" value="<? echo $classes_pron_attendance_quran_this_quarter; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of members reading / studying the Holy Qur`an on daily basis</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_reading_quran_last_quarter" value="<? echo $members_reading_quran_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_reading_quran_this_quarter" value="<? echo $members_reading_quran_this_quarter ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_reading_quran_target_quarter " value="<? echo $members_reading_quran_target_quarter ; ?>" class="boxCSS1"></td>
					<td colspan=2>&nbsp;</td>
				</tr>
				<tr>
					<td>No. of members who know translation of the Holy Qur`an</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_trans_quran_last_quarter " value="<? echo $members_trans_quran_last_quarter ; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_trans_quran_this_quarter" value="<? echo $members_trans_quran_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_trans_quran_target_quarter" value="<? echo $members_trans_quran_target_quarter; ?>" class="boxCSS1"></td>
					<td colspan=2>&nbsp;</td>
				</tr>
				<tr>
					<td>No. of members who worked under Waqf `Ardi scheme</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_did_waqf_ardi_last_quarter " value="<? echo $members_did_waqf_ardi_last_quarter ; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_did_waqf_ardi_this_quarter" value="<? echo $members_did_waqf_ardi_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_did_waqf_ardi_target_quarter" value="<? echo $members_did_waqf_ardi_target_quarter; ?>" class="boxCSS1"></td>
					<td colspan=2>&nbsp;</td>
				</tr>
				<tr>
					<td>No. of members pledged for Waqf `Ardi</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_pledged_waqf_ardi_last_quarter " value="<? echo $members_pledged_waqf_ardi_last_quarter ; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_pledged_waqf_ardi_this_quarter" value="<? echo $members_pledged_waqf_ardi_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="members_pledged_waqf_ardi_target_quarter" value="<? echo $members_pledged_waqf_ardi_target_quarter; ?>" class="boxCSS1"></td>
					<td colspan=2>&nbsp;</td>
				</tr>
			</table>
		</td><tr>
		<? // Isha'at report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=4>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td rowspan=2><b>ISHA`AT DEPARTMENT</b></td>
								<td colspan=3 align=center><b>Quarter</b></td>
							</tr>
							<tr>
								<td width=60><b>Last</b></td>
								<td width=60><b>This</b></td>
								<td width=60><b>Target</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>No. of exhibitions organized on Islam & Ahmadiyyat</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_exhibition_last_quarter" value="<? echo $number_of_exhibition_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_exhibition_this_quarter" value="<? echo $number_of_exhibition_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_exhibition_target_quarter" value="<? echo $number_of_exhibition_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of articles published in support of Islam in newspapers or other media</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_articles_last_quarter" value="<? echo $number_of_articles_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_articles_this_quarter" value="<? echo $number_of_articles_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_articles_target_quarter" value="<? echo $number_of_articles_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of books acquired for local library</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_books_last_quarter" value="<? echo $number_of_books_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_books_this_quarter" value="<? echo $number_of_books_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_books_target_quarter" value="<? echo $number_of_books_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of Jama`at books sold</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_books_sold_last_quarter" value="<? echo $number_of_books_sold_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_books_sold_this_quarter" value="<? echo $number_of_books_sold_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_books_sold_target_quarter"  value="<? echo $number_of_books_sold_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of members identified, brought forward and/or educated about the art of writing and public speaking</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_members_writing_speaking_last_quarter" value="<? echo $number_of_members_writing_speaking_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_members_writing_speaking_this_quarter" value="<? echo $number_of_members_writing_speaking_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_members_writing_speaking_target_quarter" value="<? echo $number_of_members_writing_speaking_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of  members who subscribed to various Jama`at publications, such as Alfazal etc.</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_members_subscribed_last_quarter" value="<? echo $number_of_members_subscribed_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_members_subscribed_this_quarter" value="<? echo $number_of_members_subscribed_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_members_subscribed_target_quarter" value="<? echo $number_of_members_subscribed_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of members in the team formed to write articles for Jama`at publications & in media</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_teams_formed_last_quarter" value="<? echo $number_of_teams_formed_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_teams_formed_this_quarter" value="<? echo $number_of_teams_formed_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_teams_formed_target_quarter" value="<? echo $number_of_teams_formed_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // Sami wa Basari report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=6>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=6><b>SAM`I WA BASARI DEPARTMENT</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>No. of MTA connections provided this quarter </td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="mta_connection_this_quarter" value="<? echo $mta_connection_this_quarter; ?>" class="boxCSS1"></td>
					<td>Total households in Jama`at</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="total_household_in_branch" value="<? echo $total_household_in_branch; ?>" class="boxCSS1"></td>
					<td>Households still without MTA</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="household_without_mta"  value="<? echo $household_without_mta ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of programs produced for MTA or local Radio</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="program_produced_for_mta" value="<? echo $program_produced_for_mta; ?>" class="boxCSS1"></td>
					<td colspan=3>No. of  Jama`at programs telecasted on local TV or Radio</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="program_telecasted" value="<? echo $program_telecasted ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of technically capable members in the department</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="technical_members" value="<? echo $technical_members; ?>" class="boxCSS1"></td>
					<td colspan=3>No. of A/V resources added in local library</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="av_added_to_local_library" value="<? echo $av_added_to_local_library ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=5>Any message of Islam and Ahmadiyyat broadcasted using A/V equipment from Jama`at or other resources</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="message_broadcast" value="<? echo $message_broadcast ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=5>Technically trained members for the production of MTA programs</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="trained_member_produced_mta_program" value="<? echo $trained_member_produced_mta_program ; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // Rishta nata report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=5>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=5><b>RISHTA NATA DEPARTMENT</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>No. marriageable individuals in the database </td>
					<td>Male</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="marriageable_male" value="<? echo $marriageable_male; ?>" class="boxCSS1"></td>
					<td>Female</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="marriageable_female" value="<? echo $marriageable_female ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of match making activities</td>
					<td>Completed successfully</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="matchmaking_completed" value="<? echo $matchmaking_completed; ?>" class="boxCSS1"></td>
					<td>In Progress</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="matchmaking_inprogress" value="<? echo $matchmaking_inprogress ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=4>No. of counseling or informational sessions held for parents </td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="counseling_sessions" value="<? echo $counseling_sessions ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=4>No. of sessions held for married members to educate them about their matrimonial responsibilities</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_married_members" value="<? echo $sessions_for_married_members ; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // Umur kharijiyya report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=4>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td rowspan=2><b>UMUR KHARIJIYYA DEPARTMENT</b> (Provide details where necessary)</td>
								<td colspan=3 align=center><b>Quarter</b></td>
							</tr>
							<tr>
								<td width=60><b>Last</b></td>
								<td width=60><b>This</b></td>
								<td width=60><b>Plan</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>No. of government officials contacted to introduce Jama`at, its values and objectives</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_gov_official_last_quarter" value="<? echo $number_of_gov_official_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_gov_official_this_quarter" value="<? echo $number_of_gov_official_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_gov_official_target_quarter" value="<? echo $number_of_gov_official_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of officials contacted to ensure Jama`at interests are protected nationally and internationally </td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_gov_official_rights_last_quarter" value="<? echo $number_of_gov_official_rights_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_gov_official_rights_this_quarter" value="<? echo $number_of_gov_official_rights_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_gov_official_rights_target_quarter" value="<? echo $number_of_gov_official_rights_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>Officials/MPs contacted to create awareness about persecution of Ahmadis in Pakistan and elsewhere</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_gov_official_persecution_last_quarter" value="<? echo $number_of_gov_official_persecution_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_gov_official_persecution_this_quarter" value="<? echo $number_of_gov_official_persecution_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_gov_official_persecution_target_quarter" value="<? echo $number_of_gov_official_persecution_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of contacts established or articles written to create awareness about Islam-Ahmadiyyat</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_article_written_last_quarter" value="<? echo $number_of_article_written_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_article_written_this_quarter" value="<? echo $number_of_article_written_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_article_written_target_quarter" value="<? echo $number_of_article_written_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of religious and other communities contacted to promote mutual understanding, trust and friendship </td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_com_understanding_last_quarter" value="<? echo $number_of_com_understanding_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_com_understanding_this_quarter" value="<? echo $number_of_com_understanding_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_com_understanding_target_quarter" value="<? echo $number_of_com_understanding_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=4>Please provide details here (if necessary)</td>
				</tr>
				<tr>
					<td colspan=4><textarea class="BoxCSS1" name="uk_details" cols=82 rows=4 <? echo $read_only; ?>><? echo $uk_details; ?></textarea></td>
				</tr>
			</table>
		</td><tr>
		<? // Umur amma report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=6>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=6><b>UMUR AMMA DEPARTMENT</b> (Provide details where necessary)</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan=3>In case of disputes, how many cases were resolved </td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="dispute_resolved" value="<? echo $dispute_resolved; ?>" class="boxCSS1"></td>
					<td ># of disputes outstanding</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="dispute_outstanding" value="<? echo $dispute_outstanding ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>No. of Ijlas `Aam organized to promote an atmosphere of reconciliation in case of disputes</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="ijlas_e_aam" value="<? echo $ijlas_e_aam; ?>" class="boxCSS1"></td>
					<td >Attendance</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="ijlas_e_aam_attendance" value="<? echo $ijlas_e_aam_attendance ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of members who were helped in getting </td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_helped" value="<? echo $members_helped; ?>" class="boxCSS1"></td>
					<td >Getting settled in Canadian system</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_settled_in_canada" value="<? echo $members_settled_in_canada ; ?>" class="boxCSS1"></td>
					<td ># Still unemployed</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_unemployed " value="<? echo $members_unemployed ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>No. of training sessions organized to train members in various trades and professions</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="training_for_trade" value="<? echo $training_for_trade; ?>" class="boxCSS1"></td>
					<td >Attendance</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="training_for_trade_attendance" value="<? echo $training_for_trade_attendance ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>No. of informational sessions organized to guide members on emerging market trends</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_emerging_market" value="<? echo $sessions_for_emerging_market; ?>" class="boxCSS1"></td>
					<td >Attendance</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_emerging_market_attendance" value="<? echo $sessions_for_emerging_market_attendance ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>No of media channels monitored to detect any malicious propaganda against Jama`at </td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="media_propaganda_against_jamaat" value="<? echo $media_propaganda_against_jamaat; ?>" class="boxCSS1"></td>
					<td ># of responses initiated </td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="media_propaganda_against_jamaat_response" value="<? echo $media_propaganda_against_jamaat_response ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=6>Please provide details here (if necessary)</td>
				</tr>
				<tr>
					<td colspan=6><textarea class="BoxCSS1" name="ua_details" cols=82 rows=4 <? echo $read_only; ?>><? echo $ua_details; ?></textarea></td>
				</tr>
			</table>
		</td><tr>
		<? // Finance report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=8>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=8><b>FINANCE DEPARTMENT</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>Number of earning members</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_earning_members" value="<? echo $number_of_earning_members; ?>" class="boxCSS1"></td>
					<td >Members who pay Chanda regularly</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_pay_chanda_regulaly" value="<? echo $members_pay_chanda_regulaly ; ?>" class="boxCSS1"></td>
					<td >Irregularly</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_pay_chanda_irregulaly" value="<? echo $members_pay_chanda_irregulaly ; ?>" class="boxCSS1"></td>
					<td >Not at all</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_pay_chanda_not_at_all" value="<? echo $members_pay_chanda_not_at_all ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=7>How many of the irregular or non-paying members have started paying chanda regularly since this quarter</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_start_pay_chanda_this_quarter" value="<? echo $members_start_pay_chanda_this_quarter; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=8>Efforts made to improve above figures (Attach Sheet)</td>
				</tr>
				<tr>
					<td colspan=8><textarea class="BoxCSS1" name="efforts_made" cols=82 rows=4 <? echo $read_only; ?>><? echo $efforts_made; ?></textarea></td>
				</tr>
				<tr><td colspan="8">&nbsp;</td></tr>
				<? if (($read_only=="") && ($allow_attachments=="1")) {?>
				<tr><td colspan="8" >
					Attach Sheet:&nbsp;
					<input type="file" name="efforts_made_attachment" class="boxCSS1">
				</td></tr>
				<? }?>
				<tr><td colspan="8">
					<? if (($qrrid) && ($efforts_made_attachment)) {?>
						<a href="attachments/<? print "$efforts_made_attachment\"";?> target="_blank"><? print "$efforts_made_attachment";?></a>
					<? } ?> &nbsp;
				</td></tr>
				<tr>
					<td colspan=2>Chanda collected to date</td>
					<td align=right>Aam</td>
					<td width=60><input type="text" <? echo $read_only; ?> size="6"   name="chanda_aam_collected" value="<? echo $chanda_aam_collected ; ?>" class="boxCSS1"></td>
					<td >Jalsa Salana</td>
					<td width=60><input type="text" <? echo $read_only; ?> size="6"   name="chanda_jalsa_salana_collected" value="<? echo $chanda_jalsa_salana_collected ; ?>" class="boxCSS1"></td>
					<td >Wasiyyat</td>
					<td width=60><input type="text" <? echo $read_only; ?> size="6"   name="chanda_wasiyyat_collected" value="<? echo $chanda_wasiyyat_collected ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>Expenses this quarter</td>
					<td width=60><input type="text" <? echo $read_only; ?> size="6"   name="expense_this_quarter" value="<? echo $expense_this_quarter ; ?>" class="boxCSS1"></td>
					<td colspan=3>Expenses to date</td>
					<td width=60><input type="text" <? echo $read_only; ?> size="6"   name="expense_to_date" value="<? echo $expense_to_date ; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // diyafat report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=4>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=4><b>DIYAFAT DEPARTMENT</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>No. of training sessions held to train members in hospitality activities</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="training_sessions_hospitality" value="<? echo $training_sessions_hospitality; ?>" class="boxCSS1"></td>
					<td ># of trained members</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="training_hospitality_member" value="<? echo $training_hospitality_member ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of homes ready to offer accommodation to Guests</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="homes_ready_accommodation_to_guest" value="<? echo $homes_ready_accommodation_to_guest; ?>" class="boxCSS1"></td>
					<td >No. of  members ready to offer Diyafat to guests</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_ready_diyafat_for_guest" value="<? echo $member_ready_diyafat_for_guest ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of events where Diyafat services were provided this quarter</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="diyafat_served_events" value="<? echo $diyafat_served_events; ?>" class="boxCSS1"></td>
					<td >Total guests served</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="guest_served" value="<? echo $guest_served ; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // wasaya report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=4>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td rowspan=2><b>WASAYA DEPARTMENT</b></td>
								<td colspan=3 align=center><b>Quarter</b></td>
							</tr>
							<tr>
								<td width=60><b>Last</b></td>
								<td width=60><b>This</b></td>
								<td width=60><b>Plan</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>No. of sessions organized to educate members about philosophy and history of Nizam Wasiyyat</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_wasiyyat_last_quarter" value="<? echo $sessions_for_wasiyyat_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_wasiyyat_this_quarter" value="<? echo $sessions_for_wasiyyat_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_wasiyyat_target_quarter" value="<? echo $sessions_for_wasiyyat_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>Which condition of Wasiyyat was targeted this quarter to inculcate in members</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_wasiyyat_condition_last_quarter" value="<? echo $sessions_for_wasiyyat_condition_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_wasiyyat_condition_this_quarter" value="<? echo $sessions_for_wasiyyat_condition_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_wasiyyat_condition_target_quarter" value="<? echo $sessions_for_wasiyyat_condition_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of meetings organized for musis to draw their attention towards their responsibilities</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_musis_res_last_quarter" value="<? echo $sessions_for_musis_res_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_musis_res_this_quarter" value="<? echo $sessions_for_musis_res_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_musis_res_target_quarter" value="<? echo $sessions_for_musis_res_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of educational programs organized for musis</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_musis_edu_last_quarter" value="<? echo $sessions_for_musis_edu_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_musis_edu_this_quarter" value="<? echo $sessions_for_musis_edu_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_musis_edu_target_quarter" value="<? echo $sessions_for_musis_edu_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of members included in Nizam Wasiyyat this quarter</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_become_musis_last_quarter" value="<? echo $member_become_musis_last_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_become_musis_this_quarter" value="<? echo $member_become_musis_this_quarter; ?>" class="boxCSS1"></td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_become_musis_target_quarter" value="<? echo $member_become_musis_target_quarter ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>No. of members offered to teach the Holy Qur`an</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_offered_teaching_quran" value="<? echo $member_offered_teaching_quran; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // tahrik jadid report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=4>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=4><b>TAHRIK JADID DEPARTMENT</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan=3>No. of sessions organized to educate members about philosophy, history and demands of this Tahrik</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_edu_tahrik" value="<? echo $member_edu_tahrik; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>No. of members included in this Tahrik this quarter who were not participating previously</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_entered_tahrik" value="<? echo $member_entered_tahrik; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td >No. of members who were approached to increase their pledge</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_entered_tahrik" value="<? echo $member_approached_tahrik; ?>" class="boxCSS1"></td>
					<td >No. of members who increased their pledge</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_increased_tahrik_pledge" value="<? echo $member_increased_tahrik_pledge; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // waqf jadid report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=4>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=4><b>WAQF JADID DEPARTMENT</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan=3>No. of sessions organized to educate members about philosophy, history, demands and importance of this Tahrik</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_edu_waqf_jadid" value="<? echo $member_edu_waqf_jadid; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>No. of members included in this Tahrik this quarter who were not participating previously</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_entered_waqf_jadid" value="<? echo $member_entered_waqf_jadid; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td >Target for this Year , Collection To-date</td>
					<td width=60><input type="text" <? echo $read_only; ?> size="6"   name="waqf_jadid_collection_target" value="<? echo $waqf_jadid_collection_target; ?>" class="boxCSS1"></td>
					<td >No. of members with pledge</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_waqf_jadid_with_pledge" value="<? echo $member_waqf_jadid_with_pledge; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // MUHASIB report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=6>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=6><b>MUHASIB</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td >No. of Chanda receipts processed this quarter</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_receipt_processed" value="<? echo $number_receipt_processed; ?>" class="boxCSS1"></td>
					<td >Total Income</td>
					<td width=60><input type="text" <? echo $read_only; ?> size="6"   name="total_income" value="<? echo $total_income ; ?>" class="boxCSS1"></td>
					<td >Total Expenses</td>
					<td width=60><input type="text" <? echo $read_only; ?> size="6"   name="total_expense" value="<? echo $total_expense ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=2>Receipt books issued this quarter</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="receipt_books_issued" value="<? echo $receipt_books_issued; ?>" class="boxCSS1"></td>
					<td colspan=2>Receipt books returned</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="receipt_books_returned" value="<? echo $receipt_books_returned; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td >No. of members in Jama`at responsible for property maintenance</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_res_for_property_maintenance" value="<? echo $members_res_for_property_maintenance; ?>" class="boxCSS1"></td>
					<td >Security</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_res_for_property_security" value="<? echo $members_res_for_property_security; ?>" class="boxCSS1"></td>
					<td >Cleanliness</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_res_for_property_cleanliness" value="<? echo $members_res_for_property_cleanliness; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td >No. of sessions organized to train members for above activities</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_trained_above_activities" value="<? echo $members_trained_above_activities; ?>" class="boxCSS1"></td>
					<td >Participation</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_trained_above_participation" value="<? echo $members_trained_above_participation; ?>" class="boxCSS1"></td>
					<td >Total team members</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="muhasib_team_memebrs" value="<? echo $muhasib_team_memebrs; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=2>Expenses incurred this quarter in carrying out above activities</td>
					<td width=60><input type="text" <? echo $read_only; ?> size="6"   name="expense_for_the_activities" value="<? echo $expense_for_the_activities ; ?>" class="boxCSS1"></td>
					<td  colspan=2>Expenses to date</td>
					<td width=60><input type="text" <? echo $read_only; ?> size="6"   name="expense_muhasib_to_date" value="<? echo $expense_muhasib_to_date ; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=5>No. of search activities carried out to address future requirements of Jama`at</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_activities_for_future_needs" value="<? echo $number_of_activities_for_future_needs; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // waqf nau report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=8>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=8><b>WAQF NAU DEPARTMENT</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>Total no. of Waqifeen</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="total_number_of_waqifeen" value="<? echo $total_number_of_waqifeen; ?>" class="boxCSS1"></td>
					<td>Waqifeen punctual in Namaz</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_punctual_in_namaz" value="<? echo $waqifeen_punctual_in_namaz; ?>" class="boxCSS1"></td>
					<td>Tahajjud</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_punctual_in_tahajjud" value="<? echo $waqifeen_punctual_in_tahajjud; ?>" class="boxCSS1"></td>
					<td>The Holy Qur'an</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="waqifeen_punctual_reading_quran" value="<? echo $waqifeen_punctual_reading_quran; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>Waqifeen made punctual this quarter in Namaz</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_made_punctual_in_namaz" value="<? echo $waqifeen_made_punctual_in_namaz; ?>" class="boxCSS1"></td>
					<td>Tahajjud</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_made_punctual_in_tahajjud" value="<? echo $waqifeen_made_punctual_in_tahajjud; ?>" class="boxCSS1"></td>
					<td>The Holy Qur'an</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="waqifeen_made_punctual_reading_quran" value="<? echo $waqifeen_made_punctual_reading_quran; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=5>Percentage of Waqifeen watching MTA, specially, Hudur's Friday sermon </td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_watch_mta" value="<? echo $waqifeen_watch_mta; ?>" class="boxCSS1"></td>
					<td >% Improvement this quarter</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_percent_improved_mta" value="<? echo $waqifeen_percent_improved_mta; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>No. of Waqifeen who were taught Namaz this quarter </td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_taught_namaz" value="<? echo $waqifeen_taught_namaz; ?>" class="boxCSS1"></td>
					<td>Qur'an</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_taught_quran" value="<? echo $waqifeen_taught_quran; ?>" class="boxCSS1"></td>
					<td>Translation</td>
					<td><input type="text" <? echo $read_only; ?>  maxlength="3" size="4" name="waqifeen_taught_trans" value="<? echo $waqifeen_taught_trans; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=5>Which religious book was assigned to be read this quarter?</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_book_assigned" value="<? echo $waqifeen_book_assigned; ?>" class="boxCSS1"></td>
					<td >How many Waqifeen read this book?</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_read_book_assigned" value="<? echo $waqifeen_read_book_assigned; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=5>No. of training sessions held to teach Tabligh skills</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_taught_tabligh" value="<? echo $waqifeen_taught_tabligh; ?>" class="boxCSS1"></td>
					<td >No. of Waqifeen participated</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_participated" value="<? echo $waqifeen_participated; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=5>No. of sports activities organized</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_sports_activities" value="<? echo $waqifeen_sports_activities; ?>" class="boxCSS1"></td>
					<td >No. of outdoor trips arranged</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_outdoor_trips" value="<? echo $waqifeen_outdoor_trips; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=5>No. of meetings arranged with parents</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_parent_meetings" value="<? echo $waqifeen_parent_meetings; ?>" class="boxCSS1"></td>
					<td >Average attendance</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_parent_attendance" value="<? echo $waqifeen_parent_attendance; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=5>No. of sessions organized to teach various languages</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_taught_languages" value="<? echo $waqifeen_taught_languages; ?>" class="boxCSS1"></td>
					<td >Specify languages that were taught</td>
					<td><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="waqifeen_taught_name_of_languages" value="<? echo $waqifeen_taught_name_of_languages; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // ziraat report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=4>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=4><b>ZIRA`AT DEPARTMENT</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>No. of members with academic qualification in agriculture related </td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_with_agr_knowledge" value="<? echo $members_with_agr_knowledge; ?>" class="boxCSS1"></td>
					<td>Members working in this field</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_in_agr_field" value="<? echo $members_in_agr_field; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>Educational sessions attended to learn about new developments in agriculture seeds, fertilizers, pesticides etc.</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="members_learn_agr_skills" value="<? echo $members_learn_agr_skills; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>No. of sessions organized to spread this knowledge in Jama`at members</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_for_agr_knowledge" value="<? echo $sessions_for_agr_knowledge; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td >No. of houses with kitchen garden capacity in the backyard</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_houses_with_kitchen_backyard" value="<? echo $number_of_houses_with_kitchen_backyard; ?>" class="boxCSS1"></td>
					<td >No. of houses with a kitchen garden</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_houses_with_kitchen_garden" value="<? echo $number_of_houses_with_kitchen_garden; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // sanat report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=4>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=4><b>SANAT-O-TIJARAT DEPARTMENT</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>No. of unemployed members who were helped in finding a job </td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_helped_to_find_job" value="<? echo $member_helped_to_find_job; ?>" class="boxCSS1"></td>
					<td>No. of members seeking job but still unemployed</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_still_unemployed" value="<? echo $member_still_unemployed; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td >No. of session organized to educate members about job search techniques</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_to_improve_job_hunt" value="<? echo $sessions_to_improve_job_hunt; ?>" class="boxCSS1"></td>
					<td >Effective resume writing skills</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_to_improve_resume" value="<? echo $sessions_to_improve_resume; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td >No. of training sessions organized or attended to learn new trades</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_to_learn_new_trade" value="<? echo $sessions_to_learn_new_trade; ?>" class="boxCSS1"></td>
					<td >No. of teams created to stay in touch with new trends</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="teams_in_touch_new_trend" value="<? echo $teams_in_touch_new_trend; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>No. of sessions organized to help and encourage students to enroll in university </td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="students_encouraged_for_university" value="<? echo $students_encouraged_for_university; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // amin report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=4>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=4><b>AMIN</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>No. of receipts recovered for payment</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_receipt_recovered" value="<? echo $number_of_receipt_recovered; ?>" class="boxCSS1"></td>
					<td>No. of bank deposits</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="number_of_bank_deposits" value="<? echo $number_of_bank_deposits; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td >Total amount deposited in bank</td>
					<td width=60><input type="text" <? echo $read_only; ?> size="6"   name="total_deposits" value="<? echo $total_deposits; ?>" class="boxCSS1"></td>
					<td >Total payments</td>
					<td width=60><input type="text" <? echo $read_only; ?> size="6"   name="total_payments" value="<? echo $total_payments; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // additional sec waqf jadid report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=4>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=4><b>ADDITIONAL SECRETARY WAQF JADID</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>Total new Ahmadis</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="total_new_ahmadis" value="<? echo $total_new_ahmadis; ?>" class="boxCSS1"></td>
					<td>No. of new Ahmadis contacted to promote their participation in this Tahrik</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="new_ahmadis_promot_in_tahrik" value="<? echo $new_ahmadis_promot_in_tahrik; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of sessions organized to educate the philosophy and importance of this</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_to_improve_above" value="<? echo $sessions_to_improve_above; ?>" class="boxCSS1"></td>
					<td>Attendance</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="sessions_to_improve_above_attendance" value="<? echo $sessions_to_improve_above_attendance; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>No. of members who started paying chanda this quarter </td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_start_pay_chanda" value="<? echo $member_start_pay_chanda; ?>" class="boxCSS1"></td>
					<td>No. still not paying</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_still_not_pay_chanda" value="<? echo $member_still_not_pay_chanda; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // additional sec mal report?>
		<tr><td >&nbsp;<td></tr>
		<tr><td>
			<table border="0" width="100%" class="BoxCSS1">
				<tr>
					<td colspan=4>
						<table border=0 width=100% class="BoxCSS1">
							<tr>
								<td colspan=4><b>ADDITIONAL SECRETARY MAL</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan=3>How many of the irregular or non-paying members have started paying chanda regularly since this quarter</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_start_pay_chanda_this_qurater" value="<? echo $member_start_pay_chanda_this_qurater; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td>Members still paying chanda irregularly</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_pay_chanda_irregularly" value="<? echo $member_pay_chanda_irregularly; ?>" class="boxCSS1"></td>
					<td>Not at all</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_pay_chanda_not_at_all" value="<? echo $member_pay_chanda_not_at_all; ?>" class="boxCSS1"></td>
				</tr>
				<tr>
					<td colspan=3>What strategy is devised to activate such members
				<? if (($read_only=="") && ($allow_attachments=="1")) {?>
				 (attach detail) 
				<? } ?>
					</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="strategy_to_devise_above" value="<? echo $strategy_to_devise_above; ?>" class="boxCSS1"></td>
				</tr>
				<tr><td colspan="4">&nbsp;</td></tr>
				<? if (($read_only=="") && ($allow_attachments=="1")) {?>
				<tr><td colspan="4" >
					Attach detail:&nbsp;
					<input type="file" name="strategy_to_devise_above_attachment" class="boxCSS1">
				</td></tr>
				<? }?>
				<tr><td colspan="4">
					<? if (($qrrid) && ($strategy_to_devise_above_attachment)) {?>
						<a href="attachments/<? print "$strategy_to_devise_above_attachment\"";?> target="_blank"><? print "$strategy_to_devise_above_attachment";?></a>
					<? } ?> &nbsp;
				</td></tr>
				<tr>
					<td colspan=3>No. of members who started paying chanda as Pre Authorized payment plan.</td>
					<td width=60><input type="text" <? echo $read_only; ?> maxlength="3" size="4"   name="member_started_pre_auth_chanda_payment" value="<? echo $member_started_pre_auth_chanda_payment; ?>" class="boxCSS1"></td>
				</tr>
			</table>
		</td><tr>
		<? // Submitted Reports?>
		<tr><td ><span class="pageheader"><font color="">&nbsp;</font></span><td></tr>
			<input type="hidden" name="qrrid" value=<? print "\"$qrrid\""; ?>>
			<input type="hidden" name="fvisits_attachment" value=<? print "\"$visits_attachment\""; ?>>
			<input type="hidden" name="fefforts_made_attachment" value=<? print "\"$efforts_made_attachment\""; ?>>
			<input type="hidden" name="fstrategy_to_devise_above_attachment" value=<? print "\"$strategy_to_devise_above_attachment\""; ?>>
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
