<? include ("protected.php");
 include("../incl/dbcon.php");
 ?>
<?php
$Error="";
$Error_Msg="";
$pdt = date("Y-m-d");
if (!(($user_type=='P') || ($user_level=='N')))
{
	print "Restricted Access. ($user_type,$user_level)";
	exit();
}

//if ($elect_id != '')
//	$elect_id = trim($_REQUEST['elect_id']);


if ($submit_elect_id != '')
	$elect_id = trim($_REQUEST['submit_elect_id']);

// fetch record to check if stored values match
$query = "SELECT * from ami_elections where e_id ='$elect_id';";
$result = @mysql_db_query($dbname,$query);

if (!$result || !($row = mysql_fetch_array($result)) ) {
	print "Invalid ID - unable to submit";
	//header("Location: elections.php");
	$elect_id = '';
	exit();
} 
$elect_attendance= $row['participants'];
$elect_approved= $row['approved_m_code'];
$elect_status= $row['status'];
$elect_branch= $row['branch_code'];
$elect_dept= $row['dept_code'];
$elect_shura= $row['type'];
if ($elect_shura == '1')
	$elect_dept= '';
$elect_date_held= $row['date_held']; 
$elect_held_by= $row['held_by'];
$elect_tajneed= $row['total_eligible'];
$elect_user_submitted = $row['u_id_posted'];
$elect_date_submitted = $row['date_posted'];
$elect_comments = $row['recommendations'];

mysql_free_result($result);

// fetch record to check if stored values match
$query = "SELECT sum(votes) t_votes from ami_election_votes where e_id ='$elect_id';";
$result = @mysql_db_query($dbname,$query);

if (!$result || !($row = mysql_fetch_array($result)) ) {
	print "Invalid ID - unable to submit";
	$elect_id = '';
	exit();
} 
$total_votes= $row['t_votes'];
mysql_free_result($result);

// submitting elections 
// or  submitting election to be marked as completed
if ($submit_elect_id != "")
{
	if ($elect_status != '0')
	{
                $Error = "Could not submit election as elections were already completed!";
                $Error_Msg = "Could not submit election as elections were already completed!";
	}
	else if ($elect_approved != '0')
	{
                $Error = "Could not submit election as elections were already completed and approved!";
                $Error_Msg = "Could not submit election as elections were already completed and approved!";
	}
/*	else if ($elect_attendance != $total_votes)
	{
                $Error = "Could not submit election as votes count do not match attendance!";
                $Error_Msg = "Could not submit election report as votes count do not match attendance!";
	}*/
	else
	{
		// already exist thus update
		$elect_date_submitted = $pdt;
		$elect_user_submitted = $user_id;
		$update_data = "update ami_elections set status = '$submit_elect_status',
				u_id_posted = '$elect_user_submitted',
				date_posted = '$elect_date_submitted'
				where e_id = '$submit_elect_id'";
		$result=@mysql_db_query($dbname,$update_data,$id_link);
		if ($result == "1"){
			$elect_id = '';
		} else {
			$Error = "Could not submit election report!";
			$Error_Msg = "Could not submit election report!";
		}
        }
}

if ($elect_id == '')
{
	// go to the elections details page
	header("Location: elections.php");
	exit();
}

