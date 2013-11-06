<?php

namespace components;

class Request
{

    private $request;

    public function __construct()
    {
        $this->request = $this->initFromHttp();
    }

    private function initFromHttp()
    {
        if (!empty($_POST)) return $_POST;
        if (!empty($_GET))  return $_GET;
        return array();
    }

    public function __isset($key)
    {
        return array_key_exists($key, $this->request);
    }

    public function __get($key)
    {
        return $this->request[$key];
    }

    public function __set($key, $value)
    {
        $this->request[$key] = $value;
    }

    public function isGet()
    {
        return $_SERVER["REQUEST_METHOD"] === "GET";
    }

    public function isPost()
    {
        return $_SERVER["REQUEST_METHOD"] === "POST";
    }

}

?>
