<?php

function SendEmail ($to, $from, $fromname, $subject, $message) {

	/* To send HTML mail, you can set the Content-type header. */
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

	/* additional headers */
	$headers .= "From: ".$fromname." <".$from.">\r\n";

	/* and now mail it */
	mail($to, $subject, $message, $headers);

}

?>