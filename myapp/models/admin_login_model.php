<?php
if (! defined("XYNEO"))
    die("Direct access denied!");

class Admin_login_Model extends XyneoModel
{

    public function __construct()
    {
        parent::__construct();
    }

    public function validate(XyneoForm $form)
    {
        if ($form->isSent()) {
            $form->queryValues();
            if ($form->validateFields()) {
                $elements = $form->getElements();
                /*$result = $this->db->xSelect()
                    ->xFrom("adminusers")
                    ->xWhere("email", $elements["email"]->getValue(), "=")
                    ->xWhere("password", md5(sha1($elements["password"]->getValue())), "=")
                    ->xLimit(1)
                    ->xGet();
                if ($result->rowCount()) {
                    return $result->fetch(PDO::FETCH_ASSOC);*/
                $adminUser = array(
                    "id" => 1,
                    "name" => "Test Admin",
                    "email" => "admin@admin.hu",
                    "password" => md5(sha1("adminpass")),
                    "role" => "admin",
                    "active" => true,
                    "sysadmin" => true
                );
                if ($elements["email"]->getValue() == $adminUser["email"] && md5(sha1($elements["password"]->getValue())) == $adminUser["password"]) {
                    return (object) $adminUser;
                } else {
                    $field = $form->getField("email");
                    $field->setError("Hibás e-mail cím vagy jelszó.")
                        ->setClassName($field->getClassName() . " error");
                    $field = $form->getField("password");
                    $field->setClassName($field->getClassName() . " error");
                }
            } else {
                foreach ($form->getElements() as $field) {
                    if ($field->getError()) {
                        $field->setError(Translate_Helper::get($field->getError()))
                            ->setClassName($field->getClassName() . " error");
                    } else {
                        $field->setClassName($field->getClassName() . " success");
                    }
                }
            }
        }
        return false;
    }
}
