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
     * Parsea la petición HTTP, crea el controlador asociado en caso de que exista y llama a la acción (método) que
     * corresponda. En caso de no existir un controlador, se crea uno por defecto que dirigirá al índice. En caso de que
     * no exista la acción (el método asociado a este controlador), se llamará a una acción por defecto.
     * @param  [array] $request Array que contiene la petición HTTP.
     */
    public function routeRequest($request)
    {
        $controller = "controllers\\HomeController";
        if ((isset($request->controller))) {
            $controller = $request->controller;
            $controller = "controllers\\" . ucfirst($controller) . "sController";
        }

        $action = isset($request->action) ? $request->action : "defaultAction";

        if (class_exists($controller) && method_exists($controller,$action)) {
            $controller = new $controller($request);
            $controller->$action();
        } else {
            header("HTTP/1.1 404 Not Found");
            include_once(__DIR__ . "/../assets/static/404.html");
            exit();
        }
    }

}

?>
