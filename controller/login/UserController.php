<?php

namespace controller\login;

use core\{Api, Controller,Html,Route,Functions, Request};
use model\User;

class UserController extends Controller{
    private $view = 'home/index';
    private $default_pass = '123456789';

    public function __construct()
    {
        $this->view = Functions::view($this->view);
    }

    public function index(){
        if(!Functions::isAdmin()) return Route::reload('home.index');
        Html::addVariables([
            'body' => Functions::view('home/user/index', ['users' => (new User())->get()]),
            'URL_UPDATE' => Route::get('user.update'),
            'PASSWORD' => $this->default_pass
        ]);
        return $this->view;
    }

    public function update(Request $request){
        if($request->tokenIsValid()){
            $data = Functions::getValue($request->getData(), ['adm:admin', 'ban:delete', 'user_id:id', 'reset:password']);
            if(isset($data['password'])) $data['password'] = md5($this->default_pass); 
            if($user = (new User())->find($data['id'])){
                if(!$user->admin){
                    $user->update($data);
                    return Request::response(null, "Usuario [$user->username] actualizado con exito!");
                }else{
                    return Request::response(null, 'No puedes actualizar un usuario administrador.', 'error');
                }
            }else{
                return Request::response(null, 'Este usuario no existe!', 'error');
            }
        }else{
            return Request::response($request->getData(), 'token no valido', 'error');
        }
        return Request::response($request->getData());
    }
}