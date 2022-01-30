<?php

class LoginController{
    public function index(){
        $people = new People();
        $peoples = $people->get();
        return view('login', $peoples);
    }

    public function showData(People $people){
        vdump($people->last_name);
    }
}