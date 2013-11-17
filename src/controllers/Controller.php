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

        $this->session = new \components\Session();
        $this->view    = new \views\View($this->session);
        $this->lang    = \components\Language::getStrings($this->session->lang);
    }

    protected function setFlash($msg)
    {
        $this->session->flash = $msg;
    }

    public function redirect($controller = null, $action = null)
    {
        $url = "/";
        if (isset($controller)) $url .= "index.php?controller=" . $controller;
        if (isset($action))     $url .= "&action=$action";

        header("Location: " . $url);
        exit();
    }

    public abstract function defaultAction();
}

?>
