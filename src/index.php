<?php 

require_once(__DIR__ . "/components/ClassLoader.php");

$loaders = array(
    new controllers\ClassLoader("components",  __DIR__),
    new controllers\ClassLoader("controllers", __DIR__),
    new controllers\ClassLoader("models",      __DIR__),
    new controllers\ClassLoader("views",       __DIR__)
);

foreach ($loaders as $loader) $loader->register();

?>
