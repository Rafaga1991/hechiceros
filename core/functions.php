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
function getFiles(string $path, array $exception = []):array{
    $files = [];
    $dir = opendir($path);
    while($file = readdir($dir)){
        if($file != '.' && $file != '..'){
            $tmp_file = "$path/$file";
            if(!in_array($file, $exception) && $file[0] != '.'){
                if(is_file($tmp_file)){
                    $files[pathinfo($tmp_file)['filename']] = $tmp_file;
                }else{
                    $files[strtoupper($file)] = (__FUNCTION__)($tmp_file);
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

function vdump($value, $die=true){
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    if($die) exit;
}

function generateID(){
    $id = '';
    for($i=0; $i<10; $i++){
        $id .= (($i%2) == 0) ? chr(rand(65, 89)) : rand(0,9);
    }

    return strtolower($id);
}

/**
 * Se encarga de inicializar la página
 * 
 * @return void sin retorno.
 * @version 1.0
 * @author Rafael Minaya
 * @copyright R.M.B
 */
(function(){
    date_default_timezone_set('America/Santo_Domingo');
    /* SERVER */
    foreach($_SERVER as $index => $value){
        define($index, $value);
    }
    define('HOST', REQUEST_SCHEME . '://' . HTTP_HOST);
    /* Buscando y eliminando las sesiones expiradas */
    session_start();
    foreach($_SESSION as $name => $value){
        if(is_array($value)){
            if(time() > $value['expire']){
                unset($_SESSION[$name]);
            }
        }
    }
    /* Cargando constantes globales */
    $variable = parse_ini_file(getRoute('config.cfg'));
    foreach($variable as $index => $value){
        define($index, $value);
    }
})();