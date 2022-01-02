<?php

class Database{
    private function connect(){
        return mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    }

    protected function query($query, &$rows=0){
        $query = mysqli_query($this->connect(), $query);
        $data = [];
        while($rows = $query->fetch_array()){
            $data[] = $rows;
        }
        $rows = $query->num_rows;

        return $data;
    }

    protected function execute($query){
        mysqli_query($this->connect(), $query);
    }
}