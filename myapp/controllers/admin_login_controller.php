<?php
if (! defined("XYNEO")) {
    die("Direct access denied!");
}

class Admin_login_Controller extends XyneoController
{

    private $admin;

    /**
     *
     * @var Admin_login_Model
     */
    public $model;

    public function __construct()
    {
        parent::__construct();
        $this->admin = new Adminmenu_Helper();
    }

    public function xyneo()
    {
        if (Adminauth_Helper::isLoggedIn()) {
            $this->admin->lib->redirect("dashboard");
        }
        $this->view->pageTitle = "Bejelentkezés";
        $this->form->setAction("")->setId("xfw-login");
        $field = new XInputtext("email");
        $field->addFilter("email")
            ->setLabel("E-mail:")
            ->setClassName("input")
            ->setMinLength(0);
        $this->form->addField($field);
        $field = new XPassword("password");
        $field->addFilter("password")
            ->setLabel("Jelszó:")
            ->setClassName("input")
            ->setMinLength(0);
        $this->form->addField($field);
        $this->form->setSubmitValue("Belépés");
        $userData = $this->model->validate($this->form);
        if ($userData) {
            Adminauth_Helper::login($userData);
            if ($this->xIsXhr()) {
                die(json_encode(array(
                    "process" => true,
                    "redirect" => $this->admin->lib->createUrl("dashboard")
                )));
            } else {
                $this->form->setBackAction("admin", array(
                    "dashboard"
                ))->save();
            }
        } else {
            if ($this->xIsXhr()) {
                die(json_encode(array(
                    "process" => false,
                    "form" => $this->form->renderContent()
                )));
            }
        }
        $this->view->form = $this->form;
        $this->view->xRender("admin_login/admin_login", "login");
    }

    public function logout()
    {
        Adminauth_Helper::logout();
        $this->admin->lib->redirect("admin");
    }
}
