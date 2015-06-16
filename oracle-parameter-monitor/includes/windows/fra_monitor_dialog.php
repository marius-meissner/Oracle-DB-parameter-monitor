<?php
	error_reporting(E_ALL); ini_set('display_errors', '1');

	session_start();
	if ($_SESSION['login'] != true)
	{
		header("Location: login.php");
		exit();
	}
	
	include_once ("../classes/class_parameter.php");
	include_once ("../classes/class_parameter_actions.php");
	include_once ("../classes/class_instance.php");
	include_once ("../classes/class_instance_actions.php");
	
	$instances_arr = instance_actions::get_instance_by_server($_POST['hostname']);
	foreach ($instances_arr as $instance) 
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
?>
<script>
	<?php foreach ($instances_arr as $instance) {?>
		  $(function() {
			var instance_fra_usage = <?php echo $fra_data[$instance->db_id]['used_fra_percentage'];?>;
			var instance_fra_reclaimable = <?php echo $fra_data[$instance->db_id]['reclaimable_fra_percentage'];?>;
			
		    $( "#progressbar_instance_fra_usage_<?php echo $instance->db_id;?>" ).progressbar({
		      value: instance_fra_usage
		    });

		    $( "#progressbar_instance_fra_reclaimable_<?php echo $instance->db_id;?>" ).progressbar({
			      value: instance_fra_reclaimable
			});
		    $( "#progressbar_instance_fra_reclaimable_<?php echo $instance->db_id;?> .ui-widget-header" ).css('background', 'rgba(0, 0, 0, 0.13)');
		
		    if (instance_fra_usage < 70) {$( "#progressbar_instance_fra_usage_<?php echo $instance->db_id;?> .ui-widget-header" ).css('background', '#93CA42');}
		    if (instance_fra_usage >= 70 && instance_fra_usage <= 90) {$( "#progressbar_instance_fra_usage_<?php echo $instance->db_id;?> .ui-widget-header" ).css('background', 'rgb(229, 174, 0)');}
		    if (instance_fra_usage > 90) {$( "#progressbar_instance_fra_usage_<?php echo $instance->db_id;?> .ui-widget-header" ).css('background', 'rgb(229, 0, 0)');}	    

		    $( "#fra_dialog_legend" ).accordion({
	               collapsible: true,
	               heightStyle: "content",
	               active: false
	        });
		 });
	<?php }?>
</script>
<div id="fra_dialog_legend">
   <h3>Explanation</h3>
   <div style="height: 272px;">
		<img style="width: 416px ;margin-left: -22px; margin-top: 15px;" src="images/fra_dialog_legend.png">
   </div>
</div>
<div class="summary">
	<table style="border: 0;">
		<tbody>
			<?php foreach ($instances_arr as $instance) {?>
				<tr>
				<td><img class="db_icon_flat" src="images/db_icon_flat.png"></td><td><?php echo $instance->sid;?></td>
				<td>
					<div id="progressbar_instance_fra_usage_<?php echo $instance->db_id;?>" style="float: right; height: 26px; width: 320px; background: rgba(116, 116, 116, 0.43);" class="attribute charset"><div class="progress-label"><?php echo $fra_data[$instance->db_id]['used_fra_space']; ?> / <?php echo $fra_data[$instance->db_id]['fra_space']; ?> used</div></div>
					<div id="progressbar_instance_fra_reclaimable_<?php echo $instance->db_id;?>" style="float: right; height: 26px; width: 320px; margin-top: -26px; background: rgba(116, 116, 116, 0);" class="attribute charset"></div>
				</td>
				</tr>
			<?php }?>
		</tbody>
	</table>
</div>