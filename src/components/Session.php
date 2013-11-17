<?php

namespace components;

/**
 * Gestiona los parámetros de una sesión HTTP.
 *
 * @package components;
 */

class Session
{
    /**
     * Almacena la ID de sesión.
     * @var string
     */
    public $sessionID;

    /**
     * Crea o reanuda una sesión y se almacena su ID de sesión.
     */
    public function __construct()
    {
        session_start();
        $this->sessionID = session_id();
    }

    /**
     * Asegura que la sesión se finaliza y se almacenan sus datos correctamente.
     */
    public function __destruct()
    {
        session_write_close();
    }

    /**
     * Vacía los datos de la sesión actual y se destruyen todos los datos almacenados de esta. Es útil para terminar la sesión cuando se cierra el navegador.
     */
    public function destroy()
    {
        foreach ($_SESSION as $key => $value)
            $_SESSION[$key] = null;

        session_destroy();
    }

    /**
     * Comprueba si el dato de sesión pasado como parámetro dispone de algún valor asociado.
     * @param  string $key que permite obtener su valor asociado.
     */
    public function __isset($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Elimina la posibilidad de la clonación de clases permitida por PHP.
     */
    public function __clone()
    {
        trigger_error("Clonacion no permitida para " . __CLASS__, E_USER_ERROR);
    }

    /**
     * Obtiene el valor asociado a la clave de sesión pasada como paŕámetro.
     * @param  string $key clave para obtener su valor asociado.
     * @return string|int Valor asociado a la clave pasada como parámetro dentro del array correspondiente a la sesión.
     */
    public function __get($key)
    {
        return $_SESSION[$key];
    }

    /**
     * Almacena un nuevo en el parámetro de sesión que se pasa como parámetro a la función.
     * @param string $key Clave a la cual se le va a cambiar el valor asociado.
     * @param string|int $value Valor nuevo asociado a la clave pasada como parámetro. 
     */
    public function __set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

}

?>
