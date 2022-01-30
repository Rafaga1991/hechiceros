<?php

class Route{
    private $routes = [];
    private $path = '';
    private $controller = [];
    private $name = 'url';
    private $auth = false;
    private $view = '';

    private static $redirects = null;

    public function __destruct(){
        Session::set('_ROUTES_', $this->routes);
        Session::set('route', self::$redirects ?? Session::get('route'));
    }

    public function set(string $path, $controller=[]){
        $this->path = $path;
        $this->controller = $controller;

        return $this;
    }

    public function name(string $name){
        $this->name = $name;
        return $this;
    }

    public function auth(bool $auth = true){
        $this->auth = $auth;
        return $this;
    }

    public function save(){
        $this->routes[$this->name] = [
            'path' => $this->path,
            'auth' => $this->auth,
            'action' => implode('/', $this->controller),
            'controller' => $this->controller[0],
            'function' => $this->controller[1]
        ];
        return $this;
    }

    public function get(string $name):string{
        if($value = ($this->routes[$name] ?? false)){
            if($value['auth'] == Session::auth()){
                return HOST . "/{$value['path']}";
            }
        }
        return '#';
    }

    public function init(&$view){
        $url = parse_url($_SERVER['REQUEST_URI']);
        $query = parse_ini_string(implode("\n", explode('&', $url['query'] ?? '')));
        $request = new Request($query);
        $url = $url['path'];
        $action = array_values(array_filter(explode('/', $url)));
        $data = ['controller' => '/', 'function' => '', 'params' => '', 'existparam' => false, 'cantparam' => 0];
        $id = '';

        foreach($action as $index => $item){
            if($index == 0){
                $data['controller'] = $item;
            }elseif($index == 1){
                $data['function'] = "/$item";
            }else{
                if($index == 2) $id = $item;
                $data['params'] .= "'$item',";
                $data['existparam'] = true;
                $data['cantparam']++;
            }
        }

        $data['params'] = substr($data['params'], 0, strlen($data['params'])-1);
        $url = "{$data['controller']}{$data['function']}";

        if($route = $this->getRoute($url)){
            if($route['auth'] == Session::auth()){
                ${$route['controller']} = new $route['controller']();
                $val = new ReflectionMethod($route['controller'], $route['function']);
                $cantParam = count($val->getParameters());

                if($value = $this->isRoute($id)){
                    $value = unserialize($value);
                    $view = ${$route['controller']}->{$route['function']}($value['data']);
                }else{
                    if($request->isData()){
                        if(++$data['cantparam'] == $cantParam){
                            if($data['existparam']){
                                eval('$view = $' . "{$route['controller']}->{$route['function']}($" . "request,{$data['params']});");
                            }else{
                                $view = ${$route['controller']}->{$route['function']}($request);
                            }
                        }else{
                            Errors::add("El metodo \"<b>{$route['function']}</b>\" recive $cantParam parametro.");
                        }
                    }elseif($data['existparam']){// parametros en la url
                        if($data['cantparam'] == $cantParam){
                            eval('$' . 'view = $' . "{$route['controller']}->{$route['function']}({$data['params']});");
                        }else{
                            Errors::add("El metodo \"<b>{$route['controller']}::{$route['function']}</b>\" recive $cantParam parametro.");
                        }
                    }else{
                        if($data['cantparam'] == $cantParam){
                            $view = ${$route['controller']}->{$route['function']}();
                        }else{
                            Errors::add("El metodo \"<b>{$route['controller']}::{$route['function']}</b>\" recive $cantParam parametro.");
                        }
                    }
                }
                
            }else{
                if(Session::auth()){
                    Errors::add("Debes cerrar sesión para acceder a <b>\"$url\"</b>.");
                }else{
                    Errors::add("Debes estar logueado para acceder a la ruta <b>\"$url\"</b>.");
                }
            }
        }else{
            Errors::add("La ruta \"<b>$url</b>\" no existe.");
        }
    }

    private function getRoute($action){
        foreach($this->routes as $route){
            if(strtolower($action) == strtolower($route['path'])){
                return $route;
            }
        }
        return null;
    }

    public function getRoutes(){
        return $this->routes;
    }

    private function isRoute($id){
        return Session::get('route')[$id] ?? false;
    }

    public static function redirect(string $name, $data = null){
        $routes = Session::get('_ROUTES_');
        if($route = ($routes[$name] ?? null)){
            self::$redirects[$id = generateID()] = serialize(['route' => $route, 'data' => $data]);
            return "{$route['path']}/$id";
        }
        return '#';
    }
}