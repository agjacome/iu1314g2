<?php

namespace views;

/**
 * Esta clase se ocupa de mostrar al usuario la plantilla que corresponda y recuperar los datos de la sesión de usuario
 * necesarios para dicha plantilla. 
 *
 * @package  views
 */

class View
{

    private $data;
    private $session;

    public function __construct($session)
    {
        $this->data = array();
        $this->session = $session;
    }

    /**
     * Renderiza una plantilla pasada como parámetro
     * @param  string $template plantilla a mostrar
     */
    public function render($template)
    {
        $this->loadData();
        extract($this->data);

        ob_start();
        include "templates/" . $template . ".php";
        $rendered = ob_get_clean();

        print $rendered;
    }

    /**
     * [assign description]
     * @param  string $key   Clave de la variable @data (idioma, sesión o mensaje)
     * @param  string|int $value Valor correspondiente a la clave anterior.
     */
    public function assign($key,$value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Almacena en la variable @data los datos de sesión, mensajes Flash a mostrar (Por ejemplo: una puja que no se ha podido realizar),
     * los datos de idioma y los datos de sesión.
     */
    private function loadData()
    {
        $this->loadLanguage();
        $this->loadUserSession();
        $this->loadFlash();
    }

    /**
     * Almacena el idioma en el array @data.
     * @return [type] [description]
     */
    private function loadLanguage()
    {
        $this->data["lang"] = \components\Language::getStrings();
    }

    /**
     * Carga los datos de sesión que son necesarios para la vista.
     */
    private function loadUserSession()
    {
        $this->data["logged"] = false;

        if (isset($this->session->logged))   $this->data["logged"]   = $this->session->logged;
        if (isset($this->session->username)) $this->data["username"] = $this->session->username;
        if (isset($this->session->userrole)) $this->data["userrole"] = $this->session->userrole;
    }

    /**
     * Recupera los mensajes "flash" que es necesario mostrar.
     * @return [type] [description]
     */
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
