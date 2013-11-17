<?php

namespace controllers;

class Router
{

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
