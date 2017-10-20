<?php

class Router
{
    /**
    * the registry object
    */
    private $registry;

    private $path;

    public $file;

    public $controller;

    public $action;

    public function __construct($registry)
    {
        $this->registry = $registry;
    }


    /**
    *
    * set controller directory path
    *
    * @param string $path
    *
    * @return void
    *
    */
    public function setPath($path)
    {
        /*** check if path is a directory ***/
        if (is_dir($path) == false) {
            throw new Exception('Invalid controller path: `' . $path . '`');
        }

        $this->path = $path;
    }
    
    /**
    *
    * load the controller
    *
    * @access public
    *
    * @return void
    *
    */
    public function loader()
    {
        /*** check the route ***/
        $this->getController();

        /*** include the controller ***/
        include $this->file;

        /*** a new controller class instance ***/
        $class = $this->controller . 'Controller';

        $controller = new $class($this->registry);

        /*** check if the action is callable ***/
        if (!is_callable(array($controller, $this->action))) {
            $action = 'index';
        } else {
            $action = $this->action;
        }

        /*** run the action ***/
        $controller->$action();
    }
    
    /**
    *
    * get controller
    *
    * @access private
    *
    * @return void
    *
    */
    private function getController()
    {
        /*** get Params ***/
        $this->registry->Params = $_GET;

        if (empty($this->registry->Params['co'])) {
            header("Location: ".$this->registry->baseUrl."?co=ticket/show&type=current");
            exit;
        }

        /*** get the controller from url ***/
        $route = $this->registry->Params['co'];

        /*** set the controller and action ***/
        $parts = explode('/', $route);
        $this->controller = isset($parts[0]) ? strtolower($parts[0]) : 'index';
        $this->action     = isset($parts[1]) ? strtolower($parts[1]) : 'index';

        /*** set the file path ***/
        $this->file = $this->path .'/'. $this->controller . 'Controller.php';

        /*** if the file is not there, then go to index ***/
        if (is_readable($this->file) == false) {
            $this->controller = 'ticket';
            $this->action     = 'show';
            $this->file = $this->path .'/'. $this->controller . 'Controller.php';
        }

        /*** set the controller and action name on registry ***/
        $this->registry->controller = $this->controller;
        $this->registry->action     = $this->action;

        return true;
    }
}
