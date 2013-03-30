<?php

class XyneoPanel extends XyneoController
{
    
    function __construct() {
        parent::__construct();
    }
    
    function xyneo(){
        $this -> view  -> xRender('');
    }
    
    function saveProjectData(){
        
        $this->model->saveProjectData();
        
    }
    
    function refreshLayouts(){
        
        $this->model->refreshLayouts();
        
    }
    
    function createController(){
        
        $this->model->createController();
        
    }
    
    function createLayout(){
        
        $this->model->createLayout();
        
    }
    
    function createHelper(){
        
        $this->model->createHelper();
        
    }
    
}