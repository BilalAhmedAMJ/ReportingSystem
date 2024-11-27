 <?php
 /*
if ($_SERVER['HTTPS']!='on') {
                $url='https://report.ahmadiyya.ca'.$_SERVER['REQUEST_URI'];
                  header('location: '.$url);
                  exit;
        }
*/
/*if ($_SERVER['SERVER_PORT'] != 443) 
{
   header("HTTP/1.1 301 Moved Permanently");
   header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
   exit();
}
*/

extract($_POST,EXTR_OVERWRITE);
date_default_timezone_set("America/Toronto")
?>
