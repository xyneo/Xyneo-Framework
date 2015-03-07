<?php

class XSelect extends XyneoField
{

    /**
     *
     * @var integer
     */
    protected $size = 1;

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
     * Build field from parameters
     *
     * @see XyneoField::buildFromParameters()
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
                case "SIZE":
                    $this->setSize($value);
                    break;
            }
        }
        
        return $this;
    }

    /**
     *
     * @param integer $size            
     * @return XSelect
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     *
     * @param string $sql            
     * @return XSelect
     */
    public function setSqlSource($sql)
    {
        $this->sqlsource = $sql;
        return $this;
    }

    /**
     *
     * @param mixed $options            
     * @return XSelect
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
     * @see XyneoField::setUserValue()
     */
    public function setUserValue($userValue)
    {
        if (isset($this->options) && is_array($this->options)) {
            foreach ($this->options as $key => $value) {
                if ($key == $userValue) {
                    $this->value = $key;
                    break;
                }
            }
        }
        /*
         * if (! is_null($this->sqlsource)) { $result = Db::sql($this->sqlsource); if (Db::numrows($result)) { while ($rs = Db::loop($result, "array")) { if ($rs[0] == $userValue) { $this->value = $key; break; } } } }
         */
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
     * Validate this form field
     *
     * @see XyneoField::validate()
     */
    public function validate()
    {
        if ($this->error) {
            return false;
        }
        if (! $this->required || $this->getValue()) {
            return true;
        } else {
            $this->error = "required-to-select";
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
        $ret .= "<select name=\"" . $this->id . "\" id=\"" . $this->id . "\" size=\"" . $this->size . "\"" . ($this->disabled ? " disabled" : "") . ($this->readonly ? " readonly" : "") . ($this->className ? " class=\"" . trim($this->className) . "\"" : "") . ">";
        if (isset($this->options) && is_array($this->options)) {
            foreach ($this->options as $key => $value) {
                $ret .= "<option value=\"" . $key . "\"" . ($key == $this->value ? " selected" : "") . ">" . $value . "</option>";
            }
        }
        if (! is_null($this->sqlsource)) {
            $result = $this->db->query($this->sqlsource);
            if ($result->rowCount()) {
                while ($rs = $result->fetch(PDO::FETCH_NUM)) {
                    if ($rs[0] && $rs[1]) {
                        $ret .= "<option value=\"" . $rs[0] . "\"" . (in_array($rs[0], $this->value) ? " selected" : "") . ">" . $rs[1] . "</option>";
                    }
                }
            }
        }
        $ret .= "</select>" . ($this->tooltip ? " <span id=\"tt_" . $this->id . "\" class=\"tooltip\">" . $this->tooltip . "</span>" : "");
        if (isset($this->suffix)) {
            $ret .= " " . $this->suffix;
        }
        return $ret;
    }
}
