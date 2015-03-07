<?php
if (! defined('XYNEO')) {
    die("Direct access denied!");
}

if (file_exists(XYNEO_DIR . 'xyneo/xyneodatabase.php')) {
    switch (DB_ALLOW) {
        case 'on':
            require_once XYNEO_DIR . 'xyneo/xyneodatabase.php';
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

$xyneo_cores = array(
    'xyneoerror',
    'xyneocontroller',
    'xyneovalidate',
    'xyneofile',
    'xyneoimage',
    'xyneomodel',
    'xyneoapplication',
    'xyneoview',
    'xyneohelper',
    'xyneobootstrap',
    'xyneoroute'
);

function reMap($value)
{
    return "forms/" . $value;
}

$forms = array(
    "xhtml",
    "xscript",
    "xinputtext",
    "xpassword",
    "xfile",
    "ximagefile",
    "xtextarea",
    "xcheckbox",
    "xcheckboxlist",
    "xradio",
    "xdatepicker",
    "xdatetime",
    "xhidden",
    "xselect",
    "xselectmultiple",
    "xdate"
);

$forms = array_map("reMap", $forms);
array_unshift($forms, "xyneoform", "xyneofield");

$xyneo_cores = array_merge($xyneo_cores, $forms);

foreach ($xyneo_cores as $corefile) {
    $file = XYNEO_DIR . 'xyneo/' . $corefile . '.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        die('The core files might have been damaged. ' . $corefile . '.php doesnt 
        exist');
    }
}

$app_includes = array(
    'bootstrap',
    'config/includes'
);

foreach ($app_includes as $appfile) {
    $file = 'myapp/' . $appfile . '.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        throw new XyneoError('The application files might have been damaged. ' . $appfile . '.php doesnt 
        exist');
    }
}
