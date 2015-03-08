<?php if ( ! defined("XYNEO") ) die("Direct access denied!");
class Admin_dashboard_Controller extends XyneoController
{
    private $admin;
    
    /**
     * @var Admin_dashboard_Model
     */
    public $model;

    public function __construct()
    {
        parent::__construct();
        Adminauth_Helper::protectedContent();
        $this->admin = new Adminmenu_Helper($this->xIsXhr());
    }

    public function xyneo()
    {
        $this->view->page_title = "Kezelőfelület";
        $this->view->xRender("admin_dashboard/admin_dashboard", "admin");
    }
}
