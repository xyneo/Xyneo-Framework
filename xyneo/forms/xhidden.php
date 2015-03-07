<?php

class XHidden extends XyneoField
{

    /**
     * Render field for the form
     *
     * @see XyneoField::renderContent()
     */
    public function renderContent()
    {
        return "<input type=\"hidden\" name=\"" . $this->id . "\" id=\"" . $this->id . "\" value=\"" . $this->getValue() . "\" />";
    }
}
