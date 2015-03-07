<?php

if (! defined('XYNEO')) {
    throw new XyneoError("Direct access denied!");
}

abstract class XyneoField extends XyneoHelper
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $label;

    /**
     *
     * @var boolean
     */
    protected $key = false;

    /**
     *
     * @var boolean
     */
    protected $unique = false;

    /**
     *
     * @var string
     */
    protected $prefix;

    /**
     *
     * @var string
     */
    protected $suffix;

    /**
     *
     * @var mixed
     */
    protected $value;

    /**
     *
     * @var string
     */
    protected $dbField;

    /**
     *
     * @var boolean
     */
    protected $required = true;

    /**
     *
     * @var boolean
     */
    protected $disabled = false;

    /**
     *
     * @var boolean
     */
    protected $readonly = false;

    /**
     *
     * @var boolean
     */
    protected $renderable = true;

    /**
     *
     * @var string
     */
    protected $validation;

    /**
     *
     * @var string
     */
    protected $matchTo;

    /**
     *
     * @var boolean
     */
    protected $hidden = false;

    /**
     *
     * @var string
     */
    protected $hint;

    /**
     *
     * @var string
     */
    protected $tooltip;

    /**
     *
     * @var string
     */
    protected $className;

    /**
     *
     * @var array
     */
    protected $filters = array();

    /**
     *
     * @var string
     */
    protected $error;

    /**
     *
     * @var string
     */
    protected $groupId;

    /**
     *
     * @param mixed $parameters            
     */
    public function __construct($parameters = array())
    {
        parent::__construct();
        if (! is_array($parameters) && ! empty($parameters)) {
            $this->setId($parameters);
        } else {
            $this->buildFromParameters($parameters);
        }
    }

    /**
     *
     * @param array $parameters            
     */
    public function buildFromParameters($parameters)
    {
        foreach ($parameters as $key => $value) {
            switch (strtoupper($key)) {
                case "ID":
                    $this->setId($value);
                    break;
                case "FIELD":
                    $this->setField($value);
                    break;
                case "KEY":
                    $this->setKey(strtoupper($value) == "KEY" || strtoupper($value) == "TRUE" ? true : false);
                    break;
                case "UNIQUE":
                    $this->setUnique(strtoupper($value) == "UNIQUE" || strtoupper($value) == "TRUE" ? true : false);
                    break;
                case "NAME":
                case "LABEL":
                    $this->setLabel($value);
                    break;
                case "PREFIX":
                    $this->setPrefix($value);
                    break;
                case "SUFFIX":
                    $this->setSuffix($value);
                    break;
                case "TOOLTIP":
                    $this->setTooltip($value);
                    break;
                case "HINT":
                    $this->setHint($value);
                    break;
                case "VALUE":
                    $this->setValue($value);
                    break;
                case "VALIDATEAS":
                    $this->setValidation($value);
                    break;
                case "MATCH":
                    $this->setMatchTo($value);
                    break;
                case "FILTER":
                    $this->addFilter($value);
                    break;
                case "REQUIRED":
                    $this->required($value == "required" || $value == "true" ? true : false);
                    break;
                case "HIDDEN":
                    $this->hide();
                    break;
                case "DISABLED":
                    $this->disable($value == "disabled" || $value == "true" ? true : false);
                    break;
                case "READONLY":
                    $this->setReadonly($value == "readonly" || $value == "true" ? true : false);
                    break;
                case "CLASS":
                    $this->setClassName($value);
                    break;
                case "GROUP":
                    $this->setGroup($value);
                    break;
                case "RENDERABLE":
                    $this->setRenderable($value == "false" ? false : true);
                    break;
            }
        }
    }

    /**
     *
     * @param string $id            
     * @return XyneoField
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     *
     * @param string $label            
     * @return XyneoField
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     *
     * @param mixed $value            
     * @return XyneoField
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     *
     * @param mixed $value            
     * @return XyneoField
     */
    public function setUserValue($value)
    {
        return $this->setValue($value);
    }

    /**
     *
     * @param boolean $key            
     * @return XyneoField
     */
    public function setKey($key = true)
    {
        $this->key = $key;
        return $this;
    }

    /**
     *
     * @param boolean $unique            
     * @return XyneoField
     */
    public function setUnique($unique = true)
    {
        $this->unique = $unique;
        return $this;
    }

    /**
     *
     * @param string $html            
     * @return XyneoField
     */
    public function setPrefix($html)
    {
        $this->prefix = $html;
        return $this;
    }

    /**
     *
     * @param string $html            
     * @return XyneoField
     */
    public function setSuffix($html)
    {
        $this->suffix = $html;
        return $this;
    }

    /**
     *
     * @param string $text            
     * @return XyneoField
     */
    public function setTooltip($text)
    {
        $this->tooltip = $text;
        return $this;
    }

    /**
     *
     * @param string $text            
     * @return XyneoField
     */
    public function setHint($text)
    {
        $this->hint = $text;
        return $this;
    }

    /**
     *
     * @param string $field            
     * @return XyneoField
     */
    public function setField($field)
    {
        $this->dbField = $field;
        if (is_null($this->id)) {
            $this->id = $field;
        }
        return $this;
    }

    /**
     *
     * @param string $validationRule            
     * @return XyneoField
     */
    public function setValidation($validationRule)
    {
        $this->validation = $validationRule;
        return $this;
    }

    /**
     *
     * @param string $fieldId            
     * @return XyneoField
     */
    public function setMatchTo($fieldId)
    {
        $this->matchTo = $fieldId;
        return $this;
    }

    /**
     *
     * @param string $className            
     * @return XyneoField
     */
    public function setClassName($className)
    {
        $this->className = $className;
        return $this;
    }

    /**
     *
     * @param string $message            
     * @return XyneoField
     */
    public function setError($message)
    {
        $this->error = $message;
        return $this;
    }

    /**
     *
     * @param string $groupId            
     * @return XyneoField
     */
    public function setGroup($groupId)
    {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     *
     * @param boolean $state            
     * @return XyneoField
     */
    public function setRenderable($state = true)
    {
        $this->renderable = $state;
        return $this;
    }

    /**
     *
     * @param boolean $state            
     * @return XyneoField
     */
    public function setReadonly($state = true)
    {
        $this->readonly = $state;
        return $this;
    }

    /**
     *
     * @param boolean $state            
     * @return XyneoField
     */
    public function setRequired($state = true)
    {
        $this->required = $state;
        return $this;
    }

    /**
     *
     * @param unknown $filter            
     * @return XyneoField
     */
    public function addFilter($filter)
    {
        if (is_array($filter)) { // if array given - class and method
            $this->filters[] = $filter;
        } else {
            if (is_string($filter)) { // string
                $filters = preg_split("/\s/", $filter, - 1, PREG_SPLIT_NO_EMPTY);
                foreach ($filters as $filterFunction) {
                    $this->filters[] = trim($filterFunction);
                }
            } else {
                if (is_callable($filter)) {
                    $this->filters[] = $filter;
                }
            }
        }
        return $this;
    }

    /**
     *
     * @param XyneoForm $form            
     * @return XyneoField
     */
    public function appendTo(XyneoForm $form)
    {
        $form->addField($this);
        return $this;
    }

    /**
     *
     * @param string $beforeField            
     * @param XyneoForm $form            
     * @return XyneoField
     */
    public function appendBefore($beforeField, XyneoForm $form)
    {
        $form->addFieldBefore($beforeField, $this);
        return $this;
    }

    /**
     *
     * @param string $afterField            
     * @param XyneoForm $form            
     * @return XyneoField
     */
    public function appendAfter($afterField, XyneoForm $form)
    {
        $form->addFieldAfter($afterField, $this);
        return $this;
    }

    /**
     *
     * @param boolean $state            
     * @return XyneoField
     */
    public function required($state = true)
    {
        return $this->setRequired($state);
    }

    /**
     *
     * @return XyneoField
     */
    public function hide()
    {
        $this->hidden = true;
        return $this;
    }

    /**
     *
     * @return XyneoField
     */
    public function show()
    {
        $this->hidden = false;
        return $this;
    }

    /**
     *
     * @return XyneoField
     */
    public function disable()
    {
        $this->disabled = true;
        return $this;
    }

    /**
     *
     * @return XyneoField
     */
    public function enable()
    {
        $this->disabled = false;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return substr(get_class($this), 1);
    }

    /**
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->evaluateFilters($this->value);
    }

    /**
     *
     * @param mixed $value            
     * @return mixed
     */
    public function evaluateFilters($value)
    {
        foreach ($this->filters as $filter) {
            if (is_callable($filter)) {
                $value = call_user_func($filter, $value);
            }
        }
        return $value;
    }

    /**
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     *
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     *
     * @return string
     */
    public function getTooltip()
    {
        return $this->tooltip;
    }

    /**
     *
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     *
     * @return string
     */
    public function getField()
    {
        return $this->dbField;
    }

    /**
     *
     * @return string
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     *
     * @return string
     */
    public function getMatchTo()
    {
        return $this->matchTo;
    }

    /**
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->groupId;
    }

    /**
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->readonly;
    }

    /**
     *
     * @return boolean
     */
    public function isRenderable()
    {
        return $this->renderable;
    }

    /**
     *
     * @return boolean
     */
    public function isKey()
    {
        return $this->key;
    }

    /**
     *
     * @return boolean
     */
    public function isUnique()
    {
        return $this->unique;
    }

    /**
     *
     * @return boolean
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     *
     * @return boolean
     */
    public function validate()
    {
        if ($this->error) {
            return false;
        }
        if ($this->required && ! $this->value) {
            $this->error = "required-to-fill";
            return false;
        }
        if ($this->value && $this->validation && ! $this->validate->xCheck($this->value, $this->validation)) {
            $this->error = "format-not-allowed";
            return false;
        }
        return true;
    }

    /**
     *
     * @return string
     */
    public function renderLabel()
    {
        if (! $this->label) {
            return "&nbsp;";
        }
        $ret = "<label" . ($this->required ? " class=\"required\"" : "") . " for=\"" . $this->id . "\">" . $this->label . "</label>";
        if ($this->hint) {
            $ret .= "<span class=\"field-hint\">" . $this->hint . "</span>";
        }
        return $ret;
    }

    /**
     *
     * @return string
     */
    public function renderContent()
    {
        $ret = "";
        if (isset($this->prefix)) {
            $ret .= $this->prefix . " ";
        }
        $ret .= $this->getValue();
        if (isset($this->suffix)) {
            $ret .= " " . $this->suffix;
        }
        return $ret;
    }

    /**
     *
     * @return string
     */
    public function renderError()
    {
        if ($this->error) {
            return " <span class=\"form-error\">" . $this->error . "</span>";
        }
    }
}

?>