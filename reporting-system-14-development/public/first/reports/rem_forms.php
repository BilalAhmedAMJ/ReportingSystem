<?
include("../incl/dbcon.php");
include ("protected.php");
if (($user_level!="N") && ($user_type!="GS")){
	header("Location: list_reports.php");
}?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">
<?php include '../incl/headscript.inc'; ?>
<body bgcolor="#ffffff">
<?php include '../incl/topbar.inc'; ?>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" background="../images/tablebg.gif">
  <tr>
    <td width="152" valign="top">
      <?php include '../incl/leftbar.inc'; ?>
    </td>
    <td align="right" valign="top">
    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td bgcolor="#666666"><img src="../images/spacer.gif" width="1" height="1"></td>
          <td bgcolor="#666666"><img src="../images/spacer.gif" width="1" height="1"></td>
          <td bgcolor="#666666"><img src="../images/spacer.gif" width="1" height="1"></td>
        </tr>
        <tr>
          <td colspan="2" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr valign="top">
                <td valign="top" align="center">
				<?php include 'admin_header.php'; ?>
				<?php include 'menu.php'; ?>
					<br><br>
					<table>
						<tr>
							<td>
							<?
							$q = "SELECT * FROM ami_uploaded_forms where id = '$id'";
							$result = @mysql_db_query($dbname,$q);
							if ($result) {
								$row = mysql_fetch_array($result);
								//$uploaddir = '/var/www/html/reports/download/';
                                                                $uploaddir = getenv("DOCUMENT_ROOT") . '/reports/download/';
								$uploadfile = $uploaddir . $row["file_name"];
								$insert_data = "delete from ami_uploaded_forms where id='$id'";
								$result=@mysql_db_query($dbname,$insert_data,$id_link);
								if($result) {
									print "Document removed successfully from database.<br>";
									if (!unlink($uploadfile)){
										print "Failed to delete the physical file.<br>";
									} else {
									   print "File removed successfully.<br>";
									}
								} else {
								    print "Failed to remove the document from database.<br>";
								}

							} else {
								print "Document not found in the database, please contact admin.<br>";
							}
							?>
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
<?php include '../incl/bottombar.inc'; ?>
<?php include '../incl/preload.inc'; ?>
</body>
</html>
