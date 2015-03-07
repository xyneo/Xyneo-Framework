<?php
if (! defined('XYNEO')) {
    throw new XyneoError("Direct access denied!");
}

class XyneoHelper extends Xyneomodel
{

    public function __construct()
    {
        parent::__construct();
    }
}
