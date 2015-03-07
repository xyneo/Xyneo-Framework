<?php

class XDateTime extends XDatepicker
{

    /**
     * Render field for the form
     *
     * @see XDatepicker::renderContent()
     */
    public function renderContent()
    {
        $ret = "";
        $this->className .= " timepicker";
        if ($this->tooltip) {
            $this->className .= " tooltip";
        }
        if (isset($this->prefix)) {
            $ret .= $this->prefix . " ";
        }
        $ret .= "<input type=\"datetime\" name=\"" . $this->id . "\" id=\"" . $this->id . "\" size=\"19\" maxlength=\"19\"" . ($this->disabled ? " disabled" : "") . ($this->readonly ? " readonly" : "") . ($this->className ? " class=\"" . trim($this->className) . "\"" : "") . " value=\"" . $this->getValue() . "\" />" . ($this->tooltip ? " <span id=\"tt_" . $this->id . "\" class=\"tooltip\">" . $this->tooltip . "</span>" : "");
        if (isset($this->suffix)) {
            $ret .= " " . $this->suffix;
        }
        return $ret;
    }
}
