<?php

namespace components;

/**
 * Clase estatica para la lectura de los ficheros de idioma de la aplicacion.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Language
{

    private static $strings;   // array de cadenas definidas por el lenguaje
    private static $language;  // lenguaje almacenado en $strings

    /**
     * Devuelve el array de cadenas del lenguaje recibido como parametro.
     *
     * @param string $lang
     *     Codigo del lenguaje (eg: "es", "en") a obtener.
     *
     * @return array[string => mixed]
     *     Array de cadenas del lenguaje especificado.
     */
    public static function getStrings($lang = null)
    {
        self::initLanguage($lang);
        self::initStrings();

        return self::$strings;
    }

    /**
     * Almacena el nuevo lenguaje si el previamente almacenado es distinto al 
     * actual y reinicia las cadenas de lenguaje almacenadas si fuese 
     * necesario.
     *
     * @param string $lang
     *     Codigo del lenguaje (eg: "es", "en") a iniciar.
     */
    private static function initLanguage($lang)
    {
        // si el codigo de lenguaje es distinto al almacenado, reinicia 
        // $strings a null y almacena el codigo recibido
        if (isset($lang) && $lang !== self::$language) {
            self::$strings  = null;
            self::$language = $lang;
        }
    }

    /**
     * Carga en $strings los contenidos del fichero .ini del lenguaje 
     * almacenado, siempre que $strings no haya sido previamente inicializado.
     */
    private static function initStrings()
    {
        // si ya han sido previamente cargadas, no se hace nada
        if (isset(self::$strings)) return;

        // idioma por defecto si el lenguaje almacenado en $language no se 
        // corresponde a ningun fichero
        $default = Configuration::getValue("language", "default");

        // almacena ruta a fichero del lenguaje, si no existe hace fallback al 
        // fichero de lenguaje $default
        $file = __DIR__ . "/../config/lang/" . self::$language . ".ini";
        if (!file_exists($file)) $file = __DIR__ . "/../config/lang/" . $default . ".ini";

        // parsea el fichero de lenguaje y almacena su contenido en $strings
        self::$strings = parse_ini_file($file, true);
    }

}

?>
