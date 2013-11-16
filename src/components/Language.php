<?php

namespace components;

class Language
{

    private static $strings;
    private static $language;

    public static function getStrings($session = null)
    {
        if (!isset(self::$language))
            self::$language = Configuration::getValue("language", "default");

        if (isset($session->lang) && self::$language !== $session->lang)
            self::$strings = null;

        if (!isset(self::$strings))
            return parse_ini_file(__DIR__ . "/../lang/" . self::$language . ".ini", true);

        return $strings;
    }

}

?>
