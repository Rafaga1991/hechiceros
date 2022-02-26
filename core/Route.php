<?php

namespace core;

use ReflectionMethod;

class Route
{
    public const ROL_USER = 0;
    public const ROL_ADMIN = 1;
    public const ROL = ['Usuario','Administrador'];

    private $routes = [];
    private $path = '';
    private $controller = [];
    private $name = null;
    private $auth = false;
    private $rol = self::ROL_USER;


    private static $redirects = null;

    public function __destruct() { Session::set('route', self::$redirects ?? Session::get('route')); }

    public function set(string $path, $controller = [])
    {
        $this->path = $path;
        $this->controller = $controller;

        return $this;
    }

    public function name(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function rol($rol = self::ROL_USER)
    {
        $this->rol = $rol;
        return $this;
    }

    public function auth(bool $auth = true)
    {
        $this->auth = $auth;
        return $this;
    }

    public function save()
    {
        $this->name = $this->name ?? 0;
        $this->routes[$this->name] = [
            'path' => $this->path,
            'auth' => $this->auth,
            'action' => implode('/', $this->controller),
            'controller' => $this->controller[0],
            'function' => $this->controller[1],
            'name' => $this->name,
            'rol' => $this->rol
        ];
        $this->name = count($this->routes);
        $this->auth = false;
        $this->rol = self::ROL_USER;
        Session::set('_ROUTES_', $this->routes);
        return $this;
    }

    public static function get(string $name)
    {
        $routes = Session::get('_ROUTES_');
        if ($route = ($routes[$name] ?? null)) {
            if ($route['auth'] == Session::auth()) {
                if ($route['path'] != '/') {
                    return HOST . "{$route['path']}";
                } else {
                    return HOST;
                }
            }
        }else{
            foreach($routes as $route){
                if($route['path'] == $name){
                    return $route;
                }
            }
        }
        return '#';
    }

    private function objectExist(string $class, string $function): bool
    {
        if ($exist = class_exists($class)) {
            if (!$exist = method_exists($class, $function)) Message::add("La función <b>$function</b> no existe");
        } else {
            Message::add("La clase <b>$class</b> no existe");
        }
        return $exist;
    }

    public function init(&$view)
    {
        $error = [ // mensajes de posibles errores peligrosos
            '403' => 'ERROR [{ERROR}] No tienes permiso para acceder a esta ruta.',
            '404' => 'ERROR [{ERROR}] La p&aacute;gina no existe.'
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

        foreach ($action as $index => $item) {
            if ($index <= 1) {
                $tpm_url[] = $item;
            } elseif ($index > 1) {
                $data['params'] .= "'$item',";
                $data['existparam'] = true;
                $data['cantparam']++;
            } elseif ($index == 0 && array_key_exists($item, $error)) {
                $data['error'] = ['type' => $item, 'message' => str_replace('{ERROR}', $item, $error[$item])];
            }

            if ($route = $this->isRoute($item)) {
                $data['route'] = unserialize($route);
                break;
            }
        }

        $data['params'] = substr($data['params'], 0, strlen($data['params']) - 1);
        if ($data['route']) {
            $route = $data['route']['route'];
            if ($this->objectExist($route['controller'], $route['function'])) {
                Session::set('_view_', $route['name']);
                ${$route['controller']} = new $route['controller']();

                if ($request->isData() && $data['existparam']) {
                    eval('$view = $' . $route['controller'] . '->' . $route['function'] . '($request,' . $data['route']['data'] . ',' . $data['params'] . ');');
                }elseif ($request->isData()){
                    eval('$view = $' . $route['controller'] . '->' . $route['function'] . '($request,"' . $data['route']['data'] . '");');
                }else{
                    $view = ${$route['controller']}->{$route['function']}($data['route']['data']);
                }
                // }else{
                //     eval('$view = $' . $route['controller'] . '->' . $route['function'] . '(' . $data['route']['data'] . ',' . $data['params'] . ');');
                // }

                if($view){
                    Session::set('__LAST_ROUTE__', (Session::get('__CURRENT_ROUTE__') != $route['path']?Session::get('__CURRENT_ROUTE__'):Session::get('__LAST_ROUTE__')));
                    Session::set('__CURRENT_ROUTE__', $route['path']);
                }else{
                    exit;// se detiene si la vista es null.
                }
            }
        } elseif ($route = ($this->getRoute($url) ?? $this->getRoute('/' . join('/', $tpm_url)))) {
            if ($route['auth'] == Session::auth()) {
                if ($this->objectExist($route['controller'], $route['function'])) {
                    if($route['rol'] == Session::getRol() || Session::getRol() == self::ROL_ADMIN){
                        Session::set('_view_', $route['name']);
                        $variable = 'v' . Functions::generateID();
                        ${$variable} = new $route['controller']();
                        $reflection = new ReflectionMethod($route['controller'], $route['function']);
                        
                        if ($request->isData() && $data['existparam']){
                            eval('$view = $' . $variable . '->' . $route['function'] . '($request,' . $data['params'] . ');');
                        }elseif ($request->isData()) {
                            $view = ${$variable}->{$route['function']}($request);
                        }elseif ($data['existparam']){
                            eval('$view = $' . $variable . '->' . $route['function'] . '(' . $data['params'] . ');');
                        }elseif ($reflection->getNumberOfParameters() == 0) {
                            $view = ${$variable}->{$route['function']}();
                        }else{
                            Message::add('Se requieren parámetros para esta url.');
                        }
                        
                        if($view){
                            Session::set('__LAST_ROUTE__', (Session::get('__CURRENT_ROUTE__') != $route['path']?Session::get('__CURRENT_ROUTE__'):Session::get('__LAST_ROUTE__')));
                            Session::set('__CURRENT_ROUTE__', $route['path']);
                        }else{
                            exit;// se detiene si la vista es null.
                        }
                    }else{
                        Message::add('Acceso denegado');
                    }
                }
            } else {
                if (Session::auth()) Message::add('Debes cerrar sessión para acceder a esta ruta.');
                else Message::add('Debes estar autenticado para acceder a esta ruta.');
            }
        } else {
            // Message::add("La url \"<b>$url</b>\" no existe!");
        }
        
        if (is_array($view)) $view = json_encode($view);
        elseif(is_object($view)) $view = serialize($view);
        
        if ($data['api']) {
            if (!Message::exist()) echo $view; // mostrando resultados de la consulta
            else echo json_encode(['message' => 'Se requieren parámetros.', 'type' => 'error']);
            exit; // finalizando programa
        }
        
        if ($data['error']) { // verificando si existen errores en la redireccion
            Message::clear(); // borrando errores anteriores
            Message::add($data['error']['message'], 'danger'); // agregando nuevo error
        }
        
        // if(!$view) Functions::reload(Session::get('__LAST_ROUTE__')??'');
    }

    private function getRoute($path)
    {
        foreach ($this->routes as $route) {
            if (strtolower($path) == strtolower($route['path'])) return $route;
        }
        return null;
    }

    public function getRoutes() { return $this->routes; }

    private function isRoute($id) { return Session::get('route')[$id] ?? null; }

    public static function actionAccess(array $action)
    {
        $routes = Session::get('_ROUTES_');
        foreach ($routes as $value) {
            if (implode('/', $action) == $value['action']) {
                return $value['auth'] == Session::auth();
            }
        }
        return false;
    }

    public static function redirect(string $name, $data = null)
    {
        $routes = Session::get('_ROUTES_');
        if ($route = ($routes[$name] ?? null)) {
            self::$redirects[$id = Functions::generateID()] = serialize(['route' => $route, 'data' => $data]);
            return "{$route['path']}/$id";
        }
        return '#';
    }

    public static function isView(string $viewname) { return Session::get('_view_') == $viewname; }

    public static function reload(string $name, string $data='')
    {
        if ($route = (Session::get('_ROUTES_')[$name] ?? null)) {
            header("location: " . (HOST . $route['path'] . (!empty($data)?'/'.$data.'/':'')));
            exit;
        }else{
            Message::add("El nombre <b>$name</b> no existe!");
        }
    }
}
