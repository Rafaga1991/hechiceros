<?php
class LoginController extends Controller{
	private $claninfo = [];
	private $activity = null;

	public function __construct()
	{
		if(!$this->claninfo = Session::get('clan_info')){
			$this->claninfo = (new Client())->getClan()->getClanInfo();
			Session::set('clan_info', $this->claninfo);
			Session::set('icon', $this->claninfo['badgeUrls']['small']);
		}
		Html::addVariable('description', $this->claninfo['description']);
		$this->activity = new activity();
	}

	public function index():string{
		return Session::auth()? view('home/index') : view('login/index');
	}
	
	public function access(Request $request){
		if($request->tokenIsValid()){
			$user = new user();
			if($user = $user->where(['username' => strtolower($request->username), 'password' => md5($request->password), '`delete`' => 0])->get(['id', 'username', 'email', 'admin'])){
				$user = $user[0];
				Session::setUser($user, 'admin');
				$this->activity->insert([
					'title' => 'Nuevo inicio se sesión',
					'description' => "El usuario {$user->username} inicio sesión."
				]);
				Route::reload('home.index');
			}
			return view('login/index', ['message' => 'Usuario y/o clave incorrectos.']);
		}else{
			return view('login/index', ['message' => 'Token no valido, no se permite el reenvio de formulario.']);
		}
	}

	public function logout(){
		Session::destroyUser();
		Route::reload('login.index');
	}
}