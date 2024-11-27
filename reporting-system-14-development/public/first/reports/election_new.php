<? include ("protected.php");
 include("../incl/dbcon.php");
 ?>
<?php
$Error="";
$Error_Msg="";
if (!(($user_type=='P') || ($user_level=='N')))
{
	print "Restricted Access. ($user_type,$user_level)";
	exit();
}

$elect_id = '';

$pdt = date("Y-m-d");
if ($elect_branch!='') {	

	// capture form values
        $elect_dept= trim($_REQUEST['elect_dept']);
        $elect_branch= trim($_REQUEST['elect_branch']);
        $elect_tajneed= trim($_REQUEST['elect_tajneed']);
        $elect_attendance= trim($_REQUEST['elect_attendance']);
        $elect_held_by= trim($_REQUEST['elect_held_by']);
        $elect_date_held= trim($_REQUEST['elect_date_held']);
        $elect_comments = trim($_REQUEST['elect_comments']);
	$elect_user_submitted = $user_id;	
	$elect_date_submitted = $pdt;	
	if ($elect_dept == '') {
		$elect_shura = 1;
		$elect_dept=="0";
	}
	else
		$elect_shura = 0;
	$elect_status = 0;	// not submitted

	// validity check: check all values are valid
	if ($elect_date_held == '')
		$Error_Msg .= "Election Date should be a provided<br>";
	if ($elect_date_held>$pdt)
		$Error_Msg .= "Election Date should not be a future date<br>";
	if ($elect_held_by == '')
		$Error_Msg .= "Presided By name should be provided<br>";
	if ($elect_tajneed== '')
		$Error_Msg .= "Eligible Members should be provided<br>";
	if ($elect_attendance == '')
		$Error_Msg .= "Voters Present should be provided<br>";
	if ($elect_attendance > $elect_tajneed )
		$Error_Msg .= "Total Eligible Voters should not be more than Voters Present<br>";

	if ($Error_Msg == '')
	{
		if ($update_elect_id != "")
		{
			// already exist thus update
			$elect_comments = trim($_REQUEST['elect_comments']);
			$update_data = "update ami_elections set participants = '$elect_attendance',
						total_eligible = '$elect_tajneed',
						u_id_posted = '$elect_user_submitted',
						date_posted = '$elect_date_submitted',
						recommendations= '$elect_comments',
						held_by = '$elect_held_by'
					where e_id = '$update_elect_id'";
			$result=@mysql_db_query($dbname,$update_data,$id_link);
			if ($result == "1"){
				$elect_id = $update_elect_id;
			} else {
				$Error = "Could not update election report!";
				$Error_Msg = "Could not update election report!";
			}
		}
		else
		{
			// first check if this election report is already submitted?
			$query0 = "SELECT e_id from ami_elections where branch_code='$elect_branch' and dept_code='$elect_dept' and date_held='$elect_date_held';";
			$result0 = @mysql_db_query($dbname,$query0);
			if (!result0)
			{
				$Error = "Invalid Query";
				print "Invalid Query [Elections.php]";
				exit();
			}
			if ($row0=mysql_fetch_array($result0)) {
				$elect_id = $row0['e_id'];
			// already exist thus update
			$elect_comments = trim($_REQUEST['elect_comments']);
			$update_data = "update ami_elections set participants = '$elect_attendance',
						total_eligible = '$elect_tajneed',
						u_id_posted = '$elect_user_submitted',
						date_posted = '$elect_date_submitted',
						recommendations= '$elect_comments',
						held_by = '$elect_held_by'
					where e_id = '$elect_id'";
			$result=@mysql_db_query($dbname,$update_data,$id_link);
				// should update
			}
			else
			{
				// if validitiy pass then insert into database	
				// and then move to add election details page
				  $insert_data = "insert into ami_elections
						  values ('', '$elect_branch', '$elect_dept', '$elect_shura', 
							'$elect_date_held', '$elect_held_by', '$elect_attendance', 
							'$elect_tajneed','$elect_user_submitted', '$elect_date_submitted',
							'$elect_comments', '', '$status');";
			       // print "$insert_data<br>";
				$result=@mysql_db_query($dbname,$insert_data,$id_link);
				$elect_id = mysql_insert_id();
				if ($result == "1"){
					//$Error = "Election added successfully!";
					//$subject = "$Field11 - reg success";
					//mail("$Field8", "Online Registration", $smsg, "From:mansooranasir@gmail.com");
					//print "$elect_id<br>";
				} else {
					$Error = "Could not create election report!";
					$Error_Msg = "Could not create election report!";
					$elect_id = '';
				}
			}
		}
		 for ($i=0; $i<12; $i++)
		 {
			$elect_mcode= trim($_REQUEST['elect_mcode'][$i]);
			$elect_name= trim($_REQUEST['elect_name'][$i]);
			$elect_beard= trim($_REQUEST['elect_beard'][$i]);
			$elect_prop_by= trim($_REQUEST['elect_prop_by'][$i]);
			$elect_secd_by= trim($_REQUEST['elect_secd_by'][$i]);
			$elect_votes= trim($_REQUEST['elect_votes'][$i]);

			// validity check: check all values are valid
			if ($elect_mcode == '')
				continue;
				//$Error_Msg .= "Member Code should be a provided<br>";
			if ($elect_name== '')
				continue;
				//$Error_Msg .= "Proposed name should be provided<br>";
			if ($elect_prop_by== '')
				continue;
				//$Error_Msg .= "Proposed by name should be provided<br>";
			if ($elect_secd_by== '')
				continue;
				//$Error_Msg .= "Seconded by name should be provided<br>";
			if ($elect_votes== '')
				continue;
				//$Error_Msg .= "Votes should be provided<br>";
		//        if ($elect_votes > $elect_attendance)
		 //               $Error_Msg .= "Votes should not be more than Total Attendance<br>";

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
					continue;
					// Already exist - probably correcting thus update
		/*                        $elect_id = $row0['e_id'];
					$elect_name= $row0['name'];
					$elect_beard= $row0['has_beard'];
					$elect_prop_by=$row0['proposed_by'];
					$elect_secd_by= $row0['seconded_by'];
					$elect_votes= $row0['votes'];
		*/
		//// ***** SHOULD ADD UPDATE QUERY MAYBE ******\\\\\\\
		//                        $Error_Msg2 .= "Candidate already in list. Delete existing to re-insert";


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
	}
        else
        {
                // else warn user about errors on this page

        }
	
}
					///////////////////////////////////////////////////////////////
                                        // retrieve criteria from session if not supplied by form post
                                        session_start();
                                        $myary = $_SESSION['election_new'];

                                        if ($elect_date_held=="")
                                        {
                                                $elect_date_held= $myary['elect_date_held'];
                                        }
                                        if ($elect_tajneed=="")
                                        {
                                                $elect_tajneed= $myary['elect_tajneed'];
                                        }
                                        if ($elect_attendance=="")
                                        {
                                                $elect_attendance= $myary['elect_attendance'];
                                        }
                                        if ($elect_held_by=="")
                                        {
                                                $elect_held_by= $myary['elect_held_by'];
                                        }
                                        if ($elect_dept=="")
                                        {
                                                $elect_dept= $myary['elect_dept'];
                                        }
                                        if ($elect_branch=="")
                                        {
                                                $elect_branch = $myary['elect_branch'];
                                        }

                                        $_SESSION['election_new'] = array (
                                                                        "elect_date_held" => $elect_date_held,
                                                                        "elect_held_by" => $elect_held_by,
                                                                        "elect_tajneed" => $elect_tajneed,
                                                                        "elect_attendance" => $elect_attendance,
                                                                        "elect_dept" => $elect_dept,
                                                                        "elect_branch" => $elect_branch);
                                        ///////////////////////////////////////////////////////////////


if ($elect_id != '')
{
	// go to the add election details page
//	include("election_votes.php");
	include("election_submit.php");
	exit();
}

?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">
<?php include '../incl/headscript.inc'; ?>

<script language="JavaScript">

function validaterow(row_num)
{
	var all_vals = document.userForm.elect_mcode[row_num].value;
	all_vals = all_vals + document.userForm.elect_name[row_num].value;
	all_vals = all_vals + document.userForm.elect_prop_by[row_num].value;
	all_vals = all_vals + document.userForm.elect_secd_by[row_num].value;
	all_vals = all_vals + document.userForm.elect_votes[row_num].value;

	var has_val =  (all_vals != '');

	if (has_val && document.userForm.elect_mcode[row_num].value == '')
		document.userForm.elect_mcode[row_num].style.backgroundColor="red";
	else
		document.userForm.elect_mcode[row_num].style.backgroundColor="white";
	if (has_val && document.userForm.elect_votes[row_num].value == '')
		document.userForm.elect_votes[row_num].style.backgroundColor="red";
	else
		document.userForm.elect_votes[row_num].style.backgroundColor="white";
	if (has_val && document.userForm.elect_name[row_num].value == '')
		document.userForm.elect_name[row_num].style.backgroundColor="red";
	else
		document.userForm.elect_name[row_num].style.backgroundColor="white";
	if (has_val && document.userForm.elect_prop_by[row_num].value == '')
		document.userForm.elect_prop_by[row_num].style.backgroundColor="red";
	else
		document.userForm.elect_prop_by[row_num].style.backgroundColor="white";
	if (has_val && document.userForm.elect_secd_by[row_num].value == '')
		document.userForm.elect_secd_by[row_num].style.backgroundColor="red";
	else
		document.userForm.elect_secd_by[row_num].style.backgroundColor="white";
			
}
function matchcount()
{
	sumvotes();
	validatevoters();
	var tot_votes = 0;
	for( var i = 0; i < 12; i++ ) {
		if (document.userForm.elect_votes[i].value != '')
			tot_votes = tot_votes + parseInt(document.userForm.elect_votes[i].value);
	}
	if (tot_votes == 0) {
		sumvotes();
		alert("Please complete the form and fix any errors");
		return false;
	}
}
function validatevoters()
{
	if (parseInt(document.userForm.elect_attendance.value) <= 0)
		document.userForm.elect_attendance.style.backgroundColor="red";
	else
		document.userForm.elect_attendance.style.backgroundColor="white";

	if (parseInt(document.userForm.elect_tajneed.value) <= 0)
		document.userForm.elect_tajneed.style.backgroundColor="red";
	else
		document.userForm.elect_tajneed.style.backgroundColor="white";

	if (document.userForm.elect_attendance.value == "")
		document.userForm.elect_attendance.style.backgroundColor="yellow";
	else
		document.userForm.elect_attendance.style.backgroundColor="white";

	if (document.userForm.elect_tajneed.value == "")
		document.userForm.elect_tajneed.style.backgroundColor="yellow";
	else
		document.userForm.elect_tajneed.style.backgroundColor="white";

	if (document.userForm.presided_by.value == "")
		document.userForm.presided_by.style.backgroundColor="yellow";
	else
		document.userForm.presided_by.style.backgroundColor="white";
}

function sumvotes()
{
//	var votesarray= document.userForm.elements['elect_votes[]'];
	var tot_votes = 0;
//	alert(document.userForm.elect_votes[0].value);
	//alert(document.userForm.elements['elect_votes[]'].length);
	for( var i = 0; i < 12; i++ ) {
		if (document.userForm.elect_votes[i].value != '')
			tot_votes = tot_votes + parseInt(document.userForm.elect_votes[i].value);
	}
	document.userForm._votes_total.value = tot_votes;
	if (tot_votes== 0) { 
		document.userForm._votes_total.style.backgroundColor="yellow";
		document.userForm.elect_attendance.style.backgroundColor="yellow";
		//document.userForm.Submit.disabled="true";
		document.userForm.Submit.style.backgroundColor="red";
	} else if (tot_votes != parseInt(document.userForm.elect_attendance.value)) {
		document.userForm._votes_total.style.backgroundColor="yellow";
		document.userForm.elect_attendance.style.backgroundColor="yellow";
		//document.userForm.Submit.disabled="true";
		document.userForm.Submit.style.backgroundColor="yellow";
	} else {
		document.userForm.elect_attendance.style.backgroundColor="white";
		document.userForm._votes_total.style.backgroundColor="white";
		//document.userForm.Submit.disabled="false";
		document.userForm.Submit.style.backgroundColor="white";
	}
		
}
</script>

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
				<?php $elections='Y';?>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
				<td align="center">
							<!--<center>-->
				   <table class="BoxCSS" border="0">
				   <tr>
				   <td>
					<table width="450" border="0">
					<form name="userForm" method="post" action="election_new.php" onsubmit="return matchcount();">
					<tr>
						<th colspan=4><span class="pageheader">New Election Report</span></th>
					</tr>
					<tr>
						<td colspan=4 align="center" bgcolor="white"><p class="normaltxt"><br>Complete the following to create a new election report:</p><br></td>
					</tr>

					<? if ($Error_Msg != '') {?>
						<tr>
							<td colspan="4" align="center">
							<span class="pagetextAltColorSmall"><? echo $Error_Msg;?></span>
							</td>
						</tr>
					<? } ?>
<?
?>
					<tr>
						<td bgcolor="#F2FAFB"><p class="topmenutext">Election Type:</p></td>
						<td>
										<?//Get all Reports
										$query3 = "SELECT report_name, report_code FROM ami_reports where report_code!='AR' and report_code!='QR' and report_code!='TR' order by report_name";
										$result3 = @mysql_db_query($dbname,$query3);?>
										<select name="elect_dept">
										<option value="P">President</option>
										<option value="" selected>*Shura*</option>
										<?php 
										  while ($row3 = mysql_fetch_array($result3)) { 
										
											$val = $row3['report_code'];
											$des = $row3['report_name'];
											if ($elect_dept== $val) {?>
												<option value=<? print "\"$val\"";  ?> selected><? print "$des";  ?></option>
                                                                                        <?} else {?>
                                                                                                <option value=<? print "\"$val\"";  ?>><? print "$des";  ?></option>
                                                                                        <? }
                                                                                  }?>
                                                                                 </select>
						</td>
						<td bgcolor="#F2FAFB"><p class="topmenutext">Total Eligible Voters:</p></td>
						<td bgcolor="#F2F8F1"><input name="elect_tajneed" maxlength="6"
							 size="10" type="text" class="BoxCSS1" onblur="sumvotes(); validatevoters();"
							id="elect_tajneed" value="<? echo $elect_tajneed;?>">
						</td>
					  </tr>
					 <tr>
						<td bgcolor="#F2FAFB"><p class="topmenutext">Branch:</p></td>
						<td>
							<? if ($user_level=="N") { 
                                                                                         // Get the branches
                                                                                         $query3 = "SELECT * FROM ami_branches where status=1 and branch_code!='$nat_branch' order by branch_name";
                                                                                         $result3 = @mysql_db_query($dbname,$query3);?>
                                                                                         <select name="elect_branch">
                                                                                         <!--<option value="all" selected>All</option>-->
                                                                                         <?php while ($row3 = mysql_fetch_array($result3)) { ?>
                                                                                          <?
                                                                                                        $val = $row3['branch_code'];
                                                                                                        $des = $row3['branch_name'];

                                                                                                if ($elect_branch == $val) {?>
                                                                                                        <option value=<? print "\"$val\"";  ?> selected><? print "$des";  ?></option>
                                                                                                <?} else {?>
                                                                                                        <option value=<? print "\"$val\"";  ?>><? print "$des";  ?></option>
                                                                                                <? }
                                                                                        } ?>
                                                                                                </select>
							<? } else {
								print $branch_code;
								?>
                                                                                            &nbsp;<input type="hidden" name="elect_branch" value="<? echo $branch_code?>">
                                                           <? }?>
						  </td>
						<td bgcolor="#F2FAFB"><p class="topmenutext"><b>Voters Present:</b></p></td>
						<td bgcolor="#F2F8F1"><input name="elect_attendance" maxlength="6"
							size="10" type="text" class="BoxCSS1"  onblur="sumvotes(); validatevoters();"
							id="elect_attendance" value="<? echo $elect_attendance;?>">
						</td>
					</tr>
					<tr>
						<td bgcolor="#F2FAFB"><p class="topmenutext">Date of Election:</p></td>
						<td bgcolor="#F2F8F1"><input name="elect_date_held" maxlen
							gth="10" size="10" type="text" class="BoxCSS1" 
							id="elect_date_held" value="<? echo (!$elect_date_held)?$pdt:$elect_date_held; ?>"><br>YYYY-MM-DD

						</td>
						<td bgcolor="#F2FAFB"><p class="topmenutext">Presided By:</p></td>
						<td bgcolor="#F2F8F1"><input name="elect_held_by" maxlength="100" 
							size="25" type="text" class="BoxCSS1" onblur="sumvotes(); validatevoters();"
							id="elect_held_by" value="<? echo $elect_held_by;?>">
						</td>
					</tr>

                                        <tr>
					 <td align=left valign=top bgcolor="#F2FAFB"><p class="topmenutext">Comments &amp; personal<br>recommendations:</p></td>
                                                <td colspan="3" bgcolor="#F2F8F1">
                                                        <p class="normaltxt">
                                                        <textarea class="BoxCSS1" name="elect_comments" <? echo $read_only; ?> cols="60" rows="2"><? echo $elect_comments; ?></textarea><br>
						</td>

                                        <tr>
                                        	<td align="center" colspan="4"><br><hr></td>
                                        </tr>

                                        <tr>
                                        	<td align="center" colspan="4">
						<table border=0 cellspacing=1 cellpadding=1 cellspacing="#eeeeee">
						<tr>
                                                        <td align=center bgcolor="#F2FAFB"><span class="topmenutext"></span></td>
                                                        <td align=center bgcolor="#F2FAFB"><span class="topmenutext">CODE</span></td>
                                                        <td align=center bgcolor="#F2FAFB"><span class="topmenutext">PROPOSED NAME</span></td>
                                                        <td align=center bgcolor="#F2FAFB"><span class="topmenutext">BEARD</span></td>
                                                        <td align=center bgcolor="#F2FAFB"><span class="topmenutext">PROPOSED BY</span></td>
                                                        <td align=center bgcolor="#F2FAFB"><span class="topmenutext">SECONDED BY</span></td>
                                                        <td align=center bgcolor="#F2FAFB"><span class="topmenutext">VOTES</span></td>
                                                </tr>

					<?
						for ($i=0; $i<12; $i++)
						{
					?>
						 <tr>
                                                        <td bgcolor="#F2F8F1"><?=$i+1?></td>
                                                        <td bgcolor="#F2F8F1"><input name="elect_mcode[<?=$i?>]" maxlength="6" size="6" type="text" onblur="validaterow(<?=$i?>)" class="BoxCSS1" id="elect_mcode" value="<? if ($Error_Msg!='') echo $elect_mcode[$i];?>"></td>
                                                        <td bgcolor="#F2F8F1"><input name="elect_name[<?=$i?>]" maxlength="30" size="15" onblur="validaterow(<?=$i?>)" type="text" class="BoxCSS1" id="elect_name" value="<? if ($Error_Msg!='') echo $elect_name[$i];?>"></td>
                                                        <td bgcolor="#F2F8F1">
                                                           <select name="elect_beard[<?=$i?>]">
                                                                <option value="-1" <? if ($elect_beard[$i]=='-1') echo "selected" ?> >-</option>
                                                                <option value="0" <? if ($elect_beard[$i]=='0') echo "selected" ?> >No</option>
                                                                <option value="1" <? if ($elect_beard[$i]=='1') echo "selected" ?> >Yes</option>
                                                           </select>
                                                        </td>
                                                        <td bgcolor="#F2F8F1"><input name="elect_prop_by[<?=$i?>]" maxlength="30" size="15"  onblur="validaterow(<?=$i?>) "type="text" class="BoxCSS1" id="elect_prop_by" value="<? if ($Error_Msg!='') echo $elect_prop_by[$i];?>"></td>
                                                        <td bgcolor="#F2F8F1"><input name="elect_secd_by[<?=$i?>]" maxlength="30" onblur="validaterow(<?=$i?>)" size="15" type="text" class="BoxCSS1" id="elect_secd_by" value="<? if ($Error_Msg!='') echo $elect_secd_by[$i];?>"></td>
                                                        <td bgcolor="#F2F8F1"><input name="elect_votes[<?=$i?>]" maxlength="6" size="6" type="number" class="BoxCSS1" onblur="sumvotes(); validaterow(<?=$i?>);" id="elect_votes" value="<? if ($Error_Msg!='') echo $elect_votes[$i];?>"></td>
                                                </tr>
					<?  } ?>

						<tr><td colspan=7 align=right>(Must match 'Members Present' value above)  -  Votes Count<input size="6" name="_votes_total" readonly type="text" id="_votes_total">
						</td></tr>
						</table>
						</td>
                                        </tr>
                                        <tr>
						
                                                <td colspan=2 bgcolor="white" align="center">
								<input type="button" name="none" value="Cancel" class="ButtonCSS" 
								onClick="location.href='elections.php'" value='click here'>
							</td>           
                                        	<td colspan=2 align="center">
							<input type="submit" name="Submit" value="Continue" class="ButtonCSS">
						</td>
					</form>
                                        </tr>



					  </table>
								<script language="JavaScript">
									document.userForm.elect_dept.focus();
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
