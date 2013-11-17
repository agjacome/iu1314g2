<?php

namespace views;

class View
{

    private $data;
    private $session;

    public function __construct($session)
    {
        $this->data = array();
        $this->session = $session;
    }

    public function render($template)
    {
        $this->loadData();
        extract($this->data);

        ob_start();
        include "templates/" . $template;
        $rendered = ob_get_clean();

        print $rendered;
    }

    public function assign($key,$value)
    {
        $this->data[$key] = $value;
    }

    private function loadData()
    {
        $this->loadLanguage();
        $this->loadUserSession();
        $this->loadFlash();
    }

    private function loadLanguage()
    {
        $this->data["lang"] = \components\Language::getStrings();
    }

    private function loadUserSession()
    {
        $this->data["logged"] = false;

        if (isset($this->session->logged))   $this->data["logged"]   = $this->session->logged;
        if (isset($this->session->username)) $this->data["username"] = $this->session->username;
        if (isset($this->session->userrole)) $this->data["userrole"] = $this->session->userrole;
    }

    private function loadFlash()
    {
        $this->data["flash"] = false;
        if (isset($this->session->flash)) {
            $this->data["flash"]  = $this->session->flash;
            $this->session->flash = null;
        }
    }

}

?>
