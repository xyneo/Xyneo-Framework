<?php

class Library_Helper extends XyneoHelper
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create an url string
     *
     * @param string $module            
     * @param array $parameters            
     * @param array $queryString            
     * @return string
     */
    public function createUrl($module = null, $parameters = array(), $queryString = array())
    {
        if (count($parameters)) {
            array_unshift($parameters, '');
        }
        $url = '/' . $module . implode('/', $parameters);
        $controller = XyneoApplication::getControllers();
        if (preg_match('/^admin/', $controller["created_controller"])) {
            if ($module != "admin") {
                $url = "/admin" . $url;
            }
        }
        
        if (count($queryString)) {
            $urlParts = array();
            foreach ($queryString as $key => $value) {
                $urlParts[] = sprintf("%s=%s", $key, urlencode($value));
            }
            $url .= "?" . implode("&amp;", $urlParts);
        }
        
        return $url;
    }

    /**
     * Get and parse current request uri
     *
     * @return array
     */
    public function getCurrentUri($onlyRoute = false)
    {
        $urlParts = array(
            "route" => "",
            "params" => array(),
            "query" => array()
        );
        $currentUri = $_SERVER['REQUEST_URI'];
        if (preg_match("/\?/", $currentUri)) {
            list ($base, $queryString) = explode("?", $currentUri);
            $base = trim($base, "/");
            if (preg_match("/\//", $base)) {
                $parts = explode("/", $base);
            } else {
                $parts = array(
                    $base
                );
            }
            $urlParts["route"] = $parts[0];
        } else {
            $queryString = "";
            $base = trim($currentUri, "/");
            if (preg_match("/\//", $base)) {
                $parts = explode("/", $base);
            } else {
                $parts = array(
                    $base
                );
            }
            $urlParts["route"] = $parts[0];
        }
        unset($parts[0]);
        $urlParts["params"] = array_values($parts);
        if (! empty($queryString)) {
            $queryParts = explode("&", $queryString);
            foreach ($queryParts as $i => $part) {
                list ($key, $value) = explode("=", $part);
                $urlParts["query"][$key] = urldecode($value);
            }
        }
        if ($onlyRoute) {
            return $urlParts["route"];
        }
        return $urlParts;
    }

    /**
     * Check the given link is active
     *
     * @param string $link            
     * @return bool
     */
    public function isActive($link, $onlyRoute = false)
    {
        if ($onlyRoute) {
            $currenUri = trim($this->createUrl($this->getCurrentUri(true)), "/");
        } else {
            $urlParts = $this->getCurrentUri();
            $currenUri = trim($this->createUrl($urlParts["route"], $urlParts["params"]), "/");
        }
        if (preg_match("/\?/", $link)) {
            $link = substr($link, 0, strpos($link, '?'));
        }
        $link = trim($link, "/");
        return $link == $currenUri;
    }

    /**
     * Redirect to given url.
     *
     * @param string $module            
     * @param array $parameters            
     * @param array $queryString            
     * @return string
     */
    public function redirect($module = null, $parameters = array(), $queryString = array())
    {
        header("Location: " . $this->createUrl($module, $parameters, $queryString));
        exit();
    }
}
