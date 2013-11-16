<?php

namespace components;

class Language
{

    private static $strings;

    public static function getStrings($session = null)
    {
        if (!isset(self::$strings)) {
            $file = isset($session->lang) ? $session->lang : Configuration::getValue("language", "default");
            return parse_ini_file(__DIR__ . "/../lang/" . $file . ".ini", true);
        }

        return $strings;
    }

}

?>
