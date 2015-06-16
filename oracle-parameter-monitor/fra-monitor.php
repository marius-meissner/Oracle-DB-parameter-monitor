<?php
	#error_reporting(E_ALL); ini_set('display_errors', '1');

	include ("./includes/templates/header.php");
	include ("./includes/templates/top_header.php");
	include_once ("./includes/classes/class_server.php");
	include_once ("./includes/classes/class_server_actions.php");
	include_once ("./includes/classes/class_instance.php");
	include_once ("./includes/classes/class_instance_actions.php");
	include_once ("./includes/classes/class_parameter.php");
	include_once ("./includes/classes/class_parameter_actions.php");
	
	# Generating instance objects
	$arr_server_objs = server_actions::get_all_server();
	
?>
<script type="text/javascript">
	var instance_search_objs = <?php echo server_actions::get_all_server_search_json();?>; 
</script>
<div id="bottom-header">
	<div class="container clearfix">
		<h1 id="bh_h1">FRA monitor</h1>
		<div id="target_menu_div">
			<ul id="target_menu">
			</ul>
		</div>
		<input onkeyup="instant_search_instances(this.value);" class="prod_db_search" type="text" placeholder="Search all databases ...">
	</div>
	<div style="clear: both;"></div>
</div>

<div id="content-area">
	<div id="top-shadow">
		<div class="container">
			<div class="table_header prod-db-item">
				<div class="dummy" style="width: 28px;"></div>
				<div class="attribute host">hostname</div>
				<div class="attribute fra_space">physical space (u30)</div>
			</div>
			<?php foreach ($arr_server_objs as $server) {?>
				<script>
				  // FRA Usage 
				  $(function() {
					var fra_usage = <?php echo $server->get_fra_used_percentage();?>;
					
				    $( "#progressbar_server_fra_usage_<?php echo $server->server_id;?>" ).progressbar({
				      value: fra_usage
				    });

				    if (fra_usage < 70) {$( "#progressbar_server_fra_usage_<?php echo $server->server_id;?> .ui-widget-header" ).css('background', '#93CA42');}
				    if (fra_usage >= 70 && fra_usage <= 90) {$( "#progressbar_server_fra_usage_<?php echo $server->server_id;?> .ui-widget-header" ).css('background', 'rgb(229, 174, 0)');}
				    if (fra_usage > 90) {$( "#progressbar_server_fra_usage_<?php echo $server->server_id;?> .ui-widget-header" ).css('background', 'rgb(229, 0, 0)');}	    
				  });

				  // Fra Assigned Progress Bar
				  $(function() {
					var fra_assigned = <?php echo $server->get_fra_assigned_percentage();?>;
					  
				    $( "#progressbar_server_fra_assigned_<?php echo $server->server_id;?>" ).progressbar({
				      value: fra_assigned
				    });
				    if (fra_assigned < 90) {$( "#progressbar_server_fra_assigned_<?php echo $server->server_id;?> .ui-widget-header" ).css('background', '#93CA42');}
				    if (fra_assigned >= 90 && fra_assigned < 99) {$( "#progressbar_server_fra_assigned_<?php echo $server->server_id;?> .ui-widget-header" ).css('background', 'rgb(229, 174, 0)');}
				    if (fra_assigned >= 99) {$( "#progressbar_server_fra_assigned_<?php echo $server->server_id;?> .ui-widget-header" ).css('background', 'rgb(229, 0, 0)');}	    
				    
				  });
				</script>
			
				<div onclick="load_fra_monitor_dialog (<?php echo $server->server_id;?>, '<?php echo $server->hostname;?>')" id="prod-db-item_<?php echo $server->server_id;?>" class="prod-db-item">
					<img class="db_icon_flat" src="images/icon_server.png">
					<div class="attribute host"><?php echo $server->get_hostname();?></div>
					<div class="attribute fra_space"><?php echo $server->get_fra_space();?>G</div>
					<div id="progressbar_server_fra_usage_<?php echo $server->server_id;?>" style="margin-top: 5px; height: 26px; width: 295px; background: rgba(116, 116, 116, 0.43);" class="attribute charset"><div class="progress-label"><?php echo $server->get_fra_used_space();?>G used</div></div>
					<div id="progressbar_server_fra_assigned_<?php echo $server->server_id;?>" style="margin-top: 5px; height: 26px; width: 295px; background: rgba(116, 116, 116, 0.43);" class="attribute charset"><div class="progress-label"><?php echo $server->get_fra_assigned();?>G assigned</div></div>
					<img class="arrow_extend" id="arrow_img_<?php echo $server->server_id;?>" src="images/arrow_down.png">
				</div>
				
				<script>
					// Dialog Box
					$(function() {
						$( "#dialog_fra_monitor_<?php echo $server->server_id;?>" ).dialog({
							autoOpen: false, 
							draggable: true,
							modal: false,
							width: 517,
							 
							show: {effect: "scale",	duration: 200},
							hide: {effect: "fade", duration: 200}
						});	});
				</script>
				
				<div id="dialog_fra_monitor_<?php echo $server->server_id;?>" title="<?php echo $server->get_hostname();?>"></div>
				
				<?php }?>
			</div>
	</div>
</div>
