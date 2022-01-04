<?php

class Session{
    public static function set($name, $value){
        $_SESSION[$name] = $value;
    }

    public static function get($name=null){
        return $_SESSION[$name] ?? $_SESSION;
    }

    public static function setTemp($name, $value){
        if(!isset($_SESSION['temp'][$name])) $_SESSION['temp'][$name] = $value;
    }

    public static function getTemp($name){
        if(($tmp = $_SESSION['temp'][$name])){
            unset($_SESSION['temp'][$name]);
            return $tmp;
        }
        return '';
    }

    public static function destroy($name=null){
        if($name != null){
            unset($_SESSION[$name]);
        }else{
            session_destroy();
        }
    }

    public static function setPage($value, $name='__PAGE__'){
        $_SESSION['page'][$name] = $value;
    }

    public static function getPage($name='__PAGE__'){
        return $_SESSION['page'][$name] ?? '';
    }

    public static function destroyPage($name='__PAGE__'){
        unset($_SESSION['page'][$name]);
    }

    public static function check($name){
        return isset($_SESSION[$name]);
    }

    public static function checkTemp($name){
        return isset($_SESSION['temp'][$name]);
    }

    public static function checkPage($name='__PAGE__'){
        return isset($_SESSION['page'][$name]);
    }

    public static function auth(){
        if(isset($_SESSION['_LOGIN_']['auth'])){
            return $_SESSION['_LOGIN_']['auth'];
        }
        return false;
    }

    public static function login($credential){
        $_SESSION['_LOGIN_'] = $credential;
    }

    public static function user($name='username'){
        return $_SESSION['_LOGIN_']['data'][$name] ?? '';
    }
}