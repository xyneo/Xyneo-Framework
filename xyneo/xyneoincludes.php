<?php if ( ! defined('XYNEO') ) die("Direct access denied!");

if (file_exists('xyneo/xyneodatabase.php')) {
    switch (DB_ALLOW) {
        case 'on' :
            require_once 'xyneo/xyneodatabase.php';
            break;
        case 'off':
            break;
        default:
            die("Bad value for DB_ALLOW.  Please check your config file");   
    }  
} else {
    die("The core files might have been damaged. xyneoincludes.php doesnt 
        exist");   
}

$xyneo_cores = array (  
    'xyneocontroller',
    'xyneovalidate',
    'xyneofile',
    'xyneomodel',
    'xyneoapplication',
    'xyneoview',
    'xyneohelper',
    'xyneobootstrap',
    'xyneoroute'
);

foreach ($xyneo_cores as $corefile) {
    $file = 'xyneo/'.$corefile.'.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        die('The core files might have been damaged. '.$corefile.'.php doesnt 
        exist');
    }
}

$app_includes = array (   
    'bootstrap',
    'config/includes'   
);

foreach ($app_includes as $appfile) {
    $file = 'myapp/'.$appfile.'.php';
    if ( file_exists($file) ) {
        require_once $file;
    } else {
        die('The application files might have been damaged. '.$app.'.php doesnt 
        exist');
    }  
}
