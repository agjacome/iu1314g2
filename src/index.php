<?php

require_once(__DIR__ . "/components/ClassLoader.php");

$loaders = array(
    new components\ClassLoader("components",  __DIR__),
    new components\ClassLoader("controllers", __DIR__),
    new components\ClassLoader("database",    __DIR__),
    new components\ClassLoader("models",      __DIR__),
    new components\ClassLoader("views",       __DIR__)
);

foreach ($loaders as $loader) $loader->register();

components\Configuration::readConfiguration("config.ini");

$userdao = new database\SQLUserDAO();
print_r($userdao->select(array("login"),array("rol" => "usuario")));

?>
