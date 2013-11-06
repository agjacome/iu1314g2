<?php

namespace components;

class Configuration
{

    private static $config;

    public static function readConfiguration($filename)
    {
        self::$config = parse_ini_file($filename, true);
    }

    public static function getSection($section)
    {
        if (array_key_exists($section, self::$config))
            return self::$config[$section];

        return null;
    }

    public static function getValue($section, $key)
    {
        if (array_key_exists($section, self::$config) && array_key_exists($key, self::$config[$section]))
            return self::$config[$section][$key];

        return null;
    }

}

?>
