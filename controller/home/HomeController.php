<?php

class HomeController extends Controller{
    public function index(){
        Html::addVariable('body', view('home/home'));
        return view('home/index');
    }
}