<?php

class Request{
    private $validate = true;
    private $message = [];

    public function __construct(){
        if(!empty($_REQUEST)){
            foreach($_REQUEST as $index => $post){
                if($index != '__test' && $index != 'PHPSESSID') {
                    if(empty($post)){
                        $this->message[] = "Completa el campo $index.";
                        if($this->validate){
                            $this->validate = false;
                        }
                    }
                    eval('$this->' . $index . '=' . json_encode($post) . ';');
                }
            }

            if($this->validate){
                if($this->validate = array_key_exists('__token', $_REQUEST)){
                    $this->validate = $_REQUEST['__token'] == Session::getTemp('__token');
                    $this->message[] = 'Token incorrecto.';
                }
            }

            $this->ip = $_SERVER['REMOTE_ADDR'];
            $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        }
    }

    public function validate(){
        return $this->validate;
    }

    public function getMessage(){
        return $this->message;
    }
}