<?php

namespace components;

/**
 * Parsea almacena una petición HTTP.
 * @package components;
 */

class Request
{
    /**
     * Almacena el contenido de la petición HTTP
     * @var array[string,string]
     */
    private $request;

    /**
     * Almacena en la variable @request la petición HTTP mediante una llamada a initFromHttp().
     */
    public function __construct()
    {
        $this->request = $this->initFromHttp();
    }

    /**
     * Obtiene los parámetros de la petición HTTP de las variables @_GET o @_POST, según la petición. 
     * En caso de no ser ninguna de las dos devuelve un array vacío.
     * @return array[clave => valor] con el contenido de la petición HTTP.
     */
    private function initFromHttp()
    {
        if (!empty($_POST)) return $_POST;
        if (!empty($_GET))  return $_GET;
        return array();
    }

    /**
     * [__isset description]
     * @param  [type]  $key [description]
     * @return boolean      [description]
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->request);
    }

    /**
     * Accede a @_GET[$key] o @_POST[$key], según proceda, devolviendo el valor correspondiente a la clave
     * @param  [string] $key de uno de los parámetros de la petición HTTP.
     * @return [string|int] con el valor asociado a la clave.
     */
    public function __get($key)
    {
        return $this->request[$key];
    }

    /**
     * Accede a @_GET[$key] o @_POST[$key], según proceda, y modifica el valor correspondiente a la clave.
     * @param  string $key clave a la que se quiere asignar nuevo valor.
     * @param  string|int  $value que contiene el valor nuevo a asignar a la clave pasada como primer parámetro
     */    
    public function __set($key, $value)
    {
        $this->request[$key] = $value;
    }

    /**
     * Comprueba si una petición es GET
     * @return boolean true si la petición es de tipo GET o false en caso contrario.
     */
    
    public function isGet()
    {
        return $_SERVER["REQUEST_METHOD"] === "GET";
    }

    /**
     * Comprueba si una petición es POST
     * @return boolean true si la petición es de tipo POST o false en caso contrario.
     */
    public function isPost()
    {
        return $_SERVER["REQUEST_METHOD"] === "POST";
    }

}

?>
