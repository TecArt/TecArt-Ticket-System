<?php

class BaseController
{
    public $registry;
    public $baseUrl;
    public $view;
    public $translate;

    public function __construct($registry)
    {
        $this->registry     = $registry;

        $this->view         = $registry->layout;

        $this->translate = $registry->lang;

        $this->baseUrl     = $registry->baseUrl;

        $this->check_crm_compatible();
        
        $this->check_partners();
    }

    private function check_crm_compatible()
    {
        $crm_version = '';
        if (isset($_SESSION["crm_version"])) {
            $crm_version = $_SESSION["crm_version"];
        } else {
            try {
                $crmLogon  = new SOAPClient($this->registry->config['webservice_url']."soap/index.php?op=logon&wsdl");

                $session_id = $crmLogon->crmLogin($this->registry->config['username'], $this->registry->config['password']);

                $crm_version = $crmLogon->crmgetVersion($session_id);
            } catch (Exception $e) {
                log_error('Error: crmLogin, crmgetVersion : '.$e->getMessage());
                die('Error by connecting to CRM-System. Please read the error log for more details.');
            }
        }

        $compatible_version = '3.7.8870';
        //$compatible_version = '4.0.10357';

        if (version_compare($crm_version, $compatible_version, '>=')) {
            return true;
        } else {
            die(' CRM Version is not compatible with ticket system.<br> Please use CRM with version <b>'.$compatible_version.'</b> or higher.');
        }
    }
    
    protected function check_partners()
    {
        $this->view->partners_enabled = (isset($_SESSION["partner_access"]) && $_SESSION["partner_access"] && isset($_SESSION['partner_contract']) && $_SESSION['partner_contract']) ? true : false;
    }
}
