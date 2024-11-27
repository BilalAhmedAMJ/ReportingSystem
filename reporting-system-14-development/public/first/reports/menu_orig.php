<? if ($user_id=="") {  ?>
<table border=0 valign=top cellspacing=0 cellpadding=0 bgcolor="black" width=100%>
<tr>
<td valign=top><center><br><a href="login.php"><font color="white">Home</font></a></br>
</td>
</tr>
</table>
<? } else {?>
<table border=0 valign=top cellspacing=0 cellpadding=0 bgcolor="black" width=100%>
<tr><td valign=top><center><br><font color="grey">User:</font>&nbsp;<font color="grey"><? echo $user_id; ?></br>
<?
if ( (($user_dept!="") && ($user_type!="")) || ($user_level!="") )
{
/////////////// FIND OUT IF USER IS IMARAT USER /////////////
									  //Get the branch name
									  $imarat_user = 0;
									  $query1 = "SELECT branch_name,region_code FROM ami_branches WHERE branch_code = '$branch_code'";
									  $result1 = @mysql_db_query($dbname,$query1);
									  if ($result1){
									  	$row1 = mysql_fetch_array($result1);
									  	$branch_name=$row1['branch_name'];
									  	$region_code=$row1['region_code'];
										if ($region_code==$nat_branch)
											$imarat_user = 1;
									  } else {
									  	$branch_name = $branch_code;
									  }
////////////////////////////////////////////////////////////

?>
<font color="grey">[<? echo $branch_name; ?>]</br>

				<form name="logout" method="post" action="logout.php">
					<input type="submit" name="logout" value="Logout" class="ButtonCSS7">
				</form><br><br>
<?
		if ($user_id=="Admin") {?>
				<form name="config" method="post" action="config.php">
					<input type="submit" name="config" value="Config" class="ButtonCSS6">
				</form>
		
	<?		} ?>
<br>
<?		if ($user_type!="E") {?>
				<form name="list" method="post" action="list_reports.php">
					<input type="submit" name="list_report" value="Reports" class="ButtonCSS6">
				</form>
	<?		}
			if (($user_id=="Admin") || ($user_level=="N") || ($user_type=="P") || ($user_type=="E") ) {?>
					<!--form name="new" method="post" action="elections.php">
						<input type="submit" name="elects" value="Elections" class="ButtonCSS6">
					</form-->
			<? }?>
	<?		if (($user_type=="GS") || ($user_type=="P")) {?>
		<?//		if ($user_level=="N") {?>
					<!--form name="new" method="post" action="list_local_users.php"-->
			<? //} else {?>
					<form name="new" method="post" action="list_users.php">
			<? //}?>
						<input type="submit" name="list_local_users" value="Branch Users" class="ButtonCSS6">
					</form>
			<? }?>
<?		if (($branch_code==$nat_branch) && (($user_id=="Admin") || ($user_type=="FE") || ($user_type=="UA") || ($user_type=="P")))   {?>
		
				<form name="list_user_requests" method="post" action="list_user_requests.php">
					<input type="submit" name="user_requests" value="Approval" class="ButtonCSS6">
				</form>

		 <?}?>
		<? if ((($user_level=="N") && ($user_type=="GS")) || ($user_id=="Admin")) {?>
			<? if ($user_id=="Admin") {?>
					<form name="new" method="post" action="list_users.php">
						<input type="submit" name="list_users" value="Users" class="ButtonCSS6">
					</form>
					<form name="new" method="post" action="list_branches.php">
						<input type="submit" name="list_branches" value="Branches" class="ButtonCSS6">
					</form>
			<? } ?>
				
			<? if ($branch_code==$nat_branch) { ?>
				<form name="new" method="post" action="upload_forms.php">
					<input type="submit" name="upload_forms" value="Upload Docs" class="ButtonCSS6">
				</form>
			<? } ?>
			<?// if (($user_level=="N") && ($user_type=="GS")) {?>
		 <? }?>
			<? if (($user_level=="N") || ($user_type=="GS") || ($user_type=="P")) {?>
					<form name="new" method="post" action="gs_reports.php">
						<input type="submit" name="gs_reports" value="Rep. Analysis" class="ButtonCSS6">
					</form>
			<? } ?>
				<form name="list" method="post" action="change_password.php">
					<input type="submit" name="change_password" value="Password" class="ButtonCSS6">
				</form>
<?		if ($user_type!="E") {?>
				<br>List of same office holders in<br>
				<form name="amila" method="post" action="list_amila.php">
					<input type="submit" name="amila" value="All Branches" class="ButtonCSS3">
				</form>
				List of office bearers in<br>
				<form name="amila" method="post" action="list_local_amila.php">
					<input type="submit" name="amila" value=" Your Branch" class="ButtonCSS3">
				</form>
	<?		}

}
	?>
</td>
</tr>
</table>
<? }?>
