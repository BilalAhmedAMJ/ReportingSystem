<?
include("../incl/dbcon.php");
include ("protected.php");
if (($user_level!="N") && ($user_type!="GS") && ($user_type!="P")){
	header("Location: list_reports.php");
}
?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">
<body bgcolor="#ffffff">
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" class="BoxCSS">
  <tr>
   <td valign="top" align="center">
		<table border="0" width="100%">
			<tr>
				<td>
					<table width="100%" border="0">
					<tr>
					<td>
					  <table width="100%" border="0">
						<tr>
							<td colspan="2">
								<table border="0" width="100%">
									<tr>
										<td align="left" nowrap><img src="/images/topbar-left.png" ></td>
										<td align="center" colspan="2"></td>
									</tr>
									<tr><td colspan="3"><hr width="100%"></td></tr>
									 <?
									  //Get the user name
									  $query2 = "SELECT user_name FROM ami_users WHERE user_id = '$user_id'";
									  $result2 = @mysql_db_query($dbname,$query2);
									  if ($result2){
										$row2 = mysql_fetch_array($result2);
										$user_name=$row2['user_name'];
									  } else {
										$user_name = $user_id;
									  }
									  ?>
									<tr>
										<td align="left" nowrap><b>Date:&nbsp;</b><? echo date("Y-m-d"); ?></td>
										<td align="left" width="100">&nbsp;</td>
										<td align="left"><b>Login user:&nbsp;</b><? echo $user_name ?></td>
									</tr>
									<tr>
										<td align="left" nowrap>
											<b>Status:&nbsp;</b>
											<? if ($status =='0') {?>
												Draft
											<? } else if ($status =='1') {?>
												Complete
											<? } else if ($status =='2') {?>
												Verified by president
											 <? } else if ($status =='3') {?>
												Received
											 <? } else if ($status =='all') {?>
												All
											 <? } else {?>
												Unknown
											<? }?>
										</td>
										<?
										if ($report_code!='all'){
											$query = "SELECT report_name FROM ami_reports where report_code = '$report_code'";
											$result = @mysql_db_query($dbname,$query);
											$row = mysql_fetch_array($result);
											$rname = $row['report_name'];
										} else {
											$rname = "All";
										}
										?>
										<td align="left" width="100">&nbsp;</td>
										<td align="left"><b>Office:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $rname; ?>
										<? if ($gs_report) {?>
											&nbsp;(Result based on GS report)
										<? }?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr bgcolor="#cccccc">
						  <td colspan="2">
							  <table border="0" class="BoxCSS" width="100%">
							  <tr><td>
								  <table cellspacing=0 cellpadding=2 border="1" width="100%">
						   <?if (($month=="all") && ($gs_report=="on")) {?>
								  <tr bgcolor="#cccccc">
									<th colspan="15"><span class="pageheader"><font color="#000000">Reports received during <? echo $year; ?></font></span></th>
								  </tr>
								  <tr  bgcolor="#cccccc">
									<td align="center"><span class="pageheader"><font color="#000000">Branch</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Jul <? echo $year; ?></font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Aug</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Sep</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Oct</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Nov</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Dec</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Jan <? echo $year+1; ?></font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Feb</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Mar</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Apr</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">May</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Jun</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Total<br>Reports</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Months<br>Count</font></td>
								  </tr>
									 <?
									  //Get all branches
									  //$query = "SELECT branch_name, branch_code FROM ami_branches where status=1 order by branch_name";
				if ($branch != 'all')
                                                                                      $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 AND (region_code='$branch' OR branch_code='$branch')  order by branch_name";
			 else if ($user_id=="Admin")
                                                                                      $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 order by branch_name";
										else if ($branch_code==$nat_branch)
                                                                                       $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%')  order by branch_name";
                                                                               else
                                                                                                $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code')  order by branch_name";
									  $result = @mysql_db_query($dbname,$query);
									  if ($result){
										$currow=1;
										$col1=0;$col2=0;$col3=0;$col4=0;$col5=0;$col6=0;$col7=0;$col8=0;$col9=0;$col10=0;$col11=0;$col12=0;$col13=0;
										$col1c=0;$col2c=0;$col3c=0;$col4c=0;$col5c=0;$col6c=0;$col7c=0;$col8c=0;$col9c=0;$col10c=0;$col11c=0;$col12c=0;$col13c=0;
										$tcount=0;
										while ($row = mysql_fetch_array($result)) {
											$bname = $row['branch_name'];
											$bcode = $row['branch_code'];
											$x=7;
											$yr=$year;
											if ($currow % 2 == 0) {
											   $bgc= "#F7F2F4";
											} else {
											   $bgc= "#FFFFFF";
											}
											$currow+=1;
											$rowtotal=0;
											$count=0;
											print "<tr bgcolor=\"$bgc\"><td nowrap>$bname</td>";
											while ((($x<13) && ($yr==$year)) || (($x<7) && ($yr==($year+1)))) {
												$query1 = "SELECT gs + th + tt + tm + it + sb + ra + uk + ua
													+ fe + dt + wa + tj + wj + jd + wn + zt + st + mb + an + ws + sm as cnt
													  FROM ami_all_gs_reports where branch_code = '$bcode' and month='$x' and year='$yr'";
												/*if ($report_code!="all") {
													$query1 = "SELECT $report_code as cnt
													  FROM ami_all_gs_reports where branch_code = '$bcode' and month='$x' and year='$yr'";
												}*/
												$result1 = @mysql_db_query($dbname,$query1);
												if ($result1) {
													$row1 = mysql_fetch_array($result1);
													$cnt = $row1['cnt'];
													if ($cnt>"0") {
														$count+=1;
														$rowtotal+=$cnt;
														if ($x=="1") $col1+=$cnt;
														if ($x=="2") $col2+=$cnt;
														if ($x=="3") $col3+=$cnt;
														if ($x=="4") $col4+=$cnt;
														if ($x=="5") $col5+=$cnt;
														if ($x=="6") $col6+=$cnt;
														if ($x=="7") $col7+=$cnt;
														if ($x=="8") $col8+=$cnt;
														if ($x=="9") $col9+=$cnt;
														if ($x=="10") $col10+=$cnt;
														if ($x=="11") $col11+=$cnt;
														if ($x=="12") $col12+=$cnt;
														if ($x=="1") $col1c++;
														if ($x=="2") $col2c++;
														if ($x=="3") $col3c++;
														if ($x=="4") $col4c++;
														if ($x=="5") $col5c++;
														if ($x=="6") $col6c++;
														if ($x=="7") $col7c++;
														if ($x=="8") $col8c++;
														if ($x=="9") $col9c++;
														if ($x=="10") $col10c++;
														if ($x=="11") $col11c++;
														if ($x=="12") $col12c++;
														
													} else {
														$cnt=0;
													}
												} else {
													$cnt=0;
												}
												if (($cnt==0) || ($cnt=="0")) {
													print "<td align=\"center\"></td>";
												} else {
													 if ($show_office) {
														$query_new = "SELECT a.report_code as REPCODE, r.report_name as NAME, CASE status WHEN '1' THEN 'C' WHEN '2' THEN 'V' WHEN '3' THEN 'R' WHEN '0' THEN 'D' END as STATUS FROM ami_all_reports as a, ami_reports as r  WHERE a.report_code=r.report_code and a.branch_code='$bcode' and year='$yr' and month='$x' and (a.status > 0)  order by r.report_name"; 
														$result_new = @mysql_db_query($dbname,$query_new);
														$rep_str = '';
														$cctr = 1;
														$rep_str = '<table border=1 cellpadding=1 cellspacing=0>';
														while ($row_new = mysql_fetch_array($result_new)) {
														//	if ($cctr++%2)
														//		$rep_str .= '<font color=blue>';
														//	else	
														//		$rep_str .= '<font color=red>';
															$rep_str .= '<tr><td>'.$row_new['NAME'].'</td><td>'.$row_new['STATUS'] .'</td></tr>';
													//		$rep_str .= '</font><br>';
													//		$rep_str .= '<br>';
														}
														$rep_str.='</table>';
													}
													print "<td nowrap valign=top align=\"center\"><b>$cnt</b><br>$rep_str</td>";
												}
												$x+=1;
												if ($x>12) {
													$yr=$year+1;
													$x=1;
												}
											}
											$col13+=$rowtotal;
											$tcount+=$count;
											if ($rowtotal==0) 
												print "<td align=\"center\"></td>";
											else
												print "<td align=\"center\"><b>$rowtotal</b></td>";
											if ($count==0) 
												print "<td align=\"center\"></td></tr>";
											else
												print "<td align=\"center\"><b>$count</b></td></tr>";
										}
										$col1t=(!$col1)?"":"$col1";
										$col2t=(!$col2)?"":"$col2";
										$col3t=(!$col3)?"":"$col3";
										$col4t=(!$col4)?"":"$col4";
										$col5t=(!$col5)?"":"$col5";
										$col6t=(!$col6)?"":"$col6";
										$col7t=(!$col7)?"":"$col7";
										$col8t=(!$col8)?"":"$col8";
										$col9t=(!$col9)?"":"$col9";
										$col10t=(!$col10)?"":"$col10";
										$col11t=(!$col11)?"":"$col11";
										$col12t=(!$col12)?"":"$col12";
										$col13t=(!$col13)?"":"$col13";
										$tcountt=(!$tcount)?"":"$tcount";
										print "<tr bgcolor=\"#F7F2F4\"><td><b>Total Reports:</b></td>
												<td align=\"center\"><b>$col7t</b></td><td align=\"center\"><b>$col8t</b></td>
												<td align=\"center\"><b>$col9t</b></td><td align=\"center\"><b>$col10t</b></td>
												<td align=\"center\"><b>$col11t</b></td><td align=\"center\"><b>$col12t</b></td>
												<td align=\"center\"><b>$col1t</b></td><td align=\"center\"><b>$col2t</b></td>
												<td align=\"center\"><b>$col3t</b></td><td align=\"center\"><b>$col4t</b></td>
												<td align=\"center\"><b>$col5t</b></td><td align=\"center\"><b>$col6t</b></td>
												<td align=\"center\"><b>$col13t</b></td><td align=\"center\"><b></b></td>
												</tr>";
										if ($branch=="all")
											print "<tr bgcolor=\"#F7F2F4\"><td><b>Total Branches:</b></td>
												<td align=\"center\"><b>$col7c</b></td><td align=\"center\"><b>$col8c</b></td>
												<td align=\"center\"><b>$col9c</b></td><td align=\"center\"><b>$col10c</b></td>
												<td align=\"center\"><b>$col11c</b></td><td align=\"center\"><b>$col12c</b></td>
												<td align=\"center\"><b>$col1c</b></td><td align=\"center\"><b>$col2c</b></td>
												<td align=\"center\"><b>$col3c</b></td><td align=\"center\"><b>$col4c</b></td>
												<td align=\"center\"><b>$col5c</b></td><td align=\"center\"><b>$col6c</b></td>
												<td align=\"center\"><b></b></td><td align=\"center\"><b>$tcountt</b></td>
												</tr>";
									  }
								   } else if (($month=="all") && ($gs_report=="")) {?>
								  <tr bgcolor="#cccccc">
									<th colspan="15"><span class="pageheader"><font color="#000000">Reports received during <?echo $year; ?>-<? echo $year+1; ?></font></span></th>
								  </tr>
								  <tr  bgcolor="#cccccc">
									<td align="center"><span class="pageheader"><font color="#000000">Branch</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Jul <? echo $year; ?></font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Aug</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Sep</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Oct</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Nov</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Dec</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Jan <? echo $year+1; ?></font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Feb</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Mar</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Apr</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">May</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Jun</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Total<br>Reports</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Months<br>Count</font></td>
								  </tr>
									 <?
									  //Get all branches
			//						  $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 order by branch_name";
				if ($branch != 'all')
                                                                                      $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 AND (region_code='$branch' OR branch_code='$branch')  order by branch_name";
			 else if ($user_id=="Admin")
                                                                                      $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 order by branch_name";
										else if ($branch_code==$nat_branch)
                                                                                       $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%')  order by branch_name";
                                                                               else
                                                                                                $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code')  order by branch_name";
									  $result = @mysql_db_query($dbname,$query);
									  if ($result){
										$currow=1;
										$col1=0;$col2=0;$col3=0;$col4=0;$col5=0;$col6=0;$col7=0;$col8=0;$col9=0;$col10=0;$col11=0;$col12=0;$col13=0;
										$col1c=0;$col2c=0;$col3c=0;$col4c=0;$col5c=0;$col6c=0;$col7c=0;$col8c=0;$col9c=0;$col10c=0;$col11c=0;$col12c=0;$col13c=0;
										$tcount=0;
										while ($row = mysql_fetch_array($result)) {
											$bname = $row['branch_name'];
											$bcode = $row['branch_code'];
											$x=7;
											$yr=$year;
											if ($currow % 2 == 0) {
											   $bgc= "#F7F2F4";
											} else {
											   $bgc= "#FFFFFF";
											}
											$currow+=1;
											$rowtotal=0;
											$count=0;
											print "<tr bgcolor=\"$bgc\"><td nowrap>$bname</td>";
											while ((($x<13) && ($yr==$year)) || (($x<7) && ($yr==($year+1)))) {
												if ($status=="all") {
													$query1 = "SELECT count(*) as cnt FROM ami_all_reports where branch_code = '$bcode' and month='$x' and year='$yr' and status>0";
												} else if ($status=="vf-rc") {
													$query1 = "SELECT count(*) as cnt FROM ami_all_reports where branch_code = '$bcode' and month='$x' and year='$yr' and status>1";
												} else {
													$query1 = "SELECT count(*) as cnt FROM ami_all_reports where branch_code = '$bcode' and month='$x' and year='$yr' and status='$status'";
												}
												if ($report_code!="all") {
													$query1 .= " and report_code = '$report_code'";
												}
												$result1 = @mysql_db_query($dbname,$query1);
												$row1 = mysql_fetch_array($result1);
												$cnt = $row1['cnt'];
												if ($cnt>"0") {
													$count+=1;
													$rowtotal+=$cnt;
													if ($x=="1") $col1+=$cnt;if ($x=="2") $col2+=$cnt;if ($x=="3") $col3+=$cnt;if ($x=="4") $col4+=$cnt;
													if ($x=="5") $col5+=$cnt;if ($x=="6") $col6+=$cnt;if ($x=="7") $col7+=$cnt;if ($x=="8") $col8+=$cnt;
													if ($x=="9") $col9+=$cnt;if ($x=="10") $col10+=$cnt;if ($x=="11") $col11+=$cnt;if ($x=="12") $col12+=$cnt;
														if ($x=="1") $col1c++;
														if ($x=="2") $col2c++;
														if ($x=="3") $col3c++;
														if ($x=="4") $col4c++;
														if ($x=="5") $col5c++;
														if ($x=="6") $col6c++;
														if ($x=="7") $col7c++;
														if ($x=="8") $col8c++;
														if ($x=="9") $col9c++;
														if ($x=="10") $col10c++;
														if ($x=="11") $col11c++;
														if ($x=="12") $col12c++;
												}
												if (($cnt==0) || ($cnt=="0")) {
													print "<td align=\"center\"></td>";
												} else  {
													if (($show_office) && ($rname == "All")){

														if ($status=="all") {
															$query_new = "SELECT a.report_code as REPCODE, r.report_name as NAME , CASE status WHEN '1' THEN 'C' WHEN '2' THEN 'V' WHEN '3' THEN 'R' WHEN '0' THEN 'D' END as STATUS FROM ami_all_reports as a, ami_reports as r WHERE a.report_code=r.report_code and a.branch_code='$bcode' and year='$yr' and month='$x' and (a.status > 0)  order by r.report_name"; 
														} else if ($status=="vf-rc") {
															$query_new = "SELECT a.report_code as REPCODE, r.report_name as NAME , CASE status WHEN '1' THEN 'C' WHEN '2' THEN 'V' WHEN '3' THEN 'R' WHEN '0' THEN 'D' END as STATUS FROM ami_all_reports as a, ami_reports as r WHERE a.report_code=r.report_code and a.branch_code='$bcode' and year='$yr' and month='$x' and (a.status > 1)  order by r.report_name"; 
														}
														else
														{	
															$query_new = "SELECT a.report_code as REPCODE, r.report_name as NAME FROM ami_all_reports as a, ami_reports as r WHERE a.report_code=r.report_code and a.branch_code='$bcode' and year='$yr' and month='$x' and (a.status = $status)  order by r.report_name"; 
														}
														$result_new = @mysql_db_query($dbname,$query_new);
														$rep_str = '';
														$cctr = 1;
														$rep_str = '<table border=1 cellpadding=1 cellspacing=0>';
														while ($row_new = mysql_fetch_array($result_new)) {
														//		if ($cctr++%2)
														//			$rep_str .= '<font color=#003399>';
														//		else	
																	$rep_str .= '<font color=#cccccc>';
														//	$rep_str .= '['.$row_new['NAME']. ' - '. $row_new['STATUS'].']';
															if (($status=="all") || ($status=="vf-rc")) 
																$rep_str .= '<tr><td nowrap>'.$row_new['NAME'].'</td><td>'.$row_new['STATUS'] .'</td></tr>';														  
															 else
																$rep_str .= '<tr><td nowrap>'.$row_new['NAME'].'</td></tr>';
																//$rep_str .= '</font><br>';
														}
														$rep_str .= '</table>';
													}
													if ($rname == "All")
														print "<td nowrap valign=top align=\"center\"><b>$cnt</b><br>$rep_str</td>";
													else	// specific report
													{
														if ($status=="all") 
															$query_new = "SELECT report_code, CASE status WHEN '1' THEN 'C' WHEN '2' THEN 'V' WHEN '3' THEN 'R' WHEN '0' THEN 'D' END as STATUS FROM ami_all_reports as a WHERE a.branch_code='$bcode' and year='$yr' and month='$x' and (a.status > 0) and report_code='$report_code'"; 
														else if ($status=="vf-rc") 
															$query_new = "SELECT report_code, CASE status WHEN '1' THEN 'C' WHEN '2' THEN 'V' WHEN '3' THEN 'R' WHEN '0' THEN 'D' END as STATUS FROM ami_all_reports as a WHERE a.branch_code='$bcode' and year='$yr' and month='$x' and (a.status > 1) and report_code='$report_code'"; 
														else
															$query_new = "SELECT report_code, CASE status WHEN '1' THEN 'C' WHEN '2' THEN 'V' WHEN '3' THEN 'R' WHEN '0' THEN 'D' END as STATUS FROM ami_all_reports as a WHERE a.branch_code='$bcode' and year='$yr' and month='$x' and (a.status = $status) and report_code='$report_code'"; 
														$result_new = @mysql_db_query($dbname,$query_new);
														$rep_str = '';
														if ($row_new = mysql_fetch_array($result_new)) {
															$rep_str = $row_new['STATUS'];
															print "<td nowrap valign=top align=\"center\"><b>$rep_str</b></td>";
														} else
															print "<td nowrap valign=top align=\"center\"><b>$cnt</b><br>$rep_str</td>";
													}
												}
												$x+=1;
												if ($x>12) {
													$yr=$year+1;
													$x=1;
												}
											}
											$col13+=$rowtotal;
											$tcount+=$count;
											if ($rowtotal==0) 
												print "<td align=\"center\"></td>";
											else
												print "<td align=\"center\"><b>$rowtotal</b></td>";
											if ($count==0) 
												print "<td align=\"center\"></td></tr>";
											else
												print "<td align=\"center\"><b>$count</b></td></tr>";
										}
										$col1t=(!$col1)?"":"$col1";
										$col2t=(!$col2)?"":"$col2";
										$col3t=(!$col3)?"":"$col3";
										$col4t=(!$col4)?"":"$col4";
										$col5t=(!$col5)?"":"$col5";
										$col6t=(!$col6)?"":"$col6";
										$col7t=(!$col7)?"":"$col7";
										$col8t=(!$col8)?"":"$col8";
										$col9t=(!$col9)?"":"$col9";
										$col10t=(!$col10)?"":"$col10";
										$col11t=(!$col11)?"":"$col11";
										$col12t=(!$col12)?"":"$col12";
										$col13t=(!$col13)?"":"$col13";
										$tcountt=(!$tcount)?"":"$tcount";
										print "<tr bgcolor=\"#F7F2F4\"><td><b>Total Reports:</b></td>
												<td align=\"center\"><b>$col7t</b></td><td align=\"center\"><b>$col8t</b></td>
												<td align=\"center\"><b>$col9t</b></td><td align=\"center\"><b>$col10t</b></td>
												<td align=\"center\"><b>$col11t</b></td><td align=\"center\"><b>$col12t</b></td>
												<td align=\"center\"><b>$col1t</b></td><td align=\"center\"><b>$col2t</b></td>
												<td align=\"center\"><b>$col3t</b></td><td align=\"center\"><b>$col4t</b></td>
												<td align=\"center\"><b>$col5t</b></td><td align=\"center\"><b>$col6t</b></td>
												<td align=\"center\"><b>$col13t</b></td><td align=\"center\"><b></b></td>
												</tr>";
										if ($branch=="all")
											print "<tr bgcolor=\"#F7F2F4\"><td><b>Total Branches:</b></td>
												<td align=\"center\"><b>$col7c</b></td><td align=\"center\"><b>$col8c</b></td>
												<td align=\"center\"><b>$col9c</b></td><td align=\"center\"><b>$col10c</b></td>
												<td align=\"center\"><b>$col11c</b></td><td align=\"center\"><b>$col12c</b></td>
												<td align=\"center\"><b>$col1c</b></td><td align=\"center\"><b>$col2c</b></td>
												<td align=\"center\"><b>$col3c</b></td><td align=\"center\"><b>$col4c</b></td>
												<td align=\"center\"><b>$col5c</b></td><td align=\"center\"><b>$col6c</b></td>
												<td align=\"center\"><b></b></td><td align=\"center\"><b>$tcountt</b></td>
												</tr>";
									  }
								  } else if (($month!="all") && ($branch=="all") && ($report_code=="all")) {?>
									  <tr bgcolor="#cccccc">
										<th colspan="25"><span class="pageheader"><font color="#000000">Reports received during <? echo $months["$month"]; ?><?echo $year-1; ?>-<? echo $year; ?></font></span></th>
									  </tr>
									 <?
									  //Get all branches
									  $query = "SELECT report_name, report_code FROM ami_reports order by report_name";
									  $result = @mysql_db_query($dbname,$query);
									  if ($result){
											print "<tr bgcolor=\"\#cccccc\"><td align=\"center\"><span><font color=\"#000000\"><b>Branch</b></font></span></td>";
											while ($row = mysql_fetch_array($result)) {
												$rname = $row['report_name'];
												$ami_reports .= "<b>" . $row['report_code'] . "</b> = ". $row['report_name'] . "<br>";
												//if ($rname == "Additional Secretary Mal") {
												//	$rname = "Add. Sec. Mal";
												//} else if ($rname == "Waqf Jadid (for new Ahmadis)") {
												//	$rname = "Waqf Ja. (NM)";
												//} else if ($rname == "General Secretary") {
												//	$rname = "Gen. Sec.";
												//} else if ($rname == "San`at-o-Tijarat") {
												//	$rname = "Sanat Tij.";
												//} else if ($rname == "Umur Kharijiyya") {
												//	$rname = "Umur Khar.";
												//} else if ($rname == "Sam`I Wa Basri") {
												//	$rname = "Sami Bas.";
												//} else if ($rname == "Tahrik Jadid") {
												//	$rname = "Tahrik Ja.";
												//} else if ($rname == "Rishta Nata") {
												//	$rname = "Rishta Na.";
												//}
												//print "<td align=\"center\" width=\"6\" valign=\"top\"><span><font color=\"#000000\"><b>" . implode(" ",preg_split('//', $rname, -1, PREG_SPLIT_NO_EMPTY)) . "</b></font></span></td>";
												print "<td align=\"center\" width=\"6\" valign=\"top\"><span><font color=\"#000000\"><b>" . $row['report_code'] . "</b></font></span></td>";
											}
											print "<td align=\"center\"><span><font color=\"#000000\"><b>Total</b></td>";
											print "<td align=\"center\"><span><font color=\"#000000\"><b>Legend</b></td></tr>";
									  }
									  //Get all branches
									  //$query = "SELECT branch_name, branch_code FROM ami_branches where status=1 order by branch_name";
			 if ($user_id=="Admin")
                                                                                      $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%')  order by branch_name";
										else if ($branch_code==$nat_branch)
                                                                                       $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%')  order by branch_name";
                                                                               else
                                                                                                $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code')  order by branch_name";
	
									  $result = @mysql_db_query($dbname,$query);
									  if ($result){
											$currow=1;
											$col1=0;$col2=0;$col3=0;$col4=0;$col5=0;$col6=0;$col7=0;$col8=0;$col9=0;$col10=0;$col11=0;$col12=0;
											$col13=0;$col14=0;$col15=0;$col16=0;$col17=0;$col18=0;$col19=0;$col20=0;$col21=0;$col22=0;$col23=0;
											while ($row = mysql_fetch_array($result)) {
												$bname = $row['branch_name'];
												$bcode = $row['branch_code'];
												if ($currow % 2 == 0) {
												   $bgc= "#F7F2F4";
												} else {
												   $bgc= "#FFFFFF";
												}
												$currow+=1;
												//if ($currow=="25") {
												//	print "<TR><TD colspan=\"24\" width=\"100\%\"><p style =\"page-break-after:always\"></TD></TR>";
												//}
												print "<tr bgcolor=\"$bgc\"><td  nowrap>$bname</td>";
												$query1 = "SELECT report_name, report_code FROM ami_reports order by report_name";
												$result1 = @mysql_db_query($dbname,$query1);
												$rowtotal=0;
												$x=1;
												while ($row1 = mysql_fetch_array($result1)) {
													$rcode = $row1['report_code'];
													if ($status=="all") {
														$query2 = "SELECT count(*) as cnt FROM ami_all_reports where branch_code = '$bcode' and report_code ='$rcode' and month='$month' and year='$year' and status>0";
													} else if ($status=="vf-rc") {
														$query2 = "SELECT count(*) as cnt FROM ami_all_reports where branch_code = '$bcode' and report_code ='$rcode' and month='$month' and year='$year' and status>1";
													} else {
														$query2 = "SELECT count(*) as cnt FROM ami_all_reports where branch_code = '$bcode' and report_code ='$rcode' and month='$month' and year='$year' and status='$status'";
													}
													$result2 = @mysql_db_query($dbname,$query2);
													$row2 = mysql_fetch_array($result2);
													$cnt = $row2['cnt'];
													if ($cnt=="1") {
														print "<td align=\"center\">X</td>";
														$rowtotal+=1;
														if ($x=="1") $col1+=1;if ($x=="2") $col2+=1;if ($x=="3") $col3+=1;if ($x=="4") $col4+=1;
														if ($x=="5") $col5+=1;if ($x=="6") $col6+=1;if ($x=="7") $col7+=1;if ($x=="8") $col8+=1;
														if ($x=="9") $col9+=1;if ($x=="10") $col10+=1;if ($x=="11") $col11+=1;if ($x=="12") $col12+=1;
														if ($x=="13") $col13+=1;if ($x=="14") $col14+=1;if ($x=="15") $col15+=1;if ($x=="16") $col16+=1;
														if ($x=="17") $col17+=1;if ($x=="18") $col18+=1;if ($x=="19") $col19+=1;if ($x=="20") $col20+=1;
														if ($x=="21") $col21+=1;if ($x=="22") $col22+=1;if ($x=="23") $col23+=1;
													} else {
														print "<td align=\"center\">&nbsp;</td>";
													}
													$x+=1;
												}
												$col24+=$rowtotal;
												$rowtotalt = (!$rowtotal)?"":"$rowtotal";
												if ($currow=="2") {
													print "<td align=\"center\"><b>$rowtotalt</b></td><td valign=\"top\" rowspan=\"50\">$ami_reports</td></tr>";
												} else {
													print "<td align=\"center\"><b>$rowtotalt</b></td></tr>";
												}
											}
											/*print "<tr bgcolor=\"#F7F2F4\"><td><b>Total:</b></td>
													<td align=\"center\"><b>$col1</b></td><td align=\"center\"><b>$col2</b></td>
													<td align=\"center\"><b>$col3</b></td><td align=\"center\"><b>$col4</b></td>
													<td align=\"center\"><b>$col5</b></td><td align=\"center\"><b>$col6</b></td>
													<td align=\"center\"><b>$col7</b></td><td align=\"center\"><b>$col8</b></td>
													<td align=\"center\"><b>$col9</b></td><td align=\"center\"><b>$col10</b></td>
													<td align=\"center\"><b>$col11</b></td><td align=\"center\"><b>$col12</b></td>
													<td align=\"center\"><b>$col13</b></td><td align=\"center\"><b>$col14</b></td>
													<td align=\"center\"><b>$col15</b></td><td align=\"center\"><b>$col16</b></td>
													<td align=\"center\"><b>$col17</b></td><td align=\"center\"><b>$col18</b></td>
													<td align=\"center\"><b>$col19</b></td><td align=\"center\"><b>$col20</b></td>
													<td align=\"center\"><b>$col21</b></td><td align=\"center\"><b>$col22</b></td>
													<td align=\"center\"><b>$col23</b></td><td align=\"center\"><b>$col24</b></td>
													</tr>";*/
											$col1t=(!$col1)?"":"$col1";
											$col2t=(!$col2)?"":"$col2";
											$col3t=(!$col3)?"":"$col3";
											$col4t=(!$col4)?"":"$col4";
											$col5t=(!$col5)?"":"$col5";
											$col6t=(!$col6)?"":"$col6";
											$col7t=(!$col7)?"":"$col7";
											$col8t=(!$col8)?"":"$col8";
											$col9t=(!$col9)?"":"$col9";
											$col10t=(!$col10)?"":"$col10";
											$col11t=(!$col11)?"":"$col11";
											$col12t=(!$col12)?"":"$col12";
											$col13t=(!$col13)?"":"$col13";
											$col14t=(!$col14)?"":"$col14";
											$col15t=(!$col15)?"":"$col15";
											$col16t=(!$col16)?"":"$col16";
											$col17t=(!$col17)?"":"$col17";
											$col18t=(!$col18)?"":"$col18";
											$col19t=(!$col19)?"":"$col19";
											$col20t=(!$col20)?"":"$col20";
											$col21t=(!$col21)?"":"$col21";
											$col22t=(!$col22)?"":"$col22";
											$col23t=(!$col23)?"":"$col23";
											$col24t=(!$col24)?"":"$col24";
											print "<tr bgcolor=\"#F7F2F4\"><td><b>Total:</b></td>
													<td align=\"center\"><b>$col1t</b></td><td align=\"center\"><b>$col2t</b></td>
													<td align=\"center\"><b>$col3t</b></td><td align=\"center\"><b>$col4t</b></td>
													<td align=\"center\"><b>$col5t</b></td><td align=\"center\"><b>$col6t</b></td>
													<td align=\"center\"><b>$col7t</b></td><td align=\"center\"><b>$col8t</b></td>
													<td align=\"center\"><b>$col9t</b></td><td align=\"center\"><b>$col10t</b></td>
													<td align=\"center\"><b>$col11t</b></td><td align=\"center\"><b>$col12t</b></td>
													<td align=\"center\"><b>$col13t</b></td><td align=\"center\"><b>$col14t</b></td>
													<td align=\"center\"><b>$col15t</b></td><td align=\"center\"><b>$col16t</b></td>
													<td align=\"center\"><b>$col17t</b></td><td align=\"center\"><b>$col18t</b></td>
													<td align=\"center\"><b>$col19t</b></td><td align=\"center\"><b>$col20t</b></td>
													<td align=\"center\"><b>$col21t</b></td><td align=\"center\"><b>$col22t</b></td>
													<td align=\"center\"><b>$col23t</b></td><td align=\"center\"><b>$col24t</b></td>
												</tr>";
									  }
								   }
