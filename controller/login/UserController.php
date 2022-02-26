<?php

namespace controller\login;

use core\{Controller,Html,Route,Functions};
use model\User;

class UserController extends Controller{
    private $view = 'home/index';

    public function __construct()
    {
        $this->view = Functions::view($this->view);
    }

    public function index(){
        if(!Functions::isAdmin()) return Route::reload('home.index');
        Html::addVariables([
            'body' => Functions::view('home/user/index', ['users' => (new User())->get()])
        ]);

        return $this->view;
    }

    public function update($data){
        Functions::vdump($data);
    }
}