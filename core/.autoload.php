<?php

namespace core;

header('Access-Control-Allow-Origin: *');// permitiendo acceso de origen remoto.

spl_autoload_register(function($class){ require_once str_replace("\\", '/', "$class.php"); });

Functions::init();
Session::init();

Session::set('__CURRENT_ROUTE__', '/');

$route = new Route();

require_once Functions::getRoute('route.php');

$route->init($view);

Message::loadMessageError();

Html::setBody($view??'', ['class' => 'sb-nav-fixed']);

