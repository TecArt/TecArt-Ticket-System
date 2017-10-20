<?php

include __SITE_PATH . '/application/BaseController.class.php';

class indexController extends BaseController
{
    public function index()
    {
        header("Location: ".$this->baseUrl."?co=auth/index");
        exit;
    }
}
