<?php if ( ! defined('XYNEO') ) die("Direct access denied!");

// This is the Xyneo Framework inbuilt form validation class

class XyneoValidate
{    
    // Validating email address
    
    public function xValidEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;  
        } else {
            return true;
        }    
    }
    
    // Basic built-in data filter 
    
    public function xFilter($str)
    {
        return trim(strip_tags($str));
    }
    
    // Password strength checker
    
    public function xStrongPassword($password, $minlength, $maxlength, $strength)
    {
        if (empty($strength)) {
            die('No strength given to password checker.');
        }
        
        if (empty($maxlength)) {
            die('No maxlength given to password checker.');
        }
        
        if (empty($minlength)) {
            die('No minlength given to password checker.');
        }
        
        $password=trim($password); 
        $actual_strength = 0;
        
        if (mb_strlen($password, LAYOUT_CHARSET) < $minlength) {
            return false;
        }
        
        if (mb_strlen($password, LAYOUT_CHARSET) > $maxlength) {
            return false;
        }
        
        if (preg_match("/[a-z]/", $password) && preg_match("/[A-Z]/", $password)) {
            $actual_strength++;
        }
        
        if (preg_match("/[0-9]/", $password)) {
            $actual_strength++;
        }
        
        if (preg_match("/.[!,@,#,$,%,^,&,*,?,_,~,-,Ã‚Â£,(,)]/", $password)) {
            $actual_strength++;
        }
        
        if ($actual_strength < $strength) {
            return false;
        }
        
        return true;
    }
    
    // Checks if the given value an age of a human
    
    public function xIsAge($age)
    {
        if((string)(int)$age != $age) {
            return false;
        }
        
        if($age < 1 or $age > 120) {
            return false;
        } else {
            return true;
        }   
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
        
        if (sizeof($result) < 2) {
            return false;
        }
        
        foreach ($result as $segment) {
            $segment = trim($segment);
            
            if (!preg_match("/^(?:\p{L}|[_\-'.])+$/u", $segment)) {
                return false;
            }
                    
            if (mb_strlen($segment, LAYOUT_CHARSET) < 2) {
                return false;
            }
        }
        
        return true;
    }
    
    // Validate username fields
    
    public function xIsUsername($username, $min_length = false)
    {
        $username = trim($username);
        
        if ($min_length) {
            if (mb_strlen($username, LAYOUT_CHARSET)<$min_length) {
                return false;
            }
        }
        
        if (!preg_match("/^(?:\p{L}|[\-])+$/u", $username)) {
            return false;
        }
        
        return true;
    }
    
    // Validate an integer
    
    public function xIsInt($input_data)
    {
        if ((string)(int)$input_data != $input_data) {
           return false; 
        }
        
        return true;
        
    }
    
    // Validate a natural 
    
    public function xIsN($input_data, $zero_allowed = true)
    {
        $min = 0;
        
        if (!$zero_allowed) {
            $min = 1;
        }
        
        if ((string)(int)$input_data != $input_data or $input_data < $min) {
            return false;
        }
        
        return true; 
    }
    
    // Validate a number in a range 
    
    public function xIsInRange($input_data, $min, $max)
    {
        if (!is_numeric($input_data)) {
            return false;
        }
        
        if ($input_data < $min or $input_data > $max) {
            return false;
        }
        
        return true;    
    }
    
    // Validate string exact length 
    
    public function xIsLong($input_data, $length)
    {
        if (mb_strlen($input_data, LAYOUT_CHARSET) != $length) {
            return false;
        }
        return true;
    }
    
    // Validate string min length 
    
    public function xIsLonger($input_data,$length)
    {
        if (mb_strlen($input_data, LAYOUT_CHARSET) <= $length) {
            return false;
        }
        return true;
    }
    
    // Validate string max length 
    
    public function xIsShorter($input_data,$length)
    {
        if (mb_strlen($input_data, LAYOUT_CHARSET) >= $length) {
            return false;
        }
        
        return true;          
    }
    
    // Validate string length between two values
    
    public function xIsBetween($input_data, $min_length, $max_length)
    {
        if (mb_strlen($input_data, LAYOUT_CHARSET) < $min_length or mb_strlen($input_data, LAYOUT_CHARSET) > $max_length ) {
            return false;
        }
        
        return true;           
    }
    
    // Check the post variables given in the post_vars array
    
    public function xCheckPostVars($post_vars = false)
    {
        if ($post_vars === false) {
            die('No array givento to xCheckPostVars to compare the post variables with.');
        }
        foreach ($post_vars as $index) {
            if (!isset($_POST[$index])) {
                return false;
            }
        }
        
        return true;
    }
    
    // Check the get variables given in the get_vars array
    
    public function xCheckGetVars($get_vars = false)
    {
        if ($get_vars === false) {
            die('No array givento to xCheckGetVars to compare the get variables with.');
        }
        foreach ($get_vars as $index) {
            if (!isset($_GET[$index])) {
                return false;
            }
        }
        
        return true;
    }
}
