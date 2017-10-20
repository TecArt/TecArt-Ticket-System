	<?php if (!isset($is_ajax)) {
    ?><div id="document-list"><?php
} ?>
	
	<div id="head">
		<div id="head-in">
			<h1><?php echo $this->translate['document']; ?>: <?php if (!empty($folder)) {
        print $folder;
    } ?></h1>
		</div>
	</div>

	<div id="datas">
		<div id="datas-in">
            <table class="tickets">
                <tr>
                	<th class="mimetype"></th>
                    <th width="250"><?php echo $this->translate['name']; ?></th>
                    <th width="250"><?php echo $this->translate['type']; ?></th>
                    <th width="250"><?php echo $this->translate['size']; ?></th>
                    <th width="250"><?php echo $this->translate['edit']; ?></th>
                </tr>
                <?php if (!empty($backlink) && !empty($folder)) {
        ?>
                <tr>
                	<td class="mimetype mimetype-folder"></td>
                	<td><strong><a class="doc-link" href='<?php echo $this->baseUrl; ?>?co=partners/show&tree_path=<?php echo rawurlencode(base64_encode(str_rot13($backlink))); ?>' title="<?php echo $this->translate['levelup']; ?>">..</a></strong></td>
                	<td></td>
                	<td></td>
                	<td></td>
                </tr>                	
				<?php
    } ?>            
            	<?php if (!empty($docs)) {
        ?>	                
	                <?php foreach ($docs as $doc) {
            ?>
		                <?php if (isset($doc['isfolder']) && $doc['isfolder']) {
                ?>
		                <tr>
		                	<td class="mimetype mimetype-<?php echo $doc['icon']; ?>"></td>
		                	<td><strong><a class="doc-link" href='<?php echo $this->baseUrl; ?>?co=partners/show&tree_path=<?php echo rawurlencode(base64_encode(str_rot13($doc['path']))); ?>'><?php echo $doc['name']; ?></a></strong></td>
		                	<td><?php echo $this->translate['mime_folder']; ?></td>
		                	<td></td>
		                	<td><?php echo $doc['edittime'] ? date('d.m.Y H:i:s', $doc['edittime']) : ''; ?></td>
		                </tr>
		                <?php
            } else {
                ?>
		                <tr>
		                	<td class="mimetype mimetype-<?php echo $doc['icon']; ?>"></td>
		                    <td><a href='<?php echo $this->baseUrl; ?>?co=partners/download_doc&pid=<?php echo $pid; ?>&name=<?php echo rawurlencode(base64_encode(str_rot13($doc['path']))); ?>'><?php echo $doc['name']; ?></a></td>
		                    <td><?php echo $doc['type']; ?></td>
		                    <td><?php echo $doc['size']; ?>&nbsp;kB</td>
		                    <td><?php echo date('d.m.Y H:i:s', $doc['edittime']); ?></td>
		                </tr>
		                <?php
            } ?>
	                <?php
        } ?>
                <?php
    } else {
        ?>
		            <tr>
		            	<td></td>
		                <td colspan="4"><span><?php echo $this->translate['folder_empty']; ?></span></td>
		            </tr>	                
				<?php
    }?>
                <tr>
	                <td colspan="5">
	                	<?php if (!empty($backlink) && !empty($folder)) {
        ?>
	                	<button class="button-ticket" onclick="change_folder('<?php echo $this->baseUrl; ?>?co=partners/show&tree_path=<?php echo rawurlencode(base64_encode(str_rot13($backlink))); ?>');" ><?php echo $this->translate['levelup']; ?></button>	                	
	                	<?php
    } ?>
	                	<button class="button-ticket" onclick="change_folder('<?php echo $this->baseUrl; ?>?co=partners/show&tree_path=<?php echo rawurlencode(base64_encode(str_rot13($current)));?>');" ><?php echo $this->translate['refresh']; ?></button>
	                </td>
                </tr>
            </table>         
		</div>
	</div>
	
	<?php if (!isset($is_ajax)) {
        ?></div><?php
    } ?>