<?php
	class server {
		public $server_id;
		public $hostname;
		public $fra_space;
		public $fra_used_space;
		public $instances;
		
		public function get_hostname ()
		{
			$sep = explode('.', $this->hostname);
			return $sep[0];
		}
		
		# Pysical Space on U30
		public function get_fra_space ()
		{
			return (round (($this->fra_space/1024/1024), 0));
		}
		
		# Used Space on U30
		public function get_fra_used_space ()
		{
			return (round (($this->fra_used_space/1024/1024), 0));
		}
		
		# Space assigned to DB instances
		public function get_fra_assigned ()
		{
			$fra_assigned = 0;
			foreach ($this->instances as $instance)
			{
				$fra_parameter = parameter_actions::get_parameter_by_id (1089, $instance->db_id);
				$fra_assigned = $fra_assigned + $fra_parameter->current_value;
			}
						
			return (round (($fra_assigned/1024/1024/1024), 0));
		}
		
		# Space reclaimable of all DB instances
		public function get_fra_reclaimable ()
		{
			$fra_reclaimable = 0;
			
			foreach ($this->instances as $instance)
			{
				$fra_reclaimable = parameter_actions::get_parameter_by_id (9004, $instance->db_id);
				$fra_reclaimable = $fra_reclaimable + $fra_reclaimable->current_value;
			}
			
			return (round (($fra_reclaimable/1024/1024/1024), 0));	
		}
		
		# Comparing pysical space with assigned space
		public function get_fra_assigned_percentage ()
		{
			$fra_assigned = $this->get_fra_assigned()*1024*1024;
					
			$value = round((($fra_assigned/$this->fra_space)*100),0);
			if ($value > 100) 
			{
				return 100;
			} 
			else 
			{
				if ($value == 100)
				{
					return 99;
				}
				else 
				{
					return $value;
				}
			}
		}
		
		# How much pyhsical space is in use
		public function get_fra_used_percentage ()
		{
			return (round((($this->fra_used_space/$this->fra_space)*100),0));
		}

	}
?>