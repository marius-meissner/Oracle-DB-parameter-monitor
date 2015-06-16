<?php
class server_actions
{
	static function get_all_server ()
	{
		$db = mysqli_connect($_SESSION['db_host'], $_SESSION['db_user'], $_SESSION['db_password'], $_SESSION['db_database']);
		$result = mysqli_query($db, "SELECT * FROM `server` where test_system != 'true'");
		while($row = mysqli_fetch_object($result))
		{
			$i 								= $row->server_id;
			$obj_name 						= "server".$i;
			$$obj_name						= new server();
			$$obj_name->server_id			= $row->server_id;
			$$obj_name->hostname			= $row->hostname;

			# Fra Space
			$result2 = mysqli_query($db, "SELECT * FROM `system_parameter` WHERE `server_id` = $row->server_id and `parameter_name` = 'fra-space' order by time desc");
			while($row2 = mysqli_fetch_object($result2))
			{
				$$obj_name->fra_space = $row2->value_string;
			}
			
			# Fra Used Space
			$result2 = mysqli_query($db, "SELECT * FROM `system_parameter` WHERE `server_id` = $row->server_id and `parameter_name` = 'fra-used-space' order by time desc");
			while($row2 = mysqli_fetch_object($result2))
			{
				$$obj_name->fra_used_space = $row2->value_string;
			}
						
			# Build all instance objects
			$$obj_name->instances = instance_actions::get_instance_by_server($row->hostname);

			$arr_obj[] = $$obj_name;
		}
			return $arr_obj;
	}
	
	static function get_all_server_search_json () {
		$arr_server_objs = server_actions::get_all_server();
			
		foreach ($arr_server_objs as $server)
		{
			$data[$server->server_id]['info_string'] 	= $server->hostname;
		}
		return json_encode($data);
	}
}