	
	<div id="head">
		<div id="head-in">
			<h1><?php echo $this->translate['change_password']; ?></h1>
		</div>
	</div>
    
    <form action="<?php echo $this->baseUrl;?>?co=auth/change_password" method="post">
	    <div id="datas">
			<div id="datas-in">
	           	<table class="form">
	              	<tr>
	                  	<td width="170"><?php echo $this->translate['old_password']; ?>:</td>
	                  	<td><input name="old_password" type="password" size="30" maxlength="30" style="width:200px"></td>
	                </tr>
	              	<tr>
	                  	<td><?php echo $this->translate['new_password']; ?>:</td>
	                  	<td><input name="new_password" type="password" size="30" maxlength="40" style="width:200px"></td>
	                </tr>
	              	<tr>
	                  	<td><?php echo $this->translate['confirm_password']; ?>:</td>
	                  	<td><input name="confirm_password" type="password" size="30" maxlength="40" style="width:200px"></td>
	                </tr>
	                <tr>
	                  	<td></td>
	                  	<td><input type="submit" name="login" value="<?php echo $this->translate['change']; ?>" class="button-ticket"></td>
	                </tr>
	            </table>
			</div>
		</div>
	</form>