<?php
	class instance_actions
	{
		static function get_all_instances ()
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			$result = mysqli_query($db, "SELECT * FROM `instances`");
			while($row = mysqli_fetch_object($result))
			{
				$arr_obj[] = instance_actions::build_instance_obj ($row->db_id, $row->sid, $row->hostname);
			}	
			return $arr_obj;
		}
		
		static function get_instance_by_server ($server_hostname)
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			$result = mysqli_query($db, "SELECT * FROM `instances` WHERE `hostname` = '".$server_hostname."'");
			while($row = mysqli_fetch_object($result))
			{
				$arr_obj[] = instance_actions::build_instance_obj ($row->db_id, $row->sid, $row->hostname);
			}
			return $arr_obj;
		}
		
		static function get_instance_by_id ($instance_id)
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			$result = mysqli_query($db, "SELECT * FROM `instances` WHERE `db_id` = '".$instance_id."'");
			while($row = mysqli_fetch_object($result))
			{
				return (instance_actions::build_instance_obj ($row->db_id, $row->sid, $row->hostname));
			}
		}
				
		private static function build_instance_obj ($db_id, $sid, $hostname)
		{
			$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
			
			$instance_obj					= new instance();
			$instance_obj->db_id			= $db_id;
			$instance_obj->sid				= $sid;
			$instance_obj->hostname			= $hostname;
			
			# Get Service Name
			$result2 = mysqli_query($db, "SELECT * FROM `values` WHERE `parameter_id` = 1698 and `db_id` = ".$db_id." ORDER BY `parameter_id` ASC");
			while($row2 = mysqli_fetch_object($result2))
			{
				$instance_obj->service_name = $row2->value_string;
			}
			
			# Get Charset
			$result2 = mysqli_query($db, "SELECT * FROM `values` WHERE `parameter_id` = 9001 and `db_id` = ".$db_id." ORDER BY `parameter_id` ASC");
			while($row2 = mysqli_fetch_object($result2))
			{
				$instance_obj->charset = $row2->value_string;
			}
			
			# Get Status
			$result2 = mysqli_query($db, "SELECT * FROM `values` WHERE `parameter_id` = 9002 and `db_id` = ".$db_id." ORDER BY `parameter_id` ASC");
			while($row2 = mysqli_fetch_object($result2))
			{
				$instance_obj->status = $row2->value_string;
			}
			
			return $instance_obj;
		}
		
		static function get_all_instance_search_json () {
			$arr_instance_objs = instance_actions::get_all_instances();
			
			foreach ($arr_instance_objs as $instance)
			{
				$data[$instance->db_id]['info_string'] 	= 	$instance->service_name." ".
															$instance->hostname." ".
															$instance->sid." ".
															$instance->status." ".
															$instance->charset;
			}
			return json_encode($data);
		}
		
		static function truncate($string, $length, $dots = " ...") {
			return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
		}
	}
?>