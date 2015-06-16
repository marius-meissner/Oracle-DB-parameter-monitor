<?php
	class notification 
	{
		static function check_fra_notification_flag ($server_id)
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			$query = "SELECT server_id FROM `notification_fra_assigement` WHERE `server_id` = ".$server_id.";";
			$result = mysqli_query($db, $query);
			while($row = mysqli_fetch_object($result))
			{
				return true;
			}
			return false;
		}
		
		static function drop_fra_notification_flag ($server_id)
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			$result = mysqli_query($db, "DELETE FROM `notification_fra_assigement` WHERE `server_id` = ".$server_id.";");
		}
		
		static function add_fra_notification_flag ($server_id)
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			$result = mysqli_query($db, "INSERT INTO `notification_fra_assigement`(`notification_id`, `server_id`, `time`) VALUES (NULL, ".$server_id.", CURRENT_TIMESTAMP)");
		}
		
		static function send_fra_notification ($server_obj, $email_address)
		{
			############################################
			# Server Overview
			############################################
			if ($server_obj->get_fra_used_percentage() < 70)
			{
				$fra_server_used_color = 'green';
			}
			elseif ($server_obj->get_fra_used_percentage() < 90)
			{
				$fra_server_used_color = 'orange';
			}
			else
			{
				$fra_server_used_color = 'red';
			}
			
			$mail_text = '<h2>Server Overview</h2>
			<table>
				<tr>
					<td style="border: 1px dotted; padding: 5px; width: 200px;"><b>Hostname</b></td>
					<td style="border: 1px dotted; padding: 5px; width: 300px;">'.$server_obj->hostname.'</td>
				</tr>
				<tr>
					<td style="border: 1px dotted; padding: 5px; width: 200px;"><b>FRA Space (u30)</b></td>
					<td style="border: 1px dotted; padding: 5px; width: 300px;">'.$server_obj->get_fra_space().'GB</td>
				</tr>
				<tr>
					<td style="border: 1px dotted; padding: 5px; width: 200px;"><b>Assigned FRA Space</b></td>
					<td style="border: 1px dotted; padding: 5px; width: 300px; background: red; color: #fff;">'.$server_obj->get_fra_assigned().'GB ('.(round(($server_obj->get_fra_assigned()/$server_obj->get_fra_space()*100),0)).'% assigned)</td>
				</tr>
				<tr>
					<td style="border: 1px dotted; padding: 5px; width: 200px;"><b>FRA Space used</b></td>
					<td style="border: 1px dotted; padding: 5px; width: 300px; background: '.$fra_server_used_color.'; color: #fff;">'.$server_obj->get_fra_used_space().'GB</td>
				</tr>
			</table>		
			';
			
			############################################
			# Database Overview
			############################################
			foreach ($server_obj->instances as $instance)
			{
				$fra_space_obj = parameter_actions::get_parameter_by_id (1089, $instance->db_id);
				$fra_data[$instance->db_id]['fra_space'] = $fra_space_obj->get_current_value();
				
				$fra_used_space_obj = parameter_actions::get_parameter_by_id (9003, $instance->db_id);
				$fra_data[$instance->db_id]['used_fra_space'] = $fra_used_space_obj->get_current_value();
				
				$fra_reclaimable_space_obj = parameter_actions::get_parameter_by_id (9004, $instance->db_id);
				$fra_data[$instance->db_id]['reclaimable_fra_space'] = $fra_reclaimable_space_obj->get_current_value();
				
				$fra_data[$instance->db_id]['used_fra_percentage'] = round((($fra_used_space_obj->current_value/$fra_space_obj->current_value)*100),0);
				$fra_data[$instance->db_id]['reclaimable_fra_percentage'] = round(((($fra_used_space_obj->current_value - $fra_reclaimable_space_obj->current_value)/$fra_space_obj->current_value)*100),0);			
			}
			
			$mail_text = $mail_text.'<h2>Databases</h2><table>';
			foreach ($server_obj->instances as $instance)
			{
				if ($fra_data[$instance->db_id]['used_fra_percentage'] < 70)
				{
					$fra_db_used_color = 'green';
				}
				elseif ($fra_data[$instance->db_id]['used_fra_percentage'] < 90)
				{
					$fra_db_used_color = 'orange';
				}
				else
				{
					$fra_db_used_color = 'red';
				}
				$mail_text = $mail_text.'
					<tr>
						<td style="border: 1px dotted; padding: 5px; width: 75px;"><b>'.$instance->sid.'</b></td>
						<td style="border: 1px dotted; padding: 5px; width: 447px; background: '.$fra_db_used_color.'; color: #fff;">'.$fra_data[$instance->db_id]['used_fra_space'].' '.'/ '.$fra_data[$instance->db_id]['fra_space'].' used ('.$fra_data[$instance->db_id]['reclaimable_fra_space'].' reclaimable)</td>
					</tr>
				';		
			}
			$mail_text = $mail_text.'</table>';
			
			
			############################################
			# Send E-Mail
			############################################
			$subject = '[FRA-Monitor Alert] FRA over assigned on '.$server_obj->get_hostname().' noapplix';
			
			$headers = "From: FRA-Monitor\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
			mail($email_address, $subject, $mail_text, $headers);
			
			echo $mail_text.'<br>';
			echo "Mail has been sent!<br>";
		}
	
		static function check_switchover_flag ($time, $rz1_db_id, $rz2_db_id)
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			$query = "SELECT `notificaton_id` FROM `notification_switchover` WHERE `db_id_rz1` = ".$rz1_db_id." and `db_id_rz2` = ".$rz2_db_id." and `so_time` = '".$time."';";
			$result = mysqli_query($db, $query);
			while($row = mysqli_fetch_object($result))
			{
				return true;
			}
			return false;
		}
		
		static function drop_switchover_flag ($time, $rz1_db_id, $rz2_db_id)
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			$result = mysqli_query($db, "DELETE FROM `notification_switchover`  WHERE `db_id_rz1` = ".$rz1_db_id." and `db_id_rz2` = ".$rz2_db_id." and `so_time` = '".$time."';");
		}
		
		static function add_switchover_flag ($time, $rz1_db_id, $rz2_db_id)
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			$result = mysqli_query($db, "INSERT INTO `notification_switchover`(`notificaton_id`, `db_id_rz1`, `db_id_rz2`, `so_time`) VALUES (NULL,".$rz1_db_id.",".$rz2_db_id.",'".$time."');");
		}
		
		static function send_switchover_notification ($switchover_arr, $email_address)
		{
			$rz1_instance = instance_actions::get_instance_by_id($switchover_arr['rz1_db_id']);
			$rz2_instance = instance_actions::get_instance_by_id($switchover_arr['rz2_db_id']);
			
			$html_rz1_db = '<table>
				<tr>
					<td style="border: 1px dotted; padding: 5px; width: 200px;"><b>Hostname</b></td>
					<td style="border: 1px dotted; padding: 5px; width: 300px;">'.$switchover_arr['rz1_hostname'].'</td>
				</tr>
				<tr>
					<td style="border: 1px dotted; padding: 5px; width: 200px;"><b>SID</b></td>
					<td style="border: 1px dotted; padding: 5px; width: 300px;">'.$switchover_arr['rz1_sid'].'</td>
				</tr>
				<tr>
					<td style="border: 1px dotted; padding: 5px; width: 200px;"><b>Service Name</b></td>
					<td style="border: 1px dotted; padding: 5px; width: 300px;">'.$rz1_instance->get_service_name(true).'</td>
				</tr>
			</table>';
							
			$html_rz2_db = '<table>
				<tr>
					<td style="border: 1px dotted; padding: 5px; width: 200px;"><b>Hostname</b></td>
					<td style="border: 1px dotted; padding: 5px; width: 300px;">'.$switchover_arr['rz2_hostname'].'</td>
				</tr>
				<tr>
					<td style="border: 1px dotted; padding: 5px; width: 200px;"><b>SID</b></td>
					<td style="border: 1px dotted; padding: 5px; width: 300px;">'.$switchover_arr['rz2_sid'].'</td>
				</tr>
				<tr>
					<td style="border: 1px dotted; padding: 5px; width: 200px;"><b>Service Name</b></td>
					<td style="border: 1px dotted; padding: 5px; width: 300px;">'.$rz2_instance->get_service_name(true).'</td>
				</tr>
			</table>';
			
			if ($switchover_arr['direction'] == 'to_rz2')
			{
				$mail_text = '<h2>Source DB:</h2>'.$html_rz1_db.'<h2>Destination DB:</h2>'.$html_rz2_db.'<h2>DBA Notice:</h2>'.'<div style="width: 530px; height: 123px;">'.nl2br($switchover_arr['dba_notice']).'</div>';
			}
			else
			{
				$mail_text = '<h2>Source DB:</h2>'.$html_rz2_db.'<h2>Destination DB:</h2>'.$html_rz1_db.'<h2>DBA Notice:</h2>'.'<div style="width: 530px; height: 123px;">'.nl2br($switchover_arr['dba_notice']).'</div>';
			}
			
			############################################
			# Send E-Mail
			############################################
			$subject = '[Switchover-Monitor Alert] Swichover/Failover on '.$rz1_instance->get_sid().' detected noapplix';
			
			$headers = "From: Switchover-Monitor\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
			mail($email_address, $subject, $mail_text, $headers);
			
			echo $mail_text.'<br>';
		}
	}

?>