<?php

class XPassword extends XInputtext
{

    /**
     *
     * @var boolean
     */
    protected $mustHaveNumbers = false;

    /**
     *
     * @var boolean
     */
    protected $mustHaveUppercase = false;

    /**
     *
     * @var boolean
     */
    protected $mustHaveSpecialChars = false;

    /**
     *
     * @var integer
     */
    protected $maxLength = 20;

    /**
     *
     * @var array
     */
    protected $hashMethods = array(
        "md5"
    );

    /**
     * Build field from parameters
     *
     * @see XInputtext::buildFromParameters()
     */
    public function buildFromParameters($parameters)
    {
        parent::buildFromParameters($parameters);
        
        foreach ($parameters as $key => $value) {
            switch (strtoupper($key)) {
                case "REQUIRENUMBERS":
                    $this->requireNumbers(strtoupper($value) == "REQUIRENUMBERS" || strtoupper($value) == "TRUE" ? true : false);
                    break;
                case "REQUIREUPPERCASE":
                    $this->requireUppercase(strtoupper($value) == "REQUIREUPPERCASE" || strtoupper($value) == "TRUE" ? true : false);
                    break;
                case "REQUIRSPECIALCHARS":
                    $this->requireSpecialChars(strtoupper($value) == "REQUIRSPECIALCHARS" || strtoupper($value) == "TRUE" ? true : false);
                    break;
                case "HASHMETHOD":
                    
                    break;
            }
        }
        
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->getValue() ? false : true;
    }

    /**
     *
     * @param boolean $value            
     * @return XPassword
     */
    public function requireNumbers($value)
    {
        $this->mustHaveNumbers = (bool) $value;
        return $this;
    }

    /**
     *
     * @param boolean $value            
     * @return XPassword
     */
    public function requireUppercase($value)
    {
        $this->mustHaveUppercase = (bool) $value;
        return $this;
    }

    /**
     *
     * @param boolean $value            
     * @return XPassword
     */
    public function requireSpecialChars($value)
    {
        $this->mustHaveSpecialChars = (bool) $value;
        return $this;
    }

    /**
     *
     * @param mixed $value            
     * @return XPassword
     */
    public function setHashMethods($value)
    {
        if (! is_array($value)) {
            $value = array(
                $value
            );
        }
        $methods = array();
        foreach ($value as $func) {
            if (is_callable($func)) {
                $methods[] = $func;
            }
        }
        if (count($methods)) {
            $this->hashMethods = $methods;
        }
        return $this;
    }

    /**
     * Validate this form field
     *
     * @see XInputtext::validate()
     */
    public function validate()
    {
        $isValid = parent::validate();
        
        if (($this->required || ! $this->isEmpty()) && $this->mustHaveUppercase && ! $this->validate->xStrongPassword($this->getValue(), $this->minLength, $this->maxLength ? $this->maxLength : mb_strlen($this->getValue(), LAYOUT_CHARSET), 1)) {
            $this->error = "value-does-not-contain-uppercase-letters";
            $isValid = false;
        }
        if (($this->required || ! $this->isEmpty()) && $this->mustHaveNumbers && ! $this->validate->xStrongPassword($this->getValue(), $this->minLength, $this->maxLength ? $this->maxLength : mb_strlen($this->getValue(), LAYOUT_CHARSET), 2)) {
            $this->error = "value-does-not-contain-numbers";
            $isValid = false;
        }
        if (($this->required || ! $this->isEmpty()) && $this->mustHaveSpecialChars && ! $this->validate->xStrongPassword($this->getValue(), $this->minLength, $this->maxLength ? $this->maxLength : mb_strlen($this->getValue(), LAYOUT_CHARSET), 3)) {
            $this->error = "value-does-not-contain-specialchars";
            $isValid = false;
        }
        if (! $this->isEmpty()) {
            foreach ($this->hashMethods as $func) {
                $this->setValue(call_user_func($func, $this->getValue()));
            }
        } else {
            $this->setField("");
        }
        return $isValid;
    }

    /**
     * Render field for the form
     *
     * @see XInputtext::renderContent()
     */
    public function renderContent()
    {
        $ret = "";
        if ($this->tooltip) {
            $this->className .= " tooltip";
        }
        if (isset($this->prefix)) {
            $ret .= $this->prefix . " ";
        }
        $ret .= "<input type=\"password\" name=\"" . $this->id . "\" id=\"" . $this->id . "\"" . ($this->disabled ? " disabled" : "") . ($this->readonly ? " readonly" : "") . ($this->placeholderText ? " placeholder=\"" . $this->placeholderText . "\"" : "") . (! $this->autocomplete ? " autocomplete=\"off\"" : "") . ($this->className ? " class=\"" . trim($this->className) . "\"" : "") . " size=\"" . $this->size . "\" />" . ($this->tooltip ? " <span id=\"tt_" . $this->id . "\" class=\"tooltip\">" . $this->tooltip . "</span>" : "");
        if (isset($this->suffix)) {
            $ret .= " " . $this->suffix;
        }
        return $ret;
    }
}
