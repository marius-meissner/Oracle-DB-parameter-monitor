<?php
	#error_reporting(E_ALL); ini_set('display_errors', '1');

	include ("./includes/templates/header.php");
	include ("./includes/templates/top_header.php");
	include_once ("./includes/classes/class_parameter_actions.php");
	include_once ("./includes/classes/class_instance.php");
	include_once ("./includes/classes/class_instance_actions.php");
	
	$so_data_arr = parameter_actions::get_all_switchover_as_array();
?>
<div id="bottom-header">
	<div class="container clearfix">
		<h1 id="bh_h1">Switchover Monitor</h1>
		<div id="target_menu_div">
			<ul id="target_menu">
			</ul>
		</div>
	</div>
	<div style="clear: both;"></div>
</div>

<div id="content-area">
	<div id="top-shadow">
		<div class="container">
			<div class="table_header prod-db-item">
				<div class="dummy" style="width: 28px;"></div>
				<div class="attribute time">switchover time</div>
				<div class="attribute extended_sid">database RZ1</div>
				<div class="attribute so_arrow">direction</div>
				<div class="attribute extended_sid" style="border: 0;">database RZ2</div>
			</div>
			<?php foreach ($so_data_arr as $time => $switchover) {?>
			<div class="prod-db-item">
				<img class="db_icon_flat" src="images/db_icon_flat.png">
				<div class="attribute time"> <?php echo $time; ?> </div>
				<div class="attribute extended_sid"><?php echo $switchover['rz1_sid'];?></div>
				<div class="attribute host"><?php echo $switchover['rz1_hostname'];?></div>
				<div class="attribute so_arrow">
					<?php 
						if ($switchover['direction'] == 'to_rz2') {echo '<img src="images/icon_so_arrow_right">';}
						if ($switchover['direction'] == 'to_rz1') {echo '<img src="images/icon_so_arrow_left">';}
					?>
					
				</div>
				<div class="attribute extended_sid"><?php echo $switchover['rz2_sid'];?></div>
				<div class="attribute host" style="border-right: 0;"><?php echo $switchover['rz2_hostname'];?></div>
				
				<?php if ($switchover['dba_notice'] != "")
				{ ?>
					<img onclick="$( '#dialog_dba_notice_<?php echo $switchover['value_id'];?>' ).dialog( 'open' );" class="add_notice_icon" src="images/icon_add_notice_blue.png">
				<?php }
				else
				{ ?>
					<img onclick="$( '#dialog_dba_notice_<?php echo $switchover['value_id'];?>' ).dialog( 'open' );" class="add_notice_icon" src="images/icon_add_notice.png">
				<?php }?>
			</div>
			
			<script>
				$(function() {
					$( "#dialog_dba_notice_<?php echo $switchover['value_id'];?>" ).dialog({
						autoOpen: false,
						draggable: true,
						modal: false,
						width: 517,
				
						show: {effect: "scale",	duration: 200},
						hide: {effect: "fade", duration: 200}
					});	});
			 </script>
			
			<div id="dialog_dba_notice_<?php echo $switchover['value_id'];?>" class="dialog_dba_notice" title="Add a notice">
					 		<div style="display: none" class="save_successfully" id="save_successfully_<?php echo $switchover['value_id'];?>">Notice saved successfully.</div>
					 		<div style="display: none" class="save_failed" id="save_failed_<?php echo $switchover['value_id'];?>">An Error occured.</div>
					 		<textarea id="dba_notice_<?php echo $switchover['value_id'];?>"><?php echo $switchover['dba_notice'];?></textarea>
							<a onclick="$( '#dialog_dba_notice_<?php echo $switchover['value_id'];?>' ).dialog( 'close' );" style="float: left">Cancel</a>
							<a onclick="save_parameter_change_notice ('<?php echo $switchover['value_id'];?>', '<?php echo $switchover['value_id'];?>')" style="font-weight: bold;">Save notice</a>
		 	</div>
			
			
			<?php }?>
			
		</div>
	</div>
</div>