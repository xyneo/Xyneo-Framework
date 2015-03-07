<?php
if (! defined('XYNEO')) {
    throw new XyneoError("Direct access denied!");
}

/**
 * This is the Xyneo Framework inbuilt form validation class
 */
class XyneoValidate
{

    /**
     * Validating email address
     *
     * @param string $email            
     * @return boolean
     */
    public function xValidEmail($email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            list ($username, $domainTLD) = explode("@", $email);
            return $this->xGetmxrr($domainTLD, $mxrecords);
        }
    }
    
    /**
     * Check if getmxrr function exists then check the mail online.
     * 
     * @param string $hostname
     * @param array $mxhosts
     * @param array $weight
     * @return boolean
     */
    private function xGetmxrr($hostname, &$mxhosts, &$mxweight = false)
    {
        if (! function_exists("getmxrr")) {
            return $this->xWinGetmxrr($hostname, $mxhosts, $mxweight);
        } else {
            return getmxrr($hostname, $mxhosts, $mxweight);
        }
    }

    /**
     * Predefined getmxrr function for windows.
     * 
     * @param string $hostname
     * @param array $mxhosts
     * @param array $weight
     * @return boolean
     */
    private function xWinGetmxrr($hostname, &$mxhosts, &$mxweight = false) {
        if (strtoupper(substr(PHP_OS, 0, 3)) != "WIN") {
            return;
        }
        if (! is_array($mxhosts)) {
            $mxhosts = array();
        }
        if (empty($hostname)) {
            return;
        }
        $exec = "nslookup -type=MX " . escapeshellarg($hostname);
        @exec($exec, $output);
        if (empty($output)) {
            return;
        }
        $i = -1;
        foreach ($output as $line) {
            $i++;
            if (preg_match("/^" . $hostname . "\tMX preference = ([0-9]+), mail exchanger = (.+)$/i", $line, $parts)) {
                $mxweight[$i] = trim($parts[1]);
                $mxhosts[$i] = trim($parts[2]);
            }
            if (preg_match("/responsible mail addr = (.+)$/i", $line, $parts)) {
                $mxweight[$i] = $i;
                $mxhosts[$i] = trim($parts[1]);
            }
        }
        return (boolean) $i != -1;
    }

    /**
     * Basic built-in data filter
     *
     * @param string $str            
     * @return string
     */
    public function xFilter($str)
    {
        return trim(strip_tags($str));
    }

    /**
     * Password strength checker
     *
     * @param string $password            
     * @param integer $minlength            
     * @param integer $maxlength            
     * @param integer $strength            
     * @return boolean
     */
    public function xStrongPassword($password, $minlength, $maxlength, $strength)
    {
        if (empty($strength)) {
            throw new XyneoError('No strength given to password checker.');
        }
        
        if (empty($maxlength)) {
            throw new XyneoError('No maxlength given to password checker.');
        }
        
        if (empty($minlength)) {
            throw new XyneoError('No minlength given to password checker.');
        }
        
        $password = trim($password);
        $actual_strength = 0;
        
        if (mb_strlen($password, LAYOUT_CHARSET) < $minlength) {
            return false;
        }
        
        if (mb_strlen($password, LAYOUT_CHARSET) > $maxlength) {
            return false;
        }
        
        if (preg_match("/[a-z]/", $password) && preg_match("/[A-Z]/", $password)) {
            $actual_strength ++;
        }
        
        if (preg_match("/[0-9]/", $password)) {
            $actual_strength ++;
        }
        
        if (preg_match("/.[!,@,#,$,%,^,&,*,?,_,~,-,Ã‚Â£,(,)]/", $password)) {
            $actual_strength ++;
        }
        
        if ($actual_strength < $strength) {
            return false;
        }
        
        return true;
    }

    /**
     * Checks if the given value an age of a human
     *
     * @param integer $age            
     * @return boolean
     */
    public function xIsAge($age)
    {
        if ((string) (int) $age != $age) {
            return false;
        }
        
        if ($age < 1 or $age > 120) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validate URL
     *
     * @param string $url            
     * @return number
     */
    public function xIsUrl($url)
    {
        return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
    }

    /**
     * Validate full name fields
     *
     * @param string $fullname            
     * @return boolean
     */
    public function xIsFullname($fullname)
    {
        $result = explode(' ', $fullname);
        
        if (sizeof($result) < 2) {
            return false;
        }
        
        foreach ($result as $segment) {
            $segment = trim($segment);
            
            if (! preg_match("/^(?:\p{L}|[_\-'.])+$/u", $segment)) {
                return false;
            }
            
            if (mb_strlen($segment, LAYOUT_CHARSET) < 2) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Validate username fields
     *
     * @param string $username            
     * @param integer $min_length            
     * @return boolean
     */
    public function xIsUsername($username, $min_length = false)
    {
        $username = trim($username);
        
        if ($min_length) {
            if (mb_strlen($username, LAYOUT_CHARSET) < $min_length) {
                return false;
            }
        }
        
        if (! preg_match("/^(?:\p{L}|[\-])+$/u", $username)) {
            return false;
        }
        
        return true;
    }

    /**
     * Validate an integer
     *
     * @param mixed $input_data            
     * @return boolean
     */
    public function xIsInt($input_data)
    {
        if ((string) (int) $input_data != $input_data) {
            return false;
        }
        
        return true;
    }

    /**
     * Validate a natural
     *
     * @param mixed $input_data            
     * @param boolean $zero_allowed            
     * @return boolean
     */
    public function xIsN($input_data, $zero_allowed = true)
    {
        $min = 0;
        
        if (! $zero_allowed) {
            $min = 1;
        }
        
        if ((string) (int) $input_data != $input_data or $input_data < $min) {
            return false;
        }
        
        return true;
    }

    /**
     * Validate a number in a range
     *
     * @param integer $input_data            
     * @param integer $min            
     * @param integer $max            
     * @return boolean
     */
    public function xIsInRange($input_data, $min, $max)
    {
        if (! is_numeric($input_data)) {
            return false;
        }
        
        if ($input_data < $min or $input_data > $max) {
            return false;
        }
        
        return true;
    }

    /**
     * Validate string exact length
     *
     * @param string $input_data            
     * @param integer $length            
     * @return boolean
     */
    public function xIsLong($input_data, $length)
    {
        if (mb_strlen($input_data, LAYOUT_CHARSET) != $length) {
            return false;
        }
        return true;
    }

    /**
     * Validate string min length
     *
     * @param string $input_data            
     * @param integer $length            
     * @return boolean
     */
    public function xIsLonger($input_data, $length)
    {
        if (mb_strlen($input_data, LAYOUT_CHARSET) <= $length) {
            return false;
        }
        return true;
    }

    /**
     * Validate string max length
     *
     * @param string $input_data            
     * @param integer $length            
     * @return boolean
     */
    public function xIsShorter($input_data, $length)
    {
        if (mb_strlen($input_data, LAYOUT_CHARSET) >= $length) {
            return false;
        }
        
        return true;
    }

    /**
     * Validate string length between two values
     *
     * @param string $input_data            
     * @param integer $min_length            
     * @param integer $max_length            
     * @return boolean
     */
    public function xIsBetween($input_data, $min_length, $max_length)
    {
        if (mb_strlen($input_data, LAYOUT_CHARSET) < $min_length or mb_strlen($input_data, LAYOUT_CHARSET) > $max_length) {
            return false;
        }
        
        return true;
    }

    /**
     * Check the post variables given in the post_vars array
     *
     * @param array $post_vars            
     * @return boolean
     */
    public function xCheckPostVars($post_vars = false)
    {
        if ($post_vars === false) {
            throw new XyneoError('No array given to xCheckPostVars to compare the post variables with.');
        }
        foreach ($post_vars as $index) {
            if (! isset($_POST[$index])) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check the get variables given in the get_vars array
     *
     * @param array $get_vars            
     * @return boolean
     */
    public function xCheckGetVars($get_vars = false)
    {
        if ($get_vars === false) {
            throw new XyneoError('No array given to xCheckGetVars to compare the get variables with.');
        }
        foreach ($get_vars as $index) {
            if (! isset($_GET[$index])) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Validate given value by the given masc or regexp pattern
     *
     * @param mixed $value            
     * @param string $type
     *            Masc or regexp pattern
     * @return boolean
     */
    public function xCheck($value, $type)
    {
        switch ($type) {
            case 'email':
                $match = $this->xValidEmail($value);
                break;
            case 'phone':
                $match = preg_match('/^[0-9[:space:]\+\/\(\)-]+$/i', $value);
                break;
            case 'domain':
                $match = $this->xIsUrl($value);
                break;
            case 'numbers':
                $match = preg_match('/^(\-){0,1}[0-9]+$/i', $value);
                break;
            case 'text':
                $match = preg_match('/^[a-z]+$/i', $value);
                break;
            case 'alphanumeric':
                $match = preg_match('/^[a-z0-9]+$/i', $value);
                break;
            case 'date':
                $match = strtotime($value);
                break;
            default:
                if (mb_substr($type, 0, 1, LAYOUT_CHARSET) != mb_substr($type, - 1, null, LAYOUT_CHARSET)) {
                    $type = '/' . $type . '/';
                }
                $match = preg_match($type, $value);
                break;
        }
        return (boolean) $match;
    }
}
