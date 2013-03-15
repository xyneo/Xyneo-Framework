<?php if ( ! defined('XYNEO') ) die("Direct access denied!");

class XyneoApplication
{
    private static $created_controller;
    
    private static $called_method;
    
    private static $passed_values = array();
    
    private static $layout;
    
    public static function setControllers($array)
    {
        
        self::$created_controller = $array[0];
        
        self::$called_method      = $array[1];
        
        self::$passed_values      = $array[2];
        
    }
    
    public static function getControllers()
    {
        
        return
        $controllers = array(
            'created_controller' => self::$created_controller,
            'called_method'      => self::$called_method,
            'passed_values'      => self::$passed_values
        );
        
    }
    
    public static function setLayout($layout)
    {
        
        if($layout)
            
            self::$layout = $layout;
        
        else
            
            self::$layout = "";
        
    }
    
    public static function getLayout()
    {
        
        return self::$layout;
        
    }
    
    
    
    public static function getRootUrl()
    {
        $root_url = "http";
        
        if ( isset( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) == 'on' )
            
            $root_url.= 's';
        
        $root_url.= "://";
        
        if ($_SERVER['SERVER_PORT'] != "80")
            
            $root_url.= $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF'];
        
        else
            
        $root_url.= $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
        
        $root_url= rtrim($root_url,"xyneo.php");
        
        return $root_url;
    }
    
    
}
