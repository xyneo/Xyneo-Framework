<?php

class XRadio extends XCheckboxlist
{

    /**
     *
     * @var string
     */
    protected $sqlsource;

    /**
     *
     * @var array
     */
    protected $options;

    /**
     *
     * @var string
     */
    protected $separator = "<br />";

    /**
     * Build field from parameters
     *
     * @see XCheckboxlist::buildFromParameters()
     */
    public function buildFromParameters($parameters)
    {
        parent::buildFromParameters($parameters);
        
        foreach ($parameters as $key => $value) {
            switch (strtoupper($key)) {
                case "OPTIONS":
                    $value = html_entity_decode($value, ENT_COMPAT, "utf-8");
                    if (strpos($value, ";") !== false) {
                        
                        $items = preg_split("/\;/", $value, - 1, PREG_SPLIT_NO_EMPTY);
                        $ret = array();
                        foreach ($items as $item) {
                            $parts = explode("=", $item);
                            $ret[$parts[0]] = isset($parts[1]) ? implode("=", array_slice($parts, 1)) : $parts[0];
                        }
                        $this->setOptions($ret);
                    } else {
                        $this->setOptions($value);
                    }
                    break;
                case "SQLSOURCE":
                    $this->setSQLSource($value);
                    break;
                case "SEPARATOR":
                    $this->setSeparator($value);
                    break;
            }
        }
        
        return $this;
    }

    /**
     *
     * @see XCheckboxlist::setSqlSource()
     */
    public function setSqlSource($sql)
    {
        $this->sqlsource = $sql;
        return $this;
    }

    /**
     *
     * @see XCheckboxlist::setOptions()
     */
    public function setOptions($options)
    {
        if (! is_array($options)) {
            $options = array(
                $options
            );
        }
        $this->options = $options;
        return $this;
    }

    /**
     *
     * @see XCheckboxlist::setSeparator()
     */
    public function setSeparator($value)
    {
        $this->separator = $value;
        return $this;
    }

    /**
     *
     * @see XCheckboxlist::getSqlSource()
     */
    public function getSqlSource()
    {
        return $this->sqlsource;
    }

    /**
     *
     * @see XCheckboxlist::getOptions()
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     *
     * @see XCheckboxlist::getSeparator()
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * Validate this form field
     *
     * @see XCheckboxlist::validate()
     */
    public function validate()
    {
        if ($this->error) {
            return false;
        }
        if (! $this->required || $this->getValue())
            return true;
        else {
            $this->error = "required-to-check";
            return false;
        }
    }

    /**
     * Render field for the form
     *
     * @see XCheckboxlist::renderContent()
     */
    public function renderContent()
    {
        $ret = "";
        if (isset($this->prefix)) {
            $ret .= $this->prefix . " ";
        }
        $this->className .= " radio-" . $this->id;
        $count = 0;
        if (isset($this->options) && is_array($this->options)) {
            foreach ($this->options as $key => $value) {
                if ($count ++) {
                    $ret .= $this->separator;
                }
                $ret .= "<input type=\"radio\" name=\"" . $this->id . "\" id=\"" . $this->id . "_" . $key . "\"" . " value=\"" . $key . "\"" . ($this->disabled ? " disabled" : "") . ($this->readonly ? " readonly" : "") . ($this->className ? " class=\"" . trim($this->className) . "\"" : "") . ($key == $this->getValue() ? " checked" : "") . "> <label for=\"" . $this->id . "_" . $key . "\" class=\"label-" . $key . "\">" . $value . "</label>";
            }
        }
        if (! is_null($this->sqlsource)) {
            $result = $this->db->query($this->sqlsource);
            if ($result->rowCount()) {
                $count = 0;
                while ($rs = $result->fetch(PDO::FETCH_NUM)) {
                    if ($rs[0] && $rs[1]) {
                        if ($count ++) {
                            $ret .= $this->separator;
                        }
                        $ret .= "<input type=\"radio\" name=\"" . $this->id . "\" id=\"" . $this->id . "_" . $rs[0] . "\" value=\"" . $rs[0] . "\"" . ($this->disabled ? " disabled" : "") . ($this->readonly ? " readonly" : "") . ($this->className ? " class=\"" . trim($this->className) . "\"" : "") . ($rs[0] == $this->getValue() ? " checked" : "") . " />" . " <label for=\"" . $this->id . "-" . $rs[0] . "\" class=\"label-" . $rs[0] . "\">" . $rs[1] . "</label>" . ($this->tooltip ? " <span id=\"tt_" . $this->id . "\" class=\"tooltip\">" . $this->tooltip . "</span>" : "");
                    }
                }
            }
        }
        if (isset($this->suffix)) {
            $ret .= " " . $this->suffix;
        }
        return $ret;
    }
}
