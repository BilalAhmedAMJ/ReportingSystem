                 <table width="80%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td colspan="2" valign="top">
                      	<table width="100%" border="0" cellspacing="0" cellpadding="3">
                          <tr>
                            <td align=center><p><span class="pageheader">AMJ Reporting System<br><br>
                                </span>
                                <?  if ($newuser=="Y"){ ?>
                                		Please fill the form below and press submit button to send a new user id request.
                                <?  } else if ($resetpwd=="Y"){ ?>
                                		Please fill the form below and press submit button to reset your password. Your temporary new password will be sent to your e-mail address.
                                <? } else {
                                	if (!$session_id) {?>
                                		If you are a new user and do not have user id please <a href="newuser.php"> click here</a>
                                		to submit a request for one.</p>
                                		If you are an existing user and forgot your password please <a href="resetpwd.php"> click here</a>
                                		to reset your password.</p>
                                	<? } else {?>
                                	<!--	Please select the report below to be submitted.<br>-->
                                		If you need printable report forms please <a href="download/MonthlyReportForms2004printable.pdf" target="_blank">click here</a> to download.</p>
                                	<? }
                                } ?>
                                <!--<br><br><br>-->
                            </td>
                          </tr>
                         </table>
                       </td>
                     </tr>
                 </table>
