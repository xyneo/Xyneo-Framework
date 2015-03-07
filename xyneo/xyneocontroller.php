<?php

if (! defined('XYNEO')) {
    throw new XyneoError("Direct access denied!");
}

/**
 * This is the base controller
 */
class XyneoController
{

    /**
     *
     * @var XyneoView
     */
    protected $view;

    /**
     *
     * @var XyneoForm
     */
    public $form;

    public $model;

    public function __construct()
    {
        $model = XyneoApplication::getControllers();
        $this->xLoadModel($model['created_controller']);
        $this->view = new XyneoView();
        $this->form = new XyneoForm();
    }

    /**
     * This method allows developers to load any model.
     *
     * @param string $name            
     * @param string $property            
     * @return void
     */
    public function xLoadModel($name, $property = false)
    {
        if (! $property) {
            $property = 'model';
        }
        if ($name == "xyneopanel") {
            $path = 'xyneo/xyneo_panel/' . $name . '_model.php';
        } else {
            $path = 'myapp/models/' . $name . '_model.php';
        }
        
        if (file_exists($path)) {
            require_once $path;
            $modelName = $name . '_model';
            $this->$property = new $modelName();
        }
        
        if ($property != 'model' and ! file_exists($path)) {
            die('The model, "' . $name . '" you try to load, doesn\'t exist!');
        }
        return;
    }

    /**
     * Defining the controller request_method
     *
     * @param string $method            
     * @param boolean $ajax            
     * @return void
     */
    public function xHttp($method, $ajax = false)
    {
        $result = 1;
        
        $real_method = strtolower($_SERVER['REQUEST_METHOD']);
        $set_method = strtolower($method);
        
        if ($real_method != $set_method) {
            $result = 0;
        }
        
        if ($ajax == true or $ajax == 'ajax') {
            if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) or strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
                $result = 0;
            }
        }
        
        if (! $result) {
            ob_end_clean();
            header('HTTP/1.0 403 Forbidden');
            die('Access forbidden 403');
        }
        
        return;
    }

    /**
     * Check if the request is comming from the xhr
     *
     * @return boolean
     */
    public function xIsXhr()
    {
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) or strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            return false;
        } else {
            return true;
        }
    }
}
