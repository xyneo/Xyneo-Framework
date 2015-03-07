<?php

class XScript extends XHtml
{

    /**
     * Render field for the form
     *
     * @see XHtml::renderContent()
     */
    public function renderContent()
    {
        $content = "";
        $url = "";
        if ($this->validate->xIsUrl($this->getValue())) {
            $url = $this->getValue();
        } else {
            $content = $this->getValue();
        }
        $ret = "<script id=\"" . $this->id . "\" type=\"text/javascript\"" . ($url ? " src=\"" . $url . "\"" : "") . ">" . $content . "</script>";
        return $ret;
    }
}
