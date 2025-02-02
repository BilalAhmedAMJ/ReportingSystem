<? include ("protected.php");
 include("../incl/dbcon.php");
 ?>
<?php
$Error="";
$Error_Msg="";
$Error_Msg2="";
if (!(($user_type=='P') || ($user_level=='N')))
{
	print "Restricted Access. ($user_type,$user_level)";
	exit();
}
if ($select_elect_id != '')
	$elect_id = trim($_REQUEST['select_elect_id']);

if ($elect_id == '')
{
	// go to the elections details page
	header("Location: elections.php");
	exit();
}

// query election detail form db with elect_id
$query = "SELECT * from ami_elections where e_id ='$elect_id';";
$result = @mysql_db_query($dbname,$query);

if (!$result || !($row = mysql_fetch_array($result)) ) {
	//print "Invalid ID - no records matched";
	header("Location: elections.php");
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
	$elect_comments= $row['recommendations'];
	$elect_attendance= $row['participants'];
	$elect_tajneed= $row['total_eligible'];
	$elect_user_submitted = $row['u_id_posted'];
	$elect_date_submitted = $row['date_posted'];
	$elect_comments = $row['recommendations'];
	$elect_approved= $row['approved_name'];
	$elect_status= $row['status'];
}

if ($elect_status != '0')	// only allow view mode
{
	header("Location: election_submit.php?elect_id=".$elect_id."&elect_view_only=1");
	exit();
}
// Request to delete entire election - only by GS, N
if (($delete_elect_id != '') && ($user_type=='GS') && ($user_level=='N'))
{
	// only allowed if draft
        $delete_elect_id= trim($_REQUEST['delete_elect_id']);
	if ($delete_elect_id!= '')
	{
		$queryd = "DELETE from ami_election_votes where e_id ='$delete_elect_id';";
		$resultd = @mysql_db_query($dbname,$queryd);
		if (!$resultd) 
			$Error_Msg = "Unable to delete election details (Report to Administrator)";
		else
		{
			$queryde = "DELETE from ami_elections where e_id ='$delete_elect_id';";
			$resultde = @mysql_db_query($dbname,$queryde);
			if (!$resultde) 
				$Error_Msg = "Unable to delete elections properly (Report to Administrator)";
			else 
			{
				include ('elections.php');
				exit();
			}
		}
	}

}
// Request to delete candidates for votes
if ($delete_mcode!='') {
        $delete_mcode = trim($_REQUEST['delete_mcode']);
	if ($delete_mcode != '')
	{
		$queryd = "DELETE from ami_election_votes where e_id ='$elect_id' and m_code='$delete_mcode';";
		$resultd = @mysql_db_query($dbname,$queryd);
		if (!$resultd) 
			$Error_Msg = "Unable to delete election votes (Report to Administrator)";
	}
}
// Request to add candidates for votes
if ($elect_mcode!='') {

        // capture form values
        $elect_mcode= trim($_REQUEST['elect_mcode']);
        $elect_name= trim($_REQUEST['elect_name']);
        $elect_beard= trim($_REQUEST['elect_beard']);
        $elect_prop_by= trim($_REQUEST['elect_prop_by']);
        $elect_secd_by= trim($_REQUEST['elect_secd_by']);
        $elect_votes= trim($_REQUEST['elect_votes']);

        // validity check: check all values are valid
        if ($elect_mcode == '')
                $Error_Msg .= "Member Code should be a provided<br>";
        if ($elect_name== '')
                $Error_Msg .= "Proposed name should be provided<br>";
        if ($elect_prop_by== '')
                $Error_Msg .= "Proposed by name should be provided<br>";
        if ($elect_secd_by== '')
                $Error_Msg .= "Seconded by name should be provided<br>";
        if ($elect_votes== '')
                $Error_Msg .= "Votes should be provided<br>";
        if ($elect_votes > $elect_attendance)
                $Error_Msg .= "Votes should not be more than Total Attendance<br>";

	if ($Error_Msg == '')
	{
		 // first check if this election vote for this member is already submitted?
                $query0 = "SELECT * from ami_election_votes where e_id='$elect_id' and m_code='$elect_mcode';";
                $result0 = @mysql_db_query($dbname,$query0);
                if (!result0)
                {
                        $Error = "Invalid Query";
                        print "Invalid Query [ElectionVotes.php]";
                        exit();
                }
                if ($row0=mysql_fetch_array($result0)) {
			// Already exist - probably correcting thus update
                        $elect_id = $row0['e_id'];
			$elect_name= $row0['name'];
			$elect_beard= $row0['has_beard'];
			$elect_prop_by=$row0['proposed_by']; 
			$elect_secd_by= $row0['seconded_by']; 
			$elect_votes= $row0['votes'];
//// ***** SHOULD ADD UPDATE QUERY MAYBE ******\\\\\\\	
			$Error_Msg2 .= "Candidate already in list. Delete existing to re-insert";
                }
                else
                {
			// if validitiy pass then insert into database	
			// and then move to add election details page
			  $insert_data = "insert into ami_election_votes
					  values ($elect_id, '$elect_mcode', '$elect_name', '$elect_prop_by', 
						'$elect_secd_by', '$elect_votes', '$elect_beard');";
			//print "$insert_data<br>";
			$result=@mysql_db_query($dbname,$insert_data,$id_link);
			if ($result == "1"){
				//$Error = "Election added successfully!";
				//$subject = "$Field11 - reg success";
				//mail("$Field8", "Online Registration", $smsg, "From:mansooranasir@gmail.com");
			} else {
				$Error = "Could not add election detail!";
				$Error_Msg = "Could not add election details!";
			}
		}
	}	
	else
	{
		// else warn user about errors on this page

	}
}
if ($edit_mcode != '')
{
		 // first check if this election vote for this member is already submitted?
                $query1 = "SELECT * from ami_election_votes where e_id='$elect_id' and m_code='$edit_mcode';";
                $result1 = @mysql_db_query($dbname,$query1);
                if (!result1)
                {
			$Error_Msg = "Unable to edit election votes (Dot not Exist)";
                        exit();
                }
                if ($row0=mysql_fetch_array($result1)) {
			// Already exist - probably correcting thus update
                        $elect_mcode = $edit_mcode;
			$elect_name= $row0['name'];
			$elect_beard= $row0['has_beard'];
			$elect_prop_by=$row0['proposed_by']; 
			$elect_secd_by= $row0['seconded_by']; 
			$elect_votes= $row0['votes'];
			$Error_Msg = "NOTE: You must re-save the edited row in order to save.";
		}
		mysql_free_result($result1);
		$queryd = "DELETE from ami_election_votes where e_id ='$elect_id' and m_code='$edit_mcode';";
		$resultd = @mysql_db_query($dbname,$queryd);
		if (!$resultd) 
			$Error_Msg = "Unable to delete election votes (Report to Administrator)";
}
?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="style.css" rel="stylesheet" type="text/css">
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

              <tr valign="top">
                <td valign="top">
				<?php $elections='Y';
				 ?>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
				<td align="center">
							<!--<center>-->
				   <table class="BoxCSS" border="0">


