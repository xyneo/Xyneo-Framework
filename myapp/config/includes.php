<?php
$entityDirectory = "myapp/entities/";
foreach (scandir($entityDirectory) as $file) {
    if (in_array($file, array(
        ".",
        ".."
    ))) {
        continue;
    }
    require_once $entityDirectory . $file;
}

require_once "myapp/helpers/library_helper.php";
require_once "myapp/helpers/adminauth_helper.php";
require_once "myapp/helpers/adminmenu_helper.php";
require_once "myapp/helpers/admintools_helper.php";
require_once "myapp/helpers/translate_helper.php";
new Translate_Helper();