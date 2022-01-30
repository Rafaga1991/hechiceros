<?php

/**
 * Crea, consulta, destruye y verifica las sesiones.
 * 
 * @access public
 * @version 1.0
 * @author Rafael Minaya
 * @copyright R.M.B
 */
class Session{
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
    public static function set(string $name, $value):void{
        $_SESSION[$name] = ['value' => $value, 'expire' => (time()+3600)];
    }

    /**
     * Retorna el valor de un indice en especifico.
     * 
     * @access public
     * @param string $name recive el nombre del inice.
     * @return any retorna un objeto.
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
     */
    public static function get(string $name = null){
        return is_array($_SESSION[$name] ?? '') ? $_SESSION[$name]['value'] : null;
    }

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
    public static function check(string $name):mixed{
        return isset($_SESSION[$name]);
    }

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
    public static function destroyUser(){
        self::destroy('user_' . self::get('id'));
    }

    /**
     * Agrega las credenciales de un usuario en sesion.
     * 
     * @access public
     * @param array $credentials recive un arreglo con los datos del usuario logueado.
     * @return void sin retorno.
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
     */
    public static function setUser(array $credentials = []):void{
        if(!self::auth()){
            self::set('id', substr(md5( time().chr(rand(0, 100)).rand(0, 100).chr(rand(0, 100)) ), 0, 10));
            self::set('user_' . self::get('id'), ['credential' => $credentials, 'auth' => true]);
        }
    }

    /**
     * Retorna las credenciales del usuario logueado.
     * 
     * @access public
     * @return array retorna un arreglo de las credenciales del usuario.
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
     */
    public static function getUser():array{
        return self::get('user_' . self::get('id'))['credential'] ?? [];
    }

    /**
     * Verifica si el usuario esta logueado.
     * 
     * @access public
     * @return bool retorna un valor booleano si esta logueado.
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
     */
    public static function auth():bool{
        return self::get('user_' . self::get('id'))['auth'] ?? false;
    }
}