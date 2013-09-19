<?php if ( ! defined('XYNEO') ) die("Direct access denied!");

class XyneoConfig
{  
    public function __construct()
    {
        
    }
    
    public function checkConfigFile()
    {       
        $list = array (

            'URL',
            'START_PAGE',
            'ERROR_PAGE',
            'SHUTDOWN_PAGE',
            'LAYOUT_DOCTYPE',
            'LAYOUT_CHARSET',
            'LAYOUT_LANGUAGE',
            'AUTO_COMMENT_PHP_FILES',
            'AUTO_COMMENT_JS_FILES',
            'AUTO_COMMENT_CSS_FILES',
            'AUTO_COMMENT_XYNEO_FILES',
            'SHUT_DOWN_SITE',
            'DEVELOPER_MODE',
            'START_SESSION_ON_LOAD',
            'DB_ALLOW',
            'DB_DRIVER',
            'DB_HOST',
            'DB_USE',
            'DB_USER',
            'DB_PASSWORD',
            'APPID',
            'SECRET'

        );
        
        foreach ($list as $option) {
            if (!defined($option) ) {
                die("Your config file is damaged. ".$option." is not defined.");
            }           
        }
        
        $available_charsets = mb_list_encodings();
        
        if (!in_array(LAYOUT_CHARSET,$available_charsets)) {
            die("Invalid value for LAYOUT_CHARSET. Chech your config file");
        }
    }
    
}
