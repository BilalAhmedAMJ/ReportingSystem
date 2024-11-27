<? include ("incl/dbcon.php"); ?>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
function BranchHomePage (url){
	if (url!="") {
		window.location.href = '/'+url;
	}
}
//-->
</script>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
  <form name="form2" method="post" action="search_engine.php?skip=0">
    <tr>
      <td class="rightCol"><script language="JavaScript">
   document.write(doClock("W0",",%20","D0","%20","M0","%20","Y0"));
</script> </td>
    </tr>
    <tr>
      <td class="pageheader"><span class="rightColHeader">Branch home page: </span>
      <table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
          <td><select name="branches" onchange="BranchHomePage(this.value)" style="font:12px">
          <option value="">Please select your branch</option>
		  <?
	  	  $query1 = "SELECT branch_name,lcase(replace(branch_name,' ','')) url FROM ami_branches WHERE status = '1' and branch_code != 'CA' order by branch_name";
		  $result1 = @mysql_db_query($dbname,$query1);
		  while ($row1 = mysql_fetch_array($result1))
		  {
		  	print "<option value=\"" . $row1['url'] . "\">" . $row1['branch_name'] . "</option>";
		  }?>
		  </select>
		</td>
        </tr>
        </table>
        <hr width="85%" size="1"></td>
    </tr>
    <tr>
      <td class="pageheader"><span class="rightColHeader">Search </span>
      <table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td><input class="BoxSearch" name="search_word" type="text"></td>
            <td><a href="#" onclick="javascript:document.form2.submit();">
            <img src="/images/miscbuttons/go.gif" width="21" height="17" border="0"></a>
            </td>
          </tr>
        </table></td>
    </tr>
  </form>
  <tr>
    <td class="rightCol"><span class="rightColHeader">News</span><br>
        <table border="0" cellspacing="0" cellpadding="0" width="160">
          <tr align="center">
            <td bgcolor="#F3F3F3" class="rightCol" align="left">
              <!-- News Go Here ******************************************************-->
              <script language="JavaScript1.2">

/*
Fading Scroller- By DynamicDrive.com
For full source code, and usage terms, visit http://www.dynamicdrive.com
This notice MUST stay intact for use
*/

var delay=18000 //set delay between message change (in miliseconds)
var fcontent=new Array()
var messages=new Array()
begintag=''
<?php
//include 'incl/security.php';
//include '/incl/db.php';
include 'db.php';
$sql = "SELECT * FROM tblnews where status='1' order by nid desc";
$rstnews = mysql_query ($sql, $linkcon);
$i = 0;
while ($row = mysql_fetch_array($rstnews, MYSQL_BOTH)) {
	$news = "<b>".ereg_replace("[\r\n\t]", " ", $row[1])."</b>";
	$news .= ereg_replace("[\r\n\t]", " ", $row[2]);

	if (strlen($row[3])>4) {
		$news .= "<br><a href='".$row[3]."'><font color='#5E7BC1'>More info</font></a>";
	}
   echo "fcontent[$i] = \"$news\"\n" ;
   $i = $i + 1;
}
?>
closetag=''

var fwidth='150px' //set scroller width
var fheight='100px' //set scroller height

var fadescheme=0 //set 0 to fade text color from (white to black), 1 for (black to white)
var fadelinks=1  //should links inside scroller content also fade like text? 0 for no, 1 for yes.

///No need to edit below this line/////////////////

var hex=(fadescheme==0)? 255 : 0
var startcolor=(fadescheme==0)? "rgb(255,255,255)" : "rgb(0,0,0)"
var endcolor=(fadescheme==0)? "rgb(0,0,0)" : "rgb(255,255,255)"

var ie4=document.all&&!document.getElementById
var ns4=document.layers
var DOM2=document.getElementById
var faderdelay=0
var index=0

if (DOM2)
faderdelay=2000

//function to change content
function changecontent(){
if (index>=fcontent.length)
index=0
if (DOM2){
document.getElementById("fscroller").style.color=startcolor
document.getElementById("fscroller").innerHTML=begintag+fcontent[index]+closetag
linksobj=document.getElementById("fscroller").getElementsByTagName("A")
if (fadelinks)
linkcolorchange(linksobj)
colorfade()
}
else if (ie4)
document.all.fscroller.innerHTML=begintag+fcontent[index]+closetag
else if (ns4){
document.fscrollerns.document.fscrollerns_sub.document.write(begintag+fcontent[index]+closetag)
document.fscrollerns.document.fscrollerns_sub.document.close()
}

index++
setTimeout("changecontent()",delay+faderdelay)
}

// colorfade() partially by Marcio Galli for Netscape Communications.  ////////////
// Modified by Dynamicdrive.com

frame=20;

function linkcolorchange(obj){
if (obj.length>0){
for (i=0;i<obj.length;i++)
obj[i].style.color="rgb("+hex+","+hex+","+hex+")"
}
}

function colorfade() {
// 20 frames fading process
if(frame>0) {
hex=(fadescheme==0)? hex-12 : hex+12 // increase or decrease color value depd on fadescheme
document.getElementById("fscroller").style.color="rgb("+hex+","+hex+","+hex+")"; // Set color value.
if (fadelinks)
linkcolorchange(linksobj)
frame--;
setTimeout("colorfade()",20);
}

else{
document.getElementById("fscroller").style.color=endcolor;
frame=20;
hex=(fadescheme==0)? 255 : 0
}
}

if (ie4||DOM2)
document.write('<div id="fscroller" style="border:0px solid black;width:'+fwidth+';height:'+fheight+';padding:2px"></div>')

window.onload=changecontent
    </script>
              <ilayer id="fscrollerns" width=&{fwidth}; height=&{fheight};>
              <layer id="fscrollerns_sub" width=&{fwidth}; height=&{fheight}; left=0 top=0></layer>
              </ilayer>
              <!-- Finished with News ********************************************************** -->
            </td>
          </tr>
        </table>
        <br>&nbsp;<font color="#006699"><strong><a href="../jalsa/topstories.php">more news . . .</a></strong></font><br>
        <hr width="85%" size="1">
    </td>
  </tr>
  <tr>
    <td valign="top" class="rightCol">
    	<? include 'press_release_short.php'; ?>
    </td>
  </tr>
  <tr>
    <td valign="top" class="rightCol">
	  <? include 'this_month.php'; ?>
    </td>
  </tr>
</table>
