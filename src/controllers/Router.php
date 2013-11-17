<?php

namespace controllers;

/**
 * <p>Esta clase se ocupa de, comprobando el contenido de la petición HTTP, parsearla y crear así los controladores
 * asociados y llamar a las acciones correspondientes, contenidas en otras clases.</p>
 * 
 * @package controllers
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
            // FIXME: hack temporal, deberia invocarse a una vista
            print "Pagina no encontrada.";
        }
    }

}

?>
