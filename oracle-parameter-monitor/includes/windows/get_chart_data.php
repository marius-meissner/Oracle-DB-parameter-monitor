<?php
	error_reporting(E_ALL); ini_set('display_errors', '1');
	session_start();
	if ($_SESSION['login'] != true)
	{
		header("Location: login.php");
		exit();
	}
	
	include_once ("../classes/class_parameter_actions.php");
	include_once ("../classes/class_parameter.php");

	echo json_encode(parameter_actions::get_chart_data ($_POST["parameter_id"], $_POST["instance_id"], $_POST["max_time"], $_POST["step"])); 
?>