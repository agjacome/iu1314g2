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
        include $template;
        $rendered = ob_get_clean();

        print $rendered;
    }

    public function assign($key,$value)
    {
        $this->data[$key] = $value;
    }

    private function loadData()
    {
        $this->data["lang"] = \components\Language::getStrings($this->session);
        $this->data["logged_in"] = isset($this->session->logged_in) ? $this->session->logged_in : false;

        if (isset($this->session->username)) $this->data["username"] = $this->session->username;
        if (isset($this->session->userrole)) $this->data["userrole"] = $this->session->userrole;
    }

}

?>
