<?php

class main
{
    private $registry;
    public $baseUrl;
    public $translate;
    public $wishlistUrl;
    private $vars = array();


    public function __construct($registry)
    {
        $this->registry    = $registry;
        
        $this->baseUrl     = $registry->baseUrl;
        
        $this->translate   = $registry->lang;
        
        $this->wishlistUrl = isset($registry->config['partners']['enabled']) && $registry->config['partners']['enabled'] &&
                             isset($registry->config['partners']['wishlist_url'])
                           ? $registry->config['partners']['wishlist_url']
                           : false;
    }

    /**
    *
    * set undefined vars in an assosiativ array
    *
    * @param string $index
    *
    * @param mixed $value
    *
    * @return void
    *
    */
    public function __set($index, $value)
    {
        $this->vars[$index] = $value;
    }

    /**
    *
    * get vars from an assosiativ array
    *
    * @param string $index
    *
    * @return mixed
    *
    */
    public function __get($index)
    {
        return $this->vars[$index];
    }

    /**
     * render the view
     *
     * @param string $name (template name)
     * @return boolean
     */
    public function render($name)
    {
        $path = __SITE_PATH . '/application/views' . '/' . $this->registry->controller . '/' . $name . '.php';
            
        if (file_exists($path) == false) {
            throw new Exception('Template not found in '. $path);
            return false;
        }
        
        // Load variables
        foreach ($this->vars as $key => $value) {
            $$key = $value;
        }

        /*** include the header file ***/
        if (strpos($name, 'login') === false) { // not for the login template
            include(__SITE_PATH.'/application/views/common/header.php');
        }
        
        /*** include the navigation file ***/
        if (isset($navi)) {
            include(__SITE_PATH.'/application/views/common/top_navi.php');
        }
        
        /*** include the error message file ***/
        if (isset($error_msg) && strpos($name, 'login') === false) {
            include(__SITE_PATH.'/application/views/common/error_message.php');
        }

        /*** include the controller file ***/
        include($path);

        /*** include the fuss note file ***/
        if (isset($under_note)) {
            include(__SITE_PATH.'/application/views/common/under_note.php');
        }

        /*** include the footer file ***/
        if (strpos($name, 'login') === false) { // not for the login template
            include(__SITE_PATH.'/application/views/common/footer.php');
        }

        return true;
    }
    
    /**
     * render the view for frame
     *
     * @param string $name (template name)
     * @return boolean
     */
    public function render_frame($name)
    {
        $path = __SITE_PATH . '/application/views' . '/' . $this->registry->controller . '/' . $name . '.php';

        if (file_exists($path) == false) {
            throw new Exception('Template not found in '. $path);
            return false;
        }

        /*** include the header file ***/
        include(__SITE_PATH.'/application/views/common/header.php');
            
        // Load variables
        foreach ($this->vars as $key => $value) {
            $$key = $value;
        }

        /*** include the error message file ***/
        if (isset($error_msg)) {
            include(__SITE_PATH.'/application/views/common/error_message.php');
        }

        /*** include the controller file ***/
        include($path);
        

        /*** include the footer file ***/
        include(__SITE_PATH.'/application/views/common/footer.php');
        
        return true;
    }
    
    /**
     * render the view for ajax request
     *
     * @param string $name (template name)
     * @return boolean
     */
    public function render_ajax($name)
    {
        $path = __SITE_PATH . '/application/views' . '/' . $this->registry->controller . '/' . $name . '.php';

        if (file_exists($path) == false) {
            throw new Exception('Template not found in '. $path);
            return false;
        }
            
        // Load variables
        foreach ($this->vars as $key => $value) {
            $$key = $value;
        }

        /*** include the controller file ***/
        include($path);
        
        return true;
    }
}
