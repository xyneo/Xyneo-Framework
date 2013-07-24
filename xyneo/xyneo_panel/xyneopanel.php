<?php

class XyneoPanel extends XyneoController
{
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function xyneo()
    {
        $this->view ->xRender('');
    }
    
    public function saveProjectData()
    {
        $this->model->saveProjectData();
    }
    
    public function refreshLayouts()
    {
        $this->model->refreshLayouts();  
    }
    
    public function createController()
    {
        $this->model->createController();  
    }
    
    public function createLayout()
    {
        $this->model->createLayout(); 
    }
    
    public function createHelper()
    {
        $this->model->createHelper();
    }
    
}