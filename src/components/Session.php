<?php

namespace components;

/**
 * Clase que proporciona un acceso en orientacion a objetos a la variable 
 * global $_SESSION, para obtener y almacenar parametros de la sesion.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Session
{

    public $sessionID;  // identificador de la sesion

    /**
     * Crea una nueva instancia de Session, iniciando o reanudando para ello 
     * una sesion a traves de session_start().
     */
    public function __construct()
    {
        session_start();
        $this->sessionID = session_id();
    }

    /**
     * Asegura que, cuando el objeto de la clase Session es destruido, los 
     * datos de la sesion se almacenan correctamente.
     */
    public function __destruct()
    {
        session_write_close();
    }

    /**
     * Vacía los datos de la sesión actual y se destruyen todos los datos 
     * asociados a la misma.
     */
    public function destroy()
    {
        foreach ($_SESSION as $key => $value)
            $_SESSION[$key] = null;

        session_destroy();
    }

    /**
     * Sobreescribe __isset() para una correcta comprobacion de existencia de 
     * parametros en la sesion a traves de la funcion global isset() de PHP.
     *
     * @param string $key
     *     El nombre del parametro a comprobar existencia.
     *
     * @return boolean
     *     True si el parametro recibido existe, False en caso contrario.
     */
    public function __isset($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Elimina la posibilidad de clonación de los objetos de la clase Session.
     */
    public function __clone()
    {
        trigger_error("Clonacion no permitida para " . __CLASS__, E_USER_ERROR);
    }

    /**
     * Sobreescribe __get() para un acceso mas adecuado a los parametros de la 
     * sesion. Asi, dado un objeto $sess de la clase Session y un parametro 
     * "param", podra accederse al mismo via $sess->param.
     *
     * @param string $key
     *     El nombre del parametro al que acceder.
     *
     * @return string
     *     El valor asociado al parametro solicitado.
     */
    public function __get($key)
    {
        return $_SESSION[$key];
    }

    /**
     * Sobreescribe __set() para proporcionar una escritura de parametros mas 
     * adecuada. Asi, dado un objeto $sess de la clase Session, puede 
     * almacenarse un nuevo parametro "param" con el valor "XYZ" como: 
     * $sess->param = "XYZ".
     *
     * @param string $key
     *     Nombre del parametro a almacenar/actualizar.
     * @param string $value
     *     Valor del parametro escrito.
     */
    public function __set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

}

?>
