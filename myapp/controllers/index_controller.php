<?php

/*
 * Xyneo default index controller.
 */
class Index_Controller extends XyneoController
{

    /**
     *
     * @var Index_Model
     */
    public $model;

    public function __construct()
    {
        parent::__construct();
    }

    public function xyneo()
    {
        $this->view->welcome = "Welcome to Xyneo Framework 1.0";
        $this->view->page_title = "Welcome to Xyneo Framework";
        $this->view->xRender('index/index', 'xyneo');
    }

    public function clientSide($param)
    {
        preg_match("/([a-z]+)(_layout)/", $param, $match);
        $this->model->clientSide(isset($match[1]) ? $match[1] : null, $param);
    }
}
