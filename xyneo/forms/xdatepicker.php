<?php

class XDatepicker extends XInputtext
{

    /**
     * Render field for the form
     *
     * @see XyneoField::renderContent()
     */
    public function renderContent()
    {
        $ret = "";
        $this->className .= " datepicker";
        if ($this->tooltip) {
            $this->className .= " tooltip";
        }
        if (isset($this->prefix)) {
            $ret .= $this->prefix . " ";
        }
        $ret .= "<input type=\"date\" name=\"" . $this->id . "\" id=\"" . $this->id . "\" size=\"10\" maxlength=\"10\"" . ($this->disabled ? " disabled" : "") . ($this->readonly ? " readonly" : "") . ($this->className ? " class=\"" . trim($this->className) . "\"" : "") . " value=\"" . $this->getValue() . "\" />" . ($this->tooltip ? " <span id=\"tt_" . $this->id . "\" class=\"tooltip\">" . $this->tooltip . "</span>" : "");
        if (isset($this->suffix)) {
            $ret .= " " . $this->suffix;
        }
        return $ret;
    }
}
