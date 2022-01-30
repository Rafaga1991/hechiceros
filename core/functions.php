<?php

/**
 * Busca todos los archivos en una ruta especificada
 * 
 * @access public
 * @param string $path recive la ruta a buscar.
 * @param array $exception recive los nombres de los archivos o carpetas a obviar.
 * @return array retorna un arreglo de rutas.
 * @version 1.0
 * @author Rafael Minaya
 * @copyright R.M.B
 */
function getFiles(string $path, bool $lineal = false, array $exception = []):array{
    $files = [];
    $dir = opendir($path);
    while($file = readdir($dir)){
        if($file != '.' && $file != '..'){
            $tmp_file = "$path/$file";
            if(!in_array($file, $exception) && $file[0] != '.'){
                if(is_file($tmp_file)){
                    if($lineal){
                        $files[] = [
                            'name' => $file,
                            'path' => $tmp_file,
                            'type' => 'file'
                        ];
                    }else{
                        $files[pathinfo($tmp_file)['filename']] = $tmp_file;
                    }
                }else{
                    if($lineal){
                        $files[] = [
                            'name' => $file,
                            'path' => $tmp_file,
                            'type' => 'dir'
                        ];
                    }else{
                        $files[strtoupper($file)] = (__FUNCTION__)($tmp_file);
                    }
                }
            }
        }
    }

    return $files;
}

/**
 * Busca un indice en espeficico en una matriz de archivos.
 * 
 * @access public
 * @param array $files recive una matriz de archivos.
 * @param string $search recive el nombre del indice a buscar en la matriz.
 * @return string retorna el valor del indice encontrado.
 * @version 1.0
 * @author Rafael Minaya
 * @copyright R.M.B
 */
function getPath(array $files, string $search):string{
    $data = '';
    foreach($files as $name => $value){
        if(is_array($value)){
            if($value = (__FUNCTION__)($value, $search)){
                $data = $value;
                break;
            }
        }elseif($name == $search){
            $data = $value;
            break;
        }
    }

    $data = str_replace('//', '/', $data);

    return $data;
}

/**
 * Modifica la ruta actual y retrocede una carpeta hacia la raiz.
 * 
 * @access public
 * @param string $path recive una ruta opcional la cual sera concatenada.
 * @return string retorna la ruta raiz.
 * @version 1.0
 * @author Rafael Minaya
 * @copyright R.M.B
 */
function getRoute(string $path=''):string{
    $root = explode(((php_uname('s') == 'Windows NT') ? '\\' : '/'), __DIR__);
    array_pop($root);
    return join('/', $root) . '/' . $path;
}

/**
 * Carga y retorna una vista.
 * 
 * @access public
 * @param string $view recive la ruta de la vista sin extencion.
 * @param array $data recive un arreglo de valores a utilizar en la vista.
 * @return string retorna una vista.
 * @version 1.0
 * @author Rafael Minaya
 * @copyright R.M.B
 */
function view(string $view, array $data=[]):string{
    $file = getRoute("view/$view.php");
    if(file_exists($file)){
        ob_start();
        extract($data, EXTR_PREFIX_SAME, 'dta');
        require_once $file;
        $ob = ob_get_contents();
        ob_clean();
        return $ob;
    }
    return '';
}

/**
 * Muestra el contenido de un objeto
 * 
 * @access public
 * @param $value recive un objeto cualquiera.
 * @param $die recive un valor booleano.
 * @return void sin retorno.
 * @author Rafael Minaya
 * @copyright R.M.B.
 * @version 1.0
 */
function vdump($value, $die=true){
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    if($die) exit;
}

/**
 * Genera un id aleatorio para multiples usos.
 * 
 * @access public
 * @return void
 * @author Rafael Minaya
 * @copyright R.M.B.
 * @version 1.0
 */
function generateID(){
    $id = '';
    for($i=0; $i<10; $i++){
        $id .= (($i%2) == 0) ? chr(rand(65, 89)) : rand(0,9);
    }

    return strtolower($id);
}

/**
 * Redirecciona a otra vista del mismo u otro controlador verificando que se tenga acceso a la misma.
 * 
 * @access public
 * @param array $action recive un arreglo que contiene la clase del controlador y la función.
 * @param $data recive los datos que seran enviados a la función.
 * @return string retorna la vista de la función.
 * @author Rafael Minaya
 * @copyright R.M.B.
 * @version 1.0
 */
