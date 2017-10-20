<?php

include __SITE_PATH . '/application/' . 'BaseController.class.php';

class SecureController extends BaseController
{
    public function __construct($registry)
    {
        if (!isset($_SESSION["crm_session_id"]) || $_SESSION["login"] !== true) {
            header("Location: ".$this->baseUrl."?co=auth/index");
            exit;
        }

        parent::__construct($registry);
    }
}
