<?php

class HomeController extends Controller{
    public function __construct(){
        $this->clanInfo = Session::get('clan-info');
    }

    public function index(){
        return $this->view('home', $this->clanInfo);
    }
}