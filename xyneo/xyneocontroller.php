<?php if ( ! defined('XYNEO') ) die("Direct access denied!");

// This is the base controller

class XyneoController
{
    function __construct(){
        
        $model = XyneoApplication::getControllers();
        
        $this->loadModel($model['created_controller']);
        
        $this->view = new XyneoView();
    }
   
// This method allows developers to load any model.
    
    public function loadModel($name, $property = false){
        
        if( ! $property )
            
            $property = 'model';
       
       if ($name == "xyneopanel")
           
           $path = 'xyneo/xyneo_panel/'.$name.'_model.php';
       
       else
        
            $path = 'myapp/models/'.$name.'_model.php';
        
       if(file_exists($path))
       {
            require_once $path;
            
            $modelName = $name.'_model';
            
            $this -> $property = new $modelName();
            
       }
       
       if( $property != 'model' and ! file_exists($path) )
       {
           
           die('The model, "'.$name.'" you try to load, doesnt exist!');
           
       }
         
    }
}