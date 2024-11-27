<?
include("../incl/dbcon.php");
include ("protected.php");
if (($user_level!="N") && ($user_type!="GS")){
	header("Location: list_reports.php");
}
?>
<html>
<head>
<title>Ahmadiyya Muslim Community Canada</title>
<link href="../style.css" rel="stylesheet" type="text/css">
<body bgcolor="#ffffff">
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" class="BoxCSS">
  <tr>
   <td valign="top" align="center">
		<table border="0">
			<tr>
				<td>
					<table width="100%" border="0">
					<tr>
					<td>
					  <table width="580" border="0">
						<tr>
							<td colspan="2">
								<table border="0" width="100%">
									<tr>
										<td align="left" nowrap><img src="images/ami_logo.jpg" width="121" height="54"></td>
										<td align="center" colspan="2"><font size="4">Ahmadiyya Muslim Jama`at Canada</font></td>
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
						<tr bgcolor="#000000">
						  <td colspan="2">
							  <table border="0" class="BoxCSS">
							  <tr><td>
								  <table bgcolor="#c0c0c0" border="0" width="582">
								  <?php if (($month=="all") && ($branch=="all") && ($gs_report=="on")) {?>
								  <tr bgcolor="#000000">
									<th colspan="15"><span class="pageheader"><font color="white">Reports received during <? echo $year; ?></font></span></th>
								  </tr>
								  <tr  bgcolor="#000000">
									<td align="center"><span class="pageheader"><font color="white">Branch</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Jan</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Feb</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Mar</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Apr</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">May</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Jun</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Jul</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Aug</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Sep</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Oct</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Nov</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Dec</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Total</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Count</font></td>
								  </tr>
									 <?
									  //Get all branches
			//						  $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 order by branch_name";
			 if ($user_id=="Admin")
                                                                                      $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%')  order by branch_name";
										else if ($branch_code=="CA")
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
											$x=1;
											if ($currow % 2 == 0) {
											   $bgc= "#F7F2F4";
											} else {
											   $bgc= "#FFFFFF";
											}
											$currow+=1;
											$rowtotal=0;
											$count=0;
											print "<tr bgcolor=\"$bgc\"><td nowrap>$bname</td>";
											while ($x<13) {
												$query1 = "SELECT gs + th + tt + tm + it + sb + ra + uk + ua
													+ fe + dt + wa + tj + wj + jd + wn + zt + st + mb + an + ws + sm as cnt
													  FROM ami_all_gs_reports where branch_code = '$bcode' and month='$x' and year='$year'";
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
													$query_new = "SELECT a.report_code as REPCODE, r.report_name as NAME FROM ami_all_reports as a, ami_reports as r WHERE a.report_code=r.report_code and a.branch_code='$bcode' and year='$year' and month='$x' and (a.status > 0)  order by r.report_name"; 
													$result_new = @mysql_db_query($dbname,$query_new);
													$rep_str = '';
													while ($row_new = mysql_fetch_array($result_new)) {
														$rep_str .= $row_new['NAME'];
														$rep_str .= '<br>';
													}
													print "<td valign=top align=\"center\"><b>$cnt</b><br>$rep_str</td>";

												}
												$x+=1;
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
										print "<tr bgcolor=\"#F7F2F4\"><td><b>Total:</b></td>
												<td align=\"center\"><b>$col1t</b></td><td align=\"center\"><b>$col2t</b></td>
												<td align=\"center\"><b>$col3t</b></td><td align=\"center\"><b>$col4t</b></td>
												<td align=\"center\"><b>$col5t</b></td><td align=\"center\"><b>$col6t</b></td>
												<td align=\"center\"><b>$col7t</b></td><td align=\"center\"><b>$col8t</b></td>
												<td align=\"center\"><b>$col9t</b></td><td align=\"center\"><b>$col10t</b></td>
												<td align=\"center\"><b>$col11t</b></td><td align=\"center\"><b>$col12t</b></td>
												<td align=\"center\"><b>$col13t</b></td><td align=\"center\"><b></b></td>
												</tr>";
										print "<tr bgcolor=\"#F7F2F4\"><td><b>Count:</b></td>
												<td align=\"center\"><b>$col1c</b></td><td align=\"center\"><b>$col2c</b></td>
												<td align=\"center\"><b>$col3c</b></td><td align=\"center\"><b>$col4c</b></td>
												<td align=\"center\"><b>$col5c</b></td><td align=\"center\"><b>$col6c</b></td>
												<td align=\"center\"><b>$col7c</b></td><td align=\"center\"><b>$col8c</b></td>
												<td align=\"center\"><b>$col9c</b></td><td align=\"center\"><b>$col10c</b></td>
												<td align=\"center\"><b>$col11c</b></td><td align=\"center\"><b>$col12c</b></td>
												<td align=\"center\"><b></b></td><td align=\"center\"><b>$tcountt</b></td>
												</tr>";
									  }
								   } if (($month=="all") && ($branch=="all") && ($gs_report=="")) {?>
								  <tr bgcolor="#000000">
									<th colspan="15"><span class="pageheader"><font color="white">Reports received during <? echo $year; ?></font></span></th>
								  </tr>
								  <tr  bgcolor="#000000">
									<td align="center"><span class="pageheader"><font color="white">Branch</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Jan</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Feb</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Mar</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Apr</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">May</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Jun</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Jul</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Aug</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Sep</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Oct</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Nov</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Dec</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Total</font></span></td>
									<td align="center"><span class="pageheader"><font color="white">Count</font></td>
								  </tr>
									 <?
									  //Get all branches
			//						  $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 order by branch_name";
			 if ($user_id=="Admin")
                                                                                      $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%')  order by branch_name";
										else if ($branch_code=="CA")
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
											$x=1;
											if ($currow % 2 == 0) {
											   $bgc= "#F7F2F4";
											} else {
											   $bgc= "#FFFFFF";
											}
											$currow+=1;
											$rowtotal=0;
											$count=0;
											print "<tr bgcolor=\"$bgc\"><td nowrap>$bname</td>";
											while ($x<13) {
												if ($status=="all") {
													$query1 = "SELECT count(*) as cnt FROM ami_all_reports where branch_code = '$bcode' and month='$x' and year='$year'";
												} else {
													$query1 = "SELECT count(*) as cnt FROM ami_all_reports where branch_code = '$bcode' and month='$x' and year='$year' and status='$status'";
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
												} else {

													$query_new = "SELECT a.report_code as REPCODE, r.report_name as NAME FROM ami_all_reports as a, ami_reports as r WHERE a.report_code=r.report_code and a.branch_code='$bcode' and year='$year' and month='$x' and (a.status > 0)  order by r.report_name"; 
													$result_new = @mysql_db_query($dbname,$query_new);
													$rep_str = '';
													while ($row_new = mysql_fetch_array($result_new)) {
														$rep_str .= '['.$row_new['NAME'].']';
														$rep_str .= '<br>';
													}
													print "<td valign=top align=\"center\"><b>$cnt</b><br>$rep_str</td>";
												}
												$x+=1;
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
										print "<tr bgcolor=\"#F7F2F4\"><td><b>Total:</b></td>
												<td align=\"center\"><b>$col1t</b></td><td align=\"center\"><b>$col2t</b></td>
												<td align=\"center\"><b>$col3t</b></td><td align=\"center\"><b>$col4t</b></td>
												<td align=\"center\"><b>$col5t</b></td><td align=\"center\"><b>$col6t</b></td>
												<td align=\"center\"><b>$col7t</b></td><td align=\"center\"><b>$col8t</b></td>
												<td align=\"center\"><b>$col9t</b></td><td align=\"center\"><b>$col10t</b></td>
												<td align=\"center\"><b>$col11t</b></td><td align=\"center\"><b>$col12t</b></td>
												<td align=\"center\"><b>$col13t</b></td><td align=\"center\"><b></b></td>
												</tr>";
										print "<tr bgcolor=\"#F7F2F4\"><td><b>Count:</b></td>
												<td align=\"center\"><b>$col1c</b></td><td align=\"center\"><b>$col2c</b></td>
												<td align=\"center\"><b>$col3c</b></td><td align=\"center\"><b>$col4c</b></td>
												<td align=\"center\"><b>$col5c</b></td><td align=\"center\"><b>$col6c</b></td>
												<td align=\"center\"><b>$col7c</b></td><td align=\"center\"><b>$col8c</b></td>
												<td align=\"center\"><b>$col9c</b></td><td align=\"center\"><b>$col10c</b></td>
												<td align=\"center\"><b>$col11c</b></td><td align=\"center\"><b>$col12c</b></td>
												<td align=\"center\"><b></b></td><td align=\"center\"><b>$tcountt</b></td>
												</tr>";
									  }
								  } else if (($month!="all") && ($branch=="all") && ($report_code=="all")) {?>
									  <tr bgcolor="#000000">
										<th colspan="25"><span class="pageheader"><font color="white">Reports received during <? echo $months["$month"]; ?> <? echo $year; ?></font></span></th>
									  </tr>
									 <?
									  //Get all branches
									  $query = "SELECT report_name, report_code FROM ami_reports order by report_name";
									  $result = @mysql_db_query($dbname,$query);
									  if ($result){
											print "<tr bgcolor=\"\#000000\"><td align=\"center\"><span><font color=\"white\"><b>Branch</b></font></span></td>";
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
												//print "<td align=\"center\" width=\"6\" valign=\"top\"><span><font color=\"white\"><b>" . implode(" ",preg_split('//', $rname, -1, PREG_SPLIT_NO_EMPTY)) . "</b></font></span></td>";
												print "<td align=\"center\" width=\"6\" valign=\"top\"><span><font color=\"white\"><b>" . $row['report_code'] . "</b></font></span></td>";
											}
											print "<td align=\"center\"><span><font color=\"white\"><b>Total</b></td>";
											print "<td align=\"center\"><span><font color=\"white\"><b>Legend</b></td></tr>";
									  }
									  //Get all branches
									  //$query = "SELECT branch_name, branch_code FROM ami_branches where status=1 order by branch_name";
			 if ($user_id=="Admin")
                                                                                      $query = "SELECT branch_name, branch_code FROM ami_branches where status=1 AND (region_code='$branch_code' OR branch_code='$branch_code' OR region_code LIKE 'R%')  order by branch_name";
										else if ($branch_code=="CA")
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
														$query2 = "SELECT count(*) as cnt FROM ami_all_reports where branch_code = '$bcode' and report_code ='$rcode' and month='$month' and year='$year'";
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
								   }?>
								  </table>
								</td><tr>
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
