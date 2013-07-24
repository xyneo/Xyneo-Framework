<?php

/*
* Xyneo default shutdown controller.
*/

class Shutdown_Controller extends XyneoController
{
    function __construct()
    {
        parent::__construct();       
    }
    
    function xyneo()
    {
        $this->view->page_title = "Shutdown Page";
        $this->view->xRender("shutdown/shutdown", "xyneo");
    }
}
