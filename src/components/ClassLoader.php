<?php

namespace components;

/**
 * Permite la carga de clases sin necesidad de incluir/requerir el fichero que 
 * la define. Se sigue el estandar PSR-0 para la carga automatica de ficheros 
 * segun su namespace. Implementacion basada en SplClassLoader 
 * (https://gist.github.com/jwage/221634),
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class ClassLoader
{

    private $namespace;
    private $includePath;
    private $separator    = "\\";
    private $extension    = ".php";

    /**
     * Construye una nueva instancia de ClassLoader dados un namespace y el 
     * fichero raiz que incluye al directorio base del namespace (segun 
     * estandar PSR-0, namespace "x" estara en directorio "x").
     *
     * @param string $namespace
     *     Namespace para carga automatica de clases.
     * @param string $includePath
     *     Raiz desde donde buscar el directorio del namespace.
     */
    public function __construct($namespace = null, $includePath = null)
    {
        $this->namespace   = $namespace;
        $this->includePath = $includePath;
    }

    /**
     * Registra el metodo loadClass de este objeto en el listado de 
     * autoloaders estandar de PHP.
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Elimina el metodo loadClass de este objeto del listado de autoloaders 
     * estandar de PHP.
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, "loadClass"));
    }

    /**
     * Hace un require hacia el fichero apropiado que define la clase cuyo 
     * nombre ha sido recibido como parametro. Buscara dentro del namespace 
     * definido en el constructor de esta instancia.
     *
     * @param string $className
     *     Nombre de la clase a cargar;
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
