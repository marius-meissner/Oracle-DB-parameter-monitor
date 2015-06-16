<?php
	error_reporting(E_ALL); ini_set('display_errors', '1');
	
	if ($_GET['token'] != $GLOBALS['config']['cronjob_token']) {echo "Permission denied!"; exit;}
	
	session_start();
	# Database Settings
	$_SESSION['db_host']="localhost";
	$_SESSION['db_user']="oraparacollect";
	$_SESSION['db_password']="Wv4Av7CzZZ9nCup";
	$_SESSION['db_database']="oraparacollect";
	
	#$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']); test commnect
	
	include_once ("../classes/class_server.php");
	include_once ("../classes/class_server_actions.php");
	include_once ("../classes/class_instance.php");
	include_once ("../classes/class_instance_actions.php");
	include_once ("../classes/class_parameter.php");
	include_once ("../classes/class_parameter_actions.php");
	include_once ("../classes/class_notification.php");
	
	# Generating instance objects
	$arr_server_objs = server_actions::get_all_server();
	
	# FRA Monitor Notification
	foreach ($arr_server_objs as $server) {
		if ($server->get_fra_assigned_percentage() >= 100)
		{
			if (notification::check_fra_notification_flag($server->server_id) == false)
			{
				notification::send_fra_notification($server,  $GLOBALS['config']['fra_notification_mail']);
				notification::add_fra_notification_flag($server->server_id);
			}
		}
		else 
		{
			notification::drop_fra_notification_flag($server->server_id);
		}
	}
	
	# Switchover Notificaiton 
	$so_data_arr = parameter_actions::get_all_switchover_as_array();
	foreach ($so_data_arr as $time => $switchover) 	{
		if (notification::check_switchover_flag($time, $switchover['rz1_db_id'], $switchover['rz2_db_id']) == false)
		{
			notification::send_switchover_notification($switchover,  $GLOBALS['config']['switchover_notification_mail']);
			notification::add_switchover_flag($time, $switchover['rz1_db_id'], $switchover['rz2_db_id']);
		}
	}
	
	
?>