// Saiq Report Begins
						  if ($saiq_report=="on" && $branch!='all') {

										if ($branch !='all'){
											$query = "SELECT branch_name FROM ami_branches where branch_code='$branch'";
											$result = @mysql_db_query($dbname,$query);
											$row = mysql_fetch_array($result);
											$bname = $row['branch_name'];
										} else {
											$bname = "All";
										}
							?>
				</table></td></tr><tr><td align=center><br>
						<table align=center bgcolor=white border=1 cellspacing=0 cellpadding=1 width=60%>
								
								  <tr bgcolor="#cccccc">
									<th colspan="15"><span class="pageheader"><font color="#000000"><? echo $year; ?> Sa'iqin Report for <? echo $bname; ?></font></span></th>
								  </tr>
<?	
								if (($branch == "") || ($branch=="all"))
								{
?>
									<tr><td colspan=15>Branch Not Selected</td></tr>	
<?
								}
								else 
								{	

									$start_year = $year;
									$end_year = $year+1;
									  $query = "SELECT month_desc, month, year, case amila_meeting1 when 1 then 'Yes' when 0 then 'No' end 'Amila Meeting',
case when meeting1_minutes!='' then 'Yes' when meeting1_attachment != '' then 'Yes' else 'No' end 'Amila Meeting Minutes', case gbody_meeting when 1 then 'Yes' when 0 then 'No' end 'GB Meeting', case when gbody_brief_report!='' then 'Yes' when gbody_meeting_attachment != '' then 'Yes' else 'No' end 'GB Meeting Minutes', saiq1, saiq1_visit_in_person, saiq1_contact_by_phone, saiq2, saiq2_visit_in_person, saiq2_contact_by_phone, saiq3,  saiq3_visit_in_person, saiq3_contact_by_phone, saiq4,  saiq4_visit_in_person, saiq4_contact_by_phone, saiq5,  saiq5_visit_in_person, saiq5_contact_by_phone, saiq6,  saiq6_visit_in_person, saiq6_contact_by_phone, saiq7, saiq7_visit_in_person, saiq7_contact_by_phone,saiq8,  saiq8_visit_in_person, saiq8_contact_by_phone,saiq9,  saiq9_visit_in_person, saiq9_contact_by_phone, saiq10,  saiq10_visit_in_person, saiq10_contact_by_phone FROM `ami_all_gs_reports`, months WHERE month=month_num and ((year=$start_year and month>6) or (year=$end_year and month < 7)) and branch_code='$branch' order by year, month";
//print $query;
									 $result = @mysql_db_query($dbname,$query);
									  if ($result){
?>
								  <tr  bgcolor="#cccccc">
									<td align="center" colspan=8 rowspan=2><span class="pageheader"><font color="#000000">Month/Year</font></span></td>
									<td align="center" colspan=2><span class="pageheader"><font color="#000000">Amila Meetings</font></span></td>
									<td align="center" colspan=2><span class="pageheader"><font color="#000000">GB Meetings</font></span></td>
									<td align="center" colspan=3><span class="pageheader"><font color="#000000">Sa'iqin Report</font></span></td>
								  <tr  bgcolor="#cccccc">
									<td align="center"><span class="pageheader"><font color="#000000">Held</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Report</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Held</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Report</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Num.</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Visit</font></span></td>
									<td align="center"><span class="pageheader"><font color="#000000">Phone</font></span></td>
								  </tr>
<?
										$saiq_count = 0;
										$saiq_visit= 0;
										$saiq_phone= 0;
										$tot_saiq_count = 0;
										$tot_saiq_visit= 0;
										$tot_saiq_phone= 0;
										$tot_amila_mtg = 0;
										$tot_gb_mtg = 0;
										while ($row = mysql_fetch_array($result)) {
											$saiq_count = 0;
											$saiq_visit= 0;
											$saiq_phone= 0;
											$saiq_month_desc= $row['month_desc'];
											$saiq_year = $row['year'];
											$saiq_amila_mtg = $row['Amila Meeting'];
											$saiq_amila_min = $row['Amila Meeting Minutes'];
											$saiq_gb_mtg = $row['GB Meeting'];
											$saiq_gb_min = $row['GB Meeting Minutes'];
											if (trim($row['saiq1'])!='') $saiq_count++;
											if ($row['saiq1_visit_in_person']>0) $saiq_visit++;
											if ($row['saiq1_contact_by_phone']>0) $saiq_phone++;
											if (trim($row['saiq2'])!='') $saiq_count++;
											if ($row['saiq2_visit_in_person']>0) $saiq_visit++;
											if ($row['saiq2_contact_by_phone']>0) $saiq_phone++;
											if (trim($row['saiq3'])!='') $saiq_count++;
											if ($row['saiq3_visit_in_person']>0) $saiq_visit++;
											if ($row['saiq3_contact_by_phone']>0) $saiq_phone++;
											if (trim($row['saiq4'])!='') $saiq_count++;
											if ($row['saiq4_visit_in_person']>0) $saiq_visit++;
											if ($row['saiq4_contact_by_phone']>0) $saiq_phone++;
											if (trim($row['saiq5'])!='') $saiq_count++;
											if ($row['saiq5_visit_in_person']>0) $saiq_visit++;
											if ($row['saiq5_contact_by_phone']>0) $saiq_phone++;
											if (trim($row['saiq6'])!='') $saiq_count++;
											if ($row['saiq6_visit_in_person']>0) $saiq_visit++;
											if ($row['saiq6_contact_by_phone']>0) $saiq_phone++;
                                                                                        if (trim($row['saiq7'])!='') $saiq_count++;
                                                                                        if ($row['saiq7_visit_in_person']>0) $saiq_visit++;
                                                                                        if ($row['saiq7_contact_by_phone']>0) $saiq_phone++;
                                                                                        if (trim($row['saiq8'])!='') $saiq_count++;
                                                                                        if ($row['saiq8_visit_in_person']>0) $saiq_visit++;
                                                                                        if ($row['saiq8_contact_by_phone']>0) $saiq_phone++;
                                                                                        if (trim($row['saiq9'])!='') $saiq_count++;
                                                                                        if ($row['saiq9_visit_in_person']>0) $saiq_visit++;
                                                                                        if ($row['saiq9_contact_by_phone']>0) $saiq_phone++;
                                                                                        if (trim($row['saiq10'])!='') $saiq_count++;
                                                                                        if ($row['saiq10_visit_in_person']>0) $saiq_visit++;
                                                                                        if ($row['saiq10_contact_by_phone']>0) $saiq_phone++;
											print "<tr><td colspan=8 align=left nowrap>$saiq_month_desc $saiq_year</td>";
											print "<td align=center nowrap>$saiq_amila_mtg</td>";
											if ($saiq_amila_mtg == "Yes") 
												print "<td align=center nowrap>$saiq_amila_min</td>";
											else
												print "<td align=center nowrap>No</td>";
											print "<td align=center nowrap>$saiq_gb_mtg</td>";
											if ($saiq_gb_mtg == "Yes") 
												print "<td align=center nowrap>$saiq_gb_min</td>";
											else
												print "<td align=center nowrap>No</td>";
											print "<td align=center nowrap>$saiq_count</td>";
											print "<td align=center nowrap>$saiq_visit</td>";
											print "<td align=center nowrap>$saiq_phone</td>";
											print "</tr>";
											if ($saiq_amila_mtg == "Yes") $tot_amila_mtg++;
											if ($saiq_gb_mtg == "Yes") $tot_gb_mtg++;
											$tot_saiq_count += $saiq_count>0?1:0;
											$tot_saiq_visit += $saiq_visit>0?1:0;
											$tot_saiq_phone += $saiq_phone>0?1:0;
										}
											print "<tr bgcolor=#c0c0c0><td colspan=8 align=left nowrap><b>Total</b></td>";
											print "<td align=center nowrap><b>$tot_amila_mtg</b></td>";
											print "<td align=center nowrap></td>";
											print "<td align=center nowrap><b>$tot_gb_mtg</b></td>";
											print "<td align=center nowrap></td>";
											print "<td align=center nowrap><b>$tot_saiq_count</b></td>";
											print "<td align=center nowrap><b>$tot_saiq_visit</b></td>";
											print "<td align=center nowrap><b>$tot_saiq_phone</b></td>";
											print "</tr>";
									}
								}
	
	// End of Saiq Reprot
}
?>
								</table>
						  </td>
						</tr>
								</table>
						  </td>
						</tr>
						<tr>
						  <td colspan="2">&nbsp;</td>
						</tr>
							<tr><td align="center">
								<input type="button" name="Print" value="Print" class="ButtonCSS" onclick="javascript:window.print();">
								&nbsp;&nbsp;&nbsp;<!--input type="button" name="Cancel" value="Cancel" class="ButtonCSS" onclick="javascript:window.location.href('gs_reports.php');"-->
								 <a href="gs_reports.php" class="ButtonCSS4">&nbsp;&nbsp; Back &nbsp;&nbsp;</a	
							</td></tr>
							</table>
						  </td>
						</tr>
					  </table>
					</form>
					</td>
					</tr>
					</table>
				</td>
			</tr>
		</table>
   </td>
  </tr>
</table>
</body>
</html>
