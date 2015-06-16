<?php
	error_reporting(E_ALL); ini_set('display_errors', '1');
	
	if ($_GET['token'] != $GLOBALS['config']['cronjob_token']) {echo "Permission denied!"; exit;}
	
	
	session_start();
    include("../templates/configuration.php");
	$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
	
	# Check if another import is running in parallel
	$query = "SELECT value FROM `settings` WHERE `key` = 'import_running';";
	$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
	{
		if ($row->value == 'true')
		{
			echo "<h1>Another import is running in parallel. Please run again when other import has been finished.</h1>";
			exit();
		}
		else
		{
			mysqli_query($db, "UPDATE `oraparacollect`.`settings` SET `value` = 'true' WHERE `settings`.`key` = 'import_running';");
		}
	}

	
	# Query Puppet DB
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $GLOBALS['config']['puppet_db_url']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$raw_json_data = curl_exec($ch);
	curl_close($ch);
	

	
	$arr_data = json_decode($raw_json_data, true);
	foreach ($arr_data as $puppet_fact)
	{
		$puppet_fact['name'] = strtolower($puppet_fact['name']);

		if (strpos($puppet_fact['name'],$GLOBALS['config']['puppet_fact_db_prefix']) !== false)
		{
			###############################################
			### Collect basic information
			###############################################
			#echo $puppet_fact['name'].': '.$puppet_fact['value'].'<br>';
			$sep1 = explode("_", $puppet_fact['name']);
			
			$parameter_value			= $puppet_fact['value'];
			$instance_sid				= $sep1[1];
			$instance_host				= $puppet_fact['certname'];
			$parameter_name 			= substr($puppet_fact['name'], strlen ($instance_sid) + 5);
			$current_parameter_value	= "";
			unset($instance_id);
			
			###############################################
			### Check if instance is exsisting
			###############################################
			$query = "SELECT `db_id` FROM `instances` WHERE `hostname` = '".$instance_host."' and sid = '".$instance_sid."'";
			$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
			{$instance_id = $row->db_id;}
			
			if (isset($instance_id))
			{
				$query = "UPDATE `oraparacollect`.`instances` SET `refreshed` = CURRENT_TIMESTAMP WHERE `instances`.`db_id` = ".$instance_id.";";
				$result = mysqli_query($db, $query);
			}
			else
			{
				$query = "INSERT INTO `oraparacollect`.`instances` (`db_id`, `sid`, `hostname`, `refreshed`) VALUES (NULL, '".$instance_sid."', '".$instance_host."', CURRENT_TIMESTAMP);";
				$result = mysqli_query($db, $query);
				$instance_id = mysqli_insert_id ($db);
				echo "Added instance $instance_host <br>";
			}
			
			###############################################
			### Check if value is different then current one in the db
			###############################################
			unset($parameter_id);
			$query = "SELECT `parameter_id` FROM `parameter` WHERE `name` = '".$parameter_name."';";
			$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
			{$parameter_id 				= $row->parameter_id;}
			
			if (isset($parameter_id))
			{
				$query = "SELECT parameter.parameter_id, value_string, time FROM `parameter`, `values` WHERE `values`.`db_id` = ".$instance_id." and  parameter.parameter_id = values.parameter_id and parameter.name = '".$parameter_name."' order by time desc limit 1";
				$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
				{$current_parameter_value	= $row->value_string;}
				
				if ($current_parameter_value != $parameter_value)
				{
					mysqli_query($db, "INSERT INTO `oraparacollect`.`values` (`ID`, `db_id`, `parameter_id`, `value_string`, `time`) VALUES (NULL, '".$instance_id."', '".$parameter_id."', '".$parameter_value."', CURRENT_TIMESTAMP);");
					echo "Added value $parameter_id : $parameter_value<br>";
				}
			}
			else
			{
				echo "Unknown parameter $parameter_name <br>";
			}

            ob_flush();
		}
		
		if (strpos($puppet_fact['name'],$GLOBALS['config']['puppet_fact_server_prefix']) !== false)
		{
			###############################################
			### Collect basic information
			###############################################
			#echo $puppet_fact['name'].': '.$puppet_fact['value'].'<br>';
			$sep1 = explode("_", $puppet_fact['name']);
				
			$parameter_value			= $puppet_fact['value'];
			$server_host				= $puppet_fact['certname'];
			$parameter_name 			=  $sep1[2];
			$current_parameter_value	= "";
			unset($instance_id);
			unset($server_id);
			
			###############################################
			### Check if server is exsisting
			###############################################
			$query = "SELECT `server_id` FROM `server` WHERE `hostname` = '".$server_host."'";
			$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
			{$server_id = $row->server_id;}
				
			if (isset($server_id))
			{
				$query = "UPDATE `oraparacollect`.`server` SET `refreshed` = CURRENT_TIMESTAMP WHERE `server_id` = ".$server_id.";";
				$result = mysqli_query($db, $query);
			}
			else
			{
				$query = "INSERT INTO `oraparacollect`.`server` (`server_id`, `hostname`, `refreshed`) VALUES (NULL, '".$server_host."', CURRENT_TIMESTAMP);";
				$result = mysqli_query($db, $query); 
				$server_id = mysqli_insert_id ($db);
				echo "Added server $server_host <br>";
			}
			
			###############################################
			### Check if value is different then current one in the db
			###############################################
			$query = "SELECT parameter_name, value_string, time FROM `system_parameter` WHERE parameter_name = '".$parameter_name."' and `server_id` = ".$server_id." order by time desc limit 1";
			$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
			{$current_parameter_value	= $row->value_string;}
				
			if ($current_parameter_value != $parameter_value)
			{
				mysqli_query($db, "INSERT INTO `oraparacollect`.`system_parameter` (`ID`, `server_id`, `parameter_name`, `value_string`, `time`) VALUES (NULL, '".$server_id."', '".$parameter_name."', '".$parameter_value."', CURRENT_TIMESTAMP);");
				echo "[SYSTEM] Added value $parameter_name : $parameter_value<br>";
			}
            ob_flush();
		}
	}
	
	# Import has been finished, so we will set import_running to false again
	mysqli_query($db, "UPDATE `oraparacollect`.`settings` SET `value` = 'false' WHERE `settings`.`key` = 'import_running';");
	
	?>