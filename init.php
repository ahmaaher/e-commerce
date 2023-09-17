<?php
	
	// Error reporting
	ini_set('display_errors', 'on'); // making set to option (display_errors) in "PHP ini" to "on"
	error_reporting(E_ALL); // error kind or error reprting E_ALL means that we need all kinds of errors

	include "admin/conn.php";

	$funcs 		= "includes/func/";
	$temp 		= "includes/temp/";
	$cssLips 	= "includes/libs/css/";
	$jsLips 	= "includes/libs/js/";
	$lang 		= "includes/lang/";
	$cssAdm 	= "layout/css/";
	$jsAdm 		= "layout/js/";

	include $funcs . "funcs.php";
	include $lang . "en.php";
	include $temp . "header.php";
?>