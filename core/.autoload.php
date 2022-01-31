<?php
header('Access-Control-Allow-Origin: *');// permitiendo acceso de origen remoto.

require __DIR__.'/functions.php';// Cargando las funciones globales.

spl_autoload_register(function($class){ require_once getPath(getFiles(getRoute()), $class); });

Session::init();

$route = new Route();

require_once getRoute('route.php');

$route->init($view);

Message::showMessage();

Html::setBody($view, ['class' => 'sb-nav-fixed']);