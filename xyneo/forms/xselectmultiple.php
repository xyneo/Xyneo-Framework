<?php

class XSelectmultiple extends XSelect
{

    /**
     *
     * @var array
     */
    protected $value = array();

    /**
     *
     * @var integer
     */
    protected $size = 3;

    /**
     * Render field for the form
     *
     * @see XSelect::renderContent()
     */
    public function renderContent()
    {
        if (! is_array($this->value)) {
            $this->value = array(
                $this->value
            );
        }
        if ($this->tooltip) {
            $this->className .= " tooltip";
        }
        $ret = "";
        if (isset($this->prefix)) {
            $ret .= $this->prefix . " ";
        }
        $ret .= "<select name=\"" . $this->id . "[]\" id=\"" . $this->id . "\" multiple" . ($this->disabled ? " disabled" : "") . ($this->readonly ? " readonly" : "") . ($this->className ? " class=\"" . trim($this->className) . "\"" : "") . " size=\"" . $this->size . "\">";
        if (isset($this->options) && is_array($this->options)) {
            foreach ($this->options as $key => $value) {
                $ret .= "<option value=\"" . $key . "\"" . (in_array($key, $this->value) ? " selected" : "") . ">" . $value . "</option>";
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
