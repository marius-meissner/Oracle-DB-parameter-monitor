<?php
	error_reporting(E_ALL); ini_set('display_errors', '1');
	session_start();
	if ($_SESSION['login'] != true)
	{
		header("Location: login.php");
		exit();
	}
	include_once ("../classes/class_parameter_actions.php");
	include_once ("../classes/class_parameter.php");
	
	$parameter_obj = parameter_actions::get_parameter_by_id ($_POST['parameter_id'], $_POST['instance_id']);
?>
<div id="progress" class="progress">
			<?php if ($parameter_obj->type != 2) {?>
					<table>
						<thead>
						<tr>
							<th><span><?php echo $parameter_obj->name;?></span>
							<div class="chart_links">
								<a onclick="load_parameter_chart (<?php echo $_POST['instance_id']; ?>, <?php echo $parameter_obj->parameter_id;?>, '24_hours', <?php echo $parameter_obj->type;?>);">24H</a>
								<a onclick="load_parameter_chart (<?php echo $_POST['instance_id']; ?>, <?php echo $parameter_obj->parameter_id;?>, '14_days', <?php echo $parameter_obj->type;?>);">14D</a>
								<a onclick="load_parameter_chart (<?php echo $_POST['instance_id']; ?>, <?php echo $parameter_obj->parameter_id;?>, '30_days', <?php echo $parameter_obj->type;?>);">30D</a>
								<a onclick="load_parameter_chart (<?php echo $_POST['instance_id']; ?>, <?php echo $parameter_obj->parameter_id;?>, '90_days', <?php echo $parameter_obj->type;?>);">90D</a>
								<a onclick="load_parameter_chart (<?php echo $_POST['instance_id']; ?>, <?php echo $parameter_obj->parameter_id;?>, '365_days', <?php echo $parameter_obj->type;?>);">1Y</a>
							</div>
						</th></tr>
						<tr>
						  <td style="height: 204px; padding: 0;">
						  <div id="progress_<?php echo $parameter_obj->parameter_id;?>" style="margin-top:6px;">
						  	<?php 
						  		# Legend only needed for boolean values
						  		if ($parameter_obj->type == 1) {echo '<div class="chart_legend"><ul><li>true</li><li>false</li><li>unset</li></ul>';}
						  	?>
						  	</div>
						  	<canvas style="float: left;" id="chart_<?php echo $parameter_obj->parameter_id;?>" width="596" height="190"></canvas>
						  </td>
						</tr>
					  </thead>
					</table>
				<?php 
				} else {	
				?>
					<table class="progress_string">
						<thead>
							<tr><th colspan="2"><?php echo $parameter_obj->name;?></th></tr>
						</thead><tbody>
						  	<?php 
						  		$arr_parameter_changes = parameter_actions::get_all_parameter_changes_as_array($parameter_obj->parameter_id, $_POST['instance_id']);
						  		foreach ($arr_parameter_changes as $change)
						  		{
						  			echo '<tr><td class="time">'.$change['time'].'</td><td>'.$change['value'].'</td></tr>';
						  		}
						  	?>
					  	</tbody>
					</table>
				<?php }?>	  
				</div>
				<div class="summary">
					<table>
						<thead><tr><th colspan="2">Summary</th></tr></thead>
						<tbody>
							<tr>
								<td>Current Value</td>
								<td><?php echo $parameter_obj->get_current_value();?></td>
							</tr>
							<tr>
								<td>Default Value</td>
								<td><?php echo $parameter_obj->get_default_value();?></td>
							</tr>
							<tr>
								<td>Last Value</td>
								<td><?php echo $parameter_obj->get_last_value();?></td>
							</tr>
							<tr>
								<td>Last Change</td>
								<td><?php echo $parameter_obj->last_change;?></td>
							</tr>
							<tr>
								<td>Refreshed</td>
								<td><?php echo $parameter_obj->refreshed;?></td>
							</tr>
						</tbody>
					</table>
				</div>