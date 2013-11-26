<?php

namespace controllers;

/**
 * Controlador abstracto, define una serie de metodos y atributos de utilidad 
 * para todos los controladores.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
abstract class Controller
{
    protected $lang;        // array de las cadenas del idioma activo
    protected $request;     // objeto Request que almacena la peticion HTTP
    protected $session;     // objeto Session que almacena la sesion de cliente
    protected $view;        // objeto View para el renderizado de plantillas

    /**
     * Constructor abstracto. Almacena los datos necesarios en los atributos de 
     * la clase.
     *
     * @param Request $request
     *     Objeto Request almacenando la peticion HTTP recibida.
     */
    public function __construct($request)
    {
        // almacena la peticion http para uso futuro
        $this->request = $request;

        // inicia la sesion y crea el objeto vista
        $this->session = new \components\Session();
        $this->view    = new \views\View($this->session);

        // carga las cadenas de idioma
        if (!isset($this->session->lang)) $this->session->lang = null;
        $this->lang = \components\Language::getStrings($this->session->lang);
    }

    /**
     * Almacena un mensaje temporal en la sesion para ser mostrado en la 
     * proxima vista renderizada y eliminado posteriormente. Util para mensajes 
     * de error y similares.
     *
     * @param String $msg
     *     Mensaje a mostrar en la siguiente vista renderizada.
     */
    protected function setFlash($msg)
    {
        $this->session->flash = $msg;
    }

    /**
     * Redirecciona a un controlador y accion dados, o una URL completa.
     *
     * @param String $controller
     *     Controlador al que redirigir al cliente.
     * @param String $action
     *     Accion (dentro del controlador) al que redirigir al cliente.
     * @param String $url
     *     URL completa a la que redirigir al cliente (peligroso, usar con 
     *     cuidado).
     */
    public function redirect($controller = null, $action = null, $url = null)
    {
        // FIXME: no es una buena idea permitir el redireccionamiento a pelo a 
        // una URL dada, puede dar pie a Phishing si se proporciona dicha 
        // direccion como parametro en la peticion HTTP y un atacante pone una 
        // URL maliciosa en dicho parametro.
        // Existe para que el cambio de lenguaje redireccione a la pagina 
        // previa a traves de un parametro "redirect=/index.php...", ver en 
        // HomeController->changeLanguage, View->getCurrentUrl y 
        // "/views/layouts/header.php", buscar una forma mas limpia de hacer 
        // esto.

        // TODO: permitir pasar un array de parametros, para hacer posibles 
        // ciertos tipos de redireccionamiento, eg: redirect a 
        // /index.php?controller=product&action=get&id=7.

        // crea la URL en base al controlador y accion recibidas
        if (!isset($url)) {
            $url = "/";
            if (isset($controller)) $url .= "index.php?controller=" . $controller;
            if (isset($action))     $url .= "&action=$action";
        }

        // redirige y termina el procesamiento de la peticion recibida
        header("Location: " . $url);
        exit();
    }

    /**
     * Metodo para comprobar de forma sencilla si el usuario esta identificado 
     * en el sistema.
     *
     * @return boolean
     *    True si el usuario esta identificado, False en caso contrario.
     */
    public function isLoggedIn()
    {
        // TODO: uso de traits para evitar hacer las llamadas a este metodo "a 
        // pelo" dentro de los metodos de los controladores concretos.
        // http://php.net/manual/en/language.oop5.traits.php
        return isset($this->session->logged) && $this->session->logged;
    }

    /**
     * Metodo para comprobar de forma sencilla si el usuario identificado es 
     * administrador.
     *
     * @return boolean
     *     True si el usuario es administrador, False en caso contrario.
     */
    public function isAdmin()
    {
        // TODO: uso de traits para evitar hacer las llamadas a este metodo "a 
        // pelo" dentro de los metodos de los controladores concretos.
        // http://php.net/manual/en/language.oop5.traits.php
        return $this->isLoggedIn() && $this->session->userrole === "admin";
    }

    /**
     * Metodo abstracto a implementar obligatoriamente por todos los 
     * controladores concretos, define una accion por defecto a ser invocada 
     * cuando no se reciba informacion sobre qué acción realizar.
     */
    public abstract function defaultAction();
}

?>
