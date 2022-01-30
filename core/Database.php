<?php

class Database{
    protected $lastID = '';

    private function getConnect(){
        return mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    }

    protected function query($sql){
        $data = [];
        $link = $this->getConnect();
        $query = mysqli_query($this->getConnect(), $sql);
        while($row = $query->fetch_array()) $data[] = $row;
        if(count($data) == 1) $data = $data[0];
        mysqli_close($link);
        return $data;
    }

    protected function exec($sql){
        $link = $this->getConnect();
        mysqli_query($link, $sql);
        $this->lastID = mysqli_insert_id($link);
        mysqli_close($link);
    }

    protected function tableInfo($table){
        return $this->query("DESCRIBE $table");
    }
}