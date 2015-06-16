<?php
	#error_reporting(E_ALL); ini_set('display_errors', '1');

	include ("./includes/templates/header.php");
	include ("./includes/templates/top_header.php");
	include_once ("./includes/classes/class_parameter_actions.php");
	include_once ("./includes/classes/class_instance.php");
	include_once ("./includes/classes/class_instance_actions.php");
	
	# Get last parameter changes
	$arr_last_parameter_changes = parameter_actions::get_last_parameter_chagnes_as_array();
?>
<div id="bottom-header">
	<div class="container clearfix">
		<h1 id="bh_h1">Last parameter changes</h1>
		<div id="target_menu_div">
			<ul id="target_menu">
			</ul>
		</div>
	</div>
	<div style="clear: both;"></div>
</div>

<div id="content-area">
	<div id="top-shadow">
		<div class="container" style="height: 1371px; padding-left: 19px; padding-top: 17px">
		<div class="table_header ora_parameter_item">
			<div class="dummy" style="width: 28px;"></div>
			<div class="attribute time">time</div>
			<div class="attribute sid">SID</div>
			<div class="attribute hostname">hostname</div>
			<div class="attribute parameter_value">parameter name</div>
		</div>	
			<?php 
			foreach ($arr_last_parameter_changes as $key => $change)
			{
				echo '<div id="parameter_change_'.$key.'" class="ora_parameter_item">
								<img onclick="$( \'#dialog_parameter_change_'.$key.'\' ).dialog( \'open\' );" class="parameter_icon_flat mCS_img_loaded" src="images/icon_parameter.png">
								<div onclick="$( \'#dialog_parameter_change_'.$key.'\' ).dialog( \'open\' );" class="attribute time">'.$change['time'].'</div>
								<div onclick="$( \'#dialog_parameter_change_'.$key.'\' ).dialog( \'open\' );" class="attribute sid">'.$change['sid'].'</div>
								<div onclick="$( \'#dialog_parameter_change_'.$key.'\' ).dialog( \'open\' );" class="attribute hostname">'.$change['hostname'].'</div>
								<div onclick="$( \'#dialog_parameter_change_'.$key.'\' ).dialog( \'open\' );" class="attribute parameter_value">'.$change['parameter_name'].'</div>';
				
				if ($change['dba_notice'] != "")
				{
					echo '<img onclick="$( \'#dialog_dba_notice_'.$key.'\' ).dialog( \'open\' );" class="add_notice_icon" src="images/icon_add_notice_blue.png"></div>';
				}
				else
				{
					echo '<img onclick="$( \'#dialog_dba_notice_'.$key.'\' ).dialog( \'open\' );" class="add_notice_icon" src="images/icon_add_notice.png"></div>';
				}
				
				echo '<script>
							$(function() {
								$( "#dialog_parameter_change_'.$key.'" ).dialog({
									autoOpen: false, 
									draggable: true,
									modal: false,
									width: 517,
									 
									show: {effect: "scale",	duration: 200},
									hide: {effect: "fade", duration: 200}
								});	});
					 </script>';
				
					
				echo '<div id="dialog_parameter_change_'.$key.'" title="'.$change['time'].' '.$change['sid'].' '.$change['parameter_name'].'">
						<div class="summary">
							<table style="border: 0";>
								<tbody>
									<tr>
										<td>Current Value</td>
										<td>'.$change['parameter_value'].'</td>
									</tr>
									<tr>
										<td>Last Value</td>
										<td>'.$change['parameter_last_value'].'</td>
									</tr>
									<tr>
										<td>Default Value</td>
										<td>'.$change['parameter_default_value'].'</td>
									</tr>
									<tr>
										<td>Description</td>
										<td>'.$change['parameter_description'].'</td>
									</tr>
								</tbody>
							</table>
						</div>
					  </div>';
				
				echo '<script>
							$(function() {
								$( "#dialog_dba_notice_'.$key.'" ).dialog({
									autoOpen: false,
									draggable: true,
									modal: false,
									width: 517,
				
									show: {effect: "scale",	duration: 200},
									hide: {effect: "fade", duration: 200}
								});	});
					 </script>';
				
					
				echo '<div id="dialog_dba_notice_'.$key.'" class="dialog_dba_notice" title="Add a notice">
					 		<div style="display: none" class="save_successfully" id="save_successfully_'.$key.'">Notice saved successfully.</div>
					 		<div style="display: none" class="save_failed" id="save_failed_'.$key.'">An Error occured.</div>
					 		<textarea id="dba_notice_'.$key.'">'.$change['dba_notice'].'</textarea>
							<form class="form_publish_range">
							  <div onclick="$(\'#publish_range_server_'.$key.'\').attr(\'checked\', false); $(\'#publish_range_global_'.$key.'\').attr(\'checked\', false);" class="option"><input type="checkbox" id="publish_range_dg_'.$key.'" value="dg"><span>Publish notice to both dataguard instances of '.$change['sid'].'.</span><br></div>
							  <div onclick="$(\'#publish_range_dg_'.$key.'\').attr(\'checked\', false); $(\'#publish_range_global_'.$key.'\').attr(\'checked\', false);" class="option"><input type="checkbox" id="publish_range_server_'.$key.'" value="server"><span>Publish notice to all DBs on '.$change['hostname'].'.</span><br></div>
							  <div onclick="$(\'#publish_range_server_'.$key.'\').attr(\'checked\', false); $(\'#publish_range_dg_'.$key.'\').attr(\'checked\', false);" class="option"><input type="checkbox" id="publish_range_global_'.$key.'" value="global"><span>Publish notice to all systems worldwide.</span><br></div>
							</form>			
							<a onclick="$( \'#dialog_dba_notice_'.$key.'\' ).dialog( \'close\' );" style="float: left">Cancel</a>
							<a onclick="save_parameter_change_notice ('.$change['last_value_id'].', \''.$key.'\');" style="font-weight: bold;">Save notice</a>
					  </div>';
				
			}
			?>
		</div>
	</div>
</div>		