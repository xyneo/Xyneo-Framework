<?php

class XHtml extends XyneoField
{

    /**
     * The value of this field
     *
     * @var mixed
     */
    protected $text;

    /**
     * SQL command
     *
     * @var string
     */
    protected $sqlsource;

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
                case "SQLSOURCE":
                    $this->setSQLSource($value);
                    break;
            }
        }

        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function queryValue()
    {
        return false;
    }

    /**
     *
     * @see XyneoField::setValue()
     */
    public function setValue($text)
    {
        $this->setText($text);
        return $this;
    }

    /**
     *
     * @param mixed $text
     * @return XHtml
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     *
     * @param string $sql
     * @return XHtml
     */
    public function setSqlSource($sql)
    {
        $this->sqlsource = $sql;
        return $this;
    }

    /**
     *
     * @see XyneoField::getValue()
     */
    public function getValue()
    {
        return $this->getText();
    }

    /**
     *
     * @return mixed
     */
    public function getText()
    {
        return $this->evaluateFilters($this->text);
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
     * No validation needed
     *
     * @see XyneoField::validate()
     */
    public function validate()
    {
        return true;
    }

    /**
     * Render field for the form
     *
     * @see XyneoField::renderContent()
     */
    public function renderContent()
    {
        if ($this->tooltip) {
            $this->className .= " tooltip";
        }
        $ret = "<div id=\"" . $this->id . "\"" . ($this->className ? " class=\"" . trim($this->className) . "\"" : "") . ">";
        if (isset($this->prefix)) {
            $ret .= $this->prefix . " ";
        }
        if (! is_null($this->sqlsource)) {
            $value = $this->db->query($this->sqlsource)->fetchColumn();
            if ($value) {
                $ret .= $this->evaluateFilters($value);
            }
        }
        $ret .= $this->getValue();
        if (isset($this->suffix)) {
            $ret .= " " . $this->suffix;
        }
        $ret .= "</div>" . ($this->tooltip ? " <span id=\"tt_" . $this->id . "\" class=\"tooltip\">" . $this->tooltip . "</span>" : "");
        return $ret;
    }
}
