<?php if ( ! defined('XYNEO') ) die("Direct access denied!");

// This is the base controller

class XyneoController
{
    
    public function __construct()
    {
        $model = XyneoApplication::getControllers();
        $this->xLoadModel($model['created_controller']);
        $this->view = new XyneoView();
    }
   
// This method allows developers to load any model.
    
    public function xLoadModel($name, $property = false)
    {
        if (!$property) {
            $property = 'model';
        }
        if ($name == "xyneopanel") {
            $path = 'xyneo/xyneo_panel/'.$name.'_model.php';
        } else {
            $path = 'myapp/models/'.$name.'_model.php';
        }
        
       if (file_exists($path)) {
           require_once $path;
           $modelName = $name.'_model';
           $this->$property = new $modelName();   
       }
       
       if ($property != 'model' and !file_exists($path)) {
           die('The model, "'.$name.'" you try to load, doesnt exist!');
       }
         
    }
    
// Defining the controller request_method
    
    public function xHttp($method,$ajax=false)
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
        
        if (!$result) {
            ob_end_clean();
            header('HTTP/1.0 403 Forbidden');
            die('Acces forbidden 403');
        }  
    }
    
}