if (($user_type=='GS') && ($user_level=='N'))
{
	if ($reopen != "") 
	{
		if ($elect_approved != '0')
		{
			$Error = "Could not re-open as election were already approved!";
			$Error_Msg = "Could not re-open as election were already approved!";
		}
		else
		{
			// reopen make it draft
			$update_data = "update ami_elections set status = '0'
					where e_id = '$elect_id'";
			$result=@mysql_db_query($dbname,$update_data,$id_link);
			if ($result == "1"){
			$elect_status = '0';
			} else {
				$Error = "Could not re-open!";
				$Error_Msg = "Could not re-open!";
				include ('elections.php');
				exit();
			}
			include ("election_votes.php");
			exit();
		}
	} // else if we are trying to approve then check if we can	
	else if ($approved_mcode!='')
	{
		if ($elect_status != '1')
		{
			$Error = "Could not approve as election are not completed yet!";
			$Error_Msg = "Could not approve as election are not completed yet!";
		}
		else if ($elect_approved != '0')
		{
			$Error = "Could not approve as election were already approved!";
			$Error_Msg = "Could not approve as election were already approved!";
		}
		else
		{	
			$update_data = "update ami_elections set 
					approved_m_code = '$approved_mcode'
					where e_id = '$elect_id'";
			$result=@mysql_db_query($dbname,$update_data,$id_link);
			if ($result == "1"){
				$elect_approved = $approved_mcode;

				if ($sender_page != '')
				{
					header("Location: ".$sender_page);
					exit();
				}
			} else {
				$Error = "Could not update election report!";
				$Error_Msg = "Could not update election report!";
			}
		}
	} // else if we are trying to unapprove then check if we can	
	else if (($undo_approved_mcode!='') && ($undo_approved_mcode!='0'))
	{
		if ($elect_approved == '0')
		{
			$Error = "Could not unapproved. Election is not approved!";
			$Error_Msg = "Could not unapproved. Election is not approved!";
		}
		else
		{	
			$update_data = "update ami_elections set 
					approved_m_code = '0'
					where e_id = '$elect_id'";
			$result=@mysql_db_query($dbname,$update_data,$id_link);
			if ($result == "1"){
				$elect_approved = '0';

				if ($sender_page != '')
				{
					header("Location: ".$sender_page);
					exit();
				}
			} else {
				$Error = "Could not update election report!";
				$Error_Msg = "Could not update election report!";
			}
		}
	}
}
	/*
							// query election detail form db with elect_id
							$query = "SELECT * from ami_elections where e_id ='$elect_id';";
							$result = @mysql_db_query($dbname,$query);

							if (!$result || !($row = mysql_fetch_array($result)) ) {
								print "Invalid ID - no records matched";
								//header("Location: elections.php");
								$elect_id = '';
								exit();
							} else {
								$elect_branch= $row['branch_code'];
								$elect_dept= $row['dept_code'];
								$elect_shura= $row['type'];
								if ($elect_shura == '1')
									$elect_dept= '';
								$elect_date_held= $row['date_held']; 
								$elect_held_by= $row['held_by'];
								$elect_attendance= $row['participants'];
								$elect_tajneed= $row['total_eligible'];
								$elect_user_submitted = $row['u_id_poasted'];
								$elect_date_submitted = $row['date_posted'];
								$elect_comments = $row['recommendations'];
								$elect_approved= $row['approved_m_code'];
								$elect_status= $row['status'];

								mysql_free_result($result);
							}
	*/