function redirect(array $action, $data = null):string{
    if(class_exists($action[0])){
        if(method_exists($action[0], $action[1])){
            if(Route::actionAccess($action)){
                ${$action[0]} = new $action[0]();
                if($data){
                    $view = ${$action[0]}->{$action[1]}($data);
                }else{
                    $view = ${$action[0]}->{$action[1]}();
                }
                return $view;
            }else{
                Message::add("La ruta \"<b>" . implode('/', $action) . "</b>\" no existe o no tiene acceso a esta ruta.");
            }
        }else{
            Message::add("La funci&oacute;n \"<b>{$action[1]}</b>\" del controlador \"<b>{$action[0]}</b>\" no existe.");
        }
    }else{
        Message::add("El controlador \"<b>{$action[0]}</b>\" no existe.");
    }
    return '';
}

/**
 * retrocede una carpeta y retorna la nueva ruta mediante la ruta pasada por parametro.
 * 
 * @access public
 * @param string $path recive la ruta que retrocedera.
 * @return string retorna la nueva ruta.
 * @author Rafael Minaya
 * @copyright R.M.B.
 * @version 1.0
 */
function path_back(string $path){
    $data = explode('/', $path);
    if(count($data) > 1) array_pop($data);
    return join('/', $data);
}

/**
 * retorna la ruta de un archivo dentro de una carpeta asset
 * 
 * @access public
 * @param string $path recive la ruta del archivo a retornar.
 * @return string retorna la ruta de un archivo en asset.
 * @author Rafael Minaya
 * @copyright R.M.B.
 * @version 1.0
 */
function asset(string $path){ return HOST . '/' . ASSET_DIR_NAME . '/' . $path; }

/**
 * Crea archivos o directorios.
 * 
 * @access public 
 * @param array $paths recive las rutas de los archivos o directorios a crear.
 * @param string recive la ruta a crear recursivamente.
 * @return void sin retorno.
 * @author Rafael Minaya
 * @copyright R.M.B.
 * @version 1.0
 */
function createFileOrDir(array $paths, string $route = ''){
    foreach($paths as $name => $path){
        if(!empty($path)){
            if(!is_array($path)){
                $path_info = pathinfo($path);
                if(!isset($path_info['extension'])){
                    if(!is_dir($path)){
                        if(strtolower(php_uname('s')) == 'linux') system("mkdir $path");// creando carpetas
                        else mkdir($path);
                    }
                }elseif(!file_exists($path)){
                    $content = '';
                    if($path_info['extension'] == 'json'){
                        $content = '[]';
                    }elseif($path_info['extension'] == 'html'){
                        $content = <<<HTML
                            <h1>Hola Mundo, este es un nuevo archivo.</h1>
                        HTML;
                    }elseif($path_info['extension'] == 'php'){
                        if(stripos($path_info['filename'], 'controller')){
                            $extends = stripos($path_info['filename'], 'controller') ? 'Controller' : 'Model';
                            $content = "<?php\n\nclass {$path_info['filename']} extends $extends{\n";
                            $content .= "\tpublic function index():string{\n\t\treturn view('view/login/index');\n\t}\n\n";
                            $content .= "\tpublic function show(\$id):string{\n\t\treturn view('');\n\t}\n\n";
                            $content .= "\tpublic function update(Request \$request):string{\n\t\treturn view('');\n\t}\n\n";
                            $content .= "\tpublic function destroy(Request \$request):string{\n\t\treturn view('view/login/index');\n\t}";
                            $content .= "\n}";
                        }else{
                            $content = "<?php \n\n";
                        }
                    }
                    file_put_contents($path, $content);
                }
            }else{
                if(!is_dir(getRoute($route.$name))){
                    if(strtolower(php_uname('s')) == 'linux'){
                        system("mkdir " . getRoute($route.$name));
                    }else{
                        mkdir(getRoute($route.$name));
                    }
                }
                foreach($path as &$dirname){
                    if(!is_array($dirname)) $dirname = "$route$name/$dirname";
                }
                (__FUNCTION__)($path, "$route$name/");
                $path = $route.$name;
            }
    
            if(strtolower(php_uname('s')) == 'linux' && (!is_file($path)) || file_exists($path)) system("chmod 777 $path");
        }
    }
}

/**
 * Se encarga de inicializar la página
 * 
 * @return void sin retorno.
 * @version 1.0
 * @author Rafael Minaya
 * @copyright R.M.B.
 */
(function(){
    date_default_timezone_set('America/Santo_Domingo');
    createFileOrDir(include 'file-dir.php');// creando archivos y carpetas necesarios
    /* SERVER */
    foreach($_SERVER as $index => $value) define($index, $value);
    define('HOST', REQUEST_SCHEME . '://' . str_replace('/', '', HTTP_HOST));
    /* Cargando constantes globales */
    $variable = parse_ini_file(getRoute('config.cfg'));
    foreach($variable as $index => $value) define($index, $value);
})();