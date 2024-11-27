<?php

function make_seed() {
   list($usec, $sec) = explode(' ', microtime());
   return (float) $sec + ((float) $usec * 100000);
}

function generateKey($sessionid) {
	
	mt_srand(make_seed());
	$randval = mt_rand();

	$mykey = $sessionid.time().$randval;
	return $mykey;

}

function do_replace( $find, $replace, $string )
{
   $parts = explode( strtolower($find), strtolower($string) );
   $pos = 0;
   foreach( $parts as $key=>$part ){
       $parts[ $key ] = substr($string, $pos, strlen($part));
       $pos += strlen($part) + strlen($find);
       }
   return( join( $replace, $parts ) );
}

function SQL_check( $strText ) {
	$strText = do_replace( "'", "", $strText);
	$strText = do_replace( "=", "", $strText);
	$strText = do_replace( "*", "", $strText);
	$strText = do_replace( "%", "", $strText);
	$strText = do_replace( "?", "", $strText);
	$strText = do_replace( "\\", "", $strText);
	$strText = do_replace( "/", "", $strText);
	$strText = do_replace( "--", "", $strText);
	$strText = do_replace( "#", "", $strText);
	$strText = addslashes($strText);
	return ($strText);	
}

function check_referer ($referer, $comingfrom, $redirect) {
	if (!preg_match ("/$referer/i", $comingfrom)) {
   		header("Location:$redirect");
	} 
}

?>