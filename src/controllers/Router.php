<?php

namespace controllers;

/**
 * Front Controller del sistema, se encarga de mapear peticiones HTTP con 
 * controladores y acciones concretas.
 *
 * @author Alberto Gutierrez Jacome <agjacome@esei.uvigo.es>
 * @author Daniel Alvarez Outerelo  <daouterelo@esei.uvigo.es>
 * @author David Lorenzo Dacal      <dldacal@esei.uvigo.es>
 * @author Marcos Nuñez Celeiro     <mnceleiro@esei.uvigo.es>
 */
class Router
{
    /**
     * Redirecciona la peticion HTTP recibida al controlador y metodo (accion) 
     * concreto para su procesamiento.
     *
     * @param Request $request
     *     Objeto de la clase Request que "envuelva" a la peticion HTTP a 
     *     procesar.
     */
    public function routeRequest($request)
    {
        // almacena el controlador a cargar, haciendo un fallback al 
        // controlador por defecto si no se proporciona
        $controller = "controllers\\HomeController";
        if ((isset($request->controller))) {
            $controller = $request->controller;
            $controller = "controllers\\" . ucfirst($controller) . "sController";
        }

        // almacena la accion a cargar, haciendo un fallback a la accion por 
        // defecto si no se proporciona
        $action = isset($request->action) ? $request->action : "defaultAction";

        // comprueba que el controlador y la accion existan, invocandolos si 
        // sí que existen y lanzando un 404 en caso contrario
        if (class_exists($controller) && method_exists($controller,$action)) {
            $controller = new $controller($request);
            $controller->$action();
        } else {
            // TODO: crear y renderizar un objeto View, no usar directamente un 
            // include!! (implica modificar View para cargar paginas estaticas, 
            // o crear una nueva clase StaticView o algo asi)
            header("HTTP/1.1 404 Not Found");
            include_once(__DIR__ . "/../assets/static/404.html");
            exit();
        }
    }

}

?>
