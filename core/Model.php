<?php

class Model extends Database{
    public $table = '';
    public $primaryKey = '';
    public $fk = [];
    public $variable = [];
    public $innerVariable = '';
    public $models = [];

    private $cant = 0;
    private $where = '';
    private $from = '';
    private $isInner = false;

    private $object;

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
        $this->from = $this->table;
        
        $this->table = ($this->object->table != $this->table) ? $this->object->table : $this->table;
        $this->primaryKey = ($this->object->primaryKey != $this->primaryKey) ? $this->object->primaryKey : $this->primaryKey;

        $columns = parent::tableInfo($this->table);
        $value = parent::query("SELECT * FROM $this->table LIMIT 1");

        foreach($columns as $column){
            eval('$this->' . $column[0] . ' = "' . ($value[$column[0]] ?? '') . '";');
            $this->variable[$column[0]] = $value[$column[0]];
            if($column[3] == 'PRI' && empty($this->primaryKey)) $this->primaryKey = $column[0];
            elseif($column[3] == 'MUL') $this->fk[] = $column[0];
        }

    }

    /**
     * Funcion magica, verifica y agrega una variable que fue creada dinamicamente.
     * 
     * @access public
     * @param string $name recive el nombre de la variable creada.
     * @param mixed $value recive el valor asignado a dicha varible.
     * @return void sin retorno.
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
     */
    public function __set($name, $value) {
        $this->variable[$name] = $value;
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
    public function __get($name) {
        return $this->variable[$name];
    }

    /**
     * Busca registro por registro de un modelo en especifico.
     * 
     * @access public
     * @return array retorna 1 registro de un modelo especifico.
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
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
     * @version 1.0
     * @author Rafael Minaya
     * @copyright R.M.B
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
            $parameters .= "$key='$value' $separator ";
        }
        $this->from = $this->table;
        return trim(substr(trim($parameters), 0, strlen(trim($parameters))-(strtolower($separator)=='and'? 3 : 1)));
    }

    public function find($id) : Model{
        $this->where = "WHERE $this->primaryKey='$id'";
        $row = parent::query("SELECT * FROM $this->table $this->where");
        if(count($row) > 0 && !is_array($row[0])){
            foreach($row as $index => $val){
                if(!is_numeric($index)){
                    $this->variable[$index] = $val;
                }
            }
        }
        $this->from = $this->table;
        return $this;
    }

    public function where(array $condition = []) : Model{
        if(!empty($condition)) $this->where = 'WHERE ' . $this->_where($condition, 'AND');
        return $this;
    }

    public function get(array $columns = []) : array{
        if(!$this->isInner){
            if(!empty($columns)) $columns = join(',', array_values($columns));
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

        $query = parent::query("SELECT $columns FROM $this->from $this->where");

        foreach($query as $key => $value){
            if(is_array($value)) {
                $object = clone $this->object;
                foreach($value as $index => $val){
                    if(!is_numeric($index)) $object->{$index} = $val;
                }
                $query[$key] = $object;
            }elseif(is_numeric($key)){
                unset($query[$key]);
            }
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

    public function delete($id){
        $this->from = $this->table;
        parent::exec("DELETE FROM $this->table WHERE $this->primaryKey='$id'");
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