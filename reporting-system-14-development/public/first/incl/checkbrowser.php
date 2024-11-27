<?php 
if (!$HTTP_GET_VARS["al"]==1) {
$userB = $HTTP_SERVER_VARS["HTTP_USER_AGENT"];
$pos = strpos($userB, "MSIE");
if (!is_integer($pos)) {
   $pos = strpos($userB, "Mozilla/4");
   if (is_integer($pos)) {
   		header("Location:update.htm");		
   }
} 
}
?>