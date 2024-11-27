<?
include("../incl/dbcon.php");
include ("protected.php");
							$pdt = date("Y-m-d");
							$Error="";

							//TR report starts here
							//print "Before TR\n";
							if ($report_code=="TR"){
								//load attachments
								//print "Uploading file\n";
								//$uploaddir = '/var/www/html/reports/attachments/';
								$uploaddir = getenv("DOCUMENT_ROOT") . '/reports/attachments/';
								$uploadfile = $uploaddir . $_FILES['pub_material_attachment']['name'];
								$tmpuploadfile = $_FILES['pub_material_attachment']['tmp_name'];
								//print "$uploadfile\n";
								//print "$tmpuploadfile\n";
								if ($tmpuploadfile){
									$file_name0 = $_FILES['pub_material_attachment']['name'];
									//if (copy($_FILES['pub_material_attachment']['tmp_name'], $uploadfile)) {
									if (move_uploaded_file($_FILES['pub_material_attachment']['tmp_name'], $uploadfile)) {
									   //print "File is valid, and was successfully uploaded. ";
									   chmod ($uploadfile, 0644);
									} else {
									   //print "Possible file upload failed\n";
									}
								}
								//check if the new file is empty then keep the old attachment
								if (($pub_material_attachment=="") && ($fpub_material_attachment!="") && ($gsrid)) {
									$file_name0 =$fpub_material_attachment;
								}

								//$uploaddir = '/var/www/html/reports/attachments/';
								$uploaddir = getenv("DOCUMENT_ROOT") . '/reports/attachments/';
								$uploadfile = $uploaddir . $_FILES['pub_hq_material_attachment']['name'];
								$tmpuploadfile = $_FILES['pub_hq_material_attachment']['tmp_name'];
								if ($tmpuploadfile){
									$file_name1 = $_FILES['pub_hq_material_attachment']['name'];
									//if (copy($_FILES['pub_hq_material_attachment']['tmp_name'], $uploadfile)) {
									if (move_uploaded_file($_FILES['pub_hq_material_attachment']['tmp_name'], $uploadfile)) {
									   //print "File is valid, and was successfully uploaded. ";
									   chmod ($uploadfile, 0644);
									} else {
									   //print "Possible file upload failed\n";
									}
								}
								//check if the new file is empty then keep the old attachment
								if (($pub_hq_material_attachment=="") && ($fpub_hq_material_attachment!="") && ($gsrid)) {
									$file_name1 =$fpub_hq_material_attachment;
								}

							    if ($gsrid!="") {
								    $insert_data = "update ami_tabshir_reports set
											  daeen_this_month = '$daeen_this_month',
											  daeen_total = '$daeen_total',
											  people_intro_islam = '$people_intro_islam',
											  hq_sold_this_month = '$hq_sold_this_month',
											  hq_dist_this_month = '$hq_dist_this_month',
											  intro_dig = '$intro_dig',
											  trans_remark = '$trans_remark',
											  pub_material_attachment = '$file_name0',
											  tc_this_month = '$tc_this_month',
											  participation = '$participation',
											  train = '$train',
											  desc_program = '$desc_program',
											  bait_this_month = '$bait_this_month',
											  joined_nat = '$joined_nat',
											  name_nat = '$name_nat',
											  gen_pub = '$gen_pub',
											  exhib_this_month = '$exhib_this_month',
											  non_saw_exhib = '$non_saw_exhib',
											  exhib_details = '$exhib_details',
											  hq_trans_remark = '$hq_trans_remark',
											  pub_hq_material_attachment = '$file_name1',
											  stall_this_month = '$stall_this_month',
											  non_visited_stall = '$non_visited_stall',
											  stall_detail = '$stall_detail',
											  tv_prog_this_month = '$tv_prog_this_month',
											  total_hours = '$total_hours',
											  rc_msg_this_month = '$rc_msg_this_month',
											  news_paper = '$news_paper',
											  our_news_pub = '$our_news_pub',
											  imp_of_news = '$imp_of_news',
											  people_res_to_news = '$people_res_to_news',
											  imp_person_statement = '$imp_person_statement',
											  new_area = '$new_area',
											  med_assist = '$med_assist',
											  waqf_aarzi = '$waqf_aarzi',
											  assist_hospitals = '$assist_hospitals'
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
										  $insert_data = "insert into ami_tabshir_reports
												  values ('','$branch','$month','$year',
													  '$daeen_this_month',
													  '$daeen_total',
													  '$people_intro_islam',
													  '$hq_sold_this_month',
													  '$hq_dist_this_month',
													  '$intro_dig',
													  '$trans_remark',
													  '$file_name0',
													  '$tc_this_month',
													  '$participation',
													  '$train',
													  '$desc_program',
													  '$bait_this_month',
													  '$joined_nat',
													  '$name_nat',
													  '$gen_pub',
													  '$exhib_this_month',
													  '$non_saw_exhib',
													  '$exhib_details',
													  '$hq_trans_remark',
													  '$file_name1',
													  '$stall_this_month',
													  '$non_visited_stall',
													  '$stall_detail',
													  '$tv_prog_this_month',
													  '$total_hours',
													  '$rc_msg_this_month',
													  '$news_paper',
													  '$our_news_pub',
													  '$imp_of_news',
													  '$people_res_to_news',
													  '$imp_person_statement',
													  '$new_area',
													  '$med_assist',
													  '$waqf_aarzi',
													  '$assist_hospitals');";
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
							//TR report ends here

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
