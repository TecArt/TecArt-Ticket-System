<!DOCTYPE html>
<html>

    <head>
    
        <meta charset="UTF-8">
        <meta name="publisher" content="tecart.de">
        <meta name="copyright" content="(c) tecart.de">
        <meta name="description" content="Testen Sie die das Trouble-Ticket-System verbunden mit dem TecArt-CRM via Webservice. Jetzt die Trouble-Ticket-System Live-Demo / Textversion starten.">
        <meta name="robots" content="noindex,nofollow">
        
    	<title>Live-Demo - TecArt-CRM Trouble-Ticket-Webinterface</title>
    
        <link rel="shortcut icon" type="image/x-icon" href="../public/pics/favicon.ico">
        <link rel="stylesheet" href="../public/style/style-tecart-demo.css">
        <!--[if IE 7]><link rel="stylesheet" href="../public/style/ie7only.css"><![endif]-->
        <!--[if IE 8]><link rel="stylesheet" href="../public/style/ie8only.css"><![endif]-->
    
    </head>

	<body>
	
		<?php if (isset($error_msg)) {
    include(__SITE_PATH.'/application/views/common/error_message.php');
} ?>
    
    	<div class="left">
        
            <div class="blog-head">
                <div class="blog-logo"><a href="https://www.tecart.de"><img src="../public/pics/login/tecart-logo.png" width="129" height="55" alt="TecArt GmbH - Hersteller der TecArt-CRM Software"></a></div>
                <div class="blog-title">Live-Demo</div>
                <div class="blog-subtitle">Das Ticket-Web- interface live erleben</div>
            </div>
            
            <div class="info-box"> 
                Erhalten Sie eine erste Vorstellung von der via Webservice gesteuerten <a target="_blank" href="https://www.tecart.de/crm-trouble-ticket-system"><strong>Ticket-Management-Lösung</strong></a> und davon, wie diese für Ihre Kunden zum Einsatz kommen könnte.  
            </div>
            
            <div class="info-box"> 
               	<strong>Sie haben Interesse am Einsatz des Trouble-Ticket-Systems?</strong>
                <br><br>
                Dann beantragen Sie eine kostenfreie <a target="_blank" href="https://www.tecart.de/30-tage-crm-testversion"><strong>Test-Version für 30 Tage</strong></a>.  
            </div>
            
        </div>
        
        <div class="right">
                    
            <div class="login">
                
                <div class="login-box">
                    <div class="login-top">
                        <div class="login-logo"><a target="_blank" href="https://www.tecart.de" title="TecArt&reg;-CRM"><img src="../public/pics/login/ticket-web-icon.png" alt="Logo" title="TecArt&reg;-CRM"></a></div>
                        <h1><?php echo $this->translate['headline']; ?></h1>
                    </div>
                    <div class="login-middle">
                        <form action="<?php echo $this->baseUrl;?>?co=auth/login" method="post">
                            
                            <table>
                                <tr>
                                    <td class="f-width"><?php echo $this->translate['custom_number']; ?>:</td>
                                </tr>
                                <tr>
                                    <td>
                                    	<div class="login-inner"> 
	                                        <input name="number" value="10022" type="text" size="30" maxlength="30" class="login-field">
	                                        <a class="tooltip">
	                                            <img align="right" alt="Hilfe" src="../public/pics/login/help.png">
	                                            <span><?php echo $this->translate['custom_number']; ?>: <strong>10022</strong></span>
	                                        </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="f-width"><?php echo $this->translate['password']; ?>:</td>
                                </tr>
                                <tr>
                                	<td>
                                    	<div class="login-inner"> 
	                                        <input name="password" value="testuser" type="password" size="30" maxlength="40" class="login-field">
	                                        <a class="tooltip">
	                                            <img align="right" alt="Hilfe" src="../public/pics/login/help.png">
	                                            <span><?php echo $this->translate['password']; ?>: <strong>testuser</strong></span>
	                                        </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="submit" name="login" value="Live-Demo starten" class="button"></td>
                                </tr>
                            </table>
                            
                        </form>
                    </div>
                </div>
                <div class="login-info">
                    <strong>Innovative Funktionsweise</strong>:
                    Die Daten werden zwischen dem TecArt-CRM und der Weboberfläche via <a target="_blank" href="https://www.tecart.de/crm-webservices">Webservice-Schnittstelle</a> übergeben. Auf diesem Weg werden alle Änderungen zwischen beiden Komponenten des Ticket-Systems live synchronisiert. 
                </div>
            
            </div>
        
        </div>
        
        <div class="footer">
            <div class="footer-left">
                <ul class="menu">
                    <li><a href="https://www.tecart.de/impressum">Impressum</a></li>
                    <li><a href="https://www.tecart.de/datenschutz">Datenschutz</a></li>
                    <li><a href="https://www.tecart.de/agb">AGB</a></li>
                    <li><a href="https://www.tecart.de/kontakt">Kontakt</a></li>
                </ul>
            </div>
            
            <div class="footer-right">
                Copyright &copy; by TecArt GmbH
            </div>
        </div>

	</body>
	
</html>
