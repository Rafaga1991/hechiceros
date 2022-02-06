<?php

header('Access-Control-Allow-Origin: *');// permitiendo acceso de origen remoto.

require './vendor/autoload.php';
require __DIR__.'/functions.php';// Cargando las funciones globales.

spl_autoload_register(function($class){ 
    require_once getPath(getFiles(getRoute()), $class); 
});

Session::set('__CURRENT_ROUTE__', '/');

Session::init();

$route = new Route();

require_once getRoute('route.php');

$route->init($view);

Message::loadMessageError();

Html::setBody($view, ['class' => 'sb-nav-fixed']);