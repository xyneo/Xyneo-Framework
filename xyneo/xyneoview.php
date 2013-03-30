<?php if ( ! defined('XYNEO') ) die("Direct access denied!");

// This is the base view class

class XyneoView
{    
    
// The private url is set by the constructor. It detects the base URL or uses the
// URL given in the config.php file
    
    private $url;

// Detecting the base URL
    
    function __construct()
    {      
        if(URL == 'http://mydomain.com/')
        {
            
            $this -> url = XyneoApplication::getRootUrl();
            
        }
        else
        {
            
            $this -> url = URL;
            
        }
    }
    
// Rendering the page 
    
    public function xRender($name,$layout=false)
    {
        
        $cont = XyneoApplication::getControllers();
        
        $cont = $cont['created_controller'];
        
        if($cont == "xyneopanel") 
        {
            
            if(file_exists('xyneo/xyneo_panel/'.$cont.'_view.xyneo'))
            
                require_once 'xyneo/xyneo_panel/'.$cont.'_view.xyneo';
            
            else
                
                die('The '.$cont.' view that you are trying to render is
                    missing.');
            
            return true;
            
        }
        
        XyneoApplication::setLayout($layout);
        
        if($layout)
        {
            
            if(file_exists('myapp/layouts/'.$layout.'/layout_top.xyneo'))
                    
                require 'myapp/layouts/'.$layout.'/layout_top.xyneo';
            
            else
                
                die('A top layout element is missing. Check the 
                    layout_top.xyneo in '.$layout.'.');
            
            if(file_exists('myapp/views/'.$name.'.xyneo'))
            
                require_once 'myapp/views/'.$name.'.xyneo';
            
            else
                
                die('The '.$name.' view that you are trying to render is
                    missing.');
            
            switch(DEVELOPER_MODE)
            {
                
                case 'on' :
                    
                    $filename = 'xyneo/xyneo_panel/developer.xyneo';
                    
                    if(file_exists($filename))
                        
                        require $filename;
                    
                break;
                
                case 'off' :
                
                break;
            
                default :
                    
                    die('Bad value for DEVELOPER_MODE. Please check your 
                            config file!');
                
            }
            
            
            if(file_exists('myapp/layouts/'.$layout.'/layout_bottom.xyneo'))
                    
                require 'myapp/layouts/'.$layout.'/layout_bottom.xyneo';
            
            else
                
                die('A bottom layout element is missing. Check the 
                    layout_bottom.xyneo in '.$layout.'.');
            
        }
        
        else
        {
            
            if(file_exists('myapp/views/'.$name.'.xyneo'))
            
                require_once 'myapp/views/'.$name.'.xyneo';
            
            else
                
                die('The '.$name.' view that you are trying to render is
                    missing.');
            
            switch(DEVELOPER_MODE)
            {
                
                case 'on' :
                    
                    $filename = 'xyneo/xyneo_panel/developer.xyneo';
                    
                    if(file_exists($filename))
                        
                        require $filename;
                    
                break;
                
                case 'off' :
                
                break;
            
                default :
                    
                    die('Bad value for DEVELOPER_MODE. Please check your 
                            config file!');
                
            }
            
        }
    
    }
    
// Show error message
    
    protected function xShowMessage($msg_layout=false)
    {
    
        if(isset($_SESSION['xyneomessage']))
        {
            
            if($msg_layout)
            {
                $layout = explode('[:showMessage:]',$msg_layout);
                
                if (sizeof($layout) != 2 )
                    
                    die("The showMessage method cannot display this layout!");
                
                $msg = $layout[0].$_SESSION['xyneomessage'].$layout[1];
                
                echo $msg;
                
            }
            
            else
            {
                
                echo $_SESSION['xyneomessage'];
            
            }
            
            unset($_SESSION['xyneomessage']);
            
        }
        
    }
    
// Echos the base URL to the view
    
    protected function xUrl()
    {
        
        echo $this -> url;
        
    }

// Echos the base javascript path to the view
    
    protected function xJs()
    {
        
        echo $this -> url . 'public/javascript/';
        
    }
    
// Echos the base css path to the view
    
    protected function xCss()
    {
        
        echo $this -> url . 'public/stylesheets/';
        
    }

// Echos the base images path to the view
    
    protected function xImages()
    {
        
        echo $this -> url . 'public/images/';
        
    }
    
// Echos the layout javascript path to the view
    
    protected function xLJs()
    {
        
        echo $this -> url . 'public/javascript/'.XyneoApplication::getLayout().'_layout/';
        
    }
 
 // Echos the layout css path to the view
    
    protected function xLCss()
    {
        
        echo $this -> url . 'public/stylesheets/'.XyneoApplication::getLayout().'_layout/';
        
    }
    
 // Echos the layout css path to the view
    
    protected function xLImages()
    {
        
        echo $this -> url . 'public/images/'.XyneoApplication::getLayout().'_layout/';
        
    }
   
// Returns true if the current page is set to default index page
    
    protected function xIsIndex()
    {
        
        $controllers = XyneoApplication::getControllers();
        
        $method      = $controllers['called_method'];
        
        if(class_exists(START_PAGE."_controller") and $method == 'xyneo')
                
            return true;
        
        else
            
            return false;
        
    }
    
    protected function xIsError()
    {
        
        $controllers = XyneoApplication::getControllers();
        
        $method      = $controllers['called_method'];
        
        if(class_exists(ERROR_PAGE."_controller") and $method == 'xyneo')
                
            return true;
        
        else
            
            return false;
        
    }
    
    protected function xIsShutdown()
    {
        
        $controllers = XyneoApplication::getControllers();
        
        $method      = $controllers['called_method'];
        
        if(class_exists(SHUTDOWN_PAGE."_controller") and $method == 'xyneo')
                
            return true;
        
        else
            
            return false;
        
    }
    
    protected function xShorten($str,$length,$unit = false){
        
        if(!$unit)
            $unit = 'chars';
        
        switch($unit)
        {
            case 'chars' :
                    if(mb_strlen($str,LAYOUT_CHARSET)>$length){
                    $str=mb_substr($str,0,$length,LAYOUT_CHARSET);
                    $str.='...';
                }
                echo $str;
            break;
            
            case 'words' :
                    $words = explode(' ', $str);
                    if(sizeof($words)>$length){
                    $str=implode(' ',array_slice($words,0,$length));
                    $str.='...';
                }
                echo $str;
            break;
            
            default :
                echo 'Invalid value for unit. It must be empty or "chars" or "words"';
            break;
        }
    }
    
}