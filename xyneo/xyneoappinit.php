<?php if ( ! defined('XYNEO') ) die("Direct access denied!");

/*
 * Validating config file
 *------------------------------------------------------------------------------
 *
 * Before instantiating the bootstrap we check the given config values.
 *
 */

if (file_exists('xyneo/xyneoconfig.php')) {
    require_once 'xyneo/xyneoconfig.php';
} else {
    die('The core files might have been damaged. xyneoconfig.php doesnt exist.');
}  

$xyneoconfig = new XyneoConfig();
$xyneoconfig -> checkConfigFile();

if (file_exists('xyneo/xyneoincludes.php')) {
    require_once 'xyneo/xyneoincludes.php';
} else {
    die('The core files might have been damaged. xyneoincludes.php doesnt exist.');
}
    
    
