<?php

class XTextarea extends XInputtext
{

    /**
     *
     * @var integer
     */
    protected $cols = 50;

    /**
     *
     * @var integer
     */
    protected $rows = 4;

    /**
     *
     * @var string
     */
    protected $placeholderText;

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
                case "SIZE":
                    list ($cols, $rows) = explode(",", $value);
                    $this->setSize($cols, $rows);
                    break;
            }
        }
        
        return $this;
    }

    /**
     *
     * @see XInputtext::setSize()
     */
    public function setSize($cols, $rows)
    {
        if ($cols) {
            $this->cols = (int) $cols;
        }
        if ($rows) {
            $this->rows = (int) $rows;
        }
        return $this;
    }

    /**
     *
     * @see XInputtext::getSize()
     */
    public function getSize()
    {
        return array(
            $this->rows,
            $this->cols
        );
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
        $ret .= "<textarea name=\"" . $this->id . "\" id=\"" . $this->id . "\"" . " rows=\"" . $this->rows . "\" cols=\"" . $this->cols . "\"" . ($this->className ? " class=\"" . trim($this->className) . "\"" : "") . ($this->disabled ? " disabled" : "") . ($this->readonly ? " readonly" : "") . ($this->placeholderText ? " placeholder=\"" . $this->placeholderText . "\"" : "") . ">" . $this->getValue() . "</textarea>" . ($this->tooltip ? " <span id=\"tt_" . $this->id . "\" class=\"tooltip\">" . $this->tooltip . "</span>" : "");
        if (isset($this->suffix)) {
            $ret .= " " . $this->suffix;
        }
        return $ret;
    }
}
