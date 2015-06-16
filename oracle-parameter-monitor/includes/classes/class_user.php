<?php
class user
{
	public $username 	= "not defined";
	public $user_id         = "not defined";
	public $display_name    = "not defined";
    public $department        = "not defined";
    
    public function get_display_name ()
    {
    	if (strlen($this->display_name) > 19)
    	{
    		return substr($this->display_name, 0, 15).' ...';
    	}
    	else
    	{
    		return $this->display_name;
    	}
    }
    
    public function get_department ()
    {
    	if (strlen($this->department) > 19)
    	{
    		return substr($this->department, 0, 15).' ...';
    	}
    	else
    	{
    		return $this->department;
    	}
    }
}
?>