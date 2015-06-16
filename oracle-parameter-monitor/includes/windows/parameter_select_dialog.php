<?php
	error_reporting(E_ALL); ini_set('display_errors', '1');
	session_start();
	if ($_SESSION['login'] != true)
	{
		header("Location: login.php");
		exit();
	}

	include_once ("../classes/class_parameter_actions.php");
	include_once ("../classes/class_instance.php");
	include_once ("../classes/class_instance_actions.php");
	
	# Generating the content of the dialog box for selection a parameter
	$dialog_parameter_arr 	= parameter_actions::get_all_parameter_array();	
?>

<input onsubmit="return false;" onkeyup="dialog_search_parameter(this.value);" class="oracle_parameter_search" type="text" placeholder="Search all parameter ...">
<div class="mCustomScrollbar" data-mcs-theme="dark" style="float: left; max-height: 359px;">
	<?php 
		foreach ($dialog_parameter_arr as $parameter_id => $attribute)
		{
			echo '<div onclick="load_parameter_widget ('.$_POST['instance_id'].', '.$parameter_id.', '.$attribute['type'].');" id="ora_parameter_item_'.$parameter_id.'" class="ora_parameter_item">
					<img class="parameter_icon_flat" src="images/icon_parameter.png">
					<div class="attribute name">'.$attribute['name'].'</div>
					<div class="attribute description">'.$attribute['description'].'</div>
				  </div>';
		}
	?>
</div>