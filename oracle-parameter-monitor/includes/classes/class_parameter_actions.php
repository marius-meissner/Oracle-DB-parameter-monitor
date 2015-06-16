<?php
	class parameter_actions {
		###### Get general parameter info ##################
		# Get generatl parameter information as array
		####################################################
		function get_all_parameter_array ()
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			$query = "SELECT * FROM `parameter`;";
			$result = mysqli_query($db, $query);
			while($row = mysqli_fetch_object($result))
			{
				$data[$row->parameter_id]['name'] 			= $row->name;
				$data[$row->parameter_id]['type'] 			= $row->type;
				$data[$row->parameter_id]['description']	= $row->description;
			}
			
			return $data;
		}
		
		###### Generation json object for instant search ###
		# Generates a json object for instant dialog search
		####################################################
		static function get_all_parameter_search_json () {
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			$query = "SELECT * FROM `parameter`;";
			$result = mysqli_query($db, $query);
			while($row = mysqli_fetch_object($result))
			{
				$data[$row->parameter_id]['info_string'] 	= 	$row->name." ".
																$row->description;
			}
			return json_encode($data);
		}
		
		###### Last parameter changes  #####################
		# Gets the last 25 parameter changes worldwide and returns them as array
		####################################################
		static function get_last_parameter_chagnes_as_array ()
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			$query = "SELECT * FROM (select *,DATE_FORMAT(time, '%d.%m.%y (%k:%i)') as time_formated from (SELECT * FROM `values` order by time desc) as a group by `db_id`, `parameter_id` order by ID desc) values_b, parameter, instances where values_b.parameter_id = parameter.parameter_id and values_b.db_id = instances.db_id and parameter.skip_last_parameter_overview != 'true' order by time desc limit 25";
			$result = mysqli_query($db, $query);
			while($row = mysqli_fetch_object($result))
			{
				$data[$row->db_id.'_'.$row->parameter_id]['parameter_last_value'] 		= "";
				$data[$row->db_id.'_'.$row->parameter_id]['parameter_id'] 			 	= $row->parameter_id;
				$data[$row->db_id.'_'.$row->parameter_id]['parameter_name'] 			= $row->name;
				$data[$row->db_id.'_'.$row->parameter_id]['parameter_value'] 			= $row->value_string;
				$data[$row->db_id.'_'.$row->parameter_id]['time'] 						= $row->time_formated;
				$data[$row->db_id.'_'.$row->parameter_id]['sid'] 						= $row->sid;
				$data[$row->db_id.'_'.$row->parameter_id]['hostname'] 					= str_replace ('.internal.draexlmaier.com', '', $row->hostname);
				$data[$row->db_id.'_'.$row->parameter_id]['parameter_default_value'] 	= $row->default_value;
				$data[$row->db_id.'_'.$row->parameter_id]['parameter_description'] 		= $row->description;
				$data[$row->db_id.'_'.$row->parameter_id]['dba_notice'] 				= $row->dba_notice;
				$data[$row->db_id.'_'.$row->parameter_id]['last_value_id'] 				= $row->ID;
				
				# Last value
				$query = "SELECT ID, value_string, dba_notice, time FROM `values` WHERE `parameter_id` = ".$row->parameter_id." and `db_id` = ".$row->db_id." order by ID desc limit 1 offset 1;";
				$result2 = mysqli_query($db, $query);
				while($row2 = mysqli_fetch_object($result2))
				{
					$data[$row->db_id.'_'.$row->parameter_id]['parameter_last_value'] = $row2->value_string;
				}
			}
			return $data;
		}
		
		###### Get all switchover #########################
		# Gets all switchover of the last 14 days and returns them as array
		####################################################
		static function get_all_switchover_as_array ()
		{
			# Generating instance objects
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			$arr_instance_objs = instance_actions::get_all_instances();
			
			foreach ($arr_instance_objs as $instance)
			{
				if (strpos($instance->service_name,'RZ1') !== false)
				{
					# Check if there was a failover/switchover during the last 14 days
					$query = "select count(*) as failover_count from (SELECT distinct `value_string` FROM `values` WHERE `parameter_id` = 9002 and `db_id` = ".$instance->db_id." and time > DATE_FORMAT(now(), '%Y-%m-%d') - INTERVAL 14 DAY limit 2) as a";
					$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
					{$failover_count = $row->failover_count;}
										
					if ($failover_count > 1)
					{
						unset ($rz2_db_id); unset ($rz1_status); unset($so_time);
						# Check current status of RZ1 db
						$query = "SELECT ID, dba_notice, lower(`value_string`) as value_string, DATE_FORMAT(time, '%d.%m.%y (%k:%i)') as time_formated FROM `values` WHERE `parameter_id` = 9002 and db_id = ".$instance->db_id." order by time desc limit 1;";
						$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
						{$rz1_status = $row->value_string; $so_time = $row->time_formated; $so_data_arr[$so_time]['value_id'] = $row->ID; $so_data_arr[$so_time]['dba_notice'] = $row->dba_notice;}
		
						if (strtolower($rz1_status) == "open") {$so_direction = "to_rz1";}
						if (strtolower($rz1_status) == "mounted") {$so_direction = "to_rz2";}
						
						# Get RZ2 DB
						$query = "SELECT db_id FROM `values` WHERE `parameter_id` = 1698 and `value_string` like '%".$instance->sid."_rz2%' limit 1";
						$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
						{$rz2_db_id = $row->db_id;}
	
						if (isset($rz2_db_id) && isset($so_direction))
						{
							$rz2_server_obj = instance_actions::get_instance_by_id ($rz2_db_id);
							$so_data_arr[$so_time]['rz1_hostname'] = $instance->get_hostname();
							$so_data_arr[$so_time]['rz1_sid'] = $instance->sid.'_RZ1';
							$so_data_arr[$so_time]['rz1_db_id'] = $instance->db_id;
							$so_data_arr[$so_time]['rz2_hostname'] = $rz2_server_obj->get_hostname();
							$so_data_arr[$so_time]['rz2_sid'] = $rz2_server_obj->sid.'_RZ2';
							$so_data_arr[$so_time]['rz2_db_id'] = $rz2_server_obj->db_id;
							$so_data_arr[$so_time]['direction'] = $so_direction;
						}
					}
				}
			}
			return $so_data_arr;
		}
		
		###### Generating parameter object  ################
		# Generates a parameter objects ans assignes all values
		####################################################
		static function get_parameter_by_id ($parameter_id, $instance_id)
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			
			$obj_parameter = new parameter ();
			
			# Static parameter information
			$query = "SELECT * FROM `parameter` WHERE `parameter_id` = ".$parameter_id.";";
			$result = mysqli_query($db, $query);
			while($row = mysqli_fetch_object($result))
			{
				$obj_parameter->parameter_id 	= $row->parameter_id;
				$obj_parameter->name 			= $row->name;
				$obj_parameter->type 			= $row->type;
				$obj_parameter->description 	= $row->description;
				$obj_parameter->default_value 	= $row->default_value;
			}
			
			# Current Value, refreshed date, last change
			$query = "SELECT values.ID, values.value_string, DATE_FORMAT(instances.refreshed, '%d.%m.%y (%k:%i)') as refreshed, DATE_FORMAT(values.time, '%d.%m.%y (%k:%i)') AS 'last_change_time' FROM `values`,`instances` WHERE values.parameter_id = ".$parameter_id." and values.db_id = ".$instance_id." and values.db_id = instances.db_id order by values.time desc limit 1";
			$result = mysqli_query($db, $query);
			while($row = mysqli_fetch_object($result))
			{
				$obj_parameter->current_value 	= $row->value_string;
				$obj_parameter->refreshed 		= $row->refreshed;
				$obj_parameter->last_change 	= $row->last_change_time;
			}
			
			# Last Value 
			$query = "SELECT ID, value_string, DATE_FORMAT(time, '%d.%m.%y (%k:%i)') AS 'time' FROM `values` WHERE `parameter_id` = ".$parameter_id." and `db_id` = ".$instance_id." order by time desc limit 1 offset 1;";
			$result = mysqli_query($db, $query);
			while($row = mysqli_fetch_object($result))
			{
				$obj_parameter->last_value = $row->value_string;
			}
						
			
			return $obj_parameter;			
		}

		static function get_all_parameter_changes_as_array ($parameter_id, $instance_id)
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			$result = mysqli_query($db, "SELECT ID, `value_string`, DATE_FORMAT(time, '%d.%m.%y (%k:%i)') as time FROM `values` WHERE `parameter_id` = ".$parameter_id." and `db_id` = ".$instance_id." limit 5");
			while($row = mysqli_fetch_object($result))
			{
				$data[$row->ID]['time'] 	= $row->time;
				$data[$row->ID]['value']	= $row->value_string;
			}
			return $data;
		}
		
		static function save_parameter_notice ($value_id, $message, $publish_range = 'none')
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			
			switch ($publish_range) {
				case "none":
					echo "Using none procedure ... <br>";
					$result = mysqli_query($db, "UPDATE `oraparacollect`.`values` SET `dba_notice` = '".$message."' WHERE `values`.`ID` = ".$value_id.";");
					break;
				case "dataguard":
					echo "Using Dataguard procedure ... \n";
					$query = "SELECT `parameter_id`, `time`, db_id FROM `values` WHERE `ID` = ".$value_id.";";
					$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
					{
						$parameter_id 	= $row->parameter_id;
						$source_time	= $row->time;
						$source_db_id	= $row->db_id;
					}
					
					# Get IDs of all instances belonging to the DG configuration
					$query = "SELECT distinct db_id FROM `values` WHERE value_string = (select sid from instances where db_id = ".$source_db_id.");";
					$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
					{
						$destination_db_ids[] = $row->db_id;
						echo "Found DataGuard DB ID: $row->db_id \n";
					}
					
					# For each dataguard entry we will change the notice
					foreach ($destination_db_ids as $db_id)
					{
						$query = "SELECT ID FROM `values` WHERE parameter_id = ".$parameter_id." and db_id = ".$db_id." ORDER BY ABS(TIMESTAMPDIFF(second, `time`,'".$source_time."')) ASC limit 1";
						$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
						{
							mysqli_query($db, "UPDATE `oraparacollect`.`values` SET `dba_notice` = '".$message."' WHERE `values`.`ID` = ".$row->ID.";");
							echo "Updated Entry: $row->ID \n";
						}
					}
					break;
				case "server":
					echo "Using Server procedure ... \n";
					$query = "SELECT `parameter_id`, `time`, db_id FROM `values` WHERE `ID` = ".$value_id.";";
					$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
					{
						$parameter_id 	= $row->parameter_id;
						$source_time	= $row->time;
						$source_db_id	= $row->db_id;
					}
						
					# Get IDs of all instances belonging to the Server
					$query = "SELECT distinct db_id FROM `instances` WHERE `hostname` = (select hostname from instances where db_id = ".$source_db_id.");";
					$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
					{
					$destination_db_ids[] = $row->db_id;
					echo "Found Server DB ID: $row->db_id \n";
					}
						
					# For each Server entry we will change the notice
					foreach ($destination_db_ids as $db_id)
					{
					$query = "SELECT ID FROM `values` WHERE parameter_id = ".$parameter_id." and db_id = ".$db_id." ORDER BY ABS(TIMESTAMPDIFF(second, `time`,'".$source_time."')) ASC limit 1";
						$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
											{
											mysqli_query($db, "UPDATE `oraparacollect`.`values` SET `dba_notice` = '".$message."' WHERE `values`.`ID` = ".$row->ID.";");
							echo "Updated Entry: $row->ID \n";
						}
											}
					break;
				case "global":
					echo "Using Global procedure ... \n";
					$query = "SELECT `parameter_id`, `time`, db_id FROM `values` WHERE `ID` = ".$value_id.";";
					$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
					{
						$parameter_id 	= $row->parameter_id;
						$source_time	= $row->time;
						$source_db_id	= $row->db_id;
					}
					
					# Get IDs of all instances
					$query = "SELECT distinct db_id FROM `instances`";
					$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
					{
					$destination_db_ids[] = $row->db_id;
					echo "Found Global DB ID: $row->db_id \n";
					}
					
					# For each global entry we will change the notice
					foreach ($destination_db_ids as $db_id)
					{
					$query = "SELECT ID FROM `values` WHERE parameter_id = ".$parameter_id." and db_id = ".$db_id." ORDER BY ABS(TIMESTAMPDIFF(second, `time`,'".$source_time."')) ASC limit 1";
						$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
											{
											mysqli_query($db, "UPDATE `oraparacollect`.`values` SET `dba_notice` = '".$message."' WHERE `values`.`ID` = ".$row->ID.";");
							echo "Updated Entry: $row->ID \n";
						}
					}
														break;
					break;
			}
			
		}
		
		
		###### Get all switchover #########################
		# Generating data and label for charts
		# ------------------------------------------------
		# $max_time : how long the chart should go in the past (in houres)
		# $step		: step for chart data in houres
		####################################################
		static function get_chart_data ($parameter_id, $instance_id, $max_time, $step) 
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			$labels = ""; $data = "";

			# If value is always the same the chart will not be shown, this is changed here
			$return_label[] = '';
			
			# Get inital value (first older then max time)
			$query = "SELECT * FROM `values` where time < DATE_FORMAT(now(), '%Y-%m-%d') - INTERVAL ".$max_time." HOUR and `parameter_id` = ".$parameter_id." and `db_id` = ".$instance_id." ORDER BY time asc limit 1";
			$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
			{$result_value = $row->value_string;}
			
			if ($max_time < 25) # If query only last 24 hours then format in time format
			{$query = "SELECT DATE_FORMAT(NOW() - INTERVAL ".$max_time." HOUR, '%H:%i') as time from dual";}	
			else
			{$query = "SELECT DATE_FORMAT(NOW() - INTERVAL ".$max_time." HOUR, '%d.%m') as time from dual";}			
			$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
			{$result_label = $row->time;}
			# If there is no value older then max_time 0 will be assigned
			if (isset($result_value) == false) {$result_value = 0;} 
			
			
			# Get values
			for ($x = $max_time; $x >= 0; $x = $x - $step) {
				if ($max_time < 25) # If query only last 24 hours then format in time format
				{$query = "SELECT `ID`, `value_string`, DATE_FORMAT(time, '%H.%i') AS 'time', time as time_raw FROM `values` where time <=  DATE_FORMAT(now(), '%Y-%m-%d') - INTERVAL ".($x - $step)." HOUR and time > DATE_FORMAT(now(), '%Y-%m-%d') - INTERVAL ".($x)." HOUR and `parameter_id` = ".$parameter_id." and `db_id` = ".$instance_id." ORDER BY time_raw asc limit 1";}
				else 
				{$query = "SELECT `ID`, `value_string`, DATE_FORMAT(time, '%d.%m') AS 'time', time as time_raw FROM `values` where time <=  DATE_FORMAT(now(), '%Y-%m-%d') - INTERVAL ".($x - $step)." HOUR and time > DATE_FORMAT(now(), '%Y-%m-%d') - INTERVAL ".($x)." HOUR and `parameter_id` = ".$parameter_id." and `db_id` = ".$instance_id." ORDER BY time_raw asc limit 1";}

				#echo $query.'\n';
				$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
				{
					$result_value = $row->value_string;
					$result_label = $row->time;
				}
				
				# If values returned, then use them, if not use the last values
				if (isset($result_label))
				{
					$return_label[] = $result_label;
					$data[$x]		= $result_value;
				}
				else
				{
					if ($max_time < 25) # If query only last 24 hours then format in time format
					{$query = "SELECT DATE_FORMAT(NOW() - INTERVAL ".$x." HOUR, '%H.%i') as time from dual";}
					else
					{$query = "SELECT DATE_FORMAT(NOW() - INTERVAL ".$x." HOUR, '%d.%m') as time from dual";}
					#echo $query.'\n';
					$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
					{
						$last_x = $x + $step;
						$return_label[] 	= $row->time;
						$data[$x]		= $data[$last_x];
					}
				}
				
				unset ($result_label); unset ($result_value);
			}
			
		
			# If type is 6 (byte) then convert human readable
			$query = "SELECT type FROM `parameter` WHERE `parameter_id` = ".$parameter_id;
			$result = mysqli_query($db, $query); while($row = mysqli_fetch_object($result))
			{
				if ($row->type == 6)
				{
					# If value is always the same the chart will not be shown, this is changed here
					$return_data[] = '0';
					
					foreach ($data as $value) {$return_data[] = parameter_actions::human_filesize($value, 1);}
				}
				else
				{
					# Cleant result from indexes, because they are not needed
					
						if ($row->type == 1)
						{
							# If value is always the same the chart will not be shown, this is changed here
							$return_data[] 	= '-5';
							
							foreach ($data as $value) {
								$value = strtoupper ($value);
								switch ($value) {
									case 'TRUE':
										$return_data[] = 1;
										break;
									case 'FALSE':
										$return_data[] = 0;
										break;
									case 0:
										$return_data[] = -5;
										break;
									default:
										$return_data[] = -1;
								}
							}
						}
						else
						{
							# If value is always the same the chart will not be shown, this is changed here
							$return_data[] = '0';
							foreach ($data as $value) {$return_data[] = $value;}
						};
				}
			}
			
			$arr_return['data'] 	=  $return_data;
			$arr_return['label'] 	=  $return_label;
			return $arr_return;
		}
		
		# Thanks to Jeffrey Sambells
		function human_filesize($bytes, $dec = 2)
		{
			$size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
			$factor = floor((strlen($bytes) - 1) / 3);
		
			return sprintf("%.{$dec}f", $bytes / pow(1024, $factor));
		}
		
		function human_filesize_formatted($bytes, $dec = 2)
		{
			$size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
			$factor = floor((strlen($bytes) - 1) / 3);
		
			return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
		}
		
		
	}
?>