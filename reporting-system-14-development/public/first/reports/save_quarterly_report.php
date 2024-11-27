<?
include("../incl/dbcon.php");
include ("protected.php"); 
							$pdt = date("Y-m-d");
							$Error="";

							//QR report starts here
							//print "Before QR\n";
							if ($report_code=="QR"){
								//load attachments
								//print "Uploading file\n";
								//$uploaddir = '/var/www/html/reports/attachments/';
                                                                $uploaddir = getenv("DOCUMENT_ROOT") . '/reports/attachments/';
								$uploadfile = $uploaddir . $_FILES['visits_attachment']['name'];
								$tmpuploadfile = $_FILES['visits_attachment']['tmp_name'];
								//print "$uploadfile\n";
								//print "$tmpuploadfile\n";
								if ($tmpuploadfile){
									$file_name0 = $_FILES['visits_attachment']['name'];
									//if (copy($_FILES['visits_attachment']['tmp_name'], $uploadfile)) {
									if (move_uploaded_file($_FILES['visits_attachment']['tmp_name'], $uploadfile)) {
									   //print "File is valid, and was successfully uploaded. ";
									   chmod ($uploadfile, 0644);
									} else {
									   //print "Possible file upload failed\n";
									}
								}
								//check if the new file is empty then keep the old attachment
								if (($visits_attachment=="") && ($fvisits_attachment!="") && ($qrrid)) {
									$file_name0 =$fvisits_attachment;
								}

								//$uploaddir = '/var/www/html/reports/attachments/';
                                                                $uploaddir = getenv("DOCUMENT_ROOT") . '/reports/attachments/';
								$uploadfile = $uploaddir . $_FILES['efforts_made_attachment']['name'];
								$tmpuploadfile = $_FILES['efforts_made_attachment']['tmp_name'];
								if ($tmpuploadfile){
									$file_name1 = $_FILES['efforts_made_attachment']['name'];
									//if (copy($_FILES['efforts_made_attachment']['tmp_name'], $uploadfile)) {
									if (move_uploaded_file($_FILES['efforts_made_attachment']['tmp_name'], $uploadfile)) {
									   //print "File is valid, and was successfully uploaded. ";
									   chmod ($uploadfile, 0644);
									} else {
									   //print "Possible file upload failed\n";
									}
								}
								//check if the new file is empty then keep the old attachment
								if (($efforts_made_attachment=="") && ($fefforts_made_attachment!="") && ($qrrid)) {
									$file_name1 =$fefforts_made_attachment;
								}

								//$uploaddir = '/var/www/html/reports/attachments/';
                                                                $uploaddir = getenv("DOCUMENT_ROOT") . '/reports/attachments/';
								$uploadfile = $uploaddir . $_FILES['strategy_to_devise_above_attachment']['name'];
								$tmpuploadfile = $_FILES['strategy_to_devise_above_attachment']['tmp_name'];
								if ($tmpuploadfile){
									$file_name2 = $_FILES['strategy_to_devise_above_attachment']['name'];
									//if (copy($_FILES['strategy_to_devise_above_attachment']['tmp_name'], $uploadfile)) {
									if (move_uploaded_file($_FILES['strategy_to_devise_above_attachment']['tmp_name'], $uploadfile)) {
									   //print "File is valid, and was successfully uploaded. ";
									   chmod ($uploadfile, 0644);
									} else {
									   //print "Possible file upload failed\n";
									}
								}
								//check if the new file is empty then keep the old attachment
								if (($strategy_to_devise_above_attachment=="") && ($fstrategy_to_devise_above_attachment!="") && ($gsrid)) {
									$file_name2 =$fstrategy_to_devise_above_attachment;
								}

							    if ($qrrid!="") {
								    $insert_data = "update ami_quarterly_reports set
													  quarter ='$quarter',
													  number_of_amila_meetings ='$number_of_amila_meetings',
													  total_amila_members ='$total_amila_members',
													  amila_meeting1_date ='$amila_meeting1_date',
													  amila_meeting2_date ='$amila_meeting2_date',
													  amila_meeting3_date ='$amila_meeting3_date',
													  amila_meeting1_venue ='$amila_meeting1_venue',
													  amila_meeting2_venue ='$amila_meeting2_venue',
													  amila_meeting3_venue ='$amila_meeting3_venue',
													  amila_meeting1_attendance ='$amila_meeting1_attendance',
													  amila_meeting2_attendance ='$amila_meeting2_attendance',
													  amila_meeting3_attendance ='$amila_meeting3_attendance',
													  number_of_gbody_meetings ='$number_of_gbody_meetings',
													  total_adult_members ='$total_adult_members',
													  number_of_members_not_attended ='$number_of_members_not_attended',
													  action_taken ='$action_taken',
													  gbody_meeting1_date ='$gbody_meeting1_date',
													  gbody_meeting2_date ='$gbody_meeting2_date',
													  gbody_meeting3_date ='$gbody_meeting3_date',
													  gbody_meeting1_venue ='$gbody_meeting1_venue',
													  gbody_meeting2_venue ='$gbody_meeting2_venue',
													  gbody_meeting3_venue ='$gbody_meeting3_venue',
													  gbody_gents_in_meeting1 ='$gbody_gents_in_meeting1',
													  gbody_gents_in_meeting2 ='$gbody_gents_in_meeting2',
													  gbody_gents_in_meeting3 ='$gbody_gents_in_meeting3',
													  gbody_ladies_in_meeting1 ='$gbody_ladies_in_meeting1',
													  gbody_ladies_in_meeting2 ='$gbody_ladies_in_meeting2',
													  gbody_ladies_in_meeting3 ='$gbody_ladies_in_meeting3',
													  gbody_children_in_meeting1 ='$gbody_children_in_meeting1',
													  gbody_children_in_meeting2 ='$gbody_children_in_meeting2',
													  gbody_children_in_meeting3 ='$gbody_children_in_meeting3',
													  total_households ='$total_households',
													  total_members ='$total_members',
													  members_moved_out ='$members_moved_out',
													  members_moved_in ='$members_moved_in',
													  household_visited_by_P_GS1 ='$household_visited_by_P_GS1',
													  household_visited_by_P_GS2 ='$household_visited_by_P_GS2',
													  household_visited_by_P_GS3 ='$household_visited_by_P_GS3',
													  household_visited_by_amila1 ='$household_visited_by_amila1',
													  household_visited_by_amila2 ='$household_visited_by_amila2',
													  household_visited_by_amila3 ='$household_visited_by_amila3',
													  visits_attachment ='$file_name0',
													  highlights_of_main_activities ='$highlights_of_main_activities',
													  number_of_daeen_active ='$number_of_daeen_active',
													  number_of_daeen_inactive ='$number_of_daeen_inactive',
													  number_of_waqifeen_ardi ='$number_of_waqifeen_ardi',
													  days_of_waqf ='$days_of_waqf',
													  people_benefited_from_waqifeen_ardi ='$people_benefited_from_waqifeen_ardi',
													  baits_achieved ='$baits_achieved',
													  baits_inprogress ='$baits_inprogress',
													  baits_target_for_the_year ='$baits_target_for_the_year',
													  baits_target_for_the_quarter ='$baits_target_for_the_quarter',
													  letirature_distributed_last_quarter ='$letirature_distributed_last_quarter',
													  letirature_distributed_this_quarter ='$letirature_distributed_this_quarter',
													  tabligh_training_sessions_last_quarter ='$tabligh_training_sessions_last_quarter',
													  tabligh_training_sessions_this_quarter ='$tabligh_training_sessions_this_quarter',
													  new_contacts_last_quarter ='$new_contacts_last_quarter',
													  new_contacts_this_quarter ='$new_contacts_this_quarter',
													  members_offering_5_daily_prayers_this_quarter ='$members_offering_5_daily_prayers_this_quarter',
													  members_offering_5_daily_prayers_last_quarter ='$members_offering_5_daily_prayers_last_quarter',
													  members_offering_juma_this_quarter ='$members_offering_juma_this_quarter',
													  members_offering_juma_last_quarter ='$members_offering_juma_last_quarter',
													  members_offering_1_congregational_prayer ='$members_offering_1_congregational_prayer',
													  members_listen_hudurs_sermon ='$members_listen_hudurs_sermon',
													  members_watch_mta ='$members_watch_mta',
													  namaz_centers ='$namaz_centers',
													  unislamic_practice_eliminated ='$unislamic_practice_eliminated',
													  members_provided_counseling ='$members_provided_counseling',
													  members_know_salat_trans_below_15 ='$members_know_salat_trans_below_15',
													  members_know_salat_trans_above_15 ='$members_know_salat_trans_above_15',
													  members_know_salat_trans_last_quarter ='$members_know_salat_trans_last_quarter',
													  members_know_salat_trans_this_quarter ='$members_know_salat_trans_this_quarter',
													  members_know_salat_trans_target_quarter ='$members_know_salat_trans_target_quarter',
													  members_reading_books_of_PM_below_15 ='$members_reading_books_of_PM_below_15',
													  members_reading_books_of_PM_above_15 ='$members_reading_books_of_PM_above_15',
													  members_reading_books_of_PM_last_quarter ='$members_reading_books_of_PM_last_quarter',
													  members_reading_books_of_PM_this_quarter ='$members_reading_books_of_PM_this_quarter',
													  members_reading_books_of_PM_target_quarter ='$members_reading_books_of_PM_target_quarter',
													  members_weekly_religious_classes_below_15 ='$members_weekly_religious_classes_below_15',
													  members_weekly_religious_classes_above_15 ='$members_weekly_religious_classes_above_15',
													  members_weekly_religious_classes_last_quarter ='$members_weekly_religious_classes_last_quarter',
													  members_weekly_religious_classes_this_quarter ='$members_weekly_religious_classes_this_quarter',
													  members_weekly_religious_classes_target_quarter ='$members_weekly_religious_classes_target_quarter',
													  educational_sessions_last_quarter ='$educational_sessions_last_quarter',
													  educational_sessions_this_quarter ='$educational_sessions_this_quarter',
													  educational_sessions_target_quarter ='$educational_sessions_target_quarter',
													  classes_reading_quran_last_quarter ='$classes_reading_quran_last_quarter',
													  classes_reading_quran_this_quarter ='$classes_reading_quran_this_quarter',
													  classes_reading_quran_target_quarter ='$classes_reading_quran_target_quarter',
													  classes_attendance_quran_last_quarter ='$classes_attendance_quran_last_quarter',
													  classes_attendance_quran_this_quarter ='$classes_attendance_quran_this_quarter',
													  classes_trans_quran_last_quarter ='$classes_trans_quran_last_quarter',
													  classes_trans_quran_this_quarter ='$classes_trans_quran_this_quarter',
													  classes_trans_quran_target_quarter ='$classes_trans_quran_target_quarter',
													  classes_trans_attendance_quran_last_quarter ='$classes_trans_attendance_quran_last_quarter',
													  classes_trans_attendance_quran_this_quarter ='$classes_trans_attendance_quran_this_quarter',
													  classes_pron_quran_last_quarter ='$classes_pron_quran_last_quarter',
													  classes_pron_quran_this_quarter ='$classes_pron_quran_this_quarter',
													  classes_pron_quran_target_quarter ='$classes_pron_quran_target_quarter',
													  classes_pron_attendance_quran_last_quarter ='$classes_pron_attendance_quran_last_quarter',
													  classes_pron_attendance_quran_this_quarter ='$classes_pron_attendance_quran_this_quarter',
													  members_reading_quran_last_quarter ='$members_reading_quran_last_quarter',
													  members_reading_quran_this_quarter ='$members_reading_quran_this_quarter',
													  members_reading_quran_target_quarter ='$members_reading_quran_target_quarter',
													  members_trans_quran_last_quarter ='$members_trans_quran_last_quarter',
													  members_trans_quran_this_quarter ='$members_trans_quran_this_quarter',
													  members_trans_quran_target_quarter ='$members_trans_quran_target_quarter',
													  members_did_waqf_ardi_last_quarter ='$members_did_waqf_ardi_last_quarter',
													  members_did_waqf_ardi_this_quarter ='$members_did_waqf_ardi_this_quarter',
													  members_did_waqf_ardi_target_quarter ='$members_did_waqf_ardi_target_quarter',
													  members_pledged_waqf_ardi_last_quarter ='$members_pledged_waqf_ardi_last_quarter',
													  members_pledged_waqf_ardi_this_quarter ='$members_pledged_waqf_ardi_this_quarter',
													  members_pledged_waqf_ardi_target_quarter ='$members_pledged_waqf_ardi_target_quarter',
													  number_of_exhibition_last_quarter ='$number_of_exhibition_last_quarter',
													  number_of_exhibition_this_quarter ='$number_of_exhibition_this_quarter',
													  number_of_exhibition_target_quarter ='$number_of_exhibition_target_quarter',
													  number_of_articles_last_quarter ='$number_of_articles_last_quarter',
													  number_of_articles_this_quarter ='$number_of_articles_this_quarter',
													  number_of_articles_target_quarter ='$number_of_articles_target_quarter',
													  number_of_books_last_quarter ='$number_of_books_last_quarter',
													  number_of_books_this_quarter ='$number_of_books_this_quarter',
													  number_of_books_target_quarter ='$number_of_books_target_quarter',
													  number_of_books_sold_last_quarter ='$number_of_books_sold_last_quarter',
													  number_of_books_sold_this_quarter ='$number_of_books_sold_this_quarter',
													  number_of_books_sold_target_quarter ='$number_of_books_sold_target_quarter',
													  number_of_members_writing_speaking_last_quarter ='$number_of_members_writing_speaking_last_quarter',
													  number_of_members_writing_speaking_this_quarter ='$number_of_members_writing_speaking_this_quarter',
													  number_of_members_writing_speaking_target_quarter ='$number_of_members_writing_speaking_target_quarter',
													  number_of_members_subscribed_last_quarter ='$number_of_members_subscribed_last_quarter',
													  number_of_members_subscribed_this_quarter ='$number_of_members_subscribed_this_quarter',
													  number_of_members_subscribed_target_quarter ='$number_of_members_subscribed_target_quarter',
													  number_of_teams_formed_last_quarter ='$number_of_teams_formed_last_quarter',
													  number_of_teams_formed_this_quarter ='$number_of_teams_formed_this_quarter',
													  number_of_teams_formed_target_quarter ='$number_of_teams_formed_target_quarter',
													  mta_connection_this_quarter ='$mta_connection_this_quarter',
													  total_household_in_branch ='$total_household_in_branch',
													  household_without_mta ='$household_without_mta',
													  program_produced_for_mta ='$program_produced_for_mta',
													  program_telecasted ='$program_telecasted',
													  technical_members ='$technical_members',
													  av_added_to_local_library ='$av_added_to_local_library',
													  message_broadcast ='$message_broadcast',
													  trained_member_produced_mta_program ='$trained_member_produced_mta_program',
													  uk_details ='$uk_details',
													  marriageable_male ='$marriageable_male',
													  marriageable_female ='$marriageable_female',
													  matchmaking_completed ='$matchmaking_completed',
													  matchmaking_inprogress ='$matchmaking_inprogress',
													  counseling_sessions ='$counseling_sessions',
													  sessions_for_married_members ='$sessions_for_married_members',
													  number_of_gov_official_last_quarter ='$number_of_gov_official_last_quarter',
													  number_of_gov_official_this_quarter ='$number_of_gov_official_this_quarter',
													  number_of_gov_official_target_quarter ='$number_of_gov_official_target_quarter',
													  number_of_gov_official_rights_last_quarter ='$number_of_gov_official_rights_last_quarter',
													  number_of_gov_official_rights_this_quarter ='$number_of_gov_official_rights_this_quarter',
													  number_of_gov_official_rights_target_quarter ='$number_of_gov_official_rights_target_quarter',
													  number_of_gov_official_persecution_last_quarter ='$number_of_gov_official_persecution_last_quarter',
													  number_of_gov_official_persecution_this_quarter ='$number_of_gov_official_persecution_this_quarter',
													  number_of_gov_official_persecution_target_quarter ='$number_of_gov_official_persecution_target_quarter',
													  number_of_article_written_last_quarter ='$number_of_article_written_last_quarter',
													  number_of_article_written_this_quarter ='$number_of_article_written_this_quarter',
													  number_of_article_written_target_quarter ='$number_of_article_written_target_quarter',
													  number_of_com_understanding_last_quarter ='$number_of_com_understanding_last_quarter',
													  number_of_com_understanding_this_quarter ='$number_of_com_understanding_this_quarter',
													  number_of_com_understanding_target_quarter ='$number_of_com_understanding_target_quarter',
													  dispute_resolved ='$dispute_resolved',
													  dispute_outstanding ='$dispute_outstanding',
													  ijlas_e_aam ='$ijlas_e_aam',
													  ijlas_e_aam_attendance ='$ijlas_e_aam_attendance',
													  members_helped ='$members_helped',
													  members_settled_in_canada ='$members_settled_in_canada',
													  members_unemployed ='$members_unemployed',
													  training_for_trade ='$training_for_trade',
													  training_for_trade_attendance ='$training_for_trade_attendance',
													  sessions_for_emerging_market ='$sessions_for_emerging_market',
													  sessions_for_emerging_market_attendance ='$sessions_for_emerging_market_attendance',
													  media_propaganda_against_jamaat ='$media_propaganda_against_jamaat',
													  media_propaganda_against_jamaat_response ='$media_propaganda_against_jamaat_response',
													  ua_details ='$ua_details',
													  number_of_earning_members ='$number_of_earning_members',
													  members_pay_chanda_regulaly ='$members_pay_chanda_regulaly',
													  members_pay_chanda_irregulaly ='$members_pay_chanda_irregulaly',
													  members_pay_chanda_not_at_all ='$members_pay_chanda_not_at_all',
													  members_start_pay_chanda_this_quarter ='$members_start_pay_chanda_this_quarter',
													  efforts_made ='$efforts_made',
													  efforts_made_attachment ='$file_name1',
													  chanda_aam_collected ='$chanda_aam_collected',
													  chanda_jalsa_salana_collected ='$chanda_jalsa_salana_collected',
													  chanda_wasiyyat_collected ='$chanda_wasiyyat_collected',
													  expense_this_quarter ='$expense_this_quarter',
													  expense_to_date ='$expense_to_date',
													  training_sessions_hospitality ='$training_sessions_hospitality',
													  training_hospitality_member ='$training_hospitality_member',
													  homes_ready_accommodation_to_guest  ='$homes_ready_accommodation_to_guest ',
													  member_ready_diyafat_for_guest ='$member_ready_diyafat_for_guest',
													  diyafat_served_events ='$diyafat_served_events',
													  guest_served ='$guest_served',
													  sessions_for_wasiyyat_last_quarter ='$sessions_for_wasiyyat_last_quarter',
													  sessions_for_wasiyyat_this_quarter ='$sessions_for_wasiyyat_this_quarter',
													  sessions_for_wasiyyat_target_quarter ='$sessions_for_wasiyyat_target_quarter',
													  sessions_for_wasiyyat_condition_last_quarter ='$sessions_for_wasiyyat_condition_last_quarter',
													  sessions_for_wasiyyat_condition_this_quarter ='$sessions_for_wasiyyat_condition_this_quarter',
													  sessions_for_wasiyyat_condition_target_quarter ='$sessions_for_wasiyyat_condition_target_quarter',
													  sessions_for_musis_res_last_quarter ='$sessions_for_musis_res_last_quarter',
													  sessions_for_musis_res_this_quarter ='$sessions_for_musis_res_this_quarter',
													  sessions_for_musis_res_target_quarter ='$sessions_for_musis_res_target_quarter',
													  sessions_for_musis_edu_last_quarter ='$sessions_for_musis_edu_last_quarter',
													  sessions_for_musis_edu_this_quarter ='$sessions_for_musis_edu_this_quarter',
													  sessions_for_musis_edu_target_quarter ='$sessions_for_musis_edu_target_quarter',
													  member_become_musis_last_quarter ='$member_become_musis_last_quarter',
													  member_become_musis_this_quarter ='$member_become_musis_this_quarter',
													  member_become_musis_target_quarter ='$member_become_musis_target_quarter',
													  member_offered_teaching_quran ='$member_offered_teaching_quran',
													  member_edu_tahrik ='$member_edu_tahrik',
													  member_entered_tahrik ='$member_entered_tahrik',
													  member_approached_tahrik ='$member_approached_tahrik',
													  member_increased_tahrik_pledge ='$member_increased_tahrik_pledge',
													  member_edu_waqf_jadid ='$member_edu_waqf_jadid',
													  member_entered_waqf_jadid ='$member_entered_waqf_jadid',
													  waqf_jadid_collection_target ='$waqf_jadid_collection_target',
													  member_waqf_jadid_with_pledge ='$member_waqf_jadid_with_pledge',
													  number_receipt_processed ='$number_receipt_processed',
													  total_income ='$total_income',
													  total_expense ='$total_expense',
													  receipt_books_issued ='$receipt_books_issued',
													  receipt_books_returned ='$receipt_books_returned',
													  members_res_for_property_maintenance ='$members_res_for_property_maintenance',
													  members_res_for_property_security ='$members_res_for_property_security',
													  members_res_for_property_cleanliness ='$members_res_for_property_cleanliness',
													  members_trained_above_activities ='$members_trained_above_activities',
													  members_trained_above_participation ='$members_trained_above_participation',
													  muhasib_team_memebrs ='$muhasib_team_memebrs',
													  expense_for_the_activities ='$expense_for_the_activities',
													  expense_muhasib_to_date ='$expense_muhasib_to_date',
													  number_of_activities_for_future_needs ='$number_of_activities_for_future_needs',
													  total_number_of_waqifeen ='$total_number_of_waqifeen',
													  waqifeen_punctual_in_namaz ='$waqifeen_punctual_in_namaz',
													  waqifeen_punctual_in_tahajjud ='$waqifeen_punctual_in_tahajjud',
													  waqifeen_punctual_reading_quran ='$waqifeen_punctual_reading_quran',
													  waqifeen_made_punctual_in_namaz ='$waqifeen_made_punctual_in_namaz',
													  waqifeen_made_punctual_in_tahajjud ='$waqifeen_made_punctual_in_tahajjud',
													  waqifeen_made_punctual_reading_quran ='$waqifeen_made_punctual_reading_quran',
													  waqifeen_watch_mta ='$waqifeen_watch_mta',
													  waqifeen_percent_improved_mta ='$waqifeen_percent_improved_mta',
													  waqifeen_taught_namaz ='$waqifeen_taught_namaz',
													  waqifeen_taught_quran ='$waqifeen_taught_quran',
													  waqifeen_taught_trans ='$waqifeen_taught_trans',
													  waqifeen_book_assigned ='$waqifeen_book_assigned',
													  waqifeen_read_book_assigned ='$waqifeen_read_book_assigned',
													  waqifeen_taught_tabligh ='$waqifeen_taught_tabligh',
													  waqifeen_participated ='$waqifeen_participated',
													  waqifeen_sports_activities ='$waqifeen_sports_activities',
													  waqifeen_outdoor_trips ='$waqifeen_outdoor_trips',
													  waqifeen_parent_meetings ='$waqifeen_parent_meetings',
													  waqifeen_parent_attendance ='$waqifeen_parent_attendance',
													  waqifeen_taught_languages ='$waqifeen_taught_languages',
													  waqifeen_taught_name_of_languages ='$waqifeen_taught_name_of_languages',
													  members_with_agr_knowledge ='$members_with_agr_knowledge',
													  members_in_agr_field  ='$members_in_agr_field ',
													  members_learn_agr_skills ='$members_learn_agr_skills',
													  sessions_for_agr_knowledge ='$sessions_for_agr_knowledge',
													  number_of_houses_with_kitchen_backyard ='$number_of_houses_with_kitchen_backyard',
													  number_of_houses_with_kitchen_garden ='$number_of_houses_with_kitchen_garden',
													  member_helped_to_find_job ='$member_helped_to_find_job',
													  member_still_unemployed ='$member_still_unemployed',
													  sessions_to_improve_job_hunt ='$sessions_to_improve_job_hunt',
													  sessions_to_improve_resume ='$sessions_to_improve_resume',
													  sessions_to_learn_new_trade ='$sessions_to_learn_new_trade',
													  teams_in_touch_new_trend ='$teams_in_touch_new_trend',
													  students_encouraged_for_university ='$students_encouraged_for_university',
													  number_of_receipt_recovered ='$number_of_receipt_recovered',
													  number_of_bank_deposits ='$number_of_bank_deposits',
													  total_deposits ='$total_deposits',
													  total_payments ='$total_payments',
													  total_new_ahmadis ='$total_new_ahmadis',
													  new_ahmadis_promot_in_tahrik ='$new_ahmadis_promot_in_tahrik',
													  sessions_to_improve_above ='$sessions_to_improve_above',
													  sessions_to_improve_above_attendance ='$sessions_to_improve_above_attendance',
													  member_start_pay_chanda ='$member_start_pay_chanda',
													  member_still_not_pay_chanda ='$member_still_not_pay_chanda',
													  member_start_pay_chanda_this_qurater ='$member_start_pay_chanda_this_qurater',
													  member_pay_chanda_irregularly ='$member_pay_chanda_irregularly',
													  member_pay_chanda_not_at_all ='$member_pay_chanda_not_at_all',
													  strategy_to_devise_above ='$strategy_to_devise_above',
													  strategy_to_devise_above_attachment ='$file_name2',
													  member_started_pre_auth_chanda_payment ='$member_started_pre_auth_chanda_payment'
												  WHERE rid = '$qrrid'";
									//print "$insert_data";
									$result=@mysql_db_query($dbname,$insert_data,$id_link);
									if ($result == "1"){
										//$subject = "$Field11 - reg success";
										//mail("$Field8", "Online Registration", $smsg, "From:mansooranasir@gmail.com");
									} else {
										$Error = "Error";
									}
								} else {
										//if ($branch_code=="") {
										//	$brnch = $branch;
										//} else {
										//	$brnch = $branch_code;
										//}
										  $insert_data = "insert into ami_quarterly_reports
												  values ('','$branch','$month','$year','$quarter',
													  '$number_of_amila_meetings',
													  '$total_amila_members',
													  '$amila_meeting1_date',
													  '$amila_meeting2_date',
													  '$amila_meeting3_date',
													  '$amila_meeting1_venue',
													  '$amila_meeting2_venue',
													  '$amila_meeting3_venue',
													  '$amila_meeting1_attendance',
													  '$amila_meeting2_attendance',
													  '$amila_meeting3_attendance',
													  '$number_of_gbody_meetings',
													  '$total_adult_members',
													  '$number_of_members_not_attended',
													  '$action_taken',
													  '$gbody_meeting1_date',
													  '$gbody_meeting2_date',
													  '$gbody_meeting3_date',
													  '$gbody_meeting1_venue',
													  '$gbody_meeting2_venue',
													  '$gbody_meeting3_venue',
													  '$gbody_gents_in_meeting1',
													  '$gbody_gents_in_meeting2',
													  '$gbody_gents_in_meeting3',
													  '$gbody_ladies_in_meeting1',
													  '$gbody_ladies_in_meeting2',
													  '$gbody_ladies_in_meeting3',
													  '$gbody_children_in_meeting1',
													  '$gbody_children_in_meeting2',
													  '$gbody_children_in_meeting3',
													  '$total_households',
													  '$total_members',
													  '$members_moved_out',
													  '$members_moved_in',
													  '$household_visited_by_P_GS1',
													  '$household_visited_by_P_GS2',
													  '$household_visited_by_P_GS3',
													  '$household_visited_by_amila1',
													  '$household_visited_by_amila2',
													  '$household_visited_by_amila3',
													  '$file_name0',
													  '$highlights_of_main_activities',
													  '$number_of_daeen_active',
													  '$number_of_daeen_inactive',
													  '$number_of_waqifeen_ardi',
													  '$days_of_waqf',
													  '$people_benefited_from_waqifeen_ardi',
													  '$baits_achieved',
													  '$baits_inprogress',
													  '$baits_target_for_the_year',
													  '$baits_target_for_the_quarter',
													  '$letirature_distributed_last_quarter',
													  '$letirature_distributed_this_quarter',
													  '$tabligh_training_sessions_last_quarter',
													  '$tabligh_training_sessions_this_quarter',
													  '$new_contacts_last_quarter',
													  '$new_contacts_this_quarter',
													  '$members_offering_5_daily_prayers_this_quarter',
													  '$members_offering_5_daily_prayers_last_quarter',
													  '$members_offering_juma_this_quarter',
													  '$members_offering_juma_last_quarter',
													  '$members_offering_1_congregational_prayer',
													  '$members_listen_hudurs_sermon',
													  '$members_watch_mta',
													  '$namaz_centers',
													  '$unislamic_practice_eliminated',
													  '$members_provided_counseling',
													  '$members_know_salat_trans_below_15',
													  '$members_know_salat_trans_above_15',
													  '$members_know_salat_trans_last_quarter',
													  '$members_know_salat_trans_this_quarter',
													  '$members_know_salat_trans_target_quarter',
													  '$members_reading_books_of_PM_below_15',
													  '$members_reading_books_of_PM_above_15',
													  '$members_reading_books_of_PM_last_quarter',
													  '$members_reading_books_of_PM_this_quarter',
													  '$members_reading_books_of_PM_target_quarter',
													  '$members_weekly_religious_classes_below_15',
													  '$members_weekly_religious_classes_above_15',
													  '$members_weekly_religious_classes_last_quarter',
													  '$members_weekly_religious_classes_this_quarter',
													  '$members_weekly_religious_classes_target_quarter',
													  '$educational_sessions_last_quarter',
													  '$educational_sessions_this_quarter',
													  '$educational_sessions_target_quarter',
													  '$classes_reading_quran_last_quarter',
													  '$classes_reading_quran_this_quarter',
													  '$classes_reading_quran_target_quarter',
													  '$classes_attendance_quran_last_quarter',
													  '$classes_attendance_quran_this_quarter',
													  '$classes_trans_quran_last_quarter',
													  '$classes_trans_quran_this_quarter',
													  '$classes_trans_quran_target_quarter',
													  '$classes_trans_attendance_quran_last_quarter',
													  '$classes_trans_attendance_quran_this_quarter',
													  '$classes_pron_quran_last_quarter',
													  '$classes_pron_quran_this_quarter',
													  '$classes_pron_quran_target_quarter',
													  '$classes_pron_attendance_quran_last_quarter',
													  '$classes_pron_attendance_quran_this_quarter',
													  '$members_reading_quran_last_quarter',
													  '$members_reading_quran_this_quarter',
													  '$members_reading_quran_target_quarter',
													  '$members_trans_quran_last_quarter',
													  '$members_trans_quran_this_quarter',
													  '$members_trans_quran_target_quarter',
													  '$members_did_waqf_ardi_last_quarter',
													  '$members_did_waqf_ardi_this_quarter',
													  '$members_did_waqf_ardi_target_quarter',
													  '$members_pledged_waqf_ardi_last_quarter',
													  '$members_pledged_waqf_ardi_this_quarter',
													  '$members_pledged_waqf_ardi_target_quarter',
													  '$number_of_exhibition_last_quarter',
													  '$number_of_exhibition_this_quarter',
													  '$number_of_exhibition_target_quarter',
													  '$number_of_articles_last_quarter',
													  '$number_of_articles_this_quarter',
													  '$number_of_articles_target_quarter',
													  '$number_of_books_last_quarter',
													  '$number_of_books_this_quarter',
													  '$number_of_books_target_quarter',
													  '$number_of_books_sold_last_quarter',
													  '$number_of_books_sold_this_quarter',
													  '$number_of_books_sold_target_quarter',
													  '$number_of_members_writing_speaking_last_quarter',
													  '$number_of_members_writing_speaking_this_quarter',
													  '$number_of_members_writing_speaking_target_quarter',
													  '$number_of_members_subscribed_last_quarter',
													  '$number_of_members_subscribed_this_quarter',
													  '$number_of_members_subscribed_target_quarter',
													  '$number_of_teams_formed_last_quarter',
													  '$number_of_teams_formed_this_quarter',
													  '$number_of_teams_formed_target_quarter',
													  '$mta_connection_this_quarter',
													  '$total_household_in_branch',
													  '$household_without_mta',
													  '$program_produced_for_mta',
													  '$program_telecasted',
													  '$technical_members',
													  '$av_added_to_local_library',
													  '$message_broadcast',
													  '$trained_member_produced_mta_program',
													  '$uk_details',
													  '$marriageable_male',
													  '$marriageable_female',
													  '$matchmaking_completed',
													  '$matchmaking_inprogress',
													  '$counseling_sessions',
													  '$sessions_for_married_members',
													  '$number_of_gov_official_last_quarter',
													  '$number_of_gov_official_this_quarter',
													  '$number_of_gov_official_target_quarter',
													  '$number_of_gov_official_rights_last_quarter',
													  '$number_of_gov_official_rights_this_quarter',
													  '$number_of_gov_official_rights_target_quarter',
													  '$number_of_gov_official_persecution_last_quarter',
													  '$number_of_gov_official_persecution_this_quarter',
													  '$number_of_gov_official_persecution_target_quarter',
													  '$number_of_article_written_last_quarter',
													  '$number_of_article_written_this_quarter',
													  '$number_of_article_written_target_quarter',
													  '$number_of_com_understanding_last_quarter',
													  '$number_of_com_understanding_this_quarter',
													  '$number_of_com_understanding_target_quarter',
													  '$dispute_resolved',
													  '$dispute_outstanding',
													  '$ijlas_e_aam',
													  '$ijlas_e_aam_attendance',
													  '$members_helped',
													  '$members_settled_in_canada',
													  '$members_unemployed',
													  '$training_for_trade',
													  '$training_for_trade_attendance',
													  '$sessions_for_emerging_market',
													  '$sessions_for_emerging_market_attendance',
													  '$media_propaganda_against_jamaat',
													  '$media_propaganda_against_jamaat_response',
													  '$ua_details',
													  '$number_of_earning_members',
													  '$members_pay_chanda_regulaly',
													  '$members_pay_chanda_irregulaly',
													  '$members_pay_chanda_not_at_all',
													  '$members_start_pay_chanda_this_quarter',
													  '$efforts_made',
													  '$file_name1',
													  '$chanda_aam_collected',
													  '$chanda_jalsa_salana_collected',
													  '$chanda_wasiyyat_collected',
													  '$expense_this_quarter',
													  '$expense_to_date',
													  '$training_sessions_hospitality',
													  '$training_hospitality_member',
													  '$homes_ready_accommodation_to_guest ',
													  '$member_ready_diyafat_for_guest',
													  '$diyafat_served_events',
													  '$guest_served',
													  '$sessions_for_wasiyyat_last_quarter',
													  '$sessions_for_wasiyyat_this_quarter',
													  '$sessions_for_wasiyyat_target_quarter',
													  '$sessions_for_wasiyyat_condition_last_quarter',
													  '$sessions_for_wasiyyat_condition_this_quarter',
													  '$sessions_for_wasiyyat_condition_target_quarter',
													  '$sessions_for_musis_res_last_quarter',
													  '$sessions_for_musis_res_this_quarter',
													  '$sessions_for_musis_res_target_quarter',
													  '$sessions_for_musis_edu_last_quarter',
													  '$sessions_for_musis_edu_this_quarter',
													  '$sessions_for_musis_edu_target_quarter',
													  '$member_become_musis_last_quarter',
													  '$member_become_musis_this_quarter',
													  '$member_become_musis_target_quarter',
													  '$member_offered_teaching_quran',
													  '$member_edu_tahrik',
													  '$member_entered_tahrik',
													  '$member_approached_tahrik',
													  '$member_increased_tahrik_pledge',
													  '$member_edu_waqf_jadid',
													  '$member_entered_waqf_jadid',
													  '$waqf_jadid_collection_target',
													  '$member_waqf_jadid_with_pledge',
													  '$number_receipt_processed',
													  '$total_income',
													  '$total_expense',
													  '$receipt_books_issued',
													  '$receipt_books_returned',
													  '$members_res_for_property_maintenance',
													  '$members_res_for_property_security',
													  '$members_res_for_property_cleanliness',
													  '$members_trained_above_activities',
													  '$members_trained_above_participation',
													  '$muhasib_team_memebrs',
													  '$expense_for_the_activities',
													  '$expense_muhasib_to_date',
													  '$number_of_activities_for_future_needs',
													  '$total_number_of_waqifeen',
													  '$waqifeen_punctual_in_namaz',
													  '$waqifeen_punctual_in_tahajjud',
													  '$waqifeen_punctual_reading_quran',
													  '$waqifeen_made_punctual_in_namaz',
													  '$waqifeen_made_punctual_in_tahajjud',
													  '$waqifeen_made_punctual_reading_quran',
													  '$waqifeen_watch_mta',
													  '$waqifeen_percent_improved_mta',
													  '$waqifeen_taught_namaz',
													  '$waqifeen_taught_quran',
													  '$waqifeen_taught_trans',
													  '$waqifeen_book_assigned',
													  '$waqifeen_read_book_assigned',
													  '$waqifeen_taught_tabligh',
													  '$waqifeen_participated',
													  '$waqifeen_sports_activities',
													  '$waqifeen_outdoor_trips',
													  '$waqifeen_parent_meetings',
													  '$waqifeen_parent_attendance',
													  '$waqifeen_taught_languages',
													  '$waqifeen_taught_name_of_languages',
													  '$members_with_agr_knowledge',
													  '$members_in_agr_field ',
													  '$members_learn_agr_skills',
													  '$sessions_for_agr_knowledge',
													  '$number_of_houses_with_kitchen_backyard',
													  '$number_of_houses_with_kitchen_garden',
													  '$member_helped_to_find_job',
													  '$member_still_unemployed',
													  '$sessions_to_improve_job_hunt',
													  '$sessions_to_improve_resume',
													  '$sessions_to_learn_new_trade',
													  '$teams_in_touch_new_trend',
													  '$students_encouraged_for_university',
													  '$number_of_receipt_recovered',
													  '$number_of_bank_deposits',
													  '$total_deposits',
													  '$total_payments',
													  '$total_new_ahmadis',
													  '$new_ahmadis_promot_in_tahrik',
													  '$sessions_to_improve_above',
													  '$sessions_to_improve_above_attendance',
													  '$member_start_pay_chanda',
													  '$member_still_not_pay_chanda',
													  '$member_start_pay_chanda_this_qurater',
													  '$member_pay_chanda_irregularly',
													  '$member_pay_chanda_not_at_all',
													  '$strategy_to_devise_above',
													  '$file_name2',
													  '$member_started_pre_auth_chanda_payment');";
									//print "$insert_data";
									$result=@mysql_db_query($dbname,$insert_data,$id_link);
									if ($result == "1"){
										//$subject = "$Field11 - reg success";
										//mail("$Field8", "Online Registration", $smsg, "From:mansooranasir@gmail.com");
									} else {
										$Error = "Error";
									}
								}
							}
							//QR report ends here

							if ($user_level=="N"){
								//print "national";
								//print "$rid";
								if ($rid!=""){
									//print "update";
									$insert_data = "UPDATE ami_all_reports SET
												  activities_this_month	='$activities_this_month',
												  problems 		='$problems',
												  help 			='$help',
												  activities_next_month	='$activities_next_month',
												  comments 		='$comments',
												  remarks_national_sec = '$remarks_national_sec',
												  remarks_amir	= '$remarks_amir',
												  attachment	= '$file_name',
												  status 		= '$status'
												  WHERE rid		='$rid'";
 									//print "$insert_data";
 									$result=@mysql_db_query($dbname,$insert_data,$id_link);
									if ($result == "1"){
										//$subject = "$Field11 - reg success";
										//mail("$Field8", "Online Registration", $smsg, "From:mansooranasir@gmail.com");
									} else {
										$Error = "Error";
									}
								} else {
									//print "insert";
									$insert_data = "insert into ami_all_reports
													values ('','$pdt','$branch','$report_code',
												   '$month','$year','$user_name','$activities_this_month',
												   '$problems','$help','$activities_next_month',
												   '$comments','$remarks_national_sec','$remarks_amir',
												   '$file_name','$status','0');";
 									//print "$insert_data";
 									$result=@mysql_db_query($dbname,$insert_data,$id_link);
									if ($result == "1"){
										//$subject = "$Field11 - reg success";
										//mail("$Field8", "Online Registration", $smsg, "From:mansooranasir@gmail.com");
									} else {
										$Error = "Error";
									}
								}
							} else {
								//print "local";
								if ($rid){
									//print "update";
									$insert_data = "UPDATE ami_all_reports SET
												  activities_this_month	='$activities_this_month',
												  problems 		='$problems',
												  help 			='$help',
												  activities_next_month	='$activities_next_month',
												  comments 		='$comments',
												  attachment	= '$file_name',
												  status 		= '$status'
												  WHERE rid		='$rid'";
 									//print "$insert_data";
 									$result=@mysql_db_query($dbname,$insert_data,$id_link);
									if ($result == "1"){
										//$subject = "$Field11 - reg success";
										//mail("$Field8", "Online Registration", $smsg, "From:mansooranasir@gmail.com");
									} else {
										$Error = "Error";
									}
								} else {
									//print "insert";
									$insert_data = "insert into ami_all_reports values
												  ('','$pdt','$branch','$report_code',
												   '$month','$year','$user_name','$activities_this_month',
												   '$problems','$help','$activities_next_month',
												   '$comments','','','$file_name','$status','0');";
 									//print "$insert_data";
 									$result=@mysql_db_query($dbname,$insert_data,$id_link);
									if ($result == "1"){
										//$subject = "$Field11 - reg success";
										//mail("$Field8", "Online Registration", $smsg, "From:mansooranasir@gmail.com");
									} else {
										$Error = "Error";
									}
								}
							}
							//All other reports/remaining GS report ends here
								   if ($Error != "") {
									$last_page_message = "Error occurred while submitting report. Report may already exist.";
								   } else {
									$last_page_message = "Report submitted successfully.";
								   }
									// reset these as we dont' want these to be forwarded to list_reports
								   	$month="";
                                        				$year="";
                                        				$status="";
                                        				$user_dept="";
                                        				$report_code="";
                                        				$branch="";

									include ("list_reports.php"); 
?>
