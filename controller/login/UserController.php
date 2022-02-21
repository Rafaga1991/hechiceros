<?php

class UserController extends Controller{
    private $view = 'home/index';

    public function __construct()
    {
        $this->view = view($this->view);
    }

    public function index(){
        if(!isAdmin()) return Route::reload('home.index');
        Html::addVariables([
            'body' => view('home/user/index', ['users' => (new User())->get()])
        ]);

        return $this->view;
    }

    public function update($data){
        vdump($data);
    }
}