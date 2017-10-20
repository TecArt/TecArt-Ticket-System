	<script>
		var activityURL = "<?php echo $this->baseUrl; ?>?co=ticket/show_activities&tid=<?php echo $ticket->id;?>";

		function passForm()
		{
		    var noteBuff  = document.getElementById("notes_bak");
		    var noteSrc   = document.getElementById("notes_txt");
		    var form      = document.getElementById("uploadform");

		    noteBuff.value = noteSrc.value;
		    form.submit();
		}
	</script>

	<?php
        $url = '&action=true';
        if (isset($show_emails) && $show_emails) {
            $url.= '&email=true';
        }
        if (isset($show_calls) && $show_calls) {
            $url.= '&call=true';
        }
    ?>

	<div id="head">
		<div id="head-in" <?php if ($showCloseButton) : ?>class="ticket-title-close"<?php endif; ?>>
        	<?php if ($showCloseButton) : ?>
        	<form name="form_close" method="post"><input type="submit" name="close_ticket" value="<?php echo $this->translate['ticket_close']; ?>" class="button-ticket"></form>
        	<?php endif; ?>
			<h1><?php echo $this->translate['subject'].': '.$ticket->subject; ?></h1>			
		</div>
	</div>

    <div id="datas">
		<div id="datas-in">
            <table class="form">
                <tr>
                    <td width="120"><?php echo $this->translate['tnumber']; ?>:</td>
                    <td><?php echo $ticket->tnumber; ?></td>
                    <td width="120"><?php echo $this->translate['create_date']; ?>:</td>
                    <td><?php echo date("d.M.y", $ticket->createtime).'&nbsp; - &nbsp;'.date("H:i", $ticket->createtime); ?></td>
                </tr>
                <tr>
                    <td><?php echo $this->translate['questioner']; ?>:</td>
                    <td><?php echo $ticket->name; ?></td>
                	<td><?php echo $this->translate['email']; ?>:</td>
                    <td><?php echo $ticket->email; ?></td>
                </tr>
                <tr>
                    <td><?php echo $this->translate['priority']; ?>:</td>
                    <td><?php echo isset($priorities_fields[$ticket->priority]) ? $priorities_fields[$ticket->priority] : ''; ?></td>
                	<td><?php echo $this->translate['status']; ?>:</td>
                    <td><?php echo $status_fields[$ticket->status]; ?></td>
                </tr>
                <tr>
                    <td><?php echo $this->translate['duration']; ?>:</td>
                    <td><?php echo number_format($total_duration, 2, ',', ''); ?> &nbsp; h</td>
                    <?php if (!empty($ticket->sectionName)) : ?>
	                    <td><?php echo $this->translate['tsection']; ?>:</td>
	                    <td><?php echo $ticket->sectionName; ?></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td style="vertical-align:top"><?php echo $this->translate['description']; ?>:</td>
                    <td colspan="3"><pre><?php echo $ticket->notes; ?></pre></td>
                </tr>
            </table>
		</div>
	</div>

	<?php if ($ticket->status != 3) : ?>

	<div id="head-middle">
		<div id="head-in">
			<h1><?php echo $this->translate['file_upload'].': '; ?></h1>
		</div>
	</div>

	<div id="datas">
		<div id="datas-in">
            <table class="form">
                <tr>
                    <td width="120" style="background:#fff" align="center">
                        <img src="../public/pics/upload.gif" style="padding:2px 5px 0px 0px">
                    </td>
					<td width="120">
    					<form id="uploadform" enctype="multipart/form-data" action="<?php echo $this->baseUrl;?>?co=ticket/upload_doc" method="post">
                        	<input type="hidden" name="ticket_id" value="<?php echo $ticket->id; ?>">
                        	<input id="notes_bak" type="hidden" name="notes_bak" value="" />
                            <input type="file" name="file">
                        </form>
                    </td>
					<td>
                        <input type="button" value="<?php echo $this->translate['upload']; ?>" class="button-ticket" onmouseup="passForm();">
                    </td>
                </tr>
            </table>
			<?php if (!empty($docs)) {
        ?>
            <table class="tickets">
                <tr>
                    <th width="250"><?php echo $this->translate['name']; ?></th>
                    <th width="250"><?php echo $this->translate['type']; ?></th>
                    <th width="250"><?php echo $this->translate['size']; ?></th>
                </tr>
                <?php foreach ($docs as $doc) {
            ?>
                <tr>
                    <td><a href='<?php echo $this->baseUrl; ?>?co=ticket/download_doc&tid=<?php echo $ticket->id; ?>&name=<?php echo rawurlencode($doc['name']); ?>'><?php echo $doc['name']; ?></a></td>
                    <td><?php echo $doc['type']; ?></td>
                    <td><?php echo $doc['size']; ?>&nbsp;kB</td>
                </tr>
                <?php
        } ?>
            </table>
			<?php
    }?>
        </div>
    </div>
    
    <?php endif; ?>

    <div id="head-middle">
		<div id="head-in">
			<h1><?php echo $this->translate['activity']; ?>:</h1>
		</div>
        <div>
			<div class="frame-navi">
                <form name="form_activities">
                    <div class="frame-navi-input">
	                    <input type="checkbox" name="checkbox1" onClick="show_activities()" id="checkbox1" checked>
	                    <label for="checkbox1"><?php echo $this->translate['show_ticket_actions']; ?></label>
                    </div>
                    <?php if (isset($show_emails) && $show_emails) {
        echo '<div class="frame-navi-input"><input type="checkbox" name="checkbox2" onClick="show_activities()" id="checkbox2" checked><label for="checkbox2">'.$this->translate['show_ticke_emails'].'</label></div>';
    } ?>
                    <?php if (isset($show_calls) && $show_calls) {
        echo '<div class="frame-navi-input"><input type="checkbox" name="checkbox3" onClick="show_activities()" id="checkbox3" checked><label for="checkbox3">'.$this->translate['show_ticke_calls'].'</label></div>';
    } ?>
                </form>
            </div>
		</div>
	</div>

	<div id="datas">
		<div id="datas-in" style="height:400px">
        	<iframe frameborder="0" style="height:400px;width:100%;margin:0px;position:relative" id="activities" src="<?php echo $this->baseUrl; ?>?co=ticket/show_activities&tid=<?php echo $ticket->id.$url;?>"></iframe>
     	</div>
    </div>

    <?php if ($ticket->status != 3) {
        ?>
    	<div id="head-middle">
    		<div id="head-in">
    			<h1><?php echo $this->translate['post_reply'].': '; ?></h1>
    		</div>
    	</div>

    	<form action="<?php echo $this->baseUrl; ?>?co=ticket/post_ticket_action" method="post">
	        <div id="datas">
	            <div id="datas-in">
	                <table class="form">
	                    <tr>
	                        <td>
	                            <textarea id="notes_txt" name="notes" cols="70" rows="7" style="width:100%" wrap="off"><?php if (isset($notes_bak)) {
            echo $notes_bak;
        } ?></textarea>
	                            <input type="hidden" name="ticket_id" value="<?php echo $ticket->id; ?>">
	                        </td>
	                	</tr>
	                	<tr>
	                        <td>
	                            <input type="submit" value="<?php echo $this->translate['post']; ?>" class="button-ticket">
	                        </td>
	                	</tr>
	                </table>
	            </div>
	        </div>
    	</form>
	<?php
    } ?>
