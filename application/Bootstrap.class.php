<?php

    ini_set('display_errors', 'Off');
    error_reporting(E_ALL);
    /*** webservice cache on ***/
    ini_set("soap.wsdl_cache_enabled", "0");
    /*** set timezone ***/
    date_default_timezone_set('Europe/Berlin');
    /*** start session ***/
    session_start();

    /*** define the site path ***/
    $site_path = substr(realpath(dirname(__FILE__)), 0, -12);
    define('__SITE_PATH', $site_path);

    /*** include the error log class ***/
    include_once __SITE_PATH . '/application/' . 'Error.class.php';
    /*** include the registry class ***/
    include __SITE_PATH . '/application/' . 'Registry.class.php';
    /*** include the router class ***/
    include __SITE_PATH . '/application/' . 'Router.class.php';
    /*** include the template class ***/
    include __SITE_PATH . '/layout/' . 'main.class.php';
    require_once __SITE_PATH . '/application/models/Rest/Client.php';

    /*** registry object ***/
    $registry = new registry();

    /*** set config on registry ***/
    $registry->setconfig();
    /*** set base Url on registry ***/
    $registry->baseUrl = $_SERVER['SCRIPT_NAME'];
    /*** include the language file ***/
    include __SITE_PATH . '/lang/' . $registry->config['language'].'.php';
    /*** set language variable on registry ***/
    $registry->lang = $lang;

    /*** load the router ***/
    $registry->router = new router($registry);

    /*** set the controller path ***/
    $registry->router->setPath(__SITE_PATH . '/application/controllers');

    /*** load the main template and set on registry ***/
    $registry->layout = new main($registry);

    Rest_Client::init($registry->config);

    /*** load, run the controller ***/
    $registry->router->loader();
