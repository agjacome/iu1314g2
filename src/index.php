<?php 

require_once(__DIR__ . "/components/ClassLoader.php");

$loaders = array(
    new components\ClassLoader("components",  __DIR__),
    new components\ClassLoader("controllers", __DIR__),
    new components\ClassLoader("models",      __DIR__),
    new components\ClassLoader("views",       __DIR__)
);

foreach ($loaders as $loader) $loader->register();

$configuration = new components\Configuration("config.ini");

?>
