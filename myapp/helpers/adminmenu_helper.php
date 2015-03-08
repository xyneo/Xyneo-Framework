<?php

class Adminmenu_Helper extends XyneoHelper
{

    public $lib;

    private $path = array(), $actions = array();

    private static $menuStructure = array(
        "system" => array(
            "name" => "Rendszer menük",
            "menus" => array(
                array(
                    "name" => "Kezelőfelület",
                    "icon" => "home",
                    "uriparts" => array(
                        "route" => "dashboard",
                        "params" => array(),
                        "query" => array()
                    ),
                    "children" => array()
                )
            )
        ),
        "content" => array(
            "name" => "Tartalmi menük",
            "menus" => array()
        ),
        "webshop" => array(
            "name" => "Webshop menük",
            "menus" => array()
        )
    );

    public function __construct($ajax = false)
    {
        parent::__construct();
        $this->lib = new Library_Helper();
        if ($ajax && isset($_POST["visibility"])) {
            $datas = isset($_POST["visibilitySettings"]) ? $_POST["visibilitySettings"] : array();
            $this->setAdminSettings($_POST["visibility"], $datas);
            echo json_encode(true);
            exit();
        }
        $this->path = isset($_SESSION["path"]) && is_array($_SESSION["path"]) ? $_SESSION["path"] : array();
        unset($_SESSION["path"]);
        $this->actions = isset($_SESSION["actions"]) && is_array($_SESSION["actions"]) ? $_SESSION["actions"] : array();
        unset($_SESSION["actions"]);
        if (! isset($_SESSION["adminsettings"])) {
            $_SESSION["adminsettings"] = isset($_COOKIE["adminsettings"]) ? unserialize($_COOKIE["adminsettings"]) : new \stdClass();
        }
    }

    public function setAdminSettings($module, $datas)
    {
        if (! isset($_SESSION["adminsettings"]->{$module})) {
            $_SESSION["adminsettings"]->{$module} = array();
        }
        foreach ($_SESSION["adminsettings"]->{$module} as $key => $value) {
            $_SESSION["adminsettings"]->{$module}[$key] = false;
        }
        foreach ($datas as $key => $value) {
            $_SESSION["adminsettings"]->{$module}[$key] = (boolean) $value;
        }
        setcookie("adminsettings", serialize($_SESSION["adminsettings"]), time() + 60 * 60 * 24 * 365);
    }

    public function getMenus()
    {
        $menuStructure = array();
        $controllers = XyneoApplication::getControllers();
        foreach (self::$menuStructure as $key => $main) {
            if (! count($main["menus"])) {
                continue;
            }
            $menuStructure[$key] = array(
                "uri" => "javascript:void('" . $key . "')",
                "name" => $main["name"],
                "class" => array(
                    "main-menu"
                ),
                "menus" => array()
            );
            foreach ($main["menus"] as $i => $menu) {
                $link = "";
                if (is_array($menu["uriparts"])) {
                    $link = $this->lib->createUrl($menu["uriparts"]["route"], $menu["uriparts"]["params"], $menu["uriparts"]["query"]);
                }
                $menuStructure[$key]["menus"][$i] = array(
                    "uri" => $link,
                    "name" => $menu["name"],
                    "class" => array(
                        "first-lvl-submenu"
                    ),
                    "icon" => $menu["icon"],
                    "children" => array(),
                    "active" => false
                );
                if (count($menu["children"])) {
                    $menuStructure[$key]["menus"][$i]["class"][] = "has-child";
                    $menuStructure[$key]["menus"][$i]["uri"] = "javascript:void('" . $menu["uriparts"] . "')";
                }
                if ($this->lib->isActive($link)) {
                    $menuStructure[$key]["menus"][$i]["class"][] = "active";
                    $menuStructure[$key]["class"][1] = "opened";
                    $menuStructure[$key]["menus"][$i]["active"] = true;
                    $menuStructure[$key]["active"] = true;
                }
                foreach ($menu["children"] as $j => $child) {
                    $link = $this->lib->createUrl($child["uriparts"]["route"], $child["uriparts"]["params"], $child["uriparts"]["query"]);
                    $menuStructure[$key]["menus"][$i]["children"][$j] = array(
                        "uri" => $link,
                        "name" => $child["name"],
                        "class" => array(
                            "second-lvl-submenu"
                        )
                    );
                    if ($this->lib->isActive($link)) {
                        $menuStructure[$key]["menus"][$i]["children"][$j]["class"][] = "active";
                        $menuStructure[$key]["menus"][$i]["class"][] = "opened";
                        $menuStructure[$key]["class"][1] = "opened";
                        $menuStructure[$key]["menus"][$i]["active"] = true;
                    }
                }
                if (count($menu["children"])) {
                    if (XyneoRoute::parseRoute(ltrim($this->lib->createUrl($menu["uriparts"]), "/")) == $controllers['created_controller']) {
                        if (! $menuStructure[$key]["menus"][$i]["active"]) {
                            $menuStructure[$key]["menus"][$i]["class"][] = "active";
                            $menuStructure[$key]["class"][1] = "opened";
                            $menuStructure[$key]["menus"][$i]["active"] = true;
                        }
                    }
                }
            }
        }
        return $menuStructure;
    }

    /**
     * Add an element to breadcrumb
     *
     * @param string $title            
     * @param string $link            
     * @return void
     */
    public function addPath($title, $link)
    {
        $this->path[] = array(
            "title" => $title,
            "link" => $link
        );
        $_SESSION["path"] = $this->path;
    }

    /**
     * Render breadcrumb
     *
     * @return string
     */
    public function renderPath()
    {
        $path = "";
        $path .= "<ul id=\"breadcrumb\">";
        $path .= "<li><a href=\"" . $this->lib->createUrl("dashboard") . "\"><i class=\"fa fa-home\"></i> Kezdőlap</a></li>";
        foreach ($this->path as $key => $element) {
            $path .= "<li><a href=\"" . $element["link"] . "\">" . $element["title"] . "</a></li>";
        }
        $path .= "</ul>";
        return $path;
    }

    /**
     * Add an element to actions
     *
     * @param string $icon            
     * @param string $title            
     * @param string $link            
     * @return void
     */
    public function addActions($icon, $title, $link)
    {
        $this->actions[] = array(
            "icon" => $icon,
            "title" => $title,
            "link" => $link
        );
        $_SESSION["actions"] = $this->actions;
    }

    /**
     * Render actions
     *
     * @return string
     */
    public function renderActions()
    {
        $actions = "";
        if (count($this->actions)) {
            $actions .= "<div id=\"actions-bar\">";
            foreach ($this->actions as $key => $element) {
                $actions .= "<a href=\"" . $element["link"] . "\" class=\"fa fa-" . $element["icon"] . "\" title=\"" . $element["title"] . "\"></a>";
            }
            $actions .= "</div>";
        }
        return $actions;
    }

    /**
     * Setup message from response.
     *
     * @param mixed $response            
     */
    public function setupMessage($response)
    {
        $message = "";
        if (is_array($response)) {
            if (isset($response["message"])) {
                $message = $response["message"];
            }
        } else {
            $message = $response;
        }
        $this->xSetMessage($message);
    }
}
