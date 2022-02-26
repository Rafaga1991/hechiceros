<?php

namespace core;

class Api
{
    private static $file_api = '';
    private static $file_exist = false;
    private static $limit_break = false;

    /**
     * Busca un archivo y retorna su contenido.
     * 
     * @access private
     * @return array retorna un array del cotenido el archivo encontrado.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    private static function getData(): array
    {
        if (self::$file_exist && !self::$limit_break) {
            return json_decode(file_get_contents(self::$file_api), true);
        }

        return [];
    }

    /**
     * Guarda los datos una vez realizada la busqueda.
     * 
     * @access private 
     * @param array $data recive un arreglo de los datos a guardar.
     * @return void sin retorno.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    private static function saveData(array $data): void
    {
        file_put_contents(self::$file_api, json_encode($data));
    }

    /**
     * verifica si la ip existe en el token actual.
     * 
     * @access private
     * @param string $ip recive la ip la cual se comprobará su existencia.
     * @return bool retorna un buleano verificado si existe o no la ip.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    private static function __ipExist(string $ip):bool{
        $exist = false;
        if($_IP = self::getData()['ip']){
            foreach($_IP as $value){
                if($exist = ($value['name'] == $ip && !$value['delete'])) break;
            }
        }
        return $exist;
    }

    /**
     * Verifica si el token existe.
     * 
     * @access public
     * @return array retorna un arreglo con la verificación de la existencia del token.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function access(): array
    {
        if (self::$file_exist) {
            $ip = $_SERVER['REMOTE_ADDR'];
            if (self::__ipExist($ip)) {
                return [
                    'access' => true,
                    'message' => 'Acceso permitido.'
                ];
            } else {
                return [
                    'access' => false,
                    'message' => "Su ip($ip) no tiene acceso."
                ];
            }
        }

        return [
            'access' => false,
            'message' => self::$limit_break ? 'Superaste el limite de consultas.' : 'Token no valido.'
        ];
    }

    /**
     * Re-crea las ip y retorna un arreglo.
     * 
     * @access private
     * @param array $ip revice las ip que seran modificadas.
     * @return array retorna un arreglo de ip modificadas.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    private static function __createIP(array $ip):array{
        foreach($ip as &$value){
            $value = [
                'id' => Functions::generateID(),
                'name' => $value,
                'delete' => false,
                'delete_at' => null
            ];
        }
        return $ip;
    }

    /**
     * Gerena un token unico y crea un fichero con la información suministrada
     * 
     * @access public
     * @param array $ips recive las ip que tendran acceso con el token generado.
     * @return string retorna el token unico generado.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function generateToken(array $ips=[]): string
    {
        $__token = rand(0, 9) . md5(microtime()) . chr(rand(97, 122));
        self::$file_api = Functions::getRoute("api/$__token.json");

        self::saveData([
            'ip' => self::__createIP($ips),
            'first_insert_at' => time(),
            'last_insert_at' => time(),
            'update_at' => time(),
            'query' => 0,
            'limit_query' => 50000,
            'query_update_at' => time() + 86400
        ]);

        return $__token;
    }

    /**
     * Actualiza las ips de un determinado token.
     * 
     * @access public
     * @param array $ips recive las nuevas ip.
     * @return bool retorna un buleano confirmando la actualización.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function updateIP(array $ips): bool
    {
        if($data = self::getData()) {
            $data['ip'] = self::__createIP($ips);
            $data['update_at'] = time();
            self::saveData($data);
            return true;
        }
        return false;
    }

    /**
     * verifica si el token existe.
     * 
     * @access public
     * @param string $token recive el token a verificar.
     * @return bool retorna un buleano verificando la existencia del token.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function exist(string $token): bool
    {
        self::$file_api = Functions::getRoute("api/$token.json");
        if(self::$file_exist = file_exists(self::$file_api)){
            $data = self::getData();
            $change = true;
            if(self::$limit_break = ($data['query'] >= $data['limit_query'])){
                if($data['query_update_at'] <= time()){
                    $data['query'] = 0;
                    $data['query_update_at'] = time() + 86400;
                }else{
                    $change = false;
                }
            }else{
                $data['query']++;
            }
            if($change) self::saveData($data);
        }
        return self::$file_exist;
    }

    /**
     * Agrega ips a un token en especifico.
     * 
     * @access public
     * @param array $ip recive las ip que seran agregadas.
     * @return void sin retorno.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function addIP(array $ip): void
    {
        if($data = self::getData()){
            $change = false;
            foreach($ip as $value){
                if(!self::__ipExist($value)){
                    $data['ip'] = array_merge($data['ip'], self::__createIP([$value]));
                    $data['last_insert_at'] = time();
                    $change = true;
                }
            }
            if($change) self::saveData($data);
        }
    }

    /**
     * Retorna las ip de un token en especifico.
     * 
     * @access public
     * @return array retorna el contenido del token en forma de arreglo.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function getIPs(bool $ip_delete = false): array
    {
        if($data = self::getData()){
            foreach($data['ip'] as $key => $ip){
                if($ip['delete'] != $ip_delete){
                    unset($data['ip'][$key]);
                }
            }
        }
        return $data;
    }

    /**
     * Elimina una o varias ip de un token especifico.
     * 
     * @access public
     * @param array $ip recive las ip que seran eliminadas.
     * @param array &$_IP_DELETE recive una variable de referencia que almacena las ip eliminadas.
     * @return bool retorna un buleano verificando la eliminación de las ip.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0 
     */
    public static function deleteIP(array $ip, array &$_IP_DELETE = []):bool{
        if($data = self::getData()){
            $delete = false;
            foreach($data['ip'] as &$_ip){
                if(in_array($_ip['name'], $ip)){
                    $_IP_DELETE[] = $_ip['name'];
                    $_ip['delete'] = $delete = true;
                    $_ip['delete_at'] = time();
                }
            }
            if($delete) self::saveData($data);
            return $delete;
        }else{
            return false;
        }
    }
}
