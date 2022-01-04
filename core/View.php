<?php

trait View{
    private static $views = [];

    public static function setView($name, $path, $auth=false, $data = []){
        $path = "./view/$path.php";
        if(file_exists($path)){
            self::$views[$name] = [
                'path' => $path,
                'auth' => $auth,
                'data' => $data
            ];
        }else{
            Errors::add('info', "La ruta para el nombre <b>$name</b> no existe.");
        }
    }

    public static function loadVariableView(&$view=''){
        foreach (self::$views as $index => $value){
            $search = "{!$index!}";
            if(strpos(" $view", $search)){
                if($value['auth'] == Session::auth()){
                    ob_start();
                    extract($value['data'], EXTR_PREFIX_SAME, 'dta');
                    include $value['path'];
                    $view = str_replace($search, ob_get_contents(), $view);
                    ob_clean();
                }else{
                    if(Session::auth()){
                        Errors::add('info', 'Debes estar autenticado para usar esta vista.');
                    }else{
                        Errors::add('info', 'Esta vista solo funciona sin autenticacion.');
                    }
                }
            }
        }
    }
}