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
        if (isset($_SESSION["crm_version"])) {
            $crm_version = $_SESSION["crm_version"];
        } else {
           $logon = new crmLogon();
           $crm_version = $logon->get_crm_version();
        }

        $compatible_version = '5';

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
