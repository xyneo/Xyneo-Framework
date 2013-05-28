<?php if ( ! defined('XYNEO') ) die("Direct access denied!");

// This is the Xyneo Framework inbuilt form validation class

class XyneoValidate
{
    
    // Validating email address
    
    public function xValidEmail($email)
    {
        
        if(empty($email))
            
            die('No email given to validate.');
       
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            
            return false;
            
        }
        
        else{
            
            return true;
        
        }
        
    }
    
    // Basic built-in data filter 
    
    protected function xFilter($str)
    {
        
        return trim(strip_tags($str));
        
    }
    
    
    // Password strength checker
    
    public function xStrongPassword($password,$minlength,$maxlength,$strength)
    {
        
        if(empty($strength))
            
            die('No strength given to password checker.');
        
        if(empty($maxlength))
            
            die('No maxlength given to password checker.');
        
        if(empty($minlength))
            
            die('No minlength given to password checker.');
        
        
        $password=trim($password);
        
        $actual_strength = 0;
        
        if(strlen($password)<$minlength)
        {
            
            return false;
            
        }
        
        if(strlen($password)>$maxlength)
        {
            
            return false;
            
        }
        
        if (preg_match("/[a-z]/", $password) && preg_match("/[A-Z]/", $password))
                
            $actual_strength++;
        
        if (preg_match("/[0-9]/", $password))
                
            $actual_strength++;
        
        if (preg_match("/.[!,@,#,$,%,^,&,*,?,_,~,-,Ã‚Â£,(,)]/", $password))
                
            $actual_strength++;
        
        if($actual_strength<$strength)
            
            return false;
        
        return true;
    }
    
    // Checks if the given value an age of a human
    
    public function xIsAge($age)
    {
        
        
        if((string)(int)$age != $age)
            
            return false;
        
        if($age<1 or $age>120)
            
            return false;
        
        else
            
            return true;
        
    }
    
    // Validate URL 
    
    public function xIsUrl($url)
    {
       
        return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
    }
    
    // Validate full name fields
    
    public function xIsFullname($fullname)
    {
        
        $result = explode(' ',$fullname);
        
        if (sizeof($result) < 2)
            return false;
        
        
        foreach ($result as $segment)
        {
            $segment = trim($segment);
            if(!preg_match("/^(?:\p{L}|[_\-'.])+$/u", $segment))
                    return false;
            if(mb_strlen($segment,LAYOUT_CHARSET) < 2)
                    return false;
            
        }
        
        return true;
    }
    
    // Validate username fields
    
    public function xIsUsername($username,$min_length = false)
    {
        
        $username = trim($username);
        
        if($min_length){
            if(mb_strlen($username,LAYOUT_CHARSET)<$min_length)
                return false;
        }
        
        
        if(!preg_match("/^(?:\p{L}|[\-])+$/u", $username))
                return false;
        
        return true;
    }
    
} 

?>