<?php

namespace core;

class Model extends Database{
    public $fk = [];
    public $table = '';
    public $models = [];
    public $primaryKey = '';
    public $innerVariable = '';
    
    private $object;
    private $cant = 0;
    private $from = '';
    private $where = '';
    private $variable = [];
    private $isInner = false;

    protected $columns = [];

    /**
     * Obtiene y carga toda la informacion del objeto hijo.
     * 
     * @access public
     * @return void sin retorno.
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
     */
    public function __construct() {
        $this->object = debug_backtrace()[0]['object'];
        $this->table = strtolower(get_class($this->object));
        $this->table = array_slice($data = explode("\\", $this->table), count($data)-1, count($data))[0];
        $this->from = $this->table;
        
        $this->table = ($this->object->table != $this->table) ? $this->object->table : $this->table;
        $this->primaryKey = ($this->object->primaryKey != $this->primaryKey) ? $this->object->primaryKey : $this->primaryKey;
        
        $description = parent::tableInfo($this->table);

        foreach($description as $columns){
            if($columns['Key'] == 'PRI') $this->primaryKey = $columns['Field'];
            $this->variable[$columns['Field']] = null;
            $this->columns[] = $columns['Field'];
        }
    }

    /**
     * Funcion magica, verifica y agrega una variable que fue creada dinamicamente.
     * 
     * @access public
     * @param string $name recive el nombre de la variable creada.
     * @param mixed $value recive el valor asignado a dicha varible.
     * @return void sin retorno.
     * @version 1.1
     * @author Rafael Minaya
     * @copyright R.M.B
     */
    public function __set($name, $value) {
        $this->variable[$name] = $value;
        if(isset($this->variable[$this->primaryKey])){
            if(!empty($this->variable[$this->primaryKey]) && in_array($name, $this->columns)){
                parent::exec("UPDATE $this->table SET `$name`='$value' WHERE `$this->primaryKey`='{$this->variable[$this->primaryKey]}'");
            }
        }
    }

    /**
     * Agrega los datos extraidos de la base de datos al modelo.
     * 
     * @access public
     * @param array $data recibe un arreglo de datos.
     * @return void sin retorno.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public function setData(array $data):void{
        foreach($data as $name => $value){
            $this->variable[$name] = $value;
        }
    }

    /**
     * Retorna el valor máximo de los registros en db.
     * 
     * @access public
     * @param string $column recive el nombre de la columna.
     * @return int retorna el valor máximo encontrado.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public function max(string $column):int{
        if($data = parent::query("SELECT MAX($column) 'max_$column' FROM $this->table")) return $data[0]["max_$column"];
        return 0;
    }

    /**
     * Retorna el valor mínimo de los registros en db.
     * 
     * @access public
     * @param string $column recibe el nombre de la columna.
     * @return int retorna el valor mínimo encontrado.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public function min(string $column){
        if($data = parent::query("SELECT MIN($column) 'min_$column' FROM $this->table")) return $data[0]["min_$column"];
        return 0;
    }

    /**
     * Retorna la sumatoria total de una columna.
     * 
     * @access public
     * @param string $column recive el nombre de la columna.
     * @return int retorna la sumatoria.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public function sum(string $column):int{
        if($data = parent::query("SELECT sum($column) 'sum_$column' FROM $this->table")) return $data[0]["sum_$column"];
        return 0;
    }

    /**
     * Realiza una consulta a la base de datos.
     * 
     * @access public
     * @param string sql recive un código sql.
     * @return array retorna los datos extraidos de la base de datos.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public function getQuery(string $sql):array{ return parent::query($sql); }

    /**
     * Retorna la cantidad total de registros.
     * 
     * @access public
     * @return int retorna la cantidad total de registros.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public function count():int{
        if($data = parent::query("SELECT count(*) 'count' FROM $this->table $this->where")) return $data[0]["count"];
        return 0;
    }

    /**
     * Retorna la cantidad total de registros agrupados.
     *
     * @access public
     * @return array retorna un arreglo de agrupaciones.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public function groupBy(string $column, bool $notnull=false):array{
        if(empty($this->where)){
            if($notnull) $this->where = "WHERE $column IS NOT NULL";
        }else{
            $this->where .= " AND $column IS NOT NULL";
        }
        if($data = parent::query("SELECT count(*) 'count', $column FROM $this->table $this->where GROUP BY $column")) return $data;
        return [];
    }

    /**
     * Funcion Magica, retorna el valor de una variable dinamica.
     * 
     * @access public
     * @param string $name recive el nombre de la variable a buscar.
     * @return mixed retorna el valor de la variable buscada.
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
     */
    public function __get($name) { return $this->variable[$name]; }

    /**
     * Busca registro por registro de un modelo en especifico.
     * 
     * @access public
     * @return array retorna 1 registro de un modelo especifico.
     * @author Rafael Minaya
     * @copyright R.M.B
     * @version 1.0
     */
    public function next(){
        if($row = parent::query("SELECT * FROM $this->table LIMIT $this->cant, 1")){
            $this->cant++;
            return $row[0];
        }else{
            return false;
        }
    }

