<?php

/*
* Xyneo default error controller.
*/

class Error_Controller extends XyneoController
{
    function __construct(){
        parent::__construct();
    }
    
    function xyneo(){
        $this->view->page_title = "Error Page";
        $this->view->xRender('error/error','xyneo');
    }
}
