<?php

namespace controllers;

abstract class Controller
{
    protected $view;
    protected $session;
    protected $request;

    public function __construct($request)
    {
        $this->view = null; // View aun no implementada
        $this->session = new \components\Session();
        $this->request = $request;
    }

    protected function setFlash($msg)
    {
        $this->session->flash = $msg;
    }

    public function redirect($url)
    {
        header('Location: ' . $url);
    }

    public abstract function defaultAction();
}

?>
