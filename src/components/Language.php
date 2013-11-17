<?php

namespace components;

class Language
{

    private static $strings;
    private static $language;

    public static function getStrings($lang = null)
    {
        self::initLanguage($lang);
        self::initStrings();

        return self::$strings;
    }

    private static function initLanguage($lang)
    {
        if (isset($lang) && $lang !== self::$language) {
            self::$strings  = null;
            self::$language = $lang;
        }
    }

    private static function initStrings()
    {
        if (isset(self::$strings)) return;

        $default = Configuration::getValue("language", "default");

        $file = __DIR__ . "/../lang/" . self::$language . ".ini";
        if (!file_exists($file)) $file = __DIR__ . "/../lang/" . $default . ".ini";

        self::$strings = parse_ini_file($file, true);
    }

}

?>
