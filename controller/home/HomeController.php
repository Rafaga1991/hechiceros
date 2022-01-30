<?php

class HomeController extends Controller{
    public function index(){
        return $this->redirect('show');
    }

    public function show(){
        return 'si';
    }
}