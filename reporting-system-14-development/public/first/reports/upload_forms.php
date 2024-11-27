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
          <td width="100" valign=top>
        <?php include 'menu.php'; ?></td>
          <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">

								<tr>
									<? if ($id) {?>
										<th colspan=2 ">Edit Uploaded Doc</th>
									<? } else {?>
										<th colspan=2 >Upload Document</th>
									<? }?>
								</tr>
              <tr valign="top">
                <td valign="top" align="center"><br>
					<table class=newstyletable border=0>
						<tr>
							<td>
							<? if ($action =="") {?>
								<form name="upload_forms" enctype="multipart/form-data" method="post" action="upload_forms.php">
								<table width="580" border="0" >
								<? if ($Error) {?>
									<tr>
										<td colspan="2" align="center">
										<span class="pagetextAltColorSmall"><? echo $Error;?></span>
										</td>
									</tr>
								<? }?>
								<tr>
								  <td bgcolor="">Description:</td>
								  <td bgcolor=""><input name="description" maxlength="150" type="text" class="BoxCSS" id="description" value="<? echo $description;?>">*</td>
								</tr>
								<tr>
									<td bgcolor="">Users:</td>
									<td bgcolor="">
										<select name=users id=users>
											<option selected value="A">All Users</option>
											<option value="S">Department Users</option>
											<option value="L">National Amila & Local President/GS</option>
											<option value="N">National Amila ONLY</option>
										</select>*
									</td>
								</tr>
								<tr>
									<td bgcolor="">Department:</td>
									<td bgcolor="">
									<?//Get all Reports
                                                                                 $query3 = "SELECT report_name, report_code FROM ami_reports where office_code!='' order by report_name";
                                                                                 $result3 = @mysql_db_query($dbname,$query3);?>
                                                                                 <select name="dept_code">
										 <option selected value="All">All</option>
                                                                                 <?php while ($row3 = mysql_fetch_array($result3)) { ?>
                                                                                  <?
                                                                                                $val = $row3['report_code'];
                                                                                                $des = $row3['report_name'];
                                                                                        if ($user_type1 == $val) {?>
                                                                                                <option value=<? print "\"$val\"";  ?> selected><? print "$des - $val";  ?></option>
                                                                                        <? } else {?>
                                                                                                <option value=<? print "\"$val\"";  ?>><? print "$des - $val";  ?></option>
                                                                                        <? }?>
                                                                                 <? }?>
                                                                                 *</select>
									</td>
								</tr>
								<tr>
									<td bgcolor="">Document:</td>
									<td bgcolor=""><input type="file" name="attachment" class="boxCSS1"></td>
								</tr>
								<tr>
								  <td align="center" colspan=2 >
								<? if ($id =="") {?>
									<input type="hidden" name="action" value="insert">
								<? } else {?>
									<input type="hidden" name="action" value="update">
									<input type="hidden" name="id" value="<? print $id;?>">
								<? }?><br>
									<input type="submit" name="Submit" value="Upload" class="ButtonCSS" onclick="return submit_onclick()">
								  </td>
								</tr>
								</table>
								<script language="JavaScript">
									document.upload_forms.description.focus();
								</script>
							<? } else {
									$uploaddir = getenv("DOCUMENT_ROOT") . '/reports/download/';
									$uploadfile = $uploaddir . $_FILES['attachment']['name'];
									$tmpuploadfile = $_FILES['attachment']['tmp_name'];
									if ($tmpuploadfile){
										$file_name = $_FILES['attachment']['name'];
										if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadfile)) {
										   chmod ($uploadfile, 0644);

											$q = "SELECT * FROM ami_uploaded_forms where file_name = '$file_name'";
											$result5 = @mysql_db_query($dbname,$q);
											if ($result5) {
												$row = mysql_fetch_array($result5);
												$id = $row["id"];
											}

											if ($id =="")
												$insert_data = "insert into ami_uploaded_forms values ('','$description','$file_name','$users','$dept_code')";
											else
												$insert_data = "update ami_uploaded_forms set description = '$description', file_name = '$file_name', users = '$users', dept_code='$dept_code' where id ='$id'";
											# print "$insert_data<br>";
											$result=@mysql_db_query($dbname,$insert_data,$id_link);
											if($result)
												print "File uploaded successfully.";
											else
											  print "File uploaded fine but could not update database.\n";
										} else {
										   print "File upload failed\n";
										}
									}
								}
							?>
						</td>
						</tr>
					</table>

				</td>
                <td width="160" valign=top> 
                  <?php include 'incl/rightbar.inc'; ?>
                </td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
<?php include '../incl/bottombar.inc'; ?>
<?php include '../incl/preload.inc'; ?>
</body>
</html>
