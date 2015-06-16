<?php
	error_reporting(E_ALL); ini_set('display_errors', '1');

	session_start();
	if ($_SESSION['login'] != true)
	{
		header("Location: login.php");
		exit();
	}
	
	include_once ("../classes/class_parameter.php");
	include_once ("../classes/class_parameter_actions.php");
	
	$value_id 		= filter_var($_POST['value_id'], FILTER_VALIDATE_INT);
	$dba_notice 	= filter_var($_POST['dba_notice'], FILTER_SANITIZE_STRING);
	$publish_range 	= filter_var($_POST['publish_range'], FILTER_SANITIZE_STRING);
	parameter_actions::save_parameter_notice ($value_id, $dba_notice, $publish_range);
?>