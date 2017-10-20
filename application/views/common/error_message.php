	
	<div class="msg" id="close-layer">
	
    	<div class="msg-disable-layer"></div>
    	
        <table class="f-width f-height center">
            <tr>
                <td class="f-width f-height center">
                	<?php if (isset($msg_type) && $msg_type == 'notice') {
    ?>
	                    <table class="msg-layer success">
	                        <tr>
	                            <td class="p-none">
	                                <div class="layer-inner-div">
                                		<table class="table-level-1">
	                                        <tr>
	                                            <td class="msg-image f-height"></td>
	                                            <td>
	                                            	<?php echo $error_msg; ?>
	                                            </td>
	                                        </tr>
	                                        <tr>
	                                            <td colspan="2" class="center p-none">
	                                                <div class="msg-footer center">
	                                                    <a href="javascript:void(0);" onClick="setVisibility('close-layer', 'none');" class="msg-button"><?php echo $this->translate['err_ok']; ?></a>
	                                                </div>
	                                            </td>
	                                        </tr>
	                                    </table>
	                                </div>
	                            </td>
	                        </tr>
	                    </table>
                    <?php
} else {
        ?>
	                   	<table class="msg-layer warning">
	                        <tr>
	                            <td class="p-none">
	                                <div class="layer-inner-div">
	                                    <table class="table-level-1">
	                                        <tr>
	                                            <td class="msg-image warning f-height"></td>
	                                            <td>
	                                            	<?php echo $error_msg; ?>
	                                            </td>
	                                        </tr>
	                                        <tr>
	                                            <td colspan="2" class="center p-none">
	                                                <div class="msg-footer center">
	                                                    <a href="javascript:void(0);" onClick="setVisibility('close-layer', 'none');" class="msg-button"><?php echo $this->translate['err_ok']; ?></a>
	                                                </div>
	                                            </td>
	                                        </tr>
	                                    </table>
	                                </div>
	                            </td>
	                        </tr>
	                    </table>
	              	<?php
    }?>
                </td>
            </tr>
        </table>
        
    </div>
    
    <script language="JavaScript">
		function setVisibility(id, visibility) {
		document.getElementById(id).style.display = visibility;
		}
	</script>
	