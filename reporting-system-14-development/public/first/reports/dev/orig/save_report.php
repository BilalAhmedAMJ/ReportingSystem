<?
include("../incl/dbcon.php");
include ("protected.php"); 
							 $pdt = date("Y-m-d");
							$Error="";

							//GS report starts here
							//print "Before GS\n";
							if ($report_code=="GS"){
								//load attachments
								//print "Uploading file\n";
								$uploaddir = getenv("DOCUMENT_ROOT") . '/reports/attachments/';
								//$uploaddir = '/var/www/html/reports/attachments/';
								$uploadfile = $uploaddir . $_FILES['meeting1_attachment']['name'];
								$tmpuploadfile = $_FILES['meeting1_attachment']['tmp_name'];
								//print "$uploadfile\n";
								//print "$tmpuploadfile\n";
								if ($tmpuploadfile){
									$file_name0 = $_FILES['meeting1_attachment']['name'];
									//if (copy($_FILES['meeting1_attachment']['tmp_name'], $uploadfile)) {
									if (move_uploaded_file($_FILES['meeting1_attachment']['tmp_name'], $uploadfile)) {
									   //print "File is valid, and was successfully uploaded. ";
									   chmod ($uploadfile, 0644);
									} else {
									   //print "Possible file upload failed\n";
									}
								}
								//check if the new file is empty then keep the old attachment
								if (($meeting1_attachment=="") && ($fmeeting1_attachment!="") && ($gsrid)) {
									$file_name0 =$fmeeting1_attachment;
								}

								//$uploaddir = '/var/www/html/reports/attachments/';
								$uploaddir = getenv("DOCUMENT_ROOT") . '/reports/attachments/';
								$uploadfile = $uploaddir . $_FILES['meeting2_attachment']['name'];
								$tmpuploadfile = $_FILES['meeting2_attachment']['tmp_name'];
								if ($tmpuploadfile){
									$file_name1 = $_FILES['meeting2_attachment']['name'];
									//if (copy($_FILES['meeting2_attachment']['tmp_name'], $uploadfile)) {
									if (move_uploaded_file($_FILES['meeting2_attachment']['tmp_name'], $uploadfile)) {
									   //print "File is valid, and was successfully uploaded. ";
									   chmod ($uploadfile, 0644);
									} else {
									   //print "Possible file upload failed\n";
									}
								}
								//check if the new file is empty then keep the old attachment
								if (($meeting2_attachment=="") && ($fmeeting2_attachment!="") && ($gsrid)) {
									$file_name1 =$fmeeting2_attachment;
								}

								//$uploaddir = '/var/www/html/reports/attachments/';
								$uploaddir = getenv("DOCUMENT_ROOT") . '/reports/attachments/';
								$uploadfile = $uploaddir . $_FILES['gbody_meeting_attachment']['name'];
								$tmpuploadfile = $_FILES['gbody_meeting_attachment']['tmp_name'];
								if ($tmpuploadfile){
									$file_name2 = $_FILES['gbody_meeting_attachment']['name'];
									//if (copy($_FILES['gbody_meeting_attachment']['tmp_name'], $uploadfile)) {
									if (move_uploaded_file($_FILES['gbody_meeting_attachment']['tmp_name'], $uploadfile)) {
									   //print "File is valid, and was successfully uploaded. ";
									   chmod ($uploadfile, 0644);
									} else {
									   //print "Possible file upload failed\n";
									}
								}
								//check if the new file is empty then keep the old attachment
								if (($gbody_meeting_attachment=="") && ($fgbody_meeting_attachment!="") && ($gsrid)) {
									$file_name2 =$fgbody_meeting_attachment;
								}

								//$uploaddir = '/var/www/html/reports/attachments/';
								$uploaddir = getenv("DOCUMENT_ROOT") . '/reports/attachments/';
								$uploadfile = $uploaddir . $_FILES['tajneed_attachment']['name'];
								$tmpuploadfile = $_FILES['tajneed_attachment']['tmp_name'];
								if ($tmpuploadfile){
									$file_name3 = $_FILES['tajneed_attachment']['name'];
									//if (copy($_FILES['tajneed_attachment']['tmp_name'], $uploadfile)) {
									if (move_uploaded_file($_FILES['tajneed_attachment']['tmp_name'], $uploadfile)) {
									   //print "File is valid, and was successfully uploaded. ";
									   chmod ($uploadfile, 0644);
									} else {
									   //print "Possible file upload failed\n";
									}
								}
								//check if the new file is empty then keep the old attachment
								if (($tajneed_attachment=="") && ($ftajneed_attachment!="") && ($gsrid)) {
									$file_name3 =$ftajneed_attachment;
								}

								//$uploaddir = '/var/www/html/reports/attachments/';
								$uploaddir = getenv("DOCUMENT_ROOT") . '/reports/attachments/';
								$uploadfile = $uploaddir . $_FILES['visit_attachment']['name'];
								$tmpuploadfile = $_FILES['visit_attachment']['tmp_name'];
								if ($tmpuploadfile){
									$file_name4 = $_FILES['visit_attachment']['name'];
									//if (copy($_FILES['visit_attachment']['tmp_name'], $uploadfile)) {
									if (move_uploaded_file($_FILES['visit_attachment']['tmp_name'], $uploadfile)) {
									   //print "File is valid, and was successfully uploaded. ";
									   chmod ($uploadfile, 0644);
									} else {
									   //print "Possible file upload failed\n";
									}
								}
								//check if the new file is empty then keep the old attachment
								if (($visit_attachment=="") && ($fvisit_attachment!="") && ($gsrid)) {
									$file_name4 =$fvisit_attachment;
								}
								if ($amila_meeting1=="on"){
									$amila_meeting1="1";
								} else {
									$amila_meeting1="0";
								}
								if ($amila_meeting2=="on"){
									$amila_meeting2="1";
								} else {
									$amila_meeting2="0";
								}
								if ($gbody_meeting=="on"){
									$gbody_meeting="1";
								} else {
									$gbody_meeting="0";
								}
								if ($gs=="on"){
									$gs="1";
								} else {
									$gs="0";
								}
								if ($th=="on"){
									$th="1";
								} else {
									$th="0";
								}
								if ($tt=="on"){
									$tt="1";
								} else {
									$tt="0";
								}
								if ($tm=="on"){
									$tm="1";
								} else {
									$tm="0";
								}
								if ($it=="on"){
									$it="1";
								} else {
									$it="0";
								}
								if ($sb=="on"){
									$sb="1";
								} else {
									$sb="0";
								}
								if ($ra=="on"){
									$ra="1";
								} else {
									$ra="0";
								}
								if ($uk=="on"){
									$uk="1";
								} else {
									$uk="0";
								}
								if ($ua=="on"){
									$ua="1";
								} else {
									$ua="0";
								}
								if ($fe=="on"){
									$fe="1";
								} else {
									$fe="0";
								}
								if ($dt=="on"){
									$dt="1";
								} else {
									$dt="0";
								}
								if ($wa=="on"){
									$wa="1";
								} else {
									$wa="0";
								}
								if ($tj=="on"){
									$tj="1";
								} else {
									$tj="0";
								}
								if ($wj=="on"){
									$wj="1";
								} else {
									$wj="0";
								}
								if ($jd=="on"){
									$jd="1";
								} else {
									$jd="0";
								}
								if ($wn=="on"){
									$wn="1";
								} else {
									$wn="0";
								}
								if ($zt=="on"){
									$zt="1";
								} else {
									$zt="0";
								}
								if ($st=="on"){
									$st="1";
								} else {
									$st="0";
								}
								if ($mb=="on"){
									$mb="1";
								} else {
									$mb="0";
								}
								if ($an=="on"){
									$an="1";
								} else {
									$an="0";
								}
								if ($ws=="on"){
									$ws="1";
								} else {
									$ws="0";
								}
								if ($sm=="on"){
									$sm="1";
								} else {
									$sm="0";
								}
								if ($ti=="on"){
									$ti="1";
								} else {
									$ti="0";
								}

							    if ($gsrid!="") {
								    $insert_data = "update ami_all_gs_reports set
												  amila_meeting1 = '$amila_meeting1',
												  meeting1_date = '$meeting1_date',
												  meeting1_attendance = '$meeting1_attendance',
												  meeting1_minutes = '$meeting1_minutes',
												  meeting1_attachment = '$file_name0',
												  amila_meeting2 = '$amila_meeting2',
												  meeting2_date = '$meeting2_date',
												  meeting2_attendance = '$meeting2_attendance',
												  meeting2_minutes = '$meeting2_minutes',
												  meeting2_attachment = '$file_name1',
												  gbody_meeting = '$gbody_meeting',
												  gbody_meeting_date = '$gbody_meeting_date',
												  gbody_meeting_location = '$gbody_meeting_location',
												  gents = '$gents',
												  ladies = '$ladies',
												  children = '$children',
												  total_adults = '$total_adults',
												  not_attend_last_year = '$not_attend_last_year',
												  action_taken = '$action_taken',
												  gbody_brief_report = '$gbody_brief_report',
												  gbody_meeting_attachment = '$file_name2',
												  total_households = '$total_households',
												  total_members = '$total_members',
												  total_moved_out = '$total_moved_out',
												  total_moved_in = '$total_moved_in',
												  tajneed_attachment = '$file_name3',
												  households_visit_by_p_gs = '$households_visit_by_p_gs',
												  visit1_comments = '$visit1_comments',
												  households_visit_by_amila = '$households_visit_by_amila',
												  visit2_comments = '$visit2_comments',
												  visit_attachment = '$file_name4',
												  saiq1 = '$saiq1',
												  saiq1_visit_in_person = '$saiq1_visit_in_person',
												  saiq1_contact_by_phone = '$saiq1_contact_by_phone',
												  saiq1_message = '$saiq1_message',
												  saiq2 = '$saiq2',
												  saiq2_visit_in_person = '$saiq2_visit_in_person',
												  saiq2_contact_by_phone = '$saiq2_contact_by_phone',
												  saiq2_message = '$saiq2_message',
												  saiq3 = '$saiq3',
												  saiq3_visit_in_person = '$saiq3_visit_in_person',
												  saiq3_contact_by_phone = '$saiq3_contact_by_phone',
												  saiq3_message = '$saiq3_message',
												  saiq4 = '$saiq4',
												  saiq4_visit_in_person = '$saiq4_visit_in_person',
												  saiq4_contact_by_phone = '$saiq4_contact_by_phone',
												  saiq4_message = '$saiq4_message',
												  saiq5 = '$saiq5',
												  saiq5_visit_in_person = '$saiq5_visit_in_person',
												  saiq5_contact_by_phone = '$saiq5_contact_by_phone',
												  saiq5_message = '$saiq5_message',
												  saiq6 = '$saiq6',
												  saiq6_visit_in_person = '$saiq6_visit_in_person',
												  saiq6_contact_by_phone = '$saiq6_contact_by_phone',
												  saiq6_message = '$saiq6_message',
                                                                                                  saiq7 = '$saiq7',
                                                                                                  saiq7_visit_in_person = '$saiq7_visit_in_person',
                                                                                                  saiq7_contact_by_phone = '$saiq7_contact_by_phone',
                                                                                                  saiq7_message = '$saiq7_message',
                                                                                                  saiq8 = '$saiq8',
                                                                                                  saiq8_visit_in_person = '$saiq8_visit_in_person',
                                                                                                  saiq8_contact_by_phone = '$saiq8_contact_by_phone',
                                                                                                  saiq8_message = '$saiq8_message',
                                                                                                  saiq9 = '$saiq9',
                                                                                                  saiq9_visit_in_person = '$saiq9_visit_in_person',
                                                                                                  saiq9_contact_by_phone = '$saiq9_contact_by_phone',
                                                                                                  saiq9_message = '$saiq9_message',
                                                                                                  saiq10 = '$saiq10',
                                                                                                  saiq10_visit_in_person = '$saiq10_visit_in_person',
                                                                                                  saiq10_contact_by_phone = '$saiq10_contact_by_phone',
                                                                                                  saiq10_message = '$saiq10_message',
												  gs = '$gs', th = '$th', tt = '$tt', tm = '$tm',
												  it = '$it', sb = '$sb', ra = '$ra', uk = '$uk',
												  ua = '$ua', fe = '$fe', dt = '$dt', wa = '$wa',
												  tj = '$tj', wj = '$wj', jd = '$jd', wn = '$wn',
												  zt = '$zt', st = '$st', mb = '$mb', an = '$an',
												  ws = '$ws', sm = '$sm', ti = '$ti'
												  WHERE rid = '$gsrid'";
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
										  $insert_data = "insert into ami_all_gs_reports
												  values ('','$branch','$month','$year',
												  '$amila_meeting1','$meeting1_date','$meeting1_attendance',
												  '$meeting1_minutes','$file_name0',
												  '$amila_meeting2','$meeting2_date',
												  '$meeting2_attendance','$meeting2_minutes',
												  '$file_name1','$gbody_meeting',
												  '$gbody_meeting_date','$gbody_meeting_location',
												  '$gents','$ladies','$children','$total_adults',
												  '$not_attend_last_year','$action_taken',
												  '$gbody_brief_report','$file_name2',
												  '$total_households','$total_members',
												  '$total_moved_out','$total_moved_in',
												  '$file_name3','$households_visit_by_p_gs',
												  '$visit1_comments','$households_visit_by_amila',
												  '$visit2_comments','$file_name4',
												  '$saiq1','$saiq1_visit_in_person',
												  '$saiq1_contact_by_phone','$saiq1_message',
												  '$saiq2','$saiq2_visit_in_person',
												  '$saiq2_contact_by_phone','$saiq2_message',
												  '$saiq3','$saiq3_visit_in_person',
												  '$saiq3_contact_by_phone','$saiq3_message',
												  '$saiq4','$saiq4_visit_in_person',
												  '$saiq4_contact_by_phone','$saiq4_message',
												  '$saiq5','$saiq5_visit_in_person',
												  '$saiq5_contact_by_phone','$saiq5_message',
												  '$saiq6','$saiq6_visit_in_person',
												  '$saiq6_contact_by_phone','$saiq6_message',
                                                                                                  '$saiq7','$saiq7_visit_in_person',
                                                                                                  '$saiq7_contact_by_phone','$saiq7_message',
                                                                                                  '$saiq8','$saiq8_visit_in_person',
                                                                                                  '$saiq8_contact_by_phone','$saiq8_message',
                                                                                                  '$saiq9','$saiq9_visit_in_person',
                                                                                                  '$saiq9_contact_by_phone','$saiq9_message',
                                                                                                  '$saiq10','$saiq10_visit_in_person',
                                                                                                  '$saiq10_contact_by_phone','$saiq10_message',
												  '$gs','$th','$tt','$tm','$it','$sb','$ra',
												  '$uk','$ua','$fe','$dt','$wa','$tj','$wj',
												  '$jd','$wn','$zt','$st','$mb','$an','$ws',
												  '$sm','$ti');";
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
							//GS report ends here

							//All other reports/remaining GS report starts here
							//$uploaddir = '/var/www/html/reports/attachments/';
							$uploaddir = getenv("DOCUMENT_ROOT") . '/reports/attachments/';
							$uploadfile = $uploaddir . $_FILES['attachment']['name'];
							$tmpuploadfile = $_FILES['attachment']['tmp_name'];
							if ($tmpuploadfile){
								$file_name = $_FILES['attachment']['name'];
								//if (copy($_FILES['attachment']['tmp_name'], $uploadfile)) {
								if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadfile)) {
								   //print "File is valid, and was successfully uploaded. ";
								   chmod ($uploadfile, 0644);
								} else {
								   //print "Possible file upload failed\n";
								}
							}
							if (($attachment=="") && ($file_nm!="") && ($rid)) {
								$file_name =$file_nm;
							}

							if ($user_level=="N"){
								//print "national";
								//print "$rid";
								if ($rid!=""){
									//print "update";
									if ($status < 2)
									{
										$insert_data = "UPDATE ami_all_reports SET
													date_posted = '$pdt',
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
									} else {
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
									}
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
 									$rid = mysql_insert_id();
									if ($result == "1"){
										//$subject = "$Field11 - reg success";
										//mail("$Field8", "Online Registration", $smsg, "From:mansooranasir@gmail.com");
									} else {
										$Error = "Error";
									}
								}
							} else {
								//print "local";
								if ($rid!=""){
									//print "update";
									if ($status < 2)
									{	
										$insert_data = "UPDATE ami_all_reports SET
													date_posted = '$pdt',
												  activities_this_month	='$activities_this_month',
												  problems 		='$problems',
												  help 			='$help',
												  activities_next_month	='$activities_next_month',
												  comments 		='$comments',
												  attachment	= '$file_name',
												  status 		= '$status'
												  WHERE rid		='$rid'";
									} else {
										$insert_data = "UPDATE ami_all_reports SET
												  activities_this_month	='$activities_this_month',
												  problems 		='$problems',
												  help 			='$help',
												  activities_next_month	='$activities_next_month',
												  comments 		='$comments',
												  attachment	= '$file_name',
												  status 		= '$status'
												  WHERE rid		='$rid'";
									}
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
 									$rid = mysql_insert_id();
									if ($result == "1"){
										//$subject = "$Field11 - reg success";
										//mail("$Field8", "Online Registration", $smsg, "From:mansooranasir@gmail.com");
									} else {
										$Error = "Error";
									}
								}
							}
							//All other reports/remaining GS report ends here
							
							//Send an email notification to local sec. and president,
							// if there is any comment by national murkaz
							if (($remarks_national_sec !="") || ($remarks_amir !="") || ($status=="1")){
								$query = "SELECT a.branch_code, a.report_code, b.report_name, c.month_desc, a.year FROM ami_all_reports a, ami_reports b, months c WHERE a.rid = '$rid' and a.report_code = b.report_code and a.month = c.month_num";
								$result = @mysql_db_query($dbname,$query);
								if ($result){
									$row = mysql_fetch_array($result);
			 						$branch_code = $row['branch_code'];
			 						$report_code = $row['report_code'];
			 						$report_name = $row['report_name'];
			 						$year = $row['year'];
			 						$month_desc = substr ($row['month_desc'],0,3);
			 						if ($report_code =="GS") {
										$query = "SELECT user_email FROM ami_users WHERE user_type in ('P','GS') and branch_code = '$branch_code' and user_email !=''";
			 						} else {
			 							$query = "SELECT user_email FROM ami_users WHERE user_type in ('P','GS','$report_code') and branch_code = '$branch_code' and user_email !=''";
			 						}
			 						$email_list="";
									$result = @mysql_db_query($dbname,$query);
									while ($row = mysql_fetch_array($result)) {
										$email_list .= $row['user_email'] . ",";
									}
									$email_list = substr($email_list,0,strlen($email_list)-1);
									if ($email_list !="") {
									    $message = "\n\nAssslam o Alaikum wa Rahmatullah";
									    if (($remarks_national_sec !="") || ($remarks_amir !="")) {
									        $message .= "\nThis is an email notification that there is an update";
									        $message .= "\non the $report_name ($month_desc, $year) report by National Markaz,";
									        $message .= "\nPlease login to AMJ monthly reports to review the comments.";
									    } else {
									        $message .= "\nThis is an email notification that the report for the department of";
									        $message .= "\n$report_name ($month_desc, $year) is now complete.";
									        $message .= "\nPlease login to AMJ monthly reports to review and verify the report.";
									    }
									    $message .= "\nThere is no need to reply to this email.";
									    $message .= "\n\nWassalam";
									    $message .= "\nNational GS Department";

									     mail("$email_list", "AMJ Report update notification - $report_name", $message, "From:noreply@ahmadiyya.ca");
										//mail("webmaster@ahmadiyya.ca", "AMJ Report update notification - $report_name", $message, "From:noreply@ahmadiyya.ca");
			 						}
			 					}
							}
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
