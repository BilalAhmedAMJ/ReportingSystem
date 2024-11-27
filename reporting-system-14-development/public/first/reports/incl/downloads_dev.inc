 <?
 if ($user_level=="N") {
	$query4 = "SELECT * FROM ami_uploaded_forms order by users, description";
 } else if (($user_level=="L") && (($user_type=="P") || ($user_type=="GS"))) {
	$query4 = "SELECT * FROM ami_uploaded_forms where users='A' or users = 'L' order by users,  description";
 } else {
	$query4 = "SELECT * FROM ami_uploaded_forms where users='A' or (users='S' and dept_code='$user_dept') order by description";
 }
 $result4 = @mysql_db_query($dbname,$query4);
 if ($result4) {?>
    <table width="200" border="0" class=newstylemaintable>
	<?php 
		if ($user_level=="N" && $user_type=="GS") {
			print '<tr><th colspan=2><span class="pageheader">Download Documents</span>
<br><font size=1 color=grey>Login: <font color=grey><b>docs</b></font><br>Password: <font color=grey><b>amjreports08</b></font></font>
</th></tr>';
			print '<tr><td bgcolor="#cccccc"><b>Documents</b></td><td bgcolor="#cccccc"><b>Action</b></td>';
		} else {
			print '<tr><th ><span class="pageheader"><font color="""">Download Documents</font></span>
<br><font size=1 color="">Login: <font color=grey><b>docs</b></font><br>Password: <font color=grey><b>amjreports08</b></font></font>
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
						print '<tr><th bgcolor="""" align=center colspan=2><span class="pageheader">Forms</span></th></tr>';
					} else if ($row4["users"]=='L') {
						print '<tr><th bgcolor="""" align=center colspan=2><span class="pageheader"><font color="""">Other Documents</font></span></th></tr>';
					} else if ($row4["users"]=='N') {
						print '<tr><th bgcolor="""" align=center colspan=2><span class="pageheader"><font color="""">National Amila Documents</font></span></th></tr>';
					}
				} else {
					if ($row4["users"]=='A') {
						print '<tr><th bgcolor="""" align=center ><span class="pageheader"><font color="""">Forms</font></span></th></tr>';
					} else if ($row4["users"]=='L') {
						print '<tr><th bgcolor="""" align=center><span class="pageheader"><font color="""">Other Documents</font></span></th></tr>';
					} else if ($row4["users"]=='N') {
						print '<tr><th bgcolor="""" align=center><span class="pageheader"><font color="""">National Amila Documents</font></span></th></tr>';
					}
				}
				$curlvl = $row4["users"];
			}

			if ($user_level=="N" && $user_type=="GS") {
				print "<tr><td bgcolor=$bgcolor><a href=\"download/" . $row4["file_name"] . "\" target=\"_blank\">" . $row4["description"] . "</a></td>";
				print "<td bgcolor=$bgcolor><a href=rem_forms.php?id=" . $row4["id"] . ">Remove</a></td>";
			} else {
				print "<tr><td bgcolor=$bgcolor><a href=\"download/" . $row4["file_name"] . "\" target=\"_blank\">" . $row4["description"] . "</a></td>";
			}
			$currow++;
			?>
		</tr>
	<? }?>
	</table>
  <? }?>
