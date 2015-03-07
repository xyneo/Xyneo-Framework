<?php

class XCheckbox extends XyneoField
{

    /**
     *
     * @var mixed
     */
    protected $value;

    /**
     *
     * @var string
     */
    protected $text;

    /**
     *
     * @var mixed
     */
    protected $uncheckedValue = 0;

    /**
     *
     * @var mixed
     */
    protected $checkedValue = 1;

    /**
     *
     * @param array $parameters            
     */
    public function __construct($parameters = array())
    {
        $this->value = $this->uncheckedValue;
        parent::__construct($parameters);
    }

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
                case "TEXT":
                    $this->setText($value);
                    break;
                case "CHECKEDVALUE":
                    $this->setCheckedValue($value);
                    break;
                case "UNCHECKEDVALUE":
                    $this->setUncheckedValue($value);
                    break;
            }
        }
        
        return $this;
    }

    /**
     *
     * @param mixed $value            
     * @return XCheckbox
     */
    public function setCheckedValue($value)
    {
        $this->checkedValue = $value;
        return $this;
    }

    /**
     *
     * @param mixed $value            
     * @return XCheckbox
     */
    public function setUncheckedValue($value)
    {
        $this->uncheckedValue = $value;
        return $this;
    }

    /**
     *
     * @param string $text            
     * @return XCheckbox
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     *
     * @see XyneoField::setValue()
     */
    public function setValue($value)
    {
        if ($value == $this->checkedValue || $value == $this->uncheckedValue) {
            $this->value = $value;
        } else {
            $this->value = null;
            die($this->getId() . " value not in allowed set, filling null");
        }
        return $this;
    }

    /**
     *
     * @see XyneoField::setUserValue()
     */
    public function setUserValue($userValue, $aliases = array())
    {
        if (empty($aliases)) {
            $aliases = array(
                $this->text
            );
        }
        $this->value = in_array($userValue, $aliases) ? $this->checkedValue : $this->uncheckedValue;
    }

    /**
     *
     * @return mixed
     */
    public function getCheckedValue()
    {
        return $this->checkedValue;
    }

    /**
     *
     * @return mixed
     */
    public function getUncheckedValue()
    {
        return $this->uncheckedValue;
    }

    /**
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Validate this form field
     *
     * @see XyneoField::validate()
     */
    public function validate()
    {
        if ($this->error) {
            return false;
        }
        if (! $this->required || $this->value == $this->checkedValue || $this->value == $this->uncheckedValue) {
            return true;
        } else {
            $this->error = "required-to-check";
            return false;
        }
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
        $ret .= "<input type=\"checkbox\" name=\"" . $this->id . "\" id=\"" . $this->id . "\" value=\"" . $this->checkedValue . "\"" . ($this->disabled ? " disabled" : "") . ($this->readonly ? " readonly" : "") . ($this->className ? " class=\"" . trim($this->className) . "\"" : "") . ($this->value ? " checked" : "") . " />" . ($this->text ? " <label for=\"" . $this->id . "\">" . $this->text . "</label>" : "") . ($this->tooltip ? " <span id=\"tt_" . $this->id . "\" class=\"tooltip\">" . $this->tooltip . "</span>" : "");
        if (isset($this->suffix)) {
            $ret .= " " . $this->suffix;
        }
        return $ret;
    }
}
