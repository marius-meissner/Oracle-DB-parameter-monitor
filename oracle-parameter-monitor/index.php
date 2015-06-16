<?php
	#error_reporting(E_ALL); ini_set('display_errors', '1');

	include ("./includes/templates/header.php");
	include ("./includes/templates/top_header.php");
	include_once ("./includes/classes/class_parameter_actions.php");
	include_once ("./includes/classes/class_instance.php");
	include_once ("./includes/classes/class_instance_actions.php");
	
	# Generating instance objects
	$arr_instance_objs = instance_actions::get_all_instances();
	
	# Generating the content of the dialog box for selection a parameter
	$dialog_parameter_arr 	= parameter_actions::get_all_parameter_array();
	$dialog_parameter_html 	= "";
	
?>
<script type="text/javascript">
	var instance_search_objs = <?php echo instance_actions::get_all_instance_search_json();?>; 
	var parameter_search_objs = <?php echo parameter_actions::get_all_parameter_search_json();?>;
</script>
<div id="bottom-header">
	<div class="container clearfix">
		<h1 id="bh_h1">Database Overview</h1>
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
				<div class="attribute service_name" id="service_name_1">service name</div>
				<div class="attribute sid">SID</div>
				<div class="attribute host">hostname</div>
				<div class="attribute status">db mode</div>
				<div class="attribute charset">characterset</div>
			</div>			
			<?php foreach ($arr_instance_objs as $instance) {?>
			<div onclick="show_prod_db_details(<?php echo $instance->db_id;?>);" id="prod-db-item_<?php echo $instance->db_id;?>" class="prod-db-item">
				<img class="db_icon_flat" src="images/db_icon_flat.png">
				<div class="attribute service_name" title="<?php echo $instance->get_service_name(); ?>" id="service_name_<?php echo $instance->db_id;?>"><?php echo instance_actions::truncate($instance->get_service_name(true), 30);?></div>
				<div class="attribute sid"><?php echo $instance->get_sid();?></div>
				<div class="attribute host"><?php echo $instance->get_hostname();?></div>
				<div class="attribute status"> <?php if ($instance->status == 'OPEN') {echo '<img src="images/icon_online.png">';} else {echo '<img src="images/icon_standby.png">';}?> <p><?php echo $instance->status;?></p></div>
				<div class="attribute charset"><?php echo $instance->charset;?></div>
				<img class="arrow_extend" id="arrow_img_<?php echo $instance->db_id;?>" src="images/arrow_down.png">
			</div>
			<div style="display: none; max-height: 2000px; opacity: 1;" id="details_container_<?php echo $instance->db_id;?>" class="prod_db_details">
				<div id="parameter_widgets_<?php echo $instance->db_id;?>"></div>
				
				<img onclick="load_select_parameter_dialog (<?php echo $instance->db_id;?>);" class="add_parameter" src="images/icon_add.png">
			</div>
			<?php }?>
			
		</div>
	</div>
</div>

<script>
         $(function() {
        	$( "#service_name_<?php echo $instance->db_id;?>" ).tooltip();
            $( "#add_parameter_dialog" ).dialog({
               autoOpen: false,
               draggable: false,
               modal: true,
               width: 1000,
               height: 500,
               
               show: {
                   effect: "scale",
                   duration: 200
                 },
                 hide: {
                   effect: "fade",
                   duration: 200
                 }
            });
         });
</script>
<div class="add_parameter_dialog" id="add_parameter_dialog" title="Add parameter"></div>