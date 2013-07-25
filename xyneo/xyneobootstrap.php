<?php if ( ! defined('XYNEO') ) die("Direct access denied!");

class XyneoBootstrap
{
    public function __construct()
    {        
        // Check if the site has been shut down
        switch (SHUT_DOWN_SITE) {
            case 'on' : 
                $controller_name = SHUTDOWN_PAGE."_controller";
                $controller_file = "myapp/controllers/".$controller_name.".php";
                if (!file_exists($controller_file)) { 
                    die("This site has been shut down");   
                } else {
                    require_once $controller_file;
                    $controller = new $controller_name();
                    
                    if(!method_exists($controller, 'xyneo')) {
                        die(' Missing xyneo() method from the default shut down
                             site controller. ');
                    }
                    $controller->xyneo();
                    die();
                }
                break;
           case 'off' :
                break;
           default :
               die("Bad value for SHUT_DOWN_SITE. Please check your 
                            config file!");
               break; 
        }
        
// Start session accordig to the config file
        
        switch (START_SESSION_ON_LOAD) {
            case 'on':
                session_start();
                break;
            case 'off':
                break;
            default:
                die("Bad value for START_SESSION_ON_LOAD. Please check your 
                            config file!");
                break;  
        }
        
// Get URL parts
        
        $url_base = isset($_GET['url']) ? $_GET['url'] : null;
        $url = XyneoRoute::parseRoute($url_base);
        $urlexp = explode('/',trim($url,'/'));
        $num = sizeof($urlexp);
        
// Store values from URL
        
        $cont = !empty($urlexp[0]) ? $urlexp[0] : START_PAGE;
        $method = isset($urlexp[1]) ? $urlexp[1] : "xyneo";
        $params = array();
        
        for ($i=2; $i<$num; $i++) {
            $j = $i-2;
            $params[$j] = !empty($urlexp[$i]) ? $urlexp[$i] : null;
        }
        
// Instantiate and call controller
        
        if ($cont == "xyneopanel") {
            $controller_name = $cont;
            $controller_file = "xyneo/xyneo_panel/".$controller_name.".php";  
        } else {
            $controller_name = $cont."_controller";
            $controller_file = "myapp/controllers/".$controller_name.".php";
        }
        
        if (!file_exists($controller_file)) {  
            $cont = ERROR_PAGE;
            $controller_name = $cont."_controller";  
        }
        
        XyneoApplication::setControllers(array(  
            0 => $cont,
            1 => $method,
            2 => $params
        ));
        
        if ($cont != "xyneopanel") {
            $controller_file = "myapp/controllers/".$controller_name.".php";
        }
        if (!file_exists($controller_file)) {
            die('This is an ERROR message. You can set a default error page in
                the config file.');
        }
        
        require_once $controller_file;
        $controller = new $controller_name();
        
// Handle the called method and its arguments.
        
            if (method_exists($controller_name,$method)) {
                if (!empty($params)) {
                    $status = "arg";
                } else {
                    $status = "method";
                }
            } else {
                $status = "method"; 
                $method = 'xyneo';
            }
        if (!method_exists($controller, 'xyneo')) {
            die(' Missing xyneo() method from the '.$cont.' controller. ');
        }
        switch ($status) {
            case "method":
                $controller -> $method();
                break;
            case "arg":
                call_user_func_array(array($controller, $method), $params);
                break;
        }
        
    }
}
