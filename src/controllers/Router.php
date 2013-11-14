<?php

namespace controllers;

class Router
{
	public function routeRequest($request)
	{
		$controller = $request->controller;
		$controller = "controllers\\" . (ucfirst($controller) . 'sController');
		$action = $request->action;
		
		$controller = new $controller($request);
		$controller->$action();
	}
}

?>