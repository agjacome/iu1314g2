<?php

namespace views;

/**
 * Clase contenedora de Vistas. Se encarga de renderizar (mostrar) las 
 * diferentes plantillas que componen el sistema, y asignar acceso a datos por 
 * parte de las mismas.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class View
{

    private $data;      // array asociativo de datos para la plantilla
    private $session;   // datos de sesion, necesarios para recoger idioma y datos de usuario
    private $template;  // nombre de la plantilla a cargar

    /**
     * Construye un nuevo objeto vista, almacenando para ello una referencia a 
     * un objeto Session, desde el cual se obtendran a posteriori todos los 
     * datos necesarios.
     *
     * @param Session $session
     *     Objeto sesion de donde se extraeran todos los parametros necesarios 
     *     para renderizar correctamente las plantillas.
     */
    public function __construct($session)
    {
        $this->data     = array();
        $this->session  = $session;
        $this->template = null;
    }

    /**
     * Devuelve al navegador una plantilla HTML+PHP renderizada, con todos los 
     * datos necesarios para ser mostrados correctamente.
     *
     * @param String $template
     *     Nombre de la plantilla a renderizar, solo el nombre sin la extension 
     *     ".php". Se buscara el fichero dentro de "/views/templates/".
     */
    public function render($template)
    {
        // almacena la plantilla para que pueda ser accesible desde yield()
        $this->template = $template;

        // carga el array de datos como variables para las plantillas y layouts
        $this->load();
        extract($this->data);

        // hace que el output en lugar de enviarse, se almacene en un buffer 
        // temporal
        ob_start();

        // carga el layout application.php, de obligatoria existencia, dicho 
        // fichero ".php" tiene la responsabilidad de incluir todos los 
        // ficheros de layout de la aplicacion e invocar al metodo yield() para 
        // mostrar el contenido de la plantilla alli donde sea invocado
        require "layouts/application.php";

        // envia el contenido almacenado en el buffer de output
        print ob_get_clean();
    }

    /**
     * Realiza un include de la plantilla proporcionada en render. Debe ser 
     * llamado desde layouts/application.php o desde cualquier otro fichero php 
     * incluido por el mismo.
     */
    private function yield()
    {
        include "templates/" . $this->template . ".php";
    }

    /**
     * Almacena en el array de datos, que posteriormente sera accesible por las 
     * plantillas a traves de variables con el mismo nombre de cada clave del 
     * array.
     *
     * @param String $key
     *     Clave para el dato a asignar a la vista. Sera accesible desde las 
     *     plantillas con una variable con el mismo nombre.
     * @param mixed $value
     *     Dato (cualquier tipo) que almacenara la clave nombrada por $key.
     */
    public function assign($key,$value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Carga todos los datos necesarios (sesion, idioma y flash) en el array de 
     * datos, para hacerlos posteriormente accesibles desde las plantillas.
     */
    private function load()
    {
        // hace accesible el array de cadenas de idioma en los datos de la 
        // plantilla a traves de la variable $lang
        $this->data["lang"] = \components\Language::getStrings();

        // carga datos de sesion (usuario logueado, nombre de usuario y rol)
        $this->data["logged"] = false;
        if (isset($this->session->logged))   $this->data["logged"]   = $this->session->logged;
        if (isset($this->session->username)) $this->data["username"] = $this->session->username;
        if (isset($this->session->userrole)) $this->data["userrole"] = $this->session->userrole;

        // carga y elimina auotmaticamente tras la carga (por eso es un flash!) 
        // los datos de flash de la sesion en el array de datos, para permitir 
        // su acceso desde las plantillas
        $this->data["flash"] = false;
        if (isset($this->session->flash)) {
            $this->data["flash"]  = $this->session->flash;
            $this->session->flash = null;
        }
    }

    /**
     * Funcion de utilidad para las plantillas, devuelve true/false dependiendo 
     * de si el usuario esta identificado o no en el sistema.
     */
    private function isLoggedIn()
    {
        return isset($this->session->logged) && $this->session->logged;
    }

    /**
     * Funcion de utilidad para las plantillas, devuelve true/false dependiendo 
     * de si el usuario identificado es o no administrador.
     */
    private function isAdmin()
    {
        return $this->isLoggedIn() && isset($this->session->userrole) && $this->session->userrole === "admin";
    }

    /**
     * Funcion de utilidad para las plantillas, devuelve la URL completa que ha 
     * sido solicitada al navegador para cargar dicha vista, pero sin el 
     * dominio. Eg: "http://localhost:8080/index.php" se devolvera como 
     * "/index.php".
     */
    private function getCurrentUrl()
    {
        return $_SERVER["REQUEST_URI"];
    }

}

?>
