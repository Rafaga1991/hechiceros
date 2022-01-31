<?php

class Request{
    private $data;
    private $variable = [];

    public function __construct($data=[])
    {
        unset($_GET['url']);
        if(!empty($_FILES)){
            foreach($_FILES as &$_FILE){
                if(is_array($_FILE['name'])){
                    $file = [];
                    for($i=0; $i<count($_FILE['name']); $i++){
                        $file[] = new File([
                            'name' => $_FILE['name'][$i],
                            'tmp_name' => $_FILE['tmp_name'][$i],
                            'type' => $_FILE['type'][$i],
                            'size' => $_FILE['size'][$i]
                        ]);
                    }
                    $_FILE = $file;
                }else{
                    $_FILE = new File($_FILE);
                }
            }
        }
        $this->data = array_merge($data, $_FILES, $_POST);
        $this->createVariable();
    }

    private function createVariable(){
        foreach($this->data as $name => $value){
            if(!isset($this->variable[$name])){
                $this->variable[$name] = $value;
            }
        }
    }

    public function setData(string $name, $value){
        $this->data[$name] = $value;
        $this->createVariable();
    }

    public function getData(){
        return $this->data;
    }

    public function isData(){
        return !empty($this->data);
    }

    public function __toString()
    {
        return json_encode($this->data);
    }

    public function __set($name, $value)
    {
        $this->variable[$name] = $value;
    }

    public function __get($name)
    {
        return $this->variable[$name] ?? '';
    }

    public function tokenIsValid():bool{
        if(isset($this->variable['__token'])){
            return $this->variable['__token'] == Session::get('__token');
        }
        return false;
    }

    public function tokenApiIsValid(){
        if(isset($this->variable['__token'])){
            $access = Api::access();
            if(!$access['access']) {
                $this->clearData();
                $this->setData('message', $access['message']);
                $this->setData('type', 'denegado');
            }
            $this->clearData('__token');
            return $access['access'];
        }
        return false;
    }

    public function clearData(string $name = null){
        if(!$name){
            $this->data = [];
        }else{
            unset($this->data[$name]);
        }
    }
}