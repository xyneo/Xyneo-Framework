<?php

if (! defined('XYNEO')) {
    die("Direct access denied!");
}

/**
 * Validating config file
 * ------------------------------------------------------------------------------
 *
 * Before instantiating the bootstrap we check the given config values.
 */

if (! defined('XYNEO_DIR')) {
    define('XYNEO_DIR', dirname(str_ireplace('\\', '/', __DIR__)) . '/');
}

if (file_exists(XYNEO_DIR . 'xyneo/xyneoconfig.php')) {
    require_once XYNEO_DIR . 'xyneo/xyneoconfig.php';
} else {
    die('The core files might have been damaged. xyneoconfig.php doesn\'t exist.');
}

$xyneoconfig = new XyneoConfig();
$xyneoconfig->checkConfigFile();

if (file_exists(XYNEO_DIR . 'xyneo/xyneoincludes.php')) {
    require_once XYNEO_DIR . 'xyneo/xyneoincludes.php';
} else {
    die('The core files might have been damaged. xyneoincludes.php doesn\'t exist.');
}
    
    
