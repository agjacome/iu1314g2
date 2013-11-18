<?php

namespace components;

/**
 * Clase que proporciona un acceso en orientacion a objetos a las variables
 * globales $_GET y $_POST, para obtener parametros de peticiones HTTP.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Request
{
    private $request; // almacena el contenido de $_GET o $_POST

    /**
     * Construye una nueva instancia de Request a partir de los parametros
     * recibidos por la peticion HTTP GET/POST, y obtenidos a traves de las
     * variables globales $_GET y $_POST de PHP.
     */
    public function __construct()
    {
        $this->request = $this->initFromHttp();
    }

    /**
     * Devuelve el contenido de $_POST o $_GET segun el tipo de peticion
     * recibida. O un array vacio si la peticion no es GET ni POST.
     *
     * @return array[string => string]
     *     Contenido de $_GET o $_POST, segun la peticion recibida. Array vacio
     *     en caso de no ser GET ni POST.
     */
    private function initFromHttp()
    {
        if (!empty($_POST)) return $_POST;
        if (!empty($_GET))  return $_GET;
        return array();
    }

    /**
     * Sobreescribe __isset() para proporcionar un acceso mas adecuado a traves
     * de la funcion global isset() a los valores de la peticion.
     *
     * @param string $key
     *     Clave a comprobar si definida.
     *
     * @return boolean
     *     True si la clave recibida como parametro existe, False en caso
     *     contrario.
     */
    public function __isset($key)
    {
        // se sobreescribe con array_key_exists para que devuelva True aun
        // cuando la clave tenga un valor nulo
        return array_key_exists($key, $this->request);
    }

    /**
     * Sobreescribe __get() para proporcionar un acceso mas adecuado a los 
     * parametros de la peticion. Asi, dado un objeto $req de la clase Request 
     * y un parametro "id", podra accederse al mismo via $req->id.
     *
     * @param string $key
     *     Nombre del parametro a obtener.
     *
     * @return string
     *     El valor asociado al parametro solicitado.
     */
    public function __get($key)
    {
        return $this->request[$key];
    }

    /**
     * Sobreescribe __set() para proporcionar una escritura de parametros mas 
     * adecuada. Asi, dado un objeto $req de la clase Request, puede 
     * almacenarse un nuevo parametro "id" con el valor "7" como: $req->id = 7.
     *
     * @param string $key
     *     Nombre del parametro a almacenar/actualizar.
     * @param string $value
     *     Valor del parametro escrito.
     */
    public function __set($key, $value)
    {
        $this->request[$key] = $value;
    }

    /**
     * Comprueba si la peticion HTTP que el servidor web a recibido es GET.
     *
     * @return boolean
     *     True si la peticion es GET, False en caso contrario.
     */
    public function isGet()
    {
        return $_SERVER["REQUEST_METHOD"] === "GET";
    }

    /**
     * Comprueba si la peticion HTTP que el servidor web a recibido es POST.
     *
     * @return boolean
     *     True si la peticion es POST, False en caso contrario.
     */
    public function isPost()
    {
        return $_SERVER["REQUEST_METHOD"] === "POST";
    }

}

?>