<!-------->
				  <form name="userForm" method="post" action="election_new.php">
                                        <tr>               <th bgcolor="white"><span class="pageheader">Election Report<br><br></span></th>
                                        </tr>

                                        <? if ($Error_Msg != '') {?>
                                                <tr>
                                                        <td align="center">
                                                        <span class="pagetextAltColorSmall"><? echo $Error_Msg;?></span>
                                                        </td>
                                                </tr>
                                        <? } 
                                        else if ($Error_Msg2 != '') {?>
                                                <tr>
                                                        <td align="center">
                                                        <span class="pagetextAltColorSmall"><? echo $Error_Msg2;?></span>
                                                        </td>
                                                </tr>
                                        <? } ?>
				<tr><td>
				  <table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#c0c0c0">
                                        <tr>
                                                <td bgcolor="#F2FAFB"><p class="topmenutext">Election Type:</p></td>
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
                                                                }
?>

							<input type="hidden" name="elect_type" value="<? echo $elect_type?>">
                                                </td>
                                                <td bgcolor="#F2FAFB"><p class="topmenutext">Total Eligible Voters:</p></td>
                                                <td bgcolor="#F2F8F1"><input name="elect_tajneed" maxlen
                                                        gth="6" size="10" type="text" class="BoxCSS1"
                                                        id="elect_tajneed" value="<? echo $elect_tajneed;?>">
                                                </td>
                                          </tr>
 <tr>
                                                <td bgcolor="#F2FAFB"><p class="topmenutext">Branch:</p></td>
                                                <td bgcolor="#F2F8F1">
                                                <? print $elect_branch; ?>
							<input type="hidden" name="elect_branch" value="<? echo $elect_branch?>">
                                                  </td>
                                                <td bgcolor="#F2FAFB"><p class="topmenutext">Voters Present:</p></td>
                                                <td bgcolor="#F2F8F1"><input name="elect_attendance" maxlen
                                                        gth="6" size="10" type="text" class="BoxCSS1"
                                                        id="elect_attendance" value="<? echo $elect_attendance;?>">
                                                </td>
                                        </tr>
                                        <tr>
                                                <td bgcolor="#F2FAFB"><p class="topmenutext">Date of Election:</p></td>
                                                <td bgcolor="#F2F8F1">
                                                <? print $elect_date_held; ?>
							<input type="hidden" name="elect_date_held" value="<? echo $elect_date_held?>">
                                                </td>
                                                <td bgcolor="#F2FAFB"><p class="topmenutext">Presided By:</p></td>
                                                <td bgcolor="#F2F8F1"><input name="elect_held_by" maxlen
                                                        gth="100" size="25" type="text" class="BoxCSS1"
                                                        id="elect_held_by" value="<? echo $elect_held_by;?>">
                                                </td>
                                        </tr>
                                        <tr>
                                                <td align=left valign=top bgcolor="#F2FAFB"><p class="topmenutext">Comments &amp; personal<br>recommendations:</p></td>
                                                <td colspan="3" bgcolor="#F2F8F1">
							<p class="normaltxt">
							<textarea class="BoxCSS1" name="elect_comments" <? echo $read_only; ?> cols="60" rows="2"><? echo $elect_comments; ?></textarea><br>
							<input type="hidden" name="update_elect_id" value="<? echo $elect_id?>">
							If you have modified any of the above then click to save&nbsp;
                                                        <input type="submit" name="Submit" value="Apply" class="ButtonCSS">
							</p>
                                        </form>
						</td>	
                                        </tr>

					</table>
					</td>
				</tr>
					<tr>
					<td bgcolor="white">&nbsp;</td>
					</tr>

       <!--                                 <tr> <th bgcolor="white"><span class="pageheader">Voting Results<br></span></th> </tr>-->
					<tr>
                                                <td align="center" bgcolor="white"><p class="pageheader">Election Results</p></td>
					</tr>
                                         <tr><td>
						<table border="0"  width=600 cellspacing="1" cellpadding="1"  bgcolor="#c0c0c0">
						<tr>
							<td align=center bgcolor="#F2FAFB"><span class="topmenutext">CODE</span></td>
							<td align=center bgcolor="#F2FAFB"><span class="topmenutext">PROPOSED NAME</span></td>
							<td align=center bgcolor="#F2FAFB"><span class="topmenutext">BEARD</span></td>
							<td align=center bgcolor="#F2FAFB"><span class="topmenutext">PROPOSED BY</span></td>
							<td align=center bgcolor="#F2FAFB"><span class="topmenutext">SECONDED BY</span></td>
							<td align=center bgcolor="#F2FAFB"><span class="topmenutext">VOTES</span></td>
							<td align=center bgcolor="#F2FAFB"><span class="topmenutext">DELETE</span></td>
						</tr>
						<!-- STARTS LIST OF SUMIBTTED VOTES -->
					   <?
						$vote_count = 0;
						$query4 = "SELECT * FROM ami_election_votes where e_id='$elect_id'";
						$result4 = @mysql_db_query($dbname,$query4);
						while ($row4 = mysql_fetch_array($result4)) {
							$vote_count += $row4['votes'];
						?>
							<tr>
							<td align=center bgcolor="#F2F8F1"><? print $row4['m_code']; ?></td>
							<td align=center bgcolor="#F2F8F1"><? print $row4['name']; ?></td>
							<td align=center bgcolor="#F2F8F1"><? print ($row4['has_beard'] == 0)?"No":"Yes"; ?></td>
							<td align=center bgcolor="#F2F8F1"><? print $row4['proposed_by']; ?></td>
							<td align=center bgcolor="#F2F8F1"><? print $row4['seconded_by']; ?></td>
							<td align=center bgcolor="#F2F8F1"><? print $row4['votes']; ?></td>
							<td align=center align=center bgcolor="#F2F8F1"><table border=0 cellspacing=0 cellpadding=0><tr>
							<form name="userForm" method="post" action="election_votes.php"><td>
							<input type="hidden" name="edit_mcode" value="<? echo $row4['m_code']; ?>">
							<input type="hidden" name="elect_id" value="<? echo $elect_id?>">
							<input type="submit" name="Submit" value="Edit" tooltip="Delete" class="ButtonCSSsmallBlue">
							</td></form><td>&nbsp;</td>
							<form name="userForm" method="post" action="election_votes.php"><td>
							<input type="hidden" name="delete_mcode" value="<? echo $row4['m_code']; ?>">
							<input type="hidden" name="elect_id" value="<? echo $elect_id?>">
							<input type="submit" name="Submit" value="Delete" tooltip="Delete" class="ButtonCSSsmallRed">
							</td></tr></table></form>
							</td>
							</tr>
						<? }?>
						<!-- ENDS LIST OF SUMIBTTED VOTES -->
						<tr>
							<td bgcolor="white"  colspan="5" align=right><span class="topmenutext">TOTAL&nbsp;</span></td>
							<td align=center bgcolor="white"><b><? print $vote_count; ?></b></td>
							<td align=center bgcolor="white"><b>
							<?
								if ($vote_count > $elect_attendance)
								{
							?>
								<font color="red">Over Count</font>	
							<?
								}
								else if ($vote_count == $elect_attendance)
								{
							?>
								<font color="green">Match</font>	
							<?
								}
								else 
								{
							?>
								<font color="#b0b0b0"></font>	
							<?
								}	
							?>
							</b>
							</td>
						</tr>

