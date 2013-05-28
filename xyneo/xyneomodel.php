<?php if ( ! defined('XYNEO') ) die("Direct access denied!");

class XyneoModel
{

// Instantiate the XyneoDatabaseClass

    public function __construct()
    {
        
        if(DB_ALLOW == 'on')
            
            $this -> db         = new XyneoDataBase();
            $this -> validate   = new XyneoValidate();
            $this -> file       = new XyneoFile();
        
    }
    
    
    
    protected function xSetMessage($msg = false)
    {
    
        if ($msg)
        {
            
            $_SESSION['xyneomessage'] = $msg;
            
        }
        
        else
        {
            
            $_SESSION['xyneomessage'] = "ERROR";
            
        }
        
    }

}