?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">
<?php include '../incl/headscript.inc'; ?>
</head>
<body bgcolor="#ffffff">
<?php include 'incl/topbar.inc'; ?>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="right" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="100" valign=top bgcolor=black>
        <?php include 'menu.php'; ?></td>
          <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">

          <td colspan="2" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr valign="top">
                <td valign="top">
				<?php $elections='Y';?>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
				<td align="center">
							<!--<center>-->
				   <table class="BoxCSS" border="0" width="100%">
					<tr>
						<th bgcolor="white"><span class="pageheader">Election Report<br><br></span></th>
					</tr>
					 <? if ($Error_Msg != '') {?>
                                                <tr>
                                                        <td align="center">
                                                        <span class="pagetextAltColorSmall"><? echo $Error_Msg;?></span>
                                                        </td>
                                                </tr>
                                        <? }?>
	<?
					///////////////////////////////////////////////////////////////
                                        // retrieve session if not supplied by form post
                                        session_start();
                                        $myary = $_SESSION['election_submit'];

                                        if ($sender_page == "")
                                        {
                                            $sender_page= $myary['sender_page'];
                                        }

                                        $_SESSION['election_submit'] = array ("sender_page" => $sender_page);
                                        ///////////////////////////////////////////////////////////////

	?>
	
				   <tr>
				   <td>
					<table width="600" border="0" bgcolor="#c0c0c0" cellspacing="1" cellpadding="1">

					<tr>
						<td bgcolor="#F2FAFB"><p class="topmenutext">ELECTION FOR:</p></td>
						<td bgcolor="#F2F8F1">
								<?
								if ($elect_dept == '')
									print "Shura";
								else if ($elect_dept == 'P')
                                                                        print "President";
								else
								{
									$query3 = "SELECT report_name, report_code FROM ami_reports where report_code='$elect_dept'";
									$result3 = @mysql_db_query($dbname,$query3);
									$row3 = mysql_fetch_array($result3);
									if ($row3)
										print $row3['report_name'];	
									mysql_free_result($result3);
								}
								?>
						</td>
						<td bgcolor="#F2FAFB"><p class="topmenutext">TOTAL ELIGIBLE VOTERS:</p>
						</td>
						<td bgcolor="#F2F8F1">
							<?  print $elect_tajneed; ?>
						</td>
					  </tr>
					 <tr>
						<td bgcolor="#F2FAFB"><p class="topmenutext">JAMA`AT:</p></td>
						<td bgcolor="#F2F8F1">
							<?  
									$query2 = "SELECT branch_name FROM ami_branches where branch_code='$elect_branch'";
									$result2 = @mysql_db_query($dbname,$query2);
									$row2 = mysql_fetch_array($result2);
									if ($row2)
										print $row2['branch_name'];
									else
										print $elect_branch; 
									mysql_free_result($result2);
							?>
						 </td>
						<td bgcolor="#F2FAFB"><p class="topmenutext">VOTERS PRESENT:</p>
						</td>
						<td bgcolor="#F2F8F1">
							<?  print $elect_attendance; ?>
						</td>
					</tr>
					<tr>
						<td bgcolor="#F2FAFB"><p class="topmenutext">DATE OF ELECTION:</p>
						</td>
						<td bgcolor="#F2F8F1">
							<? print $elect_date_held;?>
						</td>
						<td bgcolor="#F2FAFB"><p class="topmenutext">PRESIDED BY:</p>
						</td>
						<td bgcolor="#F2F8F1">
							<?  print $elect_held_by; ?>
						</td>
					</tr>
					<tr>
						<td valign=top align=left bgcolor="#F2FAFB"><p class="topmenutext">Comments &amp; personal<br>recommendations:</p>
						</td>
						<td colspan="3" bgcolor="#F2F8F1">
							<p class="normaltxt"><?  print $elect_comments; ?></p>
						</td>
					</tr>
					</table>
					</td>
					</tr>
					<tr>
					<td bgcolor="white">&nbsp;</td>
					</tr>

<? if (($user_type=='GS') && ($user_level=='N') && ($elect_approved=='0'))
{?>
					<tr>
					<td align=center bgcolor="white"><span class="topmenutext">To approve a candiate select corresponding [Approve] button<BR>***WARNING: THIS CANNOT BE CHANGED ONCE APPROVED***</span></td>
					</tr>
<?}?>
                                         <tr><td>
						<table border="0" cellspacing="1" cellpadding="1"  bgcolor="#c0c0c0" width="100%">
						<tr>
<? if (($user_type=='GS') && ($user_level=='N') && ($elect_approved=='0') && ($elect_status == '1')) { ?>
						<form name="userForm" method="post" action="election_submit.php">
						<input type="hidden" name="elect_id" value="<? echo $elect_id?>">
							<td align="center" bgcolor="#F2FAFB"><span class="topmenutext">&nbsp; </span></td>
<? } ?>
							<td align="center" bgcolor="#F2FAFB"><span class="topmenutext">NO.</span></td>
							<td align="center" bgcolor="#F2FAFB"><span class="topmenutext">CODE</span></td>
							<td align="center" bgcolor="#F2FAFB"><span class="topmenutext">PROPOSED NAME</span></td>
							<td align="center" bgcolor="#F2FAFB"><span class="topmenutext">BEARD</span></td>
							<td align="center" bgcolor="#F2FAFB"><span class="topmenutext">PROPOSED BY</span></td>
							<td align="center" bgcolor="#F2FAFB"><span class="topmenutext">SECONDED BY</span></td>
							<td align="center" bgcolor="#F2FAFB"><span class="topmenutext">VOTES</span></td>
						<!--	<td align="center" bgcolor="#F2FAFB"><span class="topmenutext">Status</span></td>-->
						</tr>
						<!-- STARTS LIST OF SUMIBTTED VOTES -->
					   <?
						$vote_count = 0;
						$i = 0;
						$query4 = "SELECT * FROM ami_election_votes where e_id='$elect_id' order by votes desc, name";
						$result4 = @mysql_db_query($dbname,$query4);
						while ($row4 = mysql_fetch_array($result4)) {
							$vote_count += $row4['votes'];
							$i++;	
						?>
							<tr>
<? if (($user_type=='GS') && ($user_level=='N') && ($elect_approved=='0') && ($elect_status == '1')) { ?>
							<td align="center" bgcolor="#F2F8F1">
							<input type="radio" name="approved_mcode" value="<? print $row4['m_code'] ?>">
							</td>
<? } ?>
							<td align="center" bgcolor="#F2F8F1"><? print $i; ?></td>
							<td align="center" bgcolor="#F2F8F1"><? print $row4['m_code']; ?></td>
							<td align="center" bgcolor="#F2F8F1"><? print $row4['name']; ?></td>
							<td align="center" bgcolor="#F2F8F1"><? print ($row4['has_beard'] == 0)?"No":"Yes"; ?></td>
							<td align="center" bgcolor="#F2F8F1"><? print $row4['proposed_by']; ?></td>
							<td align="center" bgcolor="#F2F8F1"><? print $row4['seconded_by']; ?></td>
							<td align="center" bgcolor="#F2F8F1"><? print $row4['votes']; ?></td>
<? if (($user_type=='GS') && ($user_level=='N') && ($elect_approved=='0') && ($elect_status == '1')) { ?>
<!--					<form name="userForm" method="post" action="election_submit.php">
						<td align="center" bgcolor="#F2FAFB">
						<input type="hidden" name="elect_id" value="<? echo $elect_id?>">
						<input type="hidden" name="approved_mcode" value="<? print $row4['m_code']; ?>">
							<input type="submit" name="Submit" value="Approve" class="ButtonCSS">
						</td>
					</form>	-->
<? } else if ($elect_approved == $row4['m_code']) { ?>
<!--							<td align="center" bgcolor="#F2F8F1"><font color="green"><b>APPROVED</b></font></td> -->
<? } else { ?>
<!--							<td align="center" bgcolor="#F2F8F1">&nbsp;</td> -->
<? } ?>
							</tr>
						<? }
						mysql_free_result($result4); ?>
						</table>
                                                </td>
                                        </tr>

<!-- FORM TO SUBMIT NEW VOTES -->
					<tr> 
		<? if (($user_type=='GS') && ($user_level=='N') && ($elect_approved=='0') && ($elect_status == '1')) { ?>
                                                <td bgcolor="white" align="left">
                                                       <br><input type="submit" name="Submit" value="Approve" class="ButtonCSS2">
                                                </td>
						</form>
		<?  } else if (($user_type=='GS') && ($user_level=='N') && ($elect_approved!='0') && ($elect_status == '1')) { ?>
						 <form name="userForm" method="post" action="election_submit.php">
						<input type="hidden" name="elect_id" value="<? echo $elect_id?>">
						<input type="hidden" name="undo_approved_mcode" value="1">
                                                <td bgcolor="white" align="left">
                                                        <br><input type="submit" name="Submit" value="Unapprove" class="ButtonCSS1">
                                                </td>
						</form>
		<? } else { ?>
					<td >&nbsp;<br></td>
		<? } ?>
					</tr>

<? if ($vote_count!=$elect_attendance) {?>
					<tr> <td align=center ><font color=red><b>Warning: Votes count (<?print $vote_count;?>) does not match Voters Present (<? print $elect_attendance; ?>)</b></font><br></td></tr>
<? } 
	//if (($elect_status =='0') && ($elect_view_only!='1') && ($vote_count==$elect_attendance)) 
	if (($elect_status =='0') && ($elect_view_only!='1')) {?>
                                        <tr><td align=center><span class="normaltxt">You can <b>Modify</b> above election report or press <b>Submit</b> to send to National Office.<br>Note: Once Submitted you will not be able to modify the report</span><br><br></td>
                                        </tr>
                                        <tr>
					<td>
						<table width="100%" border="0">
						<tr>
						<form name="userForm" method="post" action="election_votes.php">
						<!--<form name="userForm" method="post" action="election_submit.php">-->
						<input type="hidden" name="elect_id" value="<? echo $elect_id?>">
                                        	<td  bgcolor="white" align="right">
							<input type="submit" name="Submit" value="Modify" class="ButtonCSS">
						</td>
						</form>
						<form name="userForm" method="post" action="election_submit.php">
						<input type="hidden" name="submit_elect_id" value="<? echo $elect_id?>">
						<input type="hidden" name="submit_elect_status" value="1">
                                        	<td bgcolor="white" align="left">
                                                           <!--<select name="elect_status">
                                                                <option value="0" <? print ($elect_status!='0')?"":"selected"?> >Draft</option>
                                                                <option value="1" <? print ($elect_status!='1')?"":"selected"?> >Complete</option>
                                                           </select>-->

							<input type="submit" name="Submit" value="Submit" class="ButtonCSS">
						</td>
						</form>
						</tr>
						</table>
					</td>
                                        </tr>

<? } else { ?>
                                        <tr>
					<td>
						<table width="100%" border="0">
						<tr>
						<? if (($sender_page == '') || ($sender_page=='home'))
						{ 
							$sender_page = "elections.php";
						} ?>
						<form name="userForm" method="post" action="<? echo $sender_page ?>">
                                                <td bgcolor="white" align="center">
                                                        <br><input type="submit" name="Submit" value="Back" class="ButtonCSS4">
                                                </td>
						</form>
						</tr>
						</table>
					</td>
                                        </tr>
<? }?>


					 </table>
								<script language="JavaScript">
									/document.userForm.submit.focus();
								</script>
					</td>
					</tr>
					</table>
							<!--</center>-->
						</td>
					  </tr>

					</table>
				</td>
                <td width="160" bgcolor="#F3F3F3">
                  <?php $elecitons_php='Y'; include 'incl/rightbar.inc'; ?>
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