//<? if ($vote_count < $elect_attendance)
//{
?>
						<tr><td bgcolor="#F2F8F1" colspan=8>&nbsp;<br><br><center><font color="blue">
                                                <p class="normaltxt">To add <b>Election Results</b> fill the following fields then click <b>[Save]</b> button</p></font></center>
						</td></tr>
						<!--<tr><td colspan="7" bgcolor="white"><center><p class="normaltxt">Enter voting results one by one using following fields and then Click [Save]:</p></center></td></tr> -->
							<form name="ElectForm" method="post" action="election_votes.php">
								<input type="hidden" name="elect_id" value="<? echo $elect_id?>"> 
						<tr>
							<td bgcolor="#F2F8F1"><input name="elect_mcode" maxlength="6" size="6" type="text" class="BoxCSS1" id="elect_mcode" value="<? if ($Error_Msg!='') echo $elect_mcode;?>"></td>
							<td bgcolor="#F2F8F1"><input name="elect_name" maxlength="30" size="15" type="text" class="BoxCSS1" id="elect_name" value="<? if ($Error_Msg!='') echo $elect_name;?>"></td>
							<td bgcolor="#F2F8F1">
							   <select name="elect_beard">
								<option value="1" selected>Yes</option>
								<option value="0" >No</option>
							   </select>
							</td>
							<td bgcolor="#F2F8F1"><input name="elect_prop_by" maxlength="30" size="15" type="text" class="BoxCSS1" id="elect_prop_by" value="<? if ($Error_Msg!='') echo $elect_prop_by;?>"></td>
							<td bgcolor="#F2F8F1"><input name="elect_secd_by" maxlength="30" size="15" type="text" class="BoxCSS1" id="elect_secd_by" value="<? if ($Error_Msg!='') echo $elect_secd_by;?>"></td>
							<td bgcolor="#F2F8F1"><input name="elect_votes" maxlength="6" size="6" type="text" class="BoxCSS1" id="elect_votes" value="<? if ($Error_Msg!='') echo $elect_votes;?>"></td>
							<td bgcolor="#F2F8F1" align=center>
								<input type="submit" name="Submit" value="Save"><!-- class="ButtonCSS4"-->
							</td>
							</form>
						</tr>
