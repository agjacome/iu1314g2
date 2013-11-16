<?php

namespace controllers;

class Router
{
	public function routeRequest($request)
	{
		if (!(isset($request->controller))) {
			$controller = "controllers\\HomeController";
		} else {
			$controller = $request->controller;
			$controller = "controllers\\" . (ucfirst($controller) . 'sController');
		}

		if (isset($request->action)) {
			$action = $request->action;
		} else {
			$action = "defaultAction";
		}

		if ( class_exists($controller) && method_exists($controller,$action) )
		{
			$controller = new $controller($request);
			$controller->$action();
		} else {
			print("Pagina no encontratada.");
		}
			/*
			$view = new \views\View("404.html");
			$view->render();
			*/
	}
}

?>