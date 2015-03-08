<?php

class Adminauth_Helper extends XyneoHelper
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Log in user.
     *
     * @param stdClass $userData            
     * @return void
     */
    public static function login($userData)
    {
        $_SESSION["adminuser"] = new Adminuser($userData);
    }

    /**
     * Firewall for backend.
     *
     * @return void
     */
    public static function protectedContent()
    {
        if (! self::isLoggedIn()) {
            self::logout();
            header("Location: /admin");
            exit();
        }
    }

    /**
     * Check if the user has been logged in and he/she is active.
     *
     * @return boolean
     */
    public static function isLoggedIn()
    {
        if (! isset($_SESSION["adminuser"])) {
            return false;
        } elseif (! $_SESSION["adminuser"]->isActive()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Log out user.
     * 
     * @return void
     */
    public static function logout()
    {
        if (isset($_SESSION["adminuser"])) {
            unset($_SESSION["adminuser"]);
        }
    }

    /**
     * Check if the user has permission for the current module.
     *
     * @param boolean $redirect            
     * @param boolean $isAdmin            
     * @return boolean
     */
    public static function checkPermission($redirect = true, $isAdmin = true)
    {
        if ($redirect) {
            if ($isAdmin && ! $_SESSION["adminuser"]->isAdmin()) {
                header("Location: /admin");
                exit();
            }
        } else {
            if (! $isAdmin) {
                return true;
            } else {
                return $_SESSION["adminuser"]->isAdmin();
            }
        }
    }
}
