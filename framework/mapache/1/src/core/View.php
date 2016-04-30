<?php

namespace Mapache;

class View {

    private $module;
    private $subModule;
    private $menuHeaders;
    private $subMenuHeaders;
    
    
    function __construct() {}
    
    /**
     * 
     * render a view without IIDS framework
     */
    function rawRender($name)
    {
        // render view
        require 'src/' . $this->module.'/views/'.$name;
    }
    
    /**
     * Render defined view from module views
     * @param String $name name of view to be render
     * @param boolean $includeHeaders Specify if headers should be loaded in rendered view
     */
    function render($name, $includeHeaders = true)
    {
        global $mapacheConfig;
        // render view
        require $mapacheConfig['views_path'].$name;
    }

}
?>
