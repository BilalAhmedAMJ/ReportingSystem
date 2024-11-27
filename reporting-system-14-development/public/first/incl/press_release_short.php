<?php include 'dbcon.php';

$s = "SELECT * FROM press_release where status='1' order by rel_date desc limit 4";
$result=@mysql_db_query($dbname,$s,$id_link);
$cnt=0;
if($result)
{
	//print "<table>";
	while ($row = mysql_fetch_array($result))
	 {
		if ($cnt<1) {
			print "<span class=\"rightColHeader\">Press Releases</span><br>";

		}
		$month=substr($row["rel_date"],5,2);
		$day=substr($row["rel_date"],8,2);
		$year=substr($row["rel_date"],0,4);
		$rel_date = date ("l F d, Y", mktime (0,0,0,$month,$day,$year));
		print "<font color=\"#006699\"><strong>$rel_date</strong></font><br>" ;
		print $row["heading"] . " <a href=\"http://www.ahmadiyya.ca/press_release.php?id=" . $row["id"] . "\" target=\"_self\">more</a><br><br>";

		if (($cnt<3) && ($row["rel_date"]=='2008-07-02')) {

			print "<font color=\"#006699\"><strong>Saturday June 7, 2008</strong></font><br>";
			print "23 AHMADIYYA STUDENTS EXPELLED FROM PUNJAB MEDICAL COLLEGE";
			print " <a href=http://www.alislam.org/press-release/Punjab%20Medical%20College.pdf target=_blank>more</a><br><br>";

			print "<font color=\"#006699\"><strong>Monday January 21, 2008</strong></font><br>";
			print "THE AHMADIYYA MUSLIM JAMA'AT RESPONDS TO FALSE CLAIMS MADE IN 'THE JAKARTA POST'";
			print " <a href=http://www.ahmadiyya.ca/press/Jakarta_press_release20080121.pdf target=_blank>more</a><br><br>";

			print "<font color=\"#006699\"><strong>Monday November 26, 2007</strong></font><br>";
			print "Worldwide Head of the Ahmadiyya Muslim Association condemns all forms of Terrorism";
			print " <a href=http://www.ahmadiyya.ca/press/Press_release_london20071126.pdf target=_blank>more</a><br><br>";
			
		}
		$cnt+=1;
	}
	mysql_free_result($result);
	print "<a href=\"../press/archive_pressrelease.php\"><font color=\"#006699\"><strong>Archived Press Releases</strong></font></a><hr width=\"85%\" size=\"1\">";
}
?>

