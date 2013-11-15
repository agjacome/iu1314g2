<?php

namespace components;

class Session
{

    public $sessionID;

    public function __construct()
    {
        session_start();
        $this->sessionID = session_id();
    }

    public function __destruct()
    {
        session_write_close();
    }

    public function destroy()
    {
        foreach ($_SESSION as $key => $value)
            $_SESSION[$key] = null;

        session_destroy();
    }

    public function __isset($key)
    {
        return isset($_SESSION[$key]);
    }

    public function __clone()
    {
        trigger_error("Clonacion no permitida para " . __CLASS__, E_USER_ERROR);
    }

    public function __get($key)
    {
        return $_SESSION[$key];
    }

    public function __set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

}

?>
