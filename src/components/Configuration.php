<?php 

namespace components;

class Configuration
{

    private $config;

    public function __construct($filename)
    {
        $this->config = parse_ini_file($filename, true);
    }

    public function getValue($section, $key)
    {
        if (array_key_exists($section, $this->config) && array_key_exists($key, $this->config[$section]))
            return $this->config[$section][$key];

        return null;
    }

}

?>