<?
//}
?>
						</table>
                                                </td>
						</tr>

<!-- FORM TO SUBMIT NEW VOTES -->
					<tr><td>
<? if ($vote_count != $elect_attendance) { ?>
<br><center><!--When total votes match voters present then [Submit] button will become available.-->
					<br>&nbsp;<font color="red"><b><? echo $elect_attendance-$vote_count ?></b></font> outstanding votes.</center>
<? } ?>
					<table width="100%" border="0">	
                                        <tr>
                                                 <form name="userForm" method="post" action="elections.php">
                                                <td width="50%" bgcolor="white" align="right"><br>
                                                        <input type="submit" name="Submit" value="Back" class="ButtonCSS4">
                                                </td>           
                                                </form>
<? if (($user_type=='GS') && ($user_level=='N') && ($elect_status=='0')) { ?>
						<form name="userForm" method="post" action="election_votes.php">
						<input type="hidden" name="select_elect_id" value="<? echo $elect_id ?>">
						<input type="hidden" name="delete_elect_id" value="<? echo $elect_id ?>">
                                        	<td bgcolor="white" align="left"><br>
						<input type="submit" name="Submit" value="Delete" class="ButtonCSS1">
						</td>
                                                </form>

<? } ?>
						<form name="userForm" method="post" action="election_submit.php">
						<input type="hidden" name="elect_id" value="<? echo $elect_id ?>">
                                        	<td width="50%" bgcolor="white" align="left"><br>
<? //if ($vote_count == $elect_attendance) { ?>
						<input type="submit" name="Submit" value="Submit" class="ButtonCSS">
<?// } else {?>
					

<? //} ?>
						</form>
						</td>
                                        </tr>
					</table>
					</td><tr>

					  </table>
						<script language="JavaScript">
								document.ElectForm.elect_mcode.focus();
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
                  <?php $elections_php='Y'; include 'incl/rightbar.inc'; ?>
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
