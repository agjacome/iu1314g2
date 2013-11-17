<?php

namespace controllers;

/**
 * Contiene funciones y variables communes para cada controlador concreto. El controlador se ocupa de abstraer las vistas
 * del modelo. Contiene información para dirigir la acción que corresponda, ya sea redirigir a la página principal, obtener
 * información de la base de datos u cualquier otra implementada.
 * 
 * @package controllers;
 */

abstract class Controller
{
    protected $lang;
    protected $request;
    protected $session;
    protected $view;

    /**
     * Crea o recupera la sesión del usuario, una vista de usuario y le asigna un lenguaje.
     * @param [array] $request Array con la petición.
     */
    public function __construct($request)
    {
        $this->request = $request;

        $this->session = new \components\Session();
        $this->view    = new \views\View($this->session);
        $this->lang    = \components\Language::getStrings($this->session->lang);
    }

    /**
     * Almacena en la sesión un mensaje (puede servir para mostrar un mensaje una vez, envíandoselo a la vista)
     * @param [string] $msg Mensaje a enviar, por ejemplo, un error al dar de alta un nuevo producto.
     */
    protected function setFlash($msg)
    {
        $this->session->flash = $msg;
    }

    /**
     * Cuando ninguna de las acciones llamadas está disponible, redirige la petición a donde corresponda.
     * @param  Controller $controller atributo opcional.
     * @param  Action $action atributo opcional.
     */
    public function redirect($controller = null, $action = null)
    {
        $url = "/";
        if (isset($controller)) $url .= "index.php?controller=" . $controller;
        if (isset($action))     $url .= "&action=$action";

        header("Location: " . $url);
        exit();
    }

    /**
     * Función no definida que se ejecutaría en caso de existir un parámetro "action=..." en la petición
     * cuyo método no existe.
     */
    public abstract function defaultAction();
}

?>
