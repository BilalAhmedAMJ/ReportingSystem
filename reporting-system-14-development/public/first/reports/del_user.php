<?php include ("protected.php");
include ("../incl/dbcon.php");

if (($user_type!="P") && ($user_type!="GS") && ($user_id!="Admin")) {
        header("Location: list_users.php");
}

if (($Do_Delete =="Yes") && ($u_id!="")){
	if ($user_id == "Admin")
		$insert_data = "delete from ami_users where u_id ='$u_id';";
	else
		$insert_data = "delete from ami_users where u_id ='$u_id' and branch_code='$branch_code' and status='0';";
	//print "$insert_data";
	$result=@mysql_db_query($dbname,$insert_data,$id_link);
	if ($result == "1"){
		//$subject = "$Field11 - reg success";
		//mail("$Field8", "Online Registration", $smsg, "From:mansooranasir@gmail.com");
	} else {
		$Error = "Could not add user!";
	}
	header("Location: list_users.php");
}
else if ($Do_Not_Delete=="No")
{
	header("Location: list_users.php");
}
if ($id !="") {

	$query = "SELECT * FROM ami_users WHERE u_id = '$id'"; //ami_users
	$result = @mysql_db_query($dbname,$query);

	//print "$result";
	if ($result) {
		$row = mysql_fetch_array($result);
		$user_name = $row['user_name'];
		$user_id1 = $row['user_id'];
	}
}
?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">
<?php include '../incl/headscript.inc'; ?>
</head>
<body bgcolor="#ffffff">
<?php include '../incl/topbar.inc'; ?>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="right" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="100" valign=top bgcolor=black>
        <?php include 'menu.php'; ?></td>
          <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">

              <tr valign="top">
                <td valign="top"><br><br>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
						<td align="center">
							<!--<center>-->
							<table class="BoxCSS" border="0">
							<tr>
							<td><center>
							  <form name="userForm" method="post" action="del_user.php">
							  <table width="350" border="0">
								<tr>
									<th colspan=2 bgcolor="#F2FAFB"><span class="pageheader">Delete user</span></th>
								</tr>
								<tr>
								  <td colspan="2"><center><p>Are you sure to delete the <b>"<?echo $user_id1?>"</b> account?</p></td>
								  <input type="hidden" name="u_id" value="<? echo $id?>">
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
								  <td width="100"><center><input type="Submit" name="Do_Not_Delete" value="No" class="ButtonCSS"></td>
								  <td width="100"><center><input type="Submit" name="Do_Delete" value="Yes" class="ButtonCSS"></td>
								</tr>
								</table>
							</form>
							</td>
							</tr>
							</table>
							<!--</center>-->
						</td>
					  </tr>
					</table>
				</td>
                <td width="1" bgcolor="#666666">
                  <?php include '../incl/navheight.inc'; ?>
                </td>
                <td width="160" bgcolor="#F3F3F3">
                  <?php include 'incl/rightbar.inc'; ?>
                </td>
              </tr>
            </table></td>
          <td bgcolor="#666666"><img src="../images/spacer.gif" width="1" height="1"></td>
        </tr>
        <tr>
          <td colspan="3" bgcolor="#666666"><?php include '../incl/navwidth.inc'; ?></td>
        </tr>
      </table></td>
  </tr>
</table>
<?php include 'incl/bottombar.inc'; ?>
<?php include 'incl/preload.inc'; ?>
</body>
</html>
