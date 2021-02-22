<?php $months = array('01'=>'jan', '02'=>'feb', '03'=>'mar', '04'=>'apr', '05'=>'mai', '06'=>'jun', '07'=>'jul', '08'=>'aug', '09'=>'sep', '10'=>'oct', '11'=>'nov', '12'=>'dec'); ?>

    <div id="selection">
		<div id="select-field">
	        <form id="select-field-form" action="<?php echo $this->baseUrl;?>?co=ticket/show&type=<?php echo $type; ?>" method="post">
	            <table>
		            <tr>
						<td><img src="../public/pics/ansicht.gif" style="padding:3px 0px 0px 0px"></td>
		                <td><?php echo $this->translate['year'].': '; ?></td>
		                <td>
		                  	<select onchange="submit_filter_form(this.name, '');" class="search" name="year" id="year" size="1">
								<option selected value="0"><?php echo $this->translate['show_all_year']; ?></option>
	                    		<?php for ($i = $min_time; $i <= $max_time; $i++) {
    ?>
	                        		<?php if ($year == $i) {
        ?>
	                           			<option selected value="<?php echo $i; ?>"><?php echo $i; ?></option>
	                        		<?php
    } else {
        ?>
	                           			<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
	                    		<?php
    }
}?>
		                  	</select>
		                </td>
		                <td><?php echo $this->translate['month'].': '; ?></td>
		                <td>
		                  	<select onchange="submit_filter_form(this.name,'<?php echo $this->translate['err_filter_nomonth']; ?>');" class="search" name="month" id="month" size="1">
		                    	<option selected value="0"><?php echo $this->translate['show_all_month']; ?></option>
	                    		<?php foreach ($months as $key=>$val) {
    ?>
	                        		<?php if ($month !== 0 && $month == $key && $year !== 0) {
        ?>
	                            		<option selected value="<?php echo $key; ?>"><?php echo $this->translate[$val]; ?></option>
	                        		<?php
    } else {
        ?>
	                            		<option value="<?php echo $key; ?>"><?php echo $this->translate[$val]; ?></option>
	                    		<?php
    }
}?>
		                  	</select>
		                </td>
		                <?php if (count($sections) > 1) : ?>
		                <td><?php echo $this->translate['sections'].': '; ?></td>
		                <td>
		                    <select onchange="submit_filter_form(this.name, '');" class="search" name="section" size="1">
			                    <?php foreach ($sections as $id => $name) {
    ?>
			                        <?php if ($id == $selectedsection) {
        ?>
			                            <option selected value="<?php echo $id; ?>"><?php echo $name; ?></option>
			                        <?php
    } else {
        ?>
			                            <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
			                    <?php
    }
} ?>
		                    </select>
		                </td>
		                <?php else : ?>
		                <td><input type="hidden" name="section" value="<?php echo $selectedsection; ?>"></td>
		                <?php endif; ?>
		                <td>
		                    <input type="submit" value="<?php echo $this->translate['view']; ?>" class="button-ticket">
		                </td>
						<td style="width:100%">&nbsp;</td>
		                <td align="right"><strong><?php echo $this->translate['duration'].': '; ?></strong></td>
		                <td align="right"><?php if (!empty($duration)) {
    echo $duration.'&nbsp h';
} else {
    echo '0&nbsp h';
} ?></td>
		            </tr>
	            </table>
	        </form>
		</div>
    </div>

	<div id="datas">
		<div id="datas-in">
			<?php if (!empty($data)) : ?>
		        <table class="tickets">
		            <tr>
		                <th><?php echo $this->translate['tnumber']; ?></th>
		                <th><?php echo $this->translate['questioner']; ?></th>
		                <th><?php echo $this->translate['subject']; ?></th>
		                <th style="text-align:center"><?php echo $this->translate['entries']; ?></th>
		                <th><?php echo $this->translate['last_entry']; ?></th>
		                <th><?php echo $this->translate['status']; ?></th>
		                <th style="text-align:center"><?php echo $this->translate['priority']; ?></th>
		                <th style="text-align:center"><?php echo $this->translate['duration']; ?>(h)</th>
		                <th><?php echo $this->translate['category']; ?></th>
		                <th><?php echo $this->translate['create_date']; ?></th>
                        <th><?php echo $this->translate['last_activity']; ?></th>
		            </tr>

		            <?php foreach ($data as $ticket) : ?>
		            <tr>
		                <td><?php echo $ticket['tnumber']; ?></td>
		                <td>
		                    <table>
		                        <tr style="background:none">
		                            <td style="padding:2px 5px 0px 0px"><img src="../public/pics/user.gif" /></td>
		                            <td style="padding:0px"><?php echo $ticket['name']; ?></td>
		                        </tr>
		                    </table>
		                </td>
		                <td>
		                    <a href="<?php echo $this->baseUrl; ?>?co=ticket/show_ticket&tid=<?php echo base64_encode($ticket['id']); ?>"><?php echo $ticket['subject']; ?></a>
		                </td>
		    			<td align="center">
		                    <?php if (!empty($ticket['ticket_action'])) {
    echo $ticket['ticket_action']['amount'];
} ?>
		                </td>
		                <td style="white-space:nowrap">
							<?php if (!empty($ticket['ticket_action']['createtime']) && !empty($ticket['ticket_action']['user'])) {
    if ((date('d', $ticket['ticket_action']['createtime']) == date('d')) && (date('m', $ticket['ticket_action']['createtime']) == date('m')) && (date('Y', $ticket['ticket_action']['createtime']) == date('Y'))) {
        echo '<b>'.$this->translate['today'].'&nbsp;-&nbsp;'.date("H:i", $ticket['ticket_action']['createtime']).'</b>';
    } elseif ((date('d', $ticket['ticket_action']['createtime']) == date('d')-1)  && (date('m', $ticket['ticket_action']['createtime']) == date('m')) && (date('Y', $ticket['ticket_action']['createtime']) == date('Y'))) {
        echo '<b>'.$this->translate['yesterday'].'&nbsp;-&nbsp;'.date("H:i", $ticket['ticket_action']['createtime']).'</b>';
    } else {
        echo date("d. M. y", $ticket['ticket_action']['createtime']).'&nbsp;-&nbsp;'.date("H:i", $ticket['ticket_action']['createtime']);
    }
    echo '<br>'.$this->translate[$ticket['ticket_action']['type']].' '.$this->translate['from'].': '.$ticket['ticket_action']['user'];
} else {
                                echo '&nbsp;<br>&nbsp;';
                            } ?>
						</td>
		    			<td><?php echo $status_fields[$ticket['status']]; ?></td>
		                <td align="center"><?php echo isset($priorities_fields[$ticket['priority']]) ? $priorities_fields[$ticket['priority']] : ''; ?></td>
		                <td align="center"><?php echo number_format($ticket['duration'], 2, ',', ''); ?></td>
		                <td>
							<?php if (!empty($ticket['category'])) {
                                echo $ticket['category'];
                            } else {
                                echo '&nbsp;';
                            } ?>
						</td>
		                <td style="white-space:nowrap">
							<?php  if ((date('d', $ticket['createtime']) == date('d')) && (date('m', $ticket['createtime']) == date('m')) && (date('Y', $ticket['createtime']) == date('Y'))) {
                                echo $this->translate['today'].'&nbsp;-&nbsp;'.date("H:i", $ticket['createtime']);
                            } elseif ((date('d', $ticket['createtime']) == date('d')-1) && (date('m', $ticket['createtime']) == date('m')) && (date('Y', $ticket['createtime']) == date('Y'))) {
                                       echo $this->translate['yesterday'].'&nbsp;-&nbsp;'.date("H:i", $ticket['createtime']);
                                   } else {
                                       echo date("d. M. Y", $ticket['createtime']).'&nbsp;-&nbsp;'.date("H:i", $ticket['createtime']);
                                   } ?>
						</td>
                        <td style="white-space:nowrap">
                            <?php if($ticket['last_activity'] > 0) {
                                echo date("d. M. Y", $ticket['last_activity']).'&nbsp;-&nbsp;'.date("H:i", $ticket['last_activity']);
                            }
                            else {
                                echo '';
                            }?>
                        </td>
		            </tr>
            	<?php endforeach; ?>
        	</table>
		<?php else : ?>
			<div class="error">
		        <table>
		            <tr>
		                <td><span><?php echo $this->translate['err_no_ticket_found']; ?></span></td>
		            </tr>
		        </table>
			</div>
		<?php endif; ?>

		</div>
	</div>
