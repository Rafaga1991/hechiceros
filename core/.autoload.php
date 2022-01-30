<?php
header('Access-Control-Allow-Origin: *');// permitiendo acceso de origen remoto.

require __DIR__.'/functions.php';// Cargando las funciones globales.

spl_autoload_register(function($class){// muestra el nombre de la clase instanciada.
    require_once getPath(getFiles(getRoute()), $class);// carga el archivo que contiene la clase instanciada.
});

Session::init();

$route = new Route();

require_once getRoute('route.php');

$route->init($view);

Message::showMessage();

Html::setBody($view);