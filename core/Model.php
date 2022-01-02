<?php

class Model extends Database{
    protected $primaryKey = '';
    private $where = '';
    protected $columns = [];

    public function __construct()
    {
        $this->tablename = get_class(debug_backtrace()[0]['object']);

        $data = parent::query("DESCRIBE $this->tablename");
        $this->describe = $data;
        foreach($data as $value){
            eval('$this->' . $value[0] . '="";');
            if($value[3] == 'PRI') $this->primaryKey = $value[0];
            $this->columns[] = strtolower($value[0]);
        }
    }

    public function find($value){
        $this->valueSearch = $value;
        $this->where = "WHERE $this->primaryKey='$this->valueSearch'";
        return $this;
    }

    public function get($columns=[]){
        if(empty($columns)) $columns = '*';
        if(is_array($columns)){
            foreach($columns as $col){
                if(!in_array(strtolower($col), $this->columns)){
                    Errors::add('warning', "La columna <b>$col</b> no existe en el modelo <b>$this->tablename</b>.");
                    $columns = '*';
                }
            }
            if(is_array($columns)) $columns = join(',', $columns);
        }elseif(!in_array($columns, $this->columns) && $columns != '*'){
            Errors::add('warning', "La columna <b>$columns</b> no existe en el modelo <b>$this->tablename</b>.");
            $columns = '*';
        }
        return parent::query("SELECT $columns FROM $this->tablename $this->where");
    }

    public function delete(){
        parent::execute("DELETE FROM $this->tablename $this->where");
    }

    private function __values($conditions, $separator = ','){
        $acum = '';
        foreach($conditions as $col => $value){
            $acum .= "$col='$value'|$separator";
        }

        $acum = explode('|', $acum);
        array_pop($acum);
        $acum = join('', $acum);

        return $acum;
    }

    public function insert($values=[]){
        $val = $col = '';
        foreach($values as $key => $value){
            $col .= "$key,";
            $val .= "'$value',";
        }
        $col = substr($col, 0, strlen($col)-1);
        $val = substr($val, 0, strlen($val)-1);
        $query = "INSERT INTO $this->tablename($col) VALUES($val)";
        parent::execute($query);
    }

    public function where($conditions = []){
        $this->where = 'WHERE ' . $this->__values($conditions, ' AND ');
        return $this;
    }

    public function update($values=[]){
        parent::execute("UPDATE $this->tablename SET " . $this->__values($values) . " $this->where");
    }
}