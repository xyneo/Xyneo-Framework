<?php

/**
 * @author AnarchyChampion
 * @version 1.0.0
 */
class Log_Helper extends XyneoHelper
{

    /**
     * Save log file with a new line.
     * If the given directory doesn't exist, then it will be creating.
     *
     * @example new Log_Helper("directory_name", "Something to log.");
     *         
     * @param string $dir
     *            Directory name.
     * @param mixed $text
     *            Usually string to log.
     */
    public function __construct($dir, $text)
    {
        $path = str_ireplace("helpers", "", __DIR__) . "log/" . $dir . "/";
        if (! file_exists($path)) {
            mkdir(rtrim($path, "/"), 0775);
        }
        $file = $path . date("Y-m-d") . ".log";
        file_put_contents($file, date("Y-m-d H:i:s") . " - " . $text . "\n", FILE_APPEND);
    }
}
