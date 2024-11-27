## Test Suite

Following are various test scenarioes that needs to be tested for variosu areas of Reporting System. Scenarios below are grouped under major areas of Reporting System.

* **Login**
	* Simple login
	* Reset Password
	* Password expiry
	* Login for "inactive user"
	* Login for user with no office
	
* **Reports**
	* **Report List**
		* General Secretary and President/Amir roles for Local Jama\`at, Imara\`at and National Level 	
			* Default selection of department (only for GS, no default dept for President/Local Amir)
			* View of all departments
		* All other scretary at Halqa/Jama\`at level see their own reports only
		* At Local Ama\`arat level and National level, see their reports and reports of scretrary who report to their department
		* For multiple office holders, shows list of reprots for both/all offices.
	* **Report submission**
		* Creation of report:
			* Secretary able to create their report for all assigned dept (if more than one dept are assigned)
			* Preseide/Local Amir and GS able to create report for all departments.
			* Owner of report, GS and President/Amir of reporting branch are able to update report in "draft" state, no other users can update/save report in "draft" state.
		* Submission of report:
			* Report submission for each report type (currently we have GS and other secretary reports)
				* Create report of each type and at various levels and follows are stages of submission.
				* Test functionality of various UI components, at least update one question of each UI type.
				* Test email notifications and system message notifications for various stage transitions, especilally "*feed back*" and "*need help*" sections.
			* Report submission follows these states and user edit rules:

			<table cellspacing="0" cols="5" border="1" >
<tbody><tr><td height="22" align="LEFT" ><br></td><td colspan="4" align="CENTER" >Status</td></tr><tr><td height="49" align="LEFT" >Users*</td><td align="CENTER" >Current: draft<br>Next: completed</td><td align="CENTER" >Current: completed<br>Next: verified</td><td align="CENTER" >Current: verified<br>Next: received</td><td align="CENTER" >Current: received<br>Next: N/A</td></tr><tr><td height="22" align="LEFT" >Secretary and owner</td><td align="CENTER" >X</td><td align="CENTER" ><br></td><td align="CENTER" ><br></td><td align="CENTER" ><br></td></tr><tr><td height="22" align="LEFT" >GS of same Branch</td><td align="CENTER" >X</td><td align="LEFT" ><br></td><td align="CENTER" ><br></td><td align="CENTER" ><br></td></tr><tr><td height="22" align="LEFT" >President/Amir of same Branch</td><td align="CENTER" >X</td><td align="CENTER" >X</td><td align="CENTER" ><br></td><td align="CENTER" ><br></td></tr><tr><td height="22" align="LEFT" >Next level Secretary</td><td align="LEFT" ><br></td><td align="LEFT" ><br></td><td align="CENTER" >X</td><td align="CENTER" ><br></td></tr></tbody></table>
*users who can change report in this state or move report to next state
			



	
