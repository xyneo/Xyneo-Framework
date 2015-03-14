<?php

class XInputtext extends XyneoField
{

    /**
     *
     * @var integer
     */
    protected $size = 20;

    /**
     *
     * @var integer
     */
    protected $minLength = 0;

    /**
     *
     * @var integer
     */
    protected $maxLength;

    /**
     *
     * @var string
     */
    protected $placeholderText;

    /**
     *
     * @var boolean
     */
    protected $autocomplete = true;

    /**
     * Build field from parameters
     *
     * @see XyneoField::buildFromParameters()
     */
    public function buildFromParameters($parameters)
    {
        parent::buildFromParameters($parameters);
        
        foreach ($parameters as $key => $value) {
            switch (strtoupper($key)) {
                case "SIZE":
                    $this->setSize($value);
                    break;
                case "MINLENGTH":
                    $this->setMinLength($value);
                    break;
                case "MAXLENGTH":
                    $this->setMaxLength($value);
                    break;
                case "PLACEHOLDER":
                    $this->setPlaceholderText($value);
                    break;
                case "AUTOCOMPLETE":
                    $this->setAutocomplete($value);
                    break;
            }
        }
        
        return $this;
    }

    /**
     *
     * @param integer $size            
     * @return XInputText
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     *
     * @param integer $value            
     * @return XInputText
     */
    public function setMinLength($value)
    {
        $this->minLength = $value;
        return $this;
    }

    /**
     *
     * @param integer $value            
     * @return XInputText
     */
    public function setMaxLength($value)
    {
        $this->maxLength = $value;
        return $this;
    }

    /**
     *
     * @param string $value            
     * @return XInputText
     */
    public function setPlaceholderText($value)
    {
        $this->placeholderText = $value;
        return $this;
    }

    /**
     *
     * @param boolean $value            
     * @return XInputtext
     */
    public function setAutocomplete($value)
    {
        $this->autocomplete = (boolean) $value;
        return $this;
    }

    /**
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     *
     * @return integer
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     *
     * @return integer
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     *
     * @return string
     */
    public function getPlaceholderText()
    {
        return $this->placeholderText;
    }

    /**
     *
     * @return boolean
     */
    public function getAutocomplete()
    {
        return $this->autocomplete;
    }

    /**
     * Validate this form field
     *
     * @see XyneoField::validate()
     */
    public function validate()
    {
        $isValid = true;
        if ($this->maxLength && ! $this->validate->xIsShorter($this->getValue(), $this->maxLength)) {
            $this->error = "length-is-too-long";
            $isValid = false;
        }
        if ($this->required && $this->minLength && ! $this->validate->xIsLonger($this->getValue(), $this->minLength)) {
            $this->error = "length-is-too-short";
            $isValid = false;
        }
        return parent::validate() && $isValid;
    }

    /**
     * Render field for the form
     *
     * @see XyneoField::renderContent()
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
        $ret .= "<input type=\"text\" name=\"" . $this->id . "\" id=\"" . $this->id . "\" size=\"" . $this->size . "\"" . ($this->maxLength ? " maxlength=\"" . $this->maxLength . "\"" : "") . ($this->className ? " class=\"" . trim($this->className) . "\"" : "") . ($this->disabled ? " disabled" : "") . ($this->readonly ? " readonly" : "") . ($this->placeholderText ? " placeholder=\"" . $this->placeholderText . "\"" : "") . (! $this->autocomplete ? " autocomplete=\"off\"" : "") . " value=\"" . htmlspecialchars($this->getValue()) . "\">" . ($this->tooltip ? " <span id=\"tt_" . $this->id . "\" class=\"tooltip\">" . $this->tooltip . "</span>" : "");
        if (isset($this->suffix)) {
            $ret .= " " . $this->suffix;
        }
        return $ret;
    }
}

?>