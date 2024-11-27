<?php include ("protected.php");
include ("../incl/dbcon.php");
if (($user_type!="P") && ($user_type!="GS") && ($user_id!="Admin")) {
        header("Location: list_reports.php");
}
$display_text = "";
if ($_SESSION['request_submitted'] == 1)
{
	$display_text = "The Request Has been Submitted to National Markaz. You will be notified via e-mail once approved.<br><br>Department of National General Secretary";
}
else 
{

	$today = date("Y-m-d");
	// checks.

	$branch_code1 = trim($_POST['branch_code1']);
	$dept_code = trim($_POST['dept_code']);
	$user_name = trim($_POST['user_name']);
	if (($user_name == "") || ($mem_code == "") 
		|| ($user_email1== "")
		|| ($user_phone== ""))
	{
		if ($user_email1== "")
			$display_text = "Incomplete Form: E-mail must be provided";
		if ($user_phone== "")
			$display_text = "Incomplete Form: Phone must be provided";
		if ($user_name== "")
			$display_text = "Incomplete Form: User Name must be provided";
		if ($mem_code == "")
			$display_text = "Incomplete Form: Member Code must be provided";
	}
	if ($display_text == "")
	{
		$mem_code = trim($_POST['mem_code']);
		if ($display_text == "")
		{
			$reason = trim($_POST['reason']);
			$by_birth = trim($_POST['by_birth']);
			$mem_since = trim($_POST['mem_since']);
			$curr_dept = trim($_POST['curr_dept']);
			$past_depts = trim($_POST['past_depts']);
			$age = trim($_POST['age']);
			$moosi = trim($_POST['moosi']);
			$beard = trim($_POST['beard']);
			$vehicle = trim($_POST['vehicle']);
			$chanda_report = trim($_POST['chanda_report']);
			$family = trim($_POST['family']);
			$tazeer = trim($_POST['tazeer']);
			$health = trim($_POST['health']);
			$rel_knowedge = trim($_POST['rel_knowedge']);
			$date_submitted = date("Y-m-d");
			$submitted_by = $user_id;
			$user_phone = trim($_POST['user_phone']);
			$user_email1 = trim($_POST['user_email1']);
			$comments = trim($_POST['comments']);
			$status = 0;

			$insert_query = "INSERT INTO ami_office_request (reqid, branch_code, dept_code, user_name, mem_code, reason, by_birth, mem_since, curr_dept, past_depts, age, moosi, beard, vehicle, chanda_report, family, tazeer, health, rel_knowedge, date_submitted, submitted_by, user_phone, user_email, comments, status)
			VALUES ('', '$branch_code1', '$dept_code', '$user_name', '$mem_code', '$reason', '$by_birth', '$mem_since', '$curr_dept', '$past_depts', '$age', '$moosi', '$beard', '$vehicle', '$chanda_report', '$family', '$tazeer', '$health', '$rel_knowedge', '$date_submitted', '$submitted_by', '$user_phone', '$user_email1', '$comments', '$status')";
			//$results = mysql_query($query);
			//print $insert_query;
			$results=@mysql_db_query($dbname,$insert_query,$id_link);
			if ($results)
			{
				$display_text = "The Request Has been Submitted to National Markaz. You will be notified via e-mail once approved.<br><br>Department of National General Secretary";
				$_SESSION['request_submitted'] = 1;
				$msg = "Attention AMJ Admin\n\nA request has been submitted for 'New Office Bearer' from '".$branch_code1."' branch for the office of '".$dept_code."'.\n\nOnline Reporting System\n".$amjwho; 
				mail("reports@ahmadiyya.ca", "New Office Bearer Request", $msg, "From:reports@ahmadiyya.ca");
				//mail("8340720@gmail.com", "New Office Bearer Request", $msg, "From:reports@ahmadiyya.ca");
			}
			else
			{
				$_SESSION['request_submitted'] = 0;
				$display_text = "Error. Please Advise Administrator<br>";
				print "<br><!--".$insert_query."--><br>";
			}
		}
	}	
}
?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">


</head>
<body bgcolor="#ffffff">
<?php include 'incl/topbar.inc'; ?>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="left" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="100" valign=top bgcolor=black>
        <?php include 'menu.php'; ?></td>
          <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr valign="top">
                <td valign="top"><center>

<?php include '../incl/headscript.inc'; ?>
<br><br>
<span class="pageheader">
<?
print $display_text;
?>
<br><br>
<a href="new_user_request.php">Send Another Request</a>
</span>

</td>
                <td width="160" bgcolor="#F3F3F3">
                  <?php $add_user_php='Y'; include 'incl/rightbar.php'; ?>
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


