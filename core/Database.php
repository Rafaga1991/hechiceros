<?php

namespace core;

class Database{
    protected $lastID = '';

    /**
     * Retorna la conexión a la base de datos.
     * 
     * @access private
     * @return mysqli|false|null retorna la base de datos.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    private function getConnect(){
        return mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    }

    /**
     * Realiza las consultas hacia la base de datos.
     * 
     * @access protected
     * @param string $sql recive la consulta a realizar.
     * @return array retorna un arreglo de registros encontrados.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.1
     */
    protected function query(string $sql):array{
        $data = [];
        $link = $this->getConnect();
        $query = mysqli_query($link, $sql);
        while($row = $query->fetch_array()){
            foreach($row as $key => $col){
                if(is_numeric($key)) unset($row[$key]);
            }
            $data[] = $row;
        }
        mysqli_close($link);
        return $data;
    }

    /**
     * Ejecuta la consultas que no requieren respuesta.
     * 
     * @access protected
     * @param string $sql recive la consulta a ejecutar.
     * @return void sin retorno.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    protected function exec(string $sql):void{
        $link = $this->getConnect();
        mysqli_query($link, $sql);
        $this->lastID = mysqli_insert_id($link);
        mysqli_close($link);
    }

    /**
     * Obtiene información de una tabla en especifico.
     * 
     * @access protected
     * @param string $table recive el nombre de la tabla.
     * @return array retorna un arreglo de la información obtenida.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    protected function tableInfo(string $table):array{
        return $this->query("DESCRIBE $table");
    }
}