<?php

namespace core;

include './vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;
use stdClass;

/**
 * Busca todos los archivos en una ruta especificada
 * 
 * @access public
 * @param string $path recive la ruta a buscar.
 * @param array $exception recive los nombres de los archivos o carpetas a obviar.
 * @return array retorna un arreglo de rutas.
 * @author Rafael Minaya
 * @copyright R.M.B
 * @version 1.0
 */
function getFiles(string $path, bool $lineal = false, array $exception = []): array
{
    $files = [];
    $dir = opendir($path);
    while ($file = readdir($dir)) {
        if ($file != '.' && $file != '..') {
            $tmp_file = "$path/$file";
            if (!in_array($file, $exception) && $file[0] != '.') {
                if (is_file($tmp_file)) {
                    if ($lineal) {
                        $files[] = [
                            'name' => $file,
                            'path' => $tmp_file,
                            'type' => 'file'
                        ];
                    } else {
                        $files[pathinfo($tmp_file)['filename']] = $tmp_file;
                    }
                } else {
                    if ($lineal) {
                        $files[] = [
                            'name' => $file,
                            'path' => $tmp_file,
                            'type' => 'dir'
                        ];
                    } else {
                        $files[strtoupper($file)] = getFiles($tmp_file);
                    }
                }
            }
        }
    }

    return $files;
}

function html2pdf(string $html):string{
    // Instanciamos un objeto de la clase DOMPDF.
    $option = new Options();
    $option->setIsRemoteEnabled(true);

    $pdf = new DOMPDF($option);

    // htmlspecialchars()
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->setPaper('letter');
    
    // Renderizamos el documento PDF.
    $pdf->render();
    
    return $pdf->output();
}

function createImage($path, $type='image'):string{
    $path_info = pathinfo($path);
    $extension = $path_info['extension'] ?? 'png';
    $data = (strpos($path, 'http') == 0) ? curl_get_contents($path) : file_get_contents($path);
    return "data:$type/$extension;base64," . base64_encode($data);
}

