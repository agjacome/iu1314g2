<?php

namespace components;

// AutoLoader de clases siguiendo el estandar PSR-0.
// Basado en SplClassLoader: https://gist.github.com/jwage/221634
class ClassLoader
{

    private $namespace;
    private $includePath;
    private $separator    = "\\";
    private $extension    = ".php";

    public function __construct($namespace = null, $includePath = null)
    {
        $this->namespace   = $namespace;
        $this->includePath = $includePath;
    }

    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    public function unregister()
    {
        spl_autoload_unregister(array($this, "loadClass"));
    }

    public function loadClass($className)
    {
        $ns = $this->namespace . $this->separator;

        if ($this->namespace === null || $ns === substr($className, 0, strlen($ns))) {

            $fileName  = "";
            $lastNsPos = strripos($className, $this->separator);

            if ($lastNsPos !== false) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName  = str_replace($this->separator, DIRECTORY_SEPARATOR, $namespace);
                $fileName .= DIRECTORY_SEPARATOR;
            }

            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className);
            $fileName .= $this->extension;

            $require = "";
            if ($this->includePath !== null)
                $require .= $this->includePath . DIRECTORY_SEPARATOR;

            require $require . $fileName;
        }
    }

}

?>
