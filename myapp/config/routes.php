<?php
$routes = array(
    /**
     * General routes
     */
    "xyneopanel" => "xyneopanel",
    "xyneopanel/{param}" => "xyneopanel/{param}",
    "public/clientside/{param}" => "index/clientSide/{param}",
    
    /**
     * Backend routes
     */
    "admin" => "admin_login",
    "admin/logout" => "admin_login/logout",
    // -------------------------------------------
    "admin/dashboard" => "admin_dashboard",
    
    /**
     * Frontend routes
     */
     "home" => "index"
);

foreach ($routes as $route => $original) {
    XyneoRoute::add($route, $original);
}
