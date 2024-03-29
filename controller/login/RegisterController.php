<?php

namespace controller\login;

use core\Controller;
use core\Html;
use core\Message;
use core\Request;
use core\Route;
use core\Session;
use model\Activity;
use model\User;

use function core\view;

class RegisterController extends Controller {
    public function index(){
        Html::addVariable('description', Session::get('clan_info')['description'] ?? '-------');
        return view('login/register');
    }

    public function create(Request $request){
        $view = '';
        if($request->tokenIsValid()){
            $validations = $request->validate([
                'username' => [
                    'empty' => false,
                    'length:min' => 5
                ],
                'password' => [
                    'equal' => 'rpassword',
                    'length:min' => 6
                ],
                'email' => [
                    'empty' => false,
                    'length:min' => 5
                ],
            ]);
            if($validations['validation']){
                if((new User)->where(['username' => $request->username])->get()){
                    $view = view(
                        'login/register',
                        [
                            'message' => "Usuario no válido!"
                        ]
                    );
                }else{
                    (new User)->insert([
                        'username' => $request->username,
                        'password' => md5($request->password),
                        'clan_id' => $request->tag,
                        'email' => $request->email
                    ]);

                    (new Activity)->insert([
                        'title' => '[Registro] Nuevo usuario',
                        'description' => "El usuario $request->username se creo con exito."
                    ]);

                    Message::add("Usuario <b>$request->username</b> creado con exito!", 'success');
                    Route::reload('register.index');
                }
            }else{
                $view = view(
                    'login/register', 
                    [
                        'message' => '<br/>' . join('<br/>', $validations['error'])
                    ]
                );
            }
        }else{
            $view = view('login/register', ['message' => 'Token no válido!']);
        }

        Html::addVariables([
            'USERNAME' => $request->username,
            'PASSWORD' => $request->password,
        ]);

        return $view;
    }
}
