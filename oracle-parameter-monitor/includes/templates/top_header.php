<div id="top-header">
	<div class="container clearfix">
		<img src="images/app_logo_white.png"></img>
		<div style="float: left;">
			<h1>Oracle parameter monitor</h1>
			<h2>Reliable statistics with fast troubleshooting!</h2>
		</div>
		<ul id="top-menu">
			<li><a href="index.php">Database Overview</a></li>
			<li><a href="last_parameter_changes.php">Last Parameter Changes</a></li>
			<li><a href="fra-monitor.php">FRA Monitor</a></li>
			<li><a href="switchover_monitor.php">Switchover Monitor</a></li>
		</ul>
                 <div style="padding-top: 13px; float: left;">
                    <div id="user_container_overlay">
                        <div  class="user_container">   
                            <?php
                                require ("includes/classes/class_user.php");
                                $current_user = new user();
                                $current_user->display_name = $_SESSION['name'];
                                $current_user->department	= 'DBA';
                            ?>
                           <p>
                                <?php echo $current_user->get_display_name(); ?> <br>
                                <?php echo $current_user->get_department(); ?></p>
                               <img style=" z-index: 5;  height: 13px;  margin-top: -24px; margin-left: 153px;" src="images/arrow_down.png"></img>
                        </div>
                        <div style="height: 2px;"></div>
                        <div id="user_container_extended">
                            <a href="logout.php" style="text-decoration: none;">
                                <img src="images/exit.png" style="margin-top: 5px; margin-left: 137px; margin-left: 39px;  opacity: 0.8; margin-right: -42px; height: 30px; font-family: Calibri;">
                                <p style="margin-top: 8px;text-decoration: none;">Exit</p>
                            </a>
                        </div>
                    </div>
                </div>
        </div>  
</div>