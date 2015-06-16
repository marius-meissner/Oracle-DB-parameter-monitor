<?php
	session_start();
	if ($_SESSION['login'] != true)
	{
		header("Location: login.php");
		exit();
	}

    include("configuration.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Oracle parameter monitor</title>
	<link rel="shortcut icon" type="image/x-icon" href="images/app_logo_white.png">
	<link rel="stylesheet" href="css/style.css" type="text/css">
	<link rel="stylesheet" href="css/table.css" type="text/css">
	<link rel="stylesheet" href="css/jquery-ui.css" type="text/css">
	<link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css" type="text/css">
	<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.js"></script>
	<script type="text/javascript" src="js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script type="text/javascript" src="js/jquery.transit.min.js"></script>
	<script type="text/javascript" src="js/Chart.min.js"></script>
	<script type="text/javascript" src="js/draw_chart.js"></script>
	<script type="text/javascript" src="js/prod_db.js"></script>
	<script type="text/javascript" src="js/ora_parameter.js"></script>
	<script type="text/javascript" src="js/fra_monitor.js"></script>
</head>
