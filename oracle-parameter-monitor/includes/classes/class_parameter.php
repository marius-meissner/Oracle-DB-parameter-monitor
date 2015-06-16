<?php
	class parameter {
		public $parameter_id;
		public $name;
		public $type;
		public $description;
		public $current_value;
		public $default_value;
		public $last_value;
		public $last_change;
		public $refreshed;
		
		public function get_current_value ()
		{
			if ($this->type == 6)
			{
				return parameter_actions::human_filesize_formatted($this->current_value, 1);
			}
			else
			{
				return $this->current_value;
			}
		}
		
		public function get_last_value ()
		{
			if ($this->type == 6)
			{
				return parameter_actions::human_filesize_formatted($this->last_value, 1);
			}
			else
			{
				return $this->last_value;
			}
		}
		
		public function get_default_value ()
		{
			if ($this->type == 6)
			{
				return parameter_actions::human_filesize_formatted($this->default_value, 1);
			}
			else
			{
				return $this->default_value;
			}
		}
	}
?>