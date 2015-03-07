<?php

class XCheckboxlist extends XCheckbox
{

    /**
     *
     * @var array
     */
    protected $value = array();

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
     * @var string
     */
    protected $separator = "<br />";

    /**
     * Build field from parameters
     *
     * @see XCheckbox::buildFromParameters()
     */
    public function buildFromParameters($parameters)
    {
        parent::buildFromParameters($parameters);
        
        foreach ($parameters as $key => $value) {
            switch (strtoupper($key)) {
                case "OPTIONS":
                    if (strpos($value, ";") !== false) {
                        $items = preg_split("/\;/", $value, - 1, PREG_SPLIT_NO_EMPTY);
                        $ret = array();
                        foreach ($items as $item) {
                            if (strpos($item, "=") !== false) {
                                list ($i, $v) = explode("=", $item);
                                $ret[$i] = $v ? $v : $i;
                            } else {
                                $ret[$item] = $item;
                            }
                        }
                        $this->setOptions($ret);
                    } else {
                        $this->setOptions(html_entity_decode($value));
                    }
                    break;
                case "SQLSOURCE":
                    $this->setSQLSource($value);
                    break;
                case 'SEPARATOR':
                    $this->setSeparator($value);
                    break;
            }
        }
        
        return $this;
    }

    /**
     *
     * @see XCheckbox::setValue()
     */
    public function setValue($values)
    {
        if (! is_array($values)) {
            $values = array(
                $values
            );
        }
        foreach ($values as $key => $value) {
            if ($value == $this->checkedValue) {
                $this->value[] = $key;
            }
        }
        return $this;
    }

    /**
     *
     * @param string $sql            
     * @return XCheckboxList
     */
    public function setSqlSource($sql)
    {
        $this->sqlsource = $sql;
        return $this;
    }

    /**
     *
     * @param array $options            
     * @return XCheckboxList
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
     * @param string $value            
     * @return XCheckboxList
     */
    public function setSeparator($value)
    {
        $this->separator = $value;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getSqlSource()
    {
        return $this->sqlsource;
    }

    /**
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     *
     * @see XCheckbox::validate()
     */
    public function validate()
    {
        if ($this->error) {
            return false;
        }
        if (! $this->required || count($this->getValue())) {
            return true;
        } else {
            $this->error = "required-to-select";
            return false;
        }
    }

    /**
     * Render field for the form
     *
     * @see XCheckbox::renderContent()
     */
    public function renderContent()
    {
        if (! is_array($this->value)) {
            $this->value = array(
                $this->value
            );
        }
        $ret = "";
        if (isset($this->prefix)) {
            $ret .= $this->prefix . " ";
        }
        $count = 0;
        if (isset($this->options) && is_array($this->options)) {
            foreach ($this->options as $key => $value) {
                if ($count ++) {
                    $ret .= $this->separator;
                }
                $ret .= "<input type=\"checkbox\" name=\"" . $this->id . "[" . $key . "]\" id=\"" . $this->id . "-" . $key . "\" value=\"" . $this->checkedValue . "\"" . ($this->disabled ? " disabled" : "") . ($this->readonly ? " readonly" : "") . ($this->className ? " class=\"" . trim($this->className) . "\"" : "") . (in_array($key, $this->value) ? " checked" : "") . " />" . " <label for=\"" . $this->id . "-" . $key . "\">" . $value . "</label>" . ($this->tooltip ? " <span id=\"tt_" . $this->id . "\" class=\"tooltip\">" . $this->tooltip . "</span>" : "");
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
                        $ret .= "<input type=\"checkbox\" name=\"" . $this->id . "[" . $rs[0] . "]\" id=\"" . $this->id . "-" . $rs[0] . "\" value=\"" . $this->checkedValue . "\"" . ($this->disabled ? " disabled" : "") . ($this->readonly ? " readonly" : "") . ($this->className ? " class=\"" . trim($this->className) . "\"" : "") . (in_array($rs[0], $this->value) ? " checked" : "") . " />" . " <label for=\"" . $this->id . "-" . $rs[0] . "\">" . $rs[1] . "</label>" . ($this->tooltip ? " <span id=\"tt_" . $this->id . "\" class=\"tooltip\">" . $this->tooltip . "</span>" : "");
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