function curl_get_contents($url)
{
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

function strRepeat($html, $function, $isArray=false){
    preg_match_all('/%[a-zA-Z]{0,20}%/', $html, $values);
    $values = $values[0];
    $acum_html = $isArray ? [] : '';
    $class = new stdClass;
    $class->__ID__ = 0;
    $class->__STOP__ = true;
    foreach($values as $key => $value){
        $values[$key] = str_replace('%', '', $value);
        $class->{$values[$key]} = '';
    }
    while(true){
        if(!($break = !(($data = $function($class)) instanceOf $class))){
            $acum = $html;
            
            if($data->__STOP__ && $data === $class) break;

            foreach($values as $value){
                $acum = str_replace("%$value%", $data->{$value}, $acum);
            }

            if($isArray) $acum_html[] = $acum;
            else $acum_html .= $acum;

            if(!$values && $isArray) $acum_html = [];
            elseif(!$values && !$isArray) $acum_html = '';

            $class->__ID__++;
        }
        if($break || $data->__STOP__) break;
    }
    return $acum_html;
}

/**
 * Busca un indice en espeficico en una matriz de archivos.
 * 
 * @access public
 * @param array $files recive una matriz de archivos.
 * @param string $search recive el nombre del indice a buscar en la matriz.
 * @return string retorna el valor del indice encontrado.
 * @author Rafael Minaya
 * @copyright R.M.B
 * @version 1.0
 */
function getPath(array $files, string $search): string
{
    $data = '';
    foreach ($files as $name => $value) {
        if (is_array($value)) {
            if ($value = getPath($value, $search)) {
                $data = $value;
                break;
            }
        } elseif ($name == $search) {
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
 * @author Rafael Minaya
 * @copyright R.M.B
 * @version 1.0
 */
function getRoute(string $path = ''): string
{
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
 * @author Rafael Minaya
 * @copyright R.M.B
 * @version 1.0
 */
function view(string $view, array $data = []): string
{
    $file = getRoute("view/$view.php");
    if (file_exists($file)) {
        ob_start();
        extract($data, EXTR_PREFIX_SAME, 'dta');
        include $file;
        $ob = ob_get_contents();
        ob_clean();
        ob_flush();
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
function dd($value, $die = true): void
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    if ($die) exit;
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
function generateID(): string
{
    $id = '';
    for ($i = 0; $i < 10; $i++) {
        $id .= (($i % 2) == 0) ? chr(rand(65, 89)) : rand(0, 9);
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
function redirect(array $action, $data = null): string
{
    if (class_exists($action[0])) {
        if (method_exists($action[0], $action[1])) {
            if (Route::actionAccess($action)) {
                ${$action[0]} = new $action[0]();
                if ($data) {
                    $view = ${$action[0]}->{$action[1]}($data);
                } else {
                    $view = ${$action[0]}->{$action[1]}();
                }
                return $view;
            } else {
                Message::add("La ruta \"<b>" . implode('/', $action) . "</b>\" no existe o no tiene acceso a esta ruta.");
            }
        } else {
            Message::add("La funci&oacute;n \"<b>{$action[1]}</b>\" del controlador \"<b>{$action[0]}</b>\" no existe.");
        }
    } else {
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
function path_back(string $path): string
{
    $data = explode('/', $path);
    if (count($data) > 1) array_pop($data);
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
function asset(string $path): string
{
    return HOST . '/' . ASSET_DIR_NAME . '/' . $path;
}

/**
 * Crea archivos o directorios.
 * 
 * @access 
 * @param array $paths recive las rutas de los archivos o directorios a crear.
 * @param string recive la ruta a crear recursivamente.
 * @return void sin retorno.
 * @author Rafael Minaya
 * @copyright R.M.B.
 * @version 1.0
 */
function createFileOrDir(array $paths, string $route = ''): void
{
    foreach ($paths as $name => $path) {
        if (!empty($path)) {
            if (!is_array($path)) {
                $path_info = pathinfo($path);
                if (!isset($path_info['extension'])) {
                    if (!is_dir($path)) {
                        if (strtolower(php_uname('s')) == 'linux') system("mkdir $path"); // creando carpetas
                        else mkdir($path);
                    }
                } elseif (!file_exists($path)) {
                    $content = '';
                    if ($path_info['extension'] == 'json') {
                        $content = '[]';
                    } elseif ($path_info['extension'] == 'html') {
                        $content = <<<HTML
                                <h1>Hola Mundo, este es un nuevo archivo.</h1>
                            HTML;
                    } elseif ($path_info['extension'] == 'php') {
                        if (stripos($path_info['filename'], 'controller')) {
                            $_PATH = pathinfo($path);
                            $_PATH['dirname'] = str_replace('/', "\\", $_PATH['dirname']);

                            $content = "<?php\n\nnamespace {$_PATH['dirname']};\n\nuse core\\{Functions,Html,Request,Controller};\n\nclass {$path_info['filename']} extends Controller{\n";
                            $content .= "\tfunction index():string{\n\t\treturn view('');\n\t}\n\n";
                            $content .= "\tfunction show(\$id):string{\n\t\treturn view('');\n\t}\n\n";
                            $content .= "\tfunction update(Request \$request):string{\n\t\treturn view('');\n\t}\n\n";
                            $content .= "\tfunction destroy(Request \$request):string{\n\t\treturn view('');\n\t}";
                            $content .= "\n}";
                        } else {
                            $content = "<?php namespace core;?>\n<h1>Hola Mundo</h1>";
                        }
                    }
                    file_put_contents($path, $content);
                }
            } else {
                if (!is_dir(getRoute($route . $name))) {
                    if (strtolower(php_uname('s')) == 'linux') {
                        system("mkdir " . getRoute($route . $name));
                    } else {
                        mkdir(getRoute($route . $name));
                    }
                }
                foreach ($path as &$dirname) {
                    if (!is_array($dirname)) $dirname = "$route$name/$dirname";
                }
                createFileOrDir($path, "$route$name/");
                $path = $route . $name;
            }

            if (strtolower(php_uname('s')) == 'linux' && (!is_file($path)) || file_exists($path)) system("chmod 777 $path");
        }
    }
}

/**
 * Verifica si el usuario actual es administrador.
 * 
 * @access public
 * @return bool retorna un buleano indicando si es administrador o no.
 * @author Rafael Minaya
 * @copyright R.M.B.
 * @version 1.0
 */
function isAdmin(): bool { return Session::getRol() == Route::ROL_ADMIN; }

/**
 * elimina una palabra u oracion en un cadena de texto.
 * 
 * @access public
 * @param string $text recive el texto donde se realizará la busqueda.
 * @param string $char_start recive el texto inicial a buscar.
 * @param string $char_end recive el texto final a buscar.
 * @return string retorna una cadena de texto ya recorrida. 
 * @author Rafael Minaya
 * @copyright R.M.B.
 * @version 1.0
 */
function replace(string $text, string $char_start, string $char_end): string
{
    $char_end = '\\' . join('\\', str_split($char_end));
    $char_start = '\\' . join('\\', str_split($char_start));
    $text = preg_replace("/$char_start\w+$char_end/", '', $text);
    return $text;
}

/**
 * Muestra una alerta.
 * 
 * @access public
 * @param string $message recive el mensaje que será mostrado.
 * @param string $type recive el tipo del mensaje que será mostrado.
 * @return string retorna una alerta.
 * @author Rafael Minaya
 * @copyright R.M.B.
 * @version 1.0
 */
function alert(string $title, string $message, string $type): string
{
    return <<<HTML
            <div class="alert alert-$type" role="alert">
                <h2>$title</h2>
                $message
            </div>
        HTML;
}

/**
 * Traduce una palabra a español o ingles.
 * 
 * @access public
 * @param string $value recive el valor a traducir.
 * @param string $lang recive el idioma al que será traducido.
 * @return string retorna la palabra traducida.
 * @author Rafael Minaya
 * @copyright R.M.B.
 * @version 1.0
 */
function traslate(string $value, string $lang = 'es'): string
{
    $word = include 'languaje.php';
    return ucfirst($word[$lang][strtolower($value)] ?? $value);
}

/**
 * Redirecciona a una url especifica.
 * 
 * @access public
 * @param string $path recive la ruta a redireccionar.
 * @return void sin retorno.
 * @author Rafael Minaya
 * @copyright R.M.B.
 * @version 1.2
 */
function reload(string $path): void
{
    $path = HOST . (($path == '/') ? '' : $path);
    header("location: $path");
}

/**
 * Verifica si existe un valor en el arreglo y retorna el valor enviado por parametros.
 * 
 * @access public
 * @param mixed $data recive el valor a buscar.
 * @param array $value recive un arreglo de posibles coincidencias.
 * @param array $return recive un arreglo de posibles valores a retornar.
 * @return string retorna un string del valor encontrado.
 * @author Rafael Minaya.
 * @copyright R.M.B.
 * @version 1.0
 */
function inArray($data, array $value, array $return)
{
    foreach ($value as $key => $val) {
        if ($data == $val) return $return[$key] ?? null;
    }
}

/**
 * incluye archivos php con sus datos correspondientes.
 * 
 * @access public
 * @param $path recive las rutas que seran incluidas.
 * @param $data recive los datos que serán incluidos en cada ruta especifica.
 * @return void sin retorno.
 * @author Rafael Minaya.
 * @copyright R.M.B.
 * @version 1.0
 */
function includes($path, $data = [])
{
    if (is_array($path)) {
        foreach ($path as $key => $value) {
            if (isset($data[$key])) extract($data[$key]);
            include $value;
        }
    } else {
        extract($data);
        include $path;
    }
}

function getValue(array $arr, array $keys): array{
    $data = [];
    foreach($keys as $key){
        if(count($rename = explode(':', $key)) > 1 && isset($arr[$rename[0]])) $data[$rename[1]] = $arr[$rename[0]];
        elseif(isset($arr[$key])) $data[$key] = $arr[$key];
    }
    return $data;
}

/**
 * Se encarga de inicializar la página
 * 
 * @return void sin retorno.
 * @author Rafael Minaya
 * @copyright R.M.B.
 * @version 1.5
 */
function init()
{
    date_default_timezone_set('America/Santo_Domingo');
    createFileOrDir(include 'file-dir.php'); // creando archivos y carpetas necesarios
    /* SERVER */
    foreach ($_SERVER as $index => $value) define($index, $value);
    define('HOST', REQUEST_SCHEME . '://' . str_replace('/', '', HTTP_HOST));
    /* Cargando constantes globales */
    $variable = parse_ini_file(getRoute('config.cfg'));
    foreach ($variable as $index => $value) define($index, $value);
}
