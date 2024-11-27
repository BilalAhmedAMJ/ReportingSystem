<?
include("../incl/dbcon.php");
include ("protected.php");
							 $pdt = date("Y-m-d");
							$Error="";

							//AR report starts here
							//print "Before AR\n";
							//print $arrid;
							if ($report_code=="AR"){
							    if ($arrid!="") {
								    $insert_data = "update ami_annual_reports set
										  total_families='$total_families',
										  total_members='$total_members',
										  active_families='$active_families',
										  active_members='$active_members',
										  new_active_members='$new_active_members',
										  new_active_families='$new_active_families',
										  num_general_meetings='$num_general_meetings',
										  average_attendance='$average_attendance',
										  num_amila_meetings='$num_amila_meetings',
										  num_reports_sent='$num_reports_sent',
										  num_tabligh_events='$num_tabligh_events',
										  new_active_tabligh_members='$new_active_tabligh_members',
										  num_prayer_centers='$num_prayer_centers',
										  num_salat_offered='$num_salat_offered',
										  attendance_prayer_centers='$attendance_prayer_centers',
										  average_jumma_attendance='$average_jumma_attendance',
										  regular_quran_class_held='$regular_quran_class_held',
										  attendance_regular_quran_class='$attendance_regular_quran_class',
										  num_paying_ba_sharaa='$num_paying_ba_sharaa',
										  started_paying_ba_sharaa='$started_paying_ba_sharaa',
										  comments='$comments'
									  WHERE rid = '$arrid'";
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
									  $insert_data = "insert into ami_annual_reports
											  values ('','$branch','$year','$month',
											  '$total_families',
											  '$total_members',
											  '$active_families',
											  '$active_members',
											  '$new_active_members',
											  '$new_active_families',
											  '$num_general_meetings',
											  '$average_attendance',
											  '$num_amila_meetings',
											  '$num_reports_sent',
											  '$num_tabligh_events',
											  '$new_active_tabligh_members',
											  '$num_prayer_centers',
											  '$num_salat_offered',
											  '$attendance_prayer_centers',
											  '$average_jumma_attendance',
											  '$regular_quran_class_held',
											  '$attendance_regular_quran_class',
											  '$num_paying_ba_sharaa',
											  '$started_paying_ba_sharaa',
											  '$comments');";
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
							//AR report ends here

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
