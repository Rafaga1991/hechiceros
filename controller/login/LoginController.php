<?php

namespace controller\login;

use core\{Controller,Html, Message, Session,Route,Request};
use model\{Activity, Clan, User};
use api\client\Client;
use function core\{dd, reload, view};

class LoginController extends Controller{
	private $claninfo = [];
	private $activity = null;

	private function loadDataClan($clan_id=null){
		$this->claninfo = (new Client())->getClan($clan_id)->getClanInfo();
		Session::set('clan_info', $this->claninfo);
		Session::set('clan_war_log', (new Client())->getClan($clan_id)->getWarLog());
		Session::set('clan_current_war', (new Client())->getClan($clan_id)->getCurrentWar());
		Session::set('clan_current_war_league', (new Client())->getClan($clan_id)->getCurrentWarLeagueGroup());

		if(isset(Session::get('clan_current_war')['state']) && Session::get('clan_current_war')['state'] == 'notInWar') Session::destroy('clan_current_war');
		if(isset(Session::get('clan_current_war_league')['state']) && Session::get('clan_current_war_league')['state'] == 'notInWar') Session::destroy('clan_current_war_league');
		
		Session::set('icon', $this->claninfo['badgeUrls']['small']);
	}

	public function __construct()
	{
		if(!$this->claninfo = Session::get('clan_info')){
			$this->loadDataClan($this->claninfo);
		}
		Html::addVariable('description', $this->claninfo['description']);
		$this->activity = new Activity();
	}

	public function index():string{ return Session::auth()? view('home/index') : view('login/index'); }
	
	public function access(Request $request){
		if($request->tokenIsValid()){
			$user = new User();
			if($user = $user->where(['`' . strtolower($request->username) . '`' => ['`email`', '`username`'], 'password' => md5($request->password), 'delete' => 0])->get(['id', 'username', 'email', 'rol', 'clan_id'])){
				$user = $user[0];
				$clan = new Clan;
				$clanExist = true;
				if($user->clan_id){
					if(!($claninfo = $clan->find($user->clan_id))){
						$claninfo = (new Client)->getClan($user->clan_id)->getClanInfo();
						if($clanExist = !isset($claninfo['reason'])){
							$clan->insert([
								'clan_id' => $user->clan_id,
								'update_at' => time()
							]);
							$claninfo = $clan->where(['clan_id' => $user->clan_id])->get()[0];
						}
					}else{
						switch($claninfo->status){
							case 'disabled':
								if(time() >= $claninfo->update_at){
									$claninfo->update_at = time();
									$claninfo->status = 'active';
								}else{
									(new Activity)->insert([
										'title' => 'Acceso Denegado',
										'description' => 'Clan baneado, todos los usuario con acceso denegado.'
									]);
									Message::add('Tu clan fue banneado de la página hasta el [' . date('m/d/Y h:i A', $claninfo->update_at) . ']');
								}
								break;
							case 'deleted':
								(new Activity)->insert([
									'title' => 'Acceso Denegado',
									'description' => 'Clan eliminado, ningun usuario tiene acceso.'
								]);
								Message::add('Tu clan fue eliminado de la página.');
								break;
							default: 
								break;
						}
					}

					if($clanExist){
						$this->loadDataClan($user->clan_id);
						
						if(in_array($claninfo->status, ['active'])){
							Session::setUser($user);
							$this->activity->insert([
								'title' => 'Nuevo inicio se sesión',
								'description' => "El usuario {$user->username}($user->id) inicio sesión."
							]);
							Route::reload('home.index');
						}
					}else{
						(new Activity)->insert([
							'title' => 'Acceso Denegado',
							'description' => 'Etiqueta del clan inexistente.'
						]);
						Message::add('La etiqueta del clan que tienes asignada, no existe.');
					}
				}else{
					(new Activity)->insert([
						'title' => 'Acceso Denegado',
						'description' => 'Etiqueta del clan requerida.'
					]);
					Message::add('Tag del clan requerido!');
				}
			}else{
				(new Activity)->insert([
					'title' => 'Acceso Denegado',
					'description' => "[{$request->username}] Usuario y/o clave invalidos."
				]);
				Message::add('Usuario y/o clave incorrectos.');
			}
		}else{
			(new Activity)->insert([
				'title' => 'Token Expirado',
				'description' => "[{$request->username}] Expiró el token de validación."
			]);
			Message::add('Token no valido, no se permite el reenvio de formulario.');
		}
		reload('/');
	}

	public function logout(){
		$this->activity->insert([
			'title' => 'Cierre de sesión',
			'description' => "El usuario " . Session::getUser('username') . " cerro la sesión."
		]);
		Session::destroyUser();
		Session::destroy();
		Route::reload('login.index');
	}
}
