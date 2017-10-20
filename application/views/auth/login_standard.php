<!DOCTYPE html>
<html>

    <head>
    
        <meta charset="UTF-8">
        <meta name="publisher" content="tecart.de">
        <meta name="copyright" content="(c) tecart.de">
        <meta name="description" content="...">
        <meta name="robots" content="noindex,nofollow">
        
    	<title>Ticket-Webinterface</title>
    
        <link rel="shortcut icon" type="image/x-icon" href="../public/pics/favicon.ico">
        <link rel="stylesheet" href="../public/style/style-standard.css">
        <!--[if IE 7]><link rel="stylesheet" href="../public/style/ie7only.css"><![endif]-->
        <!--[if IE 8]><link rel="stylesheet" href="../public/style/ie8only.css"><![endif]-->
    
    </head>

	<body>
	
		<?php if (isset($error_msg)) {
    include(__SITE_PATH.'/application/views/common/error_message.php');
} ?>
	
		<div class="container">
			<div class="login">
	                
		    	<div class="login-box">
		        	<div class="login-top">
		            	<div class="login-logo"><a target="_blank" href="https://www.tecart.de" title="TecArt&reg;-CRM"><img src="../public/pics/login/ticket-web-icon.png" alt="Logo" title="TecArt&reg;-CRM"></a></div>
		                <h1><?php echo $this->translate['headline_standard']; ?></h1>
		           	</div>
		            <div class="login-middle">
		            	<form action="<?php echo $this->baseUrl;?>?co=auth/login" method="post">
		                            
		                	<table>
		                    	<tr>
		                       		<td class="f-width"><?php echo $this->translate['custom_number']; ?>:</td>
		                      	</tr>
		                        <tr>
		                        	<td><input name="number" type="text" size="30" maxlength="30" class="login-field"></td>
		                        </tr>
		                        <tr>
		                        	<td class="f-width"><?php echo $this->translate['password']; ?>:</td>
		                        </tr>
		                        <tr>
		                        	<td><input name="password" type="password" size="30" maxlength="40" class="login-field"></td>
		                       	</tr>
		                       	<tr>
		                       		<td><input type="submit" name="login" value="<?php echo $this->translate['login']; ?>" class="button"></td>
		                		</tr>
		                  	</table>
		                            
		         		</form>
		      		</div>
		   		</div>
		            
			</div>
        </div>
        
	</body>
	
</html>
