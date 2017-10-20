	
	<?php if (!empty($activities)) {
    ?>
		<div class="activity-box">
			<div class="activity-box-inner">
		        <table class="tickets">
		            <?php  foreach ($activities as $activity) {
        ?>
		                <tr>
		                    <td class="activity-head">
		                        <table>
		                            <tr>
		                                <td><img src="../public/pics/user.gif"></td>
		                                <td><?php echo $activity['user']; ?></td>
		                                <td style="padding-left:45px!important"><img src="../public/pics/ansicht.gif"></td>
		                                <td>
		                                    <?php 
                                                if ((date('d', $activity['createtime']) == date('d')) && (date('m', $activity['createtime']) == date('m')) && (date('Y', $activity['createtime']) == date('Y'))) {
                                                    echo $this->translate['today'].'&nbsp;-&nbsp;'.date("H:i", $activity['createtime']);
                                                } elseif ((date('d', $activity['createtime']) == (date('d')-1)) && (date('m', $activity['createtime']) == date('m')) && (date('Y', $activity['createtime']) == date('Y'))) {
                                                    echo $this->translate['yesterday'].'&nbsp;-&nbsp;'.date("H:i", $activity['createtime']);
                                                } else {
                                                    echo date("d. M. Y", $activity['createtime']).'&nbsp;-&nbsp;'.date("H:i", $activity['createtime']);
                                                } ?>
		                                </td>
		    							<?php if (isset($activity['duration']) && $activity['duration'] ==! 0) {
                                                    ?>
		                                	<td><img src="../public/pics/clock.gif"></td>
		                                	<td><?php echo number_format($activity['duration'], 2, ',', ''); ?>&nbsp;h</td>
		                                <?php
                                                } ?>
		                                <td style="padding-left:45px!important;width:100%;text-align:right"><?php echo $this->translate['activity_type'].': '. $this->translate[$activity['type']]; ?></td>
		                        	</tr>
		                        </table>
		                    </td>	
		                </tr>
		                <tr>
		                    <td class="activity-text"><?php echo '<div><pre>'.$activity['body'].'</pre></div>'; ?></td>
		                </tr>
		            <?php
    } ?>
		        </table>
	        </div>
		</div>
	<?php
} else {
        ?>
		<div class="activity-box">
			<div class="activity-box-inner empty">
				<table class="activity-box-empty">
					<tr>
						<td align="center">
							<b><?php echo $this->translate['no_activity']; ?></b>
						</td>
					</tr>
				</table>
			</div>
		</div>
	<?php
    } ?>	
    
    <?php if ($not_set == true) {
        ?>
		<div class="activity-box">
			<div class="activity-box-inner empty">
	        	<table class="activity-box-empty">
	            	<tr>
	                	<td align="center">
	    					<b><?php echo $this->translate['no_set_activity']; ?></b>
	                	</td>
	                </tr>
	            </table>
	      	</div>
		</div>
	<?php
    } ?>
	