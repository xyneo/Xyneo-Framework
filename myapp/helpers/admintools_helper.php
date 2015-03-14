<?php

/**
 * @author AnarchyChampion
 *
 */
class Admintools_Helper extends XyneoHelper
{

    /**
     *
     * @var XyneoView
     */
    private $view;

    /**
     *
     * @var string
     */
    private $folder = "!admintools/";

    private $layout;

    /**
     *
     * @param XyneoView $view            
     */
    public function __construct(XyneoView $view)
    {
        parent::__construct();
        $this->view = $view;
        $this->layout = XyneoApplication::getLayout();
    }

    /**
     * Render visibility box for lists.
     *
     * @param string $module            
     */
    public function renderVisibilityBox($module)
    {
        $this->view->module = $module;
        $this->view->xRender($this->folder . "list_visibility");
        XyneoApplication::setLayout($this->layout);
    }

    /**
     * Render filter box for lists.
     *
     * @param string $module            
     */
    public function renderFilterBox($module)
    {
        $this->view->module = $module;
        $this->view->xRender($this->folder . "list_filter");
        XyneoApplication::setLayout($this->layout);
    }

    /**
     * Render list box for lists.
     *
     * @param string $module            
     */
    public function renderListBox($module)
    {
        $this->view->module = $module;
        $this->view->xRender($this->folder . "list");
        XyneoApplication::setLayout($this->layout);
    }

    public function renderSidebar($menuTree)
    {
        $this->view->menuTree = $menuTree;
        $this->view->xRender($this->folder . "sidebar");
        XyneoApplication::setLayout($this->layout);
    }
}
