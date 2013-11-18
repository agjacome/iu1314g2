<?php

/**
 * Se incluye una unica vez la clase ClassLoader, la cual se utiliza para la 
 * carga de clases, y se registran en esta todos los namespaces
 * que componen la aplicacion web, se lee el archivo de configuración y por 
 * último se instancia la clase "Router" que se ocupa de dirigir la petición 
 * HTTP al controlador oportuno.
 */

require_once(__DIR__ . "/components/ClassLoader.php");

$loaders = array(
    new components\ClassLoader("components",  __DIR__),
    new components\ClassLoader("controllers", __DIR__),
    new components\ClassLoader("database",    __DIR__),
    new components\ClassLoader("models",      __DIR__),
    new components\ClassLoader("views",       __DIR__)
);

foreach ($loaders as $loader) $loader->register();

components\Configuration::readConfiguration(__DIR__ . "/config/config.ini");

$router = new controllers\Router();
$router->routeRequest(new components\Request());

?>
