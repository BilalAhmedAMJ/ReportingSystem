 <?
$col_cnt = 1;
 if ($user_level=="N") {
	$query4 = "SELECT * FROM ami_uploaded_forms order by users, description";
 } else if (($user_level=="L") && (($user_type=="P") || ($user_type=="GS"))) {
	$query4 = "SELECT * FROM ami_uploaded_forms where users='A' or users = 'L' order by users,  description";
 } else {
	$query4 = "SELECT * FROM ami_uploaded_forms where users='A' or (users='S' and dept_code='$user_dept') order by description";
 }
 $result4 = @mysql_db_query($dbname,$query4);
 if ($result4) {?>
    <table width="100%" border="0"> 
	<?php 
		if ($user_level=="N" && $user_type=="GS" && $branch_code==$nat_branch) {
			print '<tr><th colspan=2 align=center >
<font size=1 color=#808080>
Please use the following login credentials when prompted by download dialog.<br>
Login: <font color=#800000>docs</font>&nbsp;&nbsp;&nbsp;Password:&nbsp;<font color=#800000>amjreports08</font></font><br><br>
</th></tr>';
			$col_cnt = 2;
			print '<tr><td align=center class=newstylecolumnheader><b>Documents</b></td><td align=center class=newstylecolumnheader><b>Action</b></td>';
		} else {
			print '<tr><th align=center >
<font size=1 color=#808080>
Please use the following login credentials when prompted by download dialog.<br>
Login: <font color=#800000>docs</font>&nbsp;&nbsp;&nbsp;Password:&nbsp;<font color=#800000>amjreports08</font></font><br><br>
</th></tr>';
		}

		$currow = 1;
		$curlvl ='';
		while ($row4 = mysql_fetch_array($result4)) { 
			if ($currow % 2 == 0) {
			   $bgcolor= "#F7F2F4";
			} else {
			   $bgcolor= "#FFFFFF";
			}

			if ($user_level=="N" && $curlvl != $row4["users"]) {
				if ($user_type=="GS") {
					if ($row4["users"]=='A') {
						print '<tr><th bgcolor="white" colspan=2 align=left colspan=$col_cnt><span class="">Forms</span></th></tr>';
					} else if ($row4["users"]=='L') {
						print '<tr><th bgcolor="white" colspan=2  align=left colspan=$col_cnt><span class=""><font color="""">Other Documents</font></span></th></tr>';
					} else if ($row4["users"]=='N') {
						print '<tr><th bgcolor="white" colspan=2 align=left colspan=$col_cnt><span class=""><font color="""">National Amila Documents</font></span></th></tr>';
					}
				} else {
					if ($row4["users"]=='A') {
						print '<tr><th bgcolor="white" align=left><span class=""><font color="""">Forms</font></span></th></tr>';
					} else if ($row4["users"]=='L') {
						print '<tr><th bgcolor="white" align=left><span class=""><font color="""">Other Documents</font></span></th></tr>';
					} else if ($row4["users"]=='N') {
						print '<tr><th bgcolor="white" align=left><span class=""><font color="""">National Amila Documents</font></span></th></tr>';
					}
				}
				$curlvl = $row4["users"];
			}

			if ($user_level=="N" && $user_type=="GS" && $branch_code==$nat_branch) {
				print "<tr><td bgcolor=$bgcolor><a href=\"download/" . $row4["file_name"] . "\" target=\"_blank\">" . $row4["description"] . "</a></td>";
				print "<td align=center bgcolor=$bgcolor><a class=newstylesmallbutton href=rem_forms.php?id=" . $row4["id"] . ">Remove</a></td>";
			} else {
				print "<tr><td bgcolor=$bgcolor><a href=\"download/" . $row4["file_name"] . "\" target=\"_blank\">" . $row4["description"] . "</a></td>";
			}
			$currow++;
			?>
		</tr>
	<? }?>
	</table>
  <? }?>
