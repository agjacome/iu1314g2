<?php

namespace controllers;

abstract class Controller
{
    protected $lang;
    protected $request;
    protected $session;
    protected $view;

    public function __construct($request)
    {
        $this->request = $request;

        $this->view    = null; // View aun no implementada
        $this->session = new \components\Session();
        $this->lang    = \components\Language::getStrings($this->session);
    }

    protected function setFlash($msg)
    {
        $this->session->flash = $msg;
    }

    public function redirect($controller, $action = null)
    {
        $url = "/index.php?controller=" . $controller;
        if (isset($action)) $url .= "action=$action";

        header("Location: " . $url);
    }

    public abstract function defaultAction();
}

?>
