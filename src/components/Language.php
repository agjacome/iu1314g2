<?php

namespace components;

class Language
{

    private static $strings;
    private static $language = Configuration::getValue("language", "default");

    public static function getStrings($session = null)
    {
        if (isset($session->lang) && self::$language !== $session->lang)
            self::$strings = null;

        if (!isset(self::$strings))
            return parse_ini_file(__DIR__ . "/../lang/" . $language . ".ini", true);

        return $strings;
    }

}

?>
