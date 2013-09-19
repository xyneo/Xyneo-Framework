<?php

/*
* Xyneo default error controller.
*/

class Error_Controller extends XyneoController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function xyneo()
    {
        $this->view->page_title = "Error Page";
        $this->view->xRender('error/error','xyneo');
    }
}
