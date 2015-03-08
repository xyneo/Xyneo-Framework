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

    /**
     *
     * @param XyneoView $view            
     */
    public function __construct(XyneoView $view)
    {
        parent::__construct();
        $this->view = $view;
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
    }

    public function renderSidebar($menuTree)
    {
        $this->view->menuTree = $menuTree;
        $this->view->xRender($this->folder . "sidebar");
    }
}
