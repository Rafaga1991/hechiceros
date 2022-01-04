<?php

trait Route{
    private static $routes = [];

    public static function set($path, $name, $auth = false){
        self::$routes[strtolower($name)] = ['path' => strtolower($path), 'auth' => $auth];
    }

    public static function get($name){
        return '/' . (self::$routes[$name]['path'] ?? '');
    }

    public static function exist($path){
        $data = null;
        foreach(self::$routes as $route){
            if($route['path'] == strtolower($path)){
                $data = ['exist'=>true, 'auth'=>$route['auth']];
                break;
            }
        }
        return $data;
    }

    public static function asset($path){
        $location = '.';
        if(isset($_SERVER['REDIRECT_URL'])){
            $url_data = explode('/', $_SERVER['REDIRECT_URL']);
            $location = str_repeat('/..', (count($url_data)-1));
        }
        return "$location/assets/$path";
    }

    public static function isView($name){
        return strtolower(Session::getPage()) == strtolower($name);
    }
}