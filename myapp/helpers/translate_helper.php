<?php

class Translate_Helper extends XyneoHelper
{

    public static $lang_data = array();

    private static $language = 'hu';

    public function __construct()
    {
        parent::__construct();
        self::setupLang("form");
    }

    public static function setLang($lang = 'hu')
    {
        self::$language = $lang;
    }

    public static function add($array)
    {
        self::$lang_data += $array;
    }

    public static function get($index)
    {
        if (isset(self::$lang_data[$index])) {
            return self::$lang_data[$index];
        } else {
            return $index;
        }
    }

    public static function setupLang($part)
    {
        $file = 'myapp/languages/' . self::$language . '/' . $part . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
        $lang = 'hu';
        $file = 'myapp/languages/' . $lang . '/' . $part . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
        die('Language error: ' . $lang . '/' . $part);
    }
}
