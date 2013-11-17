<?php

namespace components;

/**
 * <p>Permite cargar clases sin realizar los includes/requieres.</p>
 *
 * <p>Cuando se crea una nueva instancia de una clase que no ha sido definida esta clase se encarga de ello.</p>
 * <p>Este AutoLoader de clases sigue el estandar PSR-0 y se basa en el "SplClassLoader" definido aquí: https://gist.github.com/jwage/221634</p>
 *
 * @package components;
 */

class ClassLoader
{

    private $namespace;
    private $includePath;
    private $separator    = "\\";
    private $extension    = ".php";

    /**
     * Constructor de la clase.
     * @param string $namespace   Paquete en el que está incluida la clase (opcional).
     * @param string $includePath directorio donde se encuentra la clase (opcional).
     */
    public function __construct($namespace = null, $includePath = null)
    {
        $this->namespace   = $namespace;
        $this->includePath = $includePath;
    }

    /**
     * Registra la función dada en una pila.
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Elimina la función de una pila
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, "loadClass"));
    }

    /**
     * Carga la clase pasada como parámetro en caso de existir en el Path.
     * @param  string $className nombre de una clase;
     */
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

            if (file_exists($require . $fileName))
                require $require . $fileName;
        }
    }

}

?>
