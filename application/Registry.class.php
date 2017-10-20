<?php

class Registry
{
    private $vars = array();


    /**
    * set undefined vars in an assosiativ array
    *
    * @param string $index
    * @param mixed $value
    * @return void
    */
    public function __set($index, $value)
    {
        $this->vars[$index] = $value;
    }

    /**
    * get variables from array
    *
    * @param mixed $index
    * @return mixed
    */
    public function __get($index)
    {
        return $this->vars[$index];
    }
     
    /**
     * check and load config from file
     *
     * @param array $config
     */
    public function setconfig()
    {
        $config = array();
        /*** include the config file ***/
        include __SITE_PATH . '/config/' . 'config.php';

        if (empty($config) || !isset($config)) {
            $data =
'<?php
    /** config **/
	$config["language"]         = "de";
	$config["webservice_url"]   = "";
	$config["username"]         = "";
	$config["password"]         = md5("");
	$config["login_template"]   = "login_standard";
	$config["logout_redirect"]  = "";
	$config["ticket_sections"]	= "";
?>';

            file_put_contents(__SITE_PATH.'/config/config.php', $data);
        }

        if (empty($config['language'])) {
            trigger_error("Error! no language found in config file");
            exit;
        }
        if (empty($config['webservice_url'])) {
            trigger_error("Error! no url found in config file");
            exit;
        }
        if (empty($config['username'])) {
            trigger_error("Error! no username found in config file");
            exit;
        }
        if (empty($config['password'])) {
            trigger_error("Error! no password found in config file");
            exit;
        }
        if (empty($config['login_template'])) {
            trigger_error("Error! no login template found in config file");
            exit;
        }
        if (empty($config['logout_redirect'])) {
            trigger_error("Error! no redirect url found in config file");
            exit;
        }
        
        $this->__set('config', $config);
    }
}
