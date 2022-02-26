<?php

namespace core;

/**
 * Crea, consulta, destruye y verifica las sesiones.
 * 
 * @access public
 * @version 1.0
 * @author Rafael Minaya
 * @copyright R.M.B
 */
class Session{
    // limite en que expiran las sesiones: 1 hora en segundos
    private static $limit_expire = 3600;

    /**
     * Inicializa la sesión
     * 
     * @access public
     * @return void sin retorno.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function init(){
        date_default_timezone_set('America/Santo_Domingo');
        session_start();

        foreach($_SESSION as $name => $value){
            if(is_array($value)){
                if(time() > $value['expire']) unset($_SESSION[$name]);
                else $_SESSION[$name]['expire'] = time()+self::$limit_expire;
            }
        }
    }

    /**
     * Agrega un nuevo indice con valor en sesion.
     * 
     * @access public
     * @param string $name recive el nombre del indice.
     * @param string $value recive el valor que se le coloca al indice.
     * @return void sin retorno.
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
     */
    public static function set(string $name, $value):void{ $_SESSION[$name] = ['value' => $value, 'expire' => (time()+3600)]; }

    /**
     * Retorna el valor de un indice en especifico.
     * 
     * @access public
     * @param string $name recive el nombre del indice.
     * @return any retorna un objeto.
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
     */
    public static function get(string $name = null){ return is_array($_SESSION[$name] ?? '') ? $_SESSION[$name]['value'] : null; }

    /**
     * Verifica si el indice existe.
     * 
     * @access public
     * @param string $name recive el nombre del indice a verificar.
     * @return any retorna un onjeto.
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
     */
    public static function check(string $name):mixed{ return isset($_SESSION[$name]); }

    /**
     * Elimina uno o todos los indices en sesion.
     * 
     * @access public
     * @param string $name recive el nombre del indice a destruir.
     * @return void sin retorno.
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
     */
    public static function destroy(string $name = ''):void{
        if(empty($name)) session_destroy();
        else unset($_SESSION[$name]);
    }

    /**
     * Destruye la sesion de un usuario.
     * 
     * @access public
     * @return void sin retorno.
     * @version 1.0
     * @author Rafael Mimaya
     * @copyright R.M.B
     */
    public static function destroyUser(){ self::destroy('user_' . self::get('id')); }

    /**
     * Agrega las credenciales de un usuario en sesion.
     * 
     * @access public
     * @param mixed $credentials recive un arreglo con los datos del usuario logueado.
     * @return void sin retorno.
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
     */
    public static function setUser($credentials = [], string $rolcolumn='rol'):void{
        if(!self::auth()){
            self::set('id', substr(md5( time().chr(rand(0, 100)).rand(0, 100).chr(rand(0, 100)) ), 0, 10));
            self::set(
                'user_' . self::get('id'), 
                [
                    'credential' => serialize($credentials), 
                    'auth' => true, 
                    'rol' => $credentials->{$rolcolumn} ?? 0
                ]
            );
        }
    }

    public static function updateUser($credentials=[]){
        if(self::auth()){
            $id = 'user_' . self::get('id');
            $credential = self::get($id);
            $credential['credential'] = serialize($credentials);
            self::set($id, $credential);
        }
    }

    public static function getRol(){
        if(self::auth()) return self::get('user_' . self::get('id'))['rol'] ?? 0;
        return 0;
    }

    /**
     * Retorna las credenciales del usuario logueado.
     * 
     * @access public
     * @return tsring retorna un valor de la columna.
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
     */
    public static function getUser(string $columnname):string{ return unserialize(self::get('user_' . self::get('id'))['credential'] ?? [])->{$columnname}; }

    /**
     * Verifica si el usuario esta logueado.
     * 
     * @access public
     * @return bool retorna un valor booleano si esta logueado.
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
     */
    public static function auth():bool{ return self::get('user_' . self::get('id'))['auth'] ?? false; }
}