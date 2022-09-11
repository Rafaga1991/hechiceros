<?php

namespace core;

use model\User;

require_once './core/Functions.php';

header('Access-Control-Allow-Origin: *');// permitiendo acceso de origen remoto.

spl_autoload_register(function($class){ require_once str_replace("\\", '/', "$class.php"); });

init();

Session::init(function($ss){
    if($ss::auth()){
        if($user = (new User)->find($ss::getUser('id'))){
            if($user->close_session || $user->rol != $ss::getRol()){
                $user->close_session = false;
                return true;
            }
        }else{
            return true;
        }
    }

    return false;
});

Session::set('__CURRENT_ROUTE__', '/');

$route = new Route();

require_once getRoute('route.php');

$route->init($view);

Message::loadMessageError();

Html::setBody($view??'', ['class' => 'sb-nav-fixed']);