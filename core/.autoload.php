<?php

session_start();

require_once(__DIR__.'/.common.php');
//require_once(__DIR__.'/.dump.php');

$files = Common::dirSearch('./core');
$config = parse_ini_file('./config.ini');
$controllers = Common::dirSearch('./controller');
$models = Common::dirSearch('./model');

/**********************************/
$js = '';
$css = '';
$controllername='';
/**********************************/
usort($files, function($arr1, $arr2){// ordenando archivos centrales por longitud de titulo.
     return strlen($arr1) <= strlen($arr2);
});

foreach($files as $file) include $file;// cargando archivos centrales
foreach($config as $key => $cfg) define(strtoupper($key), $cfg);// cargando variables de archivo de configuracion
foreach($controllers as $controller) include $controller;// cargando controladores
foreach($models as $model) include $model;// cargando modelos

require_once('./route.php');// cargando rutas

//function dump($value, $die=true) { Dump::show($value, $die); }
$_token = Common::generateToken();
$view = '';
if(isset($_SERVER['REDIRECT_URL'])){
  $url_data = explode('/', $_SERVER['REDIRECT_URL']);
  $data = array_filter($url_data);
  $variable = strtolower($data[1]). 'Controller';
  $class = ucfirst($variable);
  if(class_exists($class)){
    $controllername = $data[1];
    $$variable = new $class();
    $fun = $data[2] ?? 'index';
    if($route = Route::exist("{$data[1]}/$fun")){
      if(!method_exists($$variable, $fun)){
        Errors::add('warning', "El metodo <b>$fun()</b> no existe en el controlador <b>$class</b>.");
        $fun = 'index';
      }elseif(Session::auth() == $route['auth']){
          if(empty($_REQUEST)){
            if(count($data) > 2) $view = $$variable->$fun($data[3]);
            else $view = $$variable->$fun();
          }else{
              $view = $$variable->$fun(new Request());
          }
      }else{
        if($route['auth']){
          Errors::add('info', 'Debes Iniciar Sesion Para Acceder a Esta Ruta.');
        }else{
          Errors::add('info', 'Debes Cerrar Sesion Para Acceder a Esta Ruta.');
        }
      }
    }else{
      Errors::add('warning', "La Ruta <b>{$data[1]}/$fun</b> no existe!");
    }
  }else{
    Errors::add('warning', "El controlador <b>$class</b> no existe!");
  }
}else{
  $controllername = Session::auth() ? PAGE_ACCESS : PAGE_INIT;
  $variable = strtolower($controllername) . 'Controller';
  $class = ucfirst($variable);
  $$variable = new $class();
  $view = $$variable->index();
}

$css = Route::asset("css/$controllername.css");
$js = Route::asset("js/$controllername.js");

$css = file_exists($css) ? $css : '';
$js = file_exists($js) ? $js : '';

$view = str_replace('{!__token!}', $_token, $view);

Errors::exist($view);// verificando y mostrando si hay errores