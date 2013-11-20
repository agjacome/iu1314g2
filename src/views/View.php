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
        // FUTURE TODO: los layouts estan hard-codeados ahora mismo, establecer 
        // una forma de poder cargarlos dinamicamente (eg: un solo layout 
        // obligatorio application.php, que se encargara de hacer los requires 
        // a los demas y la inclusion de la plantilla? implementacion de algo 
        // del estilo a lo que hace rails con el "yield" de ruby?)

        // carga el array de datos como variables para las plantillas y layouts
        $this->loadData();
        extract($this->data);

        // hace que el output en lugar de enviarse, se almacene en un buffer 
        // temporal
        ob_start();

        // carga cabecera y barra lateral
        require "layouts/header.php";
        require "layouts/sidebar.php";

        // carga plantilla
        include "templates/" . $template . ".php";

        // carga pie de pagina
        require "layouts/footer.php";

        // envia el contenido almacenado en el buffer de output
        print ob_get_clean();
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
