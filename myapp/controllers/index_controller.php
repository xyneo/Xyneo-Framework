<?php

/*
* Xyneo default index controller.
*/

class Index_Controller extends XyneoController
{
    function __construct(){
        parent::__construct();
    }
    
    function xyneo()
    {
        $this->view->welcome="Welcome to Xyneo Framework 1.0";
        $this->view->page_title = "Welcome to Xyneo Framework";
        $this->view->xRender('index/index','xyneo');
    }
}