    /**
     * Busca y retorna la ultima fila de un modelo en la base de datos.
     * 
     * @access public
     * @return array retorna la ultima fila de un modelo especifico en la base de datos.
     * @author Rafael Minaya
     * @copyright R.M.B
     * @version 1.0
     */
    public function lastRow() { return parent::query("SELECT * FROM $this->table ORDER BY $this->primaryKey DESC LIMIT 1"); }

    /**
     * Crea condiciones para la consulta a la base de datos.
     * 
     * @access private
     * @param array $arr recive un arreglo de condiciones.
     * @param string $separator
     */
    private function _where(array $arr, string $separator = ',') : string{
        $parameters = '';
        foreach($arr as $key => $value){
            if(substr_count($key, '`') == 0) $key = "`$key`";
            else $key = str_replace("`", "'", $key);
            if(is_array($value)){
                foreach($value as &$val){
                    if(substr_count($val, '`') == 0) $val = (!is_numeric($val))?"'$val'":$val;
                }
                $parameters .= "$key IN (" . join(",", $value) . ") $separator ";
            }else $parameters .= "$key='$value' $separator ";
        }
        $this->from = $this->table;
        return (trim(join($separator, array_filter(explode($separator,trim($parameters)), function($value){ return $value != ''; }))));
    }

    public function find($id){
        if(empty($this->where)) $this->where = "WHERE `$this->primaryKey`='$id'";
        else $this->where .= " AND `$this->primaryKey`='$id'";
        if($row = parent::query("SELECT * FROM $this->table $this->where")){
            foreach($row[0] as $index => $val) $this->variable[$index] = $val;
        }else{
            $this->where = '';
            return;
        }
        $this->from = $this->table;
        $this->where = '';
        return $this;
    }

    public function where(array $condition = []) : Model{
        if(!empty($condition)) {
            $this->where = 'WHERE ' . $this->_where($condition, 'AND');
        }
        return $this;
    }

    public function get(array $columns = []) : array{
        if(!$this->isInner){
            if(!empty($columns)) $columns = '`' . join('`,`', array_values($columns)) . '`';
            else $columns = '*';
        }else{
            $col = [];
            foreach($this->models as $model){
                $variable = array_keys($model->variable);
                foreach($variable as $value){
                    if(in_array($value, $columns)){
                        $col[] = "$model->innerVariable.$value AS '{$value}_$model->table'";
                    }
                }
            }
            if(empty($col)) $columns = '*';
            else $columns = join(',', $col);
        }
        $query = parent::query("SELECT $columns FROM `$this->from` $this->where");

        foreach($query as &$value){
            $object = clone $this->object;
            $object->setData($value);
            // foreach($value as $index => $val) $object->{$index} = $val;
            $value = $object;
        }
        $this->from = '';

        return $query;
    }

    public function update(array $values) : Model{
        $columns = $this->_where($values);

        if(array_key_exists($this->primaryKey, $values)){
            $id = $values[$this->primaryKey];
            unset($values[$this->primaryKey]);
            $columns = $this->_where($values);
            $this->where = "WHERE $this->primaryKey='$id'";
            parent::exec("UPDATE $this->table SET $columns $this->where");
        }elseif(!empty($this->where)) parent::exec("UPDATE $this->table SET $columns $this->where");
        $this->from = $this->table;
        return $this;
    }

    public function insert(array $arr) : Model{
        $columns = '(' . join(',', array_keys($arr)) . ')';
        $value = "('" . join("','", array_values($arr)) . "')";
        parent::exec("INSERT INTO $this->table$columns VALUES$value");
        $this->where = "WHERE $this->primaryKey='$this->lastID'";
        $this->from = $this->table;
        return $this;
    }

    public function delete(){
        if(isset($this->variable[$this->primaryKey])){
            if(!empty($this->variable[$this->primaryKey])){
                $this->from = $this->table;
                parent::exec("DELETE FROM $this->table WHERE $this->primaryKey='{$this->variable[$this->primaryKey]}'");
            }
        }
    }

    private function newVariable($table) : string{
        return $table[0] . '_' . substr(md5($table . rand(0, 100)), 0, 5);
    }

    public function innerJoin(Model $model) : Model{
        $model->innerVariable = $this->newVariable($model->table);
        $this->innerVariable = $this->newVariable($this->table);

        $this->isInner = true;

        $this->models[] = $model;
        $this->models = array_merge($this->models, $model->models);
        $model->models = [];

        $query = "$this->table AS $this->innerVariable ";
        $idFK = "$model->primaryKey$model->table";
        if(in_array($idFK, $this->fk)){
            $query .= "INNER JOIN $model->table AS $model->innerVariable ON $this->innerVariable.$idFK=$model->innerVariable.$model->primaryKey ";
            if(stripos(" $model->from", 'inner join')){
                $query .= "INNER JOIN $model->from";
            }
        }
        $this->from .= $query;
        return $this;
    }

    public function __toString() { return json_encode($this->variable); }
}
