<?php
	class instance {
		public $instance_id;
		public $service_name;
		public $hostname;
		public $sid;
		public $status;
		public $charset; 
		
		public function get_sid ()
		{
			$sep = explode('.', $this->sid);
			return $sep[0];
		}
		
		public function get_hostname ()
		{
			$sep = explode('.', $this->hostname);
			return $sep[0];
		}
		
		public function get_service_name ($skip_sid = false)
		{		
			if ($skip_sid == true)
			{
				$return_value = str_replace ( '.'.$GLOBALS['config']['server_domain'], '', $this->service_name);
				$return_value = str_replace ($this->sid, '', $return_value);
				$return_value = preg_replace('/_RZ./', '', $return_value);
				
				while (substr($return_value, 0, 1) == ',' || substr($return_value, 1, 1) == ',')
				{
					$return_value = preg_replace('/^,/', '', $return_value);
					$return_value = preg_replace('/^.,/', '', $return_value);
				}
				
				if ($return_value == '')
				{
					return '-';
				}
				else 
				{
					return $return_value;
				}
			}
			{
				return str_replace ('.dbs.internal.draexlmaier.com', '', $this->service_name);
			}
		}
	}
?>