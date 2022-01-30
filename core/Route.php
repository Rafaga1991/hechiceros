<?php

class Route{
    private $routes = [];
    private $path = '';
    private $controller = [];
    private $name = null;
    private $auth = false;

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
        $this->name = $this->name ?? 0;
        $this->routes[$this->name] = [
            'path' => $this->path,
            'auth' => $this->auth,
            'action' => implode('/', $this->controller),
            'controller' => $this->controller[0],
            'function' => $this->controller[1],
            'name' => $this->name
        ];
        $this->name = count($this->routes);
        $this->auth = false;
        return $this;
    }

    public static function get(string $name):string{
        $routes = Session::get('_ROUTES_');
        foreach($routes as $name_route => $route){
            if($name_route == $name){
                if($route['auth'] == Session::auth()){
                    if($route['path'] != '/'){
                        return HOST . "{$route['path']}";
                    }else{
                        return HOST;
                    }
                }
            }
        }
        return '#';
    }

    private function objectExist(string $class, string $function):bool{
        $exist = false;
        if(class_exists($class)){
            if(method_exists($class, $function)) $exist = true;
            else Message::add("La función <b>$function</b> no existe");
        }else{
            Message::add("La clase <b>$class</b> no existe");
        }
        return $exist;
    }

    public function init(&$view){
        $error = [// mensajes de posibles errores peligrosos
            '403' => 'ERROR [{ERROR}] No tienes permiso para acceder a esta ruta.',
            '404' => 'ERROR [{ERROR}] La p&aacute;gina no existe.',
            '500' => 'ERROR [{ERROR}] La p&aacute;gina no existe.'
        ];

        $url = parse_url($_SERVER['REQUEST_URI']);
        $query = parse_ini_string(implode("\n", explode('&', $url['query'] ?? '')));
        $request = new Request($query);
        $url = $url['path'];
        $tpm_url = [];
        $action = array_values(array_filter(explode('/', $url)));
        $data = [
            'url' => '',
            'params' => '', 
            'existparam' => false, 
            'cantparam' => 0, 
            'route' => null, 
            'api' => Api::exist($request->__token), 
            'error' => null
        ];
        
        foreach($action as $index => $item){
            if($index <= 1){
                $tpm_url[] = $item;
            }elseif($index > 1){
                $data['params'] .= "'$item',";
                $data['existparam'] = true;
                $data['cantparam']++;
            }elseif($index == 0 && array_key_exists($item, $error)){
                $data['error'] = ['type' => $item, 'message' => str_replace('{ERROR}', $item, $error[$item])];
            }

            if($route = $this->isRoute($item)){
                $data['route'] = unserialize($route);
            }
        }
        
        $data['params'] = substr($data['params'], 0, strlen($data['params'])-1);
        
        if($data['route']){
            $route = $data['route']['route'];
            if($this->objectExist($route['controller'], $route['function'])){
                Session::set('_view_', $route['name']);
                ${$route['controller']} = new $route['controller']();
                if($request->isData() && $data['existparam']){
                    eval('$view = $' . $route['controller'] . '->' . $route['function'] . '($request,' . $data['route']['data'] . ',' . $data['params'] . ');');
                }elseif($request->isData()){
                    eval('$view = $' . $route['controller'] . '->' . $route['function'] . '($request,"' . $data['route']['data'] . '");');
                }elseif($data['existparam']){
                    eval('$view = $' . $route['controller'] . '->' . $route['function'] . '(' . $data['route']['data'] . ',' . $data['params'] . ');');
                }else{
                    $view = ${$route['controller']}->{$route['function']}($data['route']['data']);
                }
            }
        }elseif($route = ($this->getRoute($url) ?? $this->getRoute('/' . join('/', $tpm_url)))){
            if($route['auth'] == Session::auth()){
                if($this->objectExist($route['controller'], $route['function'])){
                    Session::set('_view_', $route['name']);

                    ${$route['controller']} = new $route['controller']();
        
                    $reflection = new ReflectionMethod($route['controller'], $route['function']);
    
                    if($request->isData() && $data['existparam']){
                        eval('$view = $' . $route['controller'] . '->' . $route['function'] . '($request,' . $data['params'] . ');');
                    }elseif($request->isData()){
                        $view = ${$route['controller']}->{$route['function']}($request);
                    }elseif($data['existparam']){
                        eval('$view = $' . $route['controller'] . '->' . $route['function'] . '(' . $data['params'] . ');');
                    }elseif($reflection->getNumberOfParameters() == 0){
                        $view = ${$route['controller']}->{$route['function']}();
                    }else{
                        Message::add('Se requieren parametros para esta url.');
                    }
                }
            }else{
                if(Session::auth()){
                    Message::add('Debes cerrar sessión para acceder a esta ruta.');
                }else{
                    Message::add('Debes estar autenticado para acceder a esta ruta.');
                }
            }
        }else{
            Message::add("La url \"<b>$url</b>\" no existe!");
        }

        if(is_array($view)){
            $view = json_encode($view);
        }elseif(is_object($view)){
            $view = serialize($view);
        }else{
            $token = md5(microtime());
            $view = str_ireplace(['</form>', '{!!TOKEN!!}'], ["<input type='hidden' name='__token' value='$token' /></form>", $token], $view, $cant);
            if($cant) Session::set('__token', $token);
        }

        if($data['api']){
            if(!Message::exist()){// verificando si existen errores
                echo $view;// mostrando resultados de la consulta
            }else{
                echo json_encode([
                    'message' => 'Se requieren parametros.', 
                    'type' => 'error'
                ]);
            }
            exit;// finalizando programa
        }

        if($data['error']){// verificando si existen errores en la redireccion
            Message::clear();// borrando errores anteriores
            Message::add($data['error']['message'], 'danger');// agregando nuevo error
        }
    }

    private function getRoute($path){
        foreach($this->routes as $route){
            if(strtolower($path) == strtolower($route['path'])){
                return $route;
            }
        }
        return null;
    }

    public function getRoutes(){
        return $this->routes;
    }

    private function isRoute($id){
        return Session::get('route')[$id] ?? null;
    }

    public static function actionAccess(array $action){
        $routes = Session::get('_ROUTES_');
        foreach($routes as $value){
            if(implode('/', $action) == $value['action']){
                return $value['auth'] == Session::auth();
            }
        }
        return false;
    }

    public static function redirect(string $name, $data = null){
        $routes = Session::get('_ROUTES_');
        if($route = ($routes[$name] ?? null)){
            self::$redirects[$id = generateID()] = serialize(['route' => $route, 'data' => $data]);
            return "{$route['path']}/$id";
        }
        return '#';
    }

    public static function isView(string $viewname){
        return Session::get('_view_') == $viewname;
    }
}