<?
include("../incl/dbcon.php");
include ("protected.php");
if ($user_id!="Admin") {
	header("Location: list_reports.php");
}?>

<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">
<?php include '../incl/headscript.inc'; ?>

<script language="JavaScript" type="text/JavaScript">
<!--
function submit_onclick() {
	if (document.upload_forms.description.value == "" || document.upload_forms.description.value == " "){
		alert('You are missing description!');
		document.upload_forms.description.focus();
		return false;
	}
	return true;
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
          <td valign="top">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">

              <tr valign="top">
                <td valign="top" align="center">
			<br><br>
			<? if ($action =="") {?>
				<form name="config_form" enctype="multipart/form-data" method="post" action="config.php">
				<table width="580" border="0">
					<? if ($Error) {?>
						<tr>
							<td colspan="2" align="center">
							<span class="pagetextAltColorSmall"><? echo $Error;?></span>
							</td>
						</tr>
					<? }?>
					<tr>
					<th colspan=2>Config<br><br>
					</th>
					</tr>
					<tr>
					  <td bgcolor="#F2F8F1"><input name="do_allow_attachments" <? echo ($allow_attachments?"checked":"unchecked"); ?> type="checkbox" class="BoxCSS" id="do_allow_attachments" value="1">&nbsp;</td>
					  <td bgcolor="#F2F8F1">Allow Reports Attachments</td>
					</tr>
					<tr>
					<td colspan=2 align=center><br>
					<input type="hidden" name="action" value="1">
					<input type="submit" name="Submit" value="Save" class="ButtonCSS" onclick="return submit_onclick()">
					  </td>
					</tr>
				</table>
				<script language="JavaScript">
					document.upload_forms.do_allow_attachments.focus();
				</script>
			<? } else {
				$fp = fopen("config", "w");
				if (!$fp)
				{
					echo "Unable to Save Configurations";
				}
				else if ($do_allow_attachments=='1'){
					fwrite($fp,"ALLOW_ATTACHMENTS\r\n");
					fwrite($fp, date("F j, Y, g:i a")."\r\n");
					fwrite($fp, getenv("REMOTE_ADDR")."\4\n");
					echo "Configurations Saved"; 
					fclose($fp);
					$_SESSION['login']['config_allow_attachments'] = '1'; 
				}
				else
				{
					fwrite($fp,"NOT_ALLOW_ATTACHMENTS\r\n");
					fwrite($fp, date("F j, Y, g:i a")."\r\n");
					fwrite($fp, getenv("REMOTE_ADDR")."\4\n");
					fclose($fp);
					echo "Configurations Upated"; 
					$_SESSION['login']['config_allow_attachments'] = '0'; 
				}
			  } ?>
		</td>
		</tr>
		</table>
		</td>
                <td width="160" valign=top> 
                  <?php include 'incl/rightbar.inc'; ?>
                </td>
              </tr>
            </table>
	</td>
        </tr>
      </table></td>
  </tr>
</table>
<?php include '../incl/bottombar.inc'; ?>
<?php include '../incl/preload.inc'; ?>
</body>
</html>
