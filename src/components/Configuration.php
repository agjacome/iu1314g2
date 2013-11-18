<?php

namespace components;

/**
 * Clase estatica para la lectura del fichero de configuracion de la 
 * aplicacion.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Configuration
{

    // array[string => mixed] almacenando todos los parametros de configuracion
    private static $config;

    /**
     * Parsea y almacena los contenidos del fichero de configuracion recibido 
     * como parametro.
     *
     * @param string $filename
     *     Path hacia el fichero de configuracion .ini a ser leido.
     */
    public static function readConfiguration($filename)
    {
        self::$config = parse_ini_file($filename, true);
    }

    /**
     * Devuelve una seccion completa del fichero .ini de configuracion 
     * previamente parseado, o null si no existe.
     *
     * @param string $section
     *     Seccion que se desea obtener.
     *
     * @return mixed
     *     Un array[string => mixed] con el contenido completo de la seccion 
     *     solicitada, o null si dicha seccion no existe.
     */
    public static function getSection($section)
    {
        if (array_key_exists($section, self::$config))
            return self::$config[$section];

        return null;
    }

    /**
     * Devuelve un valor concreto del fichero .ini de configuracion previamente 
     * parseado, dadas la seccion donde se encuentra y la clave del mismo.
     *
     * @param string $section
     *     Seccion donde se encuentra el valor a obtener.
     * @param string $key
     *     Nombre de la clave que define el valor a obtener.
     *
     * @return string
     *     Valor concreto definido en el fichero de configuracion, null si no 
     *     existe.
     */
    public static function getValue($section, $key)
    {
        if (array_key_exists($section, self::$config) && array_key_exists($key, self::$config[$section]))
            return self::$config[$section][$key];

        return null;
    }

}

?>
