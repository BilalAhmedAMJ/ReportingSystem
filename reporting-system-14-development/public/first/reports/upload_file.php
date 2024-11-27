<?
print "Start time:";
echo date("H:i:s");

//include("../incl/dbcon.php");
//include ("protected.php") ?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">
<body bgcolor="#ffffff">
	<form name="report" enctype="multipart/form-data" method="post" action="upload_file.php">
    <table width="580" border="0" class="boxCSS">
	<tr>
	  <td align="center">
		<table border="0">
		<tr><td bgcolor="#000000">
			<span class="pageheader"><font color="white">Attachment:&nbsp;</font></span>
			<input type="file" name="attachment" class="boxCSS1">
		</td></tr>
		</table>
	  </td>
	</tr>
	<tr>
	  <td align="center">
		<input type="submit" name="Submit" value="Upload" class="ButtonCSS">
	  </td>
	</tr>
	</table>
	<?
	$uploaddir = '/var/www/html/reports/attachments/';
	$uploadfile = $uploaddir . $_FILES['attachment']['name'];
	$tmpuploadfile = $_FILES['attachment']['tmp_name'];
	//print "$uploadfile\n";
	//print "$tmpuploadfile\n";
	if ($tmpuploadfile){
		$file_name0 = $_FILES['attachment']['name'];
		//if (copy($_FILES['attachment']['tmp_name'], $uploadfile)) {
		if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadfile)) {
		   print "File is valid, and was successfully uploaded. ";
		} else {
		   print "Possible file upload failed\n";
		}
	}
	print "<br>End time:";
	echo date("H:i:s");
	?>
</body>
</html>
