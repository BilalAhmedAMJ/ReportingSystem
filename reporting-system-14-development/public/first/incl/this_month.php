<?php include 'dbcon.php';
$today = getdate();
$ev_month = $today['mon'];
$ev_year = $today['year'];

$event_view = "SELECT * FROM event_cal WHERE event_status='1' and event_month=$ev_month and event_year=$ev_year order by event_day";
$result=@mysql_db_query($dbname,$event_view,$id_link);
$cnt=0;
if($result)
{
	while ($row = mysql_fetch_array($result))
	 {
	 	if ($cnt<1){
			print "<span class=\"rightColHeader\">This Month</span><br>";
		}
		$cnt+=1;
		$event_name=$row["event_name"];
		$event_venue=$row["event_venue"];
		$event_file=$row["event_file"];
		$event_month=$row["event_month"];
		$event_month2=$row["event_month2"];
		$event_date=substr($today['month'],0,3) . ". " . $row["event_day"];

		if ($event_month!=$event_month2 && $event_month2>0)
		{
			 $i = $event_month2;
			 switch ($i) {
				  case 1:
					   $ev_month = "Jan.";
							 break;
				  case 2:
					   $ev_month = "Feb.";
							 break;
				  case 3:
					 $ev_month = "Mar.";
							 break;
				  case 4:
					   $ev_month = "Apr.";
							 break;
				  case 5:
					   $ev_month = "May.";
							 break;
				  case 6:
					 $ev_month = "Jun.";
							 break;
				  case 7:
					   $ev_month = "Jul.";
							 break;
				  case 8:
					   $ev_month = "Aug.";
							 break;
				  case 9:
					 $ev_month = "Sep.";
							 break;
				  case 10:
					   $ev_month = "Oct.";
							 break;
				  case 11:
					   $ev_month = "Nov.";
							 break;
				  case 12:
					 $ev_month = "Dec.";
							 break;
			}
			$event_date .= " - " . $ev_month . " " . $row["event_day2"];
		}
		else
		{
			$event_day2=$row["event_day2"];
			if ($event_day2){
				$event_date.= " - " . $event_day2;
			}
		}
		//Check if additional link is there
		//if ($event_file){
		//	$ev_file=substr($event_file,0,4);
		//	if (strtolower($ev_file)=='http'){
		//		$event_name .= " " . "<a href=\"$event_file\" target=\"_blank\">More Info</a>";
		//	}
		//	else
		//	{
		//		$event_name .= " " . "<a href=\"\\events\\doc\\$event_file\" target=\"_blank\">More Info</a>";
		//	}
		//}

		print "$event_name - $event_date<br><br>";
	}
	mysql_free_result($result);
	print '<font color="#006699"><strong><a href="http://www.islamevents.ca/" target="_blank">Interfaith events & Symposiums</a></strong></font>';
	print '<hr width="85%" size="1">';
} else {
	print '<font color="#006699"><strong><a href="http://www.islamevents.ca/" target="_blank">Interfaith events & Symposiums</a></strong></font>';
}
?>
