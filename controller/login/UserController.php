<?php

namespace controller\login;

use core\{Api, Controller,Html,Route,Functions, Request, Session};
use model\Activity;
use model\User;
use function core\{view,isRol,getValue};

class UserController extends Controller{
    private $view = 'home/index';
    private $default_pass = '123456789';

    public function __construct()
    {
        $this->view = view($this->view);
    }

    public function index(){
        Html::addVariables([
            'body' => view('home/user/index', ['users' => (new User())->get()]),
            'URL_UPDATE' => Route::get('user.update'),
            'PASSWORD' => $this->default_pass
        ]);
        return $this->view;
    }

    public function update(Request $request){
        if($request->tokenIsValid() && isRol()){
            $data = getValue($request->getData(), ['group:rol', 'ban:delete', 'reset:password', 'user_id:id', 'close:close_session']);
            if(isset($data['id'])) {
                if(Session::getUser('id') != $data['id']){
                    if(isset($data['password'])) $data['password'] = md5($this->default_pass);
                    if($user = (new User())->find($data['id'])){
                        $user->update(array_merge($data, ['update_at' => time()]));
                        (new Activity)->insert([
                            'title' => '[Administrador] Usuario Modificado',
                            'description' => Session::getUser('username') . " modificó el usuario #{$data['id']}"
                        ]);
                        return Request::response($data, "Usuario [$user->username] actualizado con exito!");
                    }
                    return Request::response(null, 'Este usuario no existe!', 'error');
                }
                return Request::response(null, 'No puedes modificar tu propia cuenta.', 'error');
            }
            return Request::response(null, 'Id de usuario requerido.', 'error');
        }else{
            return Request::response($request->getData(), 'token no valido', 'error');
        }
        return Request::response($request->getData());
    }

    public function verification(){
        $data = ['reload' => true];
        if($user = (new User)->find(Session::getUser('id'))){
            $data['reload'] = !!$user->close_session;
        }

        return Request::response($data);
    }
}
