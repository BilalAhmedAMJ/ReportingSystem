<?php include ("protected.php");
include ("../incl/dbcon.php");
if ($user_id!="Admin"){
	header("Location: list_reports.php");
}
$today = date("Y-m-d");
if ($bid1!=""){
	$Error="";
	
	if ($id!=""){
		//print "update";
		if ($region_code!="") {
			//print "update1";
			$insert_data = "UPDATE ami_branches SET
							region_code = '$region_code',
							branch_name = '$branch_name',
							status = '$status'
							WHERE bid ='$id'";
		} else {
			$Error = "Error";
			print "Invalid Region Code";
			exit();
		}
		//print "$insert_data";
		$result=@mysql_db_query($dbname,$insert_data,$id_link);
		if ($result == "1"){
			$Error = "Branch updated successfully!";
		} else {
			$Error = "Could not update branch!";
		}

	} else {
		print "Adding Branch Not Allowed!";
		exit();
/*	// IMPLEMENT IT but first check if this is unique branch name
		//print "insert";
		$insert_data = "insert into ami_branches
						values ('', '$branch_name', '$branch_code', '$region_code', '$status');";
		//print "$insert_data<br>";
		$result=@mysql_db_query($dbname,$insert_data,$id_link);
		if ($result == "1"){
			$Error = "User added successfully!";
			//$subject = "$Field11 - reg success";
			//mail("$Field8", "Online Registration", $smsg, "From:mansooranasir@gmail.com");
		} else {
			$Error = "Could not add user!";
		}
*/
	}
	if ($Error=="") {
		//header("Location: list_users.php");
		//include("list_users.php");
	}
	$bid1 ="";
	$branch_name = "";
	$branch_code = "";
	$region_code = "";
	$status = $status;
}
if ($id !="") {

	$query = "SELECT * FROM ami_branches WHERE bid = '$id'"; //ami_branches
	$result = @mysql_db_query($dbname,$query);

	//print "$result";
	if ($result) {
		$row = mysql_fetch_array($result);
		$bid1 = $row['bid'];
		$branch_name = $row['branch_name'];
		$branch_code = $row['branch_code'];
		$region_code = $row['region_code'];
		$status = $row['status'];
	}
}
else
{
//	print "Adding Branch Not Allowed!";
//	exit();
}
?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">
<?php include '../incl/headscript.inc'; ?>
<script language="JavaScript" type="text/JavaScript">
<!--
function submit_onclick() {
	var s = document.userForm.user_email.value;
	var len = s.length-1;
}
//-->
</script>
</head>
<body bgcolor="#ffffff">
<?php include 'incl/topbar.inc'; ?>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
 <tr>
    <td align="right" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="100" valign=top >
        <?php include 'menu.php'; ?></td>
          <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr valign="top">
                <td valign="top"><center><br><br>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
						<td align="center">
							<!--<center>-->
							<table border="0">
								<? if ($Error) {?>
									<tr>
										<td colspan="2" align="center">
										<font color=#800000><? echo $Error;?></font>
										</td>
									</tr>
								<? }?>
							<tr>
							<td>
							  <form name="userForm" method="post" action="add_branch.php">
							  <table width="350" border="0" class=newstyletable>
								<tr>
									<? if ($id!="") {?>
										<th colspan=2 ><span class="">Edit branch</span></th>
									<? } else {?>
										<th colspan=2 ><span class="">Add branch</span></th>
									<? }?>
								</tr>
								<tr>
								  <td bgcolor=""><p class="normaltxt">Branch Name:</p></td>
								  <td bgcolor=""><input name="branch_name" maxlength="50" type="text" class="BoxCSS" id="branch_name" value="<? echo $branch_name;?>"></td>
								</tr>
								<tr>
								  <td bgcolor=""><p class="normaltxt">Branch Code:</p></td>
								  <td bgcolor=""><? echo $branch_code;?></td>
								</tr>
								<tr><td bgcolor=""><p class="normaltxt">Region:</p></td>
									<td bgcolor="">
									<?//Get all regions
									 $query3 = "SELECT * FROM ami_regions where status=1 order by region_name";
									 $result3 = @mysql_db_query($dbname,$query3);?>
									 <select name="region_code">
									 <?php while ($row3 = mysql_fetch_array($result3)) { ?>
									  <?
											$val = $row3['region_code'];
											$des = $row3['region_name'];
											if ($region_code == $val) {?>
												<option value=<? print "\"$val\"";  ?> selected><? print "$des - $val";  ?></option>
											<? } else {?>
												<option value=<? print "\"$val\"";  ?>><? print "$des - $val";  ?></option>
											<? }?>
										<? }?>
										</select>
									  </td>
								</tr>
								<tr>
								  <td bgcolor=""><p class="normaltxt">Status:</p></td>
								  <td bgcolor="">
								  	<select name="status">
								  	<? if ($status=="1") {?>
								  		<option value="0">Inactive</option>
								  		<option value="1" selected>Active</option>
								  	<? } else {?>
								  		<option value="0" selected>Inactive</option>
								  		<option value="1">Active</option>
									<? }?>
								  	</select>*</td>
								</tr>
								<tr>
								  <td bgcolor="" colspan="2" align="center">
								  	<input name="id" type="hidden" id="id" value="<? echo $id;?>">
									<input name="bid1" type="hidden" id="bid1" value="<? echo $bid1;?>">
								  	<input type="submit" name="Submit" value="Submit" class="ButtonCSS" onclick="return submit_onclick()">&nbsp;&nbsp; 
								  	<input type="reset" name="Reset" value="Reset" class="ButtonCSS">
								  </td>
								</tr>
							  </table>
								<script language="JavaScript">
									document.userForm.branch_name.focus();
								</script>
							</form>
							</td>
							</tr>
							</table>
							<!--</center>-->
						</td>
					  </tr>
					</table>
				</td>
                <td width="160" >
                  <?php $add_branch_php='Y'; include 'incl/rightbar.inc'; ?>
                </td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
<?php include 'incl/bottombar.inc'; ?>
</body>
</html>
