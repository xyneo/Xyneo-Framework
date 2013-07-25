<?php if ( ! defined('XYNEO') ) die("Direct access denied!");

// Root URL

define('URL'            , 'http://mydomain.com/');

// Default pages

define('START_PAGE'     , 'index');
define('ERROR_PAGE'     , 'error');
define('SHUTDOWN_PAGE'  , 'shutdown');

// Layouts
define('LAYOUT_DOCTYPE'             , 'HTML5');
/* 
 * HTML4.01_STRICT, HTML4.01_TRANSITIONAL, HTML4.01_FRAMESET
 * XHTML1.0_STRICT, XHTML1.0_TRANSITIONAL, XHTML1.0_FRAMESET
 * XHTML1.1
 * HTML5
*/

define('LAYOUT_CHARSET'             , 'UTF-8');
define('LAYOUT_LANGUAGE'            , 'en');

define('AUTO_COMMENT_PHP_FILES'     , 'off');
define('AUTO_COMMENT_JS_FILES'      , 'off');
define('AUTO_COMMENT_CSS_FILES'     , 'off');
define('AUTO_COMMENT_XYNEO_FILES'   , 'off');

// Developement and Xyneo Panel

define('SHUT_DOWN_SITE'             , 'off');
define('DEVELOPER_MODE'             , 'on');
define('START_SESSION_ON_LOAD'      , 'on');



// DataBase connection data

define('DB_ALLOW'       , 'off');

define('DB_DRIVER'      , '');
define('DB_HOST'        , '');
define('DB_USE'         , '');
define('DB_USER'        , '');
define('DB_PASSWORD'    , '');

// Facebook

define('APPID'        , '');
define('SECRET'       , '');