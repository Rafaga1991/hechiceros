<?php

namespace controller\login;

use core\{Controller,Html,Functions,Session,Route,Request};
use model\{Activity,User};
use api\client\Client;

class LoginController extends Controller{
	private $claninfo = [];
	private $activity = null;

	public function __construct()
	{
		if(!$this->claninfo = Session::get('clan_info')){
			$this->claninfo = (new Client())->getClan()->getClanInfo();
			Session::set('clan_info', $this->claninfo);
			Session::set('clan_war_log', (new Client())->getClan()->getWarLog());
			Session::set('clan_current_war', (new Client())->getClan()->getCurrentWar());
			Session::set('clan_current_war_league', (new Client())->getClan()->getCurrentWarLeagueGroup());

			if(isset(Session::get('clan_current_war')['state']) && Session::get('clan_current_war')['state'] == 'notInWar') Session::destroy('clan_current_war');
			if(isset(Session::get('clan_current_war_league')['state']) && Session::get('clan_current_war_league')['state'] == 'notInWar') Session::destroy('clan_current_war_league');
			
			Session::set('icon', $this->claninfo['badgeUrls']['small']);
		}
		Html::addVariable('description', $this->claninfo['description']);
		$this->activity = new Activity();
	}

	public function index():string{ return Session::auth()? Functions::view('home/index') : Functions::view('login/index'); }
	
	public function access(Request $request){
		if($request->tokenIsValid()){
			$user = new User();
			if($user = $user->where(['username' => strtolower($request->username), 'password' => md5($request->password), 'delete' => 0])->get(['id', 'username', 'email', 'admin'])){
				$user = $user[0];
				Session::setUser($user, 'admin');
				$this->activity->insert([
					'title' => 'Nuevo inicio se sesión',
					'description' => "El usuario {$user->username} inicio sesión."
				]);
				Route::reload('home.index');
			}
			return Functions::view('login/index', ['message' => 'Usuario y/o clave incorrectos.']);
		}else{
			return Functions::view('login/index', ['message' => 'Token no valido, no se permite el reenvio de formulario.']);
		}
	}

	public function logout(){
		$this->activity->insert([
			'title' => 'Cierre de sesión',
			'description' => "El usuario " . Session::getUser('username') . " inicio sesión."
		]);
		Session::destroyUser();
		Session::destroy();
		Route::reload('login.index');

		return;
	}
}