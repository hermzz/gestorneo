<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Rewrite all outgoing text into UTF8 compatible streams
* Author: Matt Carter <m@ttcarter.com>
* Info: http://hash-bang.net/2009/02/utf8-with-codeigniter
*/
function ob_utf8($string) {
	return utf8_decode($string);
}
ob_start('ob_utf8');
 
foreach ($_POST as $key => $val) // Re-write all incoming text into UTF8 streams
	$_POST[$key] = utf8_encode($val);
?>
