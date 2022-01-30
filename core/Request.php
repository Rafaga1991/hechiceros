<?php

class Request{
    private $data;

    public function __construct($data=[])
    {
        unset($_GET['url']);
        $this->data = array_merge($data, $_POST);
        foreach($this->data as $name => $value){
            $this->{$name} = $value;
        }
    }

    public function getData(){
        return $this->data;
    }

    public function isData(){
        return !empty($this->data);
    }
}