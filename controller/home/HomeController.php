<?php

namespace controller\home;

use api\clan\Clan;
use core\{Controller,Functions,Session,Html,Route,Request};
use model\{Activity, ListWar, Player, Donations, User};
use api\client\Client;
use api\player\Players;

use function core\{view,alert, dd, isRol};

class HomeController extends Controller
{
    private $view = null;
    private $activity = null;
    private $player = null;
    private $donations = null;
    private $redirect;

    public function __construct(string $redirect = 'home.index')
    {
        $this->view = view('home/index');
        $this->activity = new Activity();
        $this->player = new Player();
        $this->donations = new Donations();
        $this->redirect = $redirect;
    }

    public function index()
    {
        $claninfo = Session::get('clan_info');

        if(!isset($claninfo['reason'])){
            usort($claninfo['memberList'], function (array $arr1, array $arr2) {
                return ($arr1['donations'] - $arr1['donationsReceived']) < ($arr2['donations'] - $arr2['donationsReceived']);
            });
            
            $players = $this->player->where(['inClan' => 1])->get();

            foreach ($players as $player) {
                $inClan = false;
                foreach ($claninfo['memberList'] as $key => $member) {
                    if ($member['tag'] == $player->id) {
                        if (!$player->inClan) {
                            $player->inClan = 1;
                            $player->cant++;
                        }
                        $inClan = true;
                        $claninfo['memberList'][$key]['name'] = htmlentities($member['name']);
                        break;
                    }
                }

                if (!$inClan) {
                    $player->inClan = 0;
                }
            }

            $donations = 0;
            $donationsReceived = 0;
            $idDonations = date('Y-m', time());
            $lists_war = (new ListWar)->get(['list', 'date', 'delete_at']);
            // cargando jugadores
            foreach ($claninfo['memberList'] as $member) {
                $image = $member['league']['iconUrls']['medium'] ?? $member['league']['iconUrls']['tiny'] ?? $member['league']['iconUrls']['small'] ?? '';
                if (!$player = (new Player)->find($member['tag'])) {
                    (new Player)->insert([
                        'id' => $member['tag'],
                        'name' => $member['name'],
                        'role' => $member['role'],
                        'image' => $image,
                        'donations' => $member['donations'],
                        'donationsReceived' => $member['donationsReceived']
                    ]);
                } else { // actualizando información de jugador
                    if ($player->name != $member['name']) $player->name = $member['name'];
                    if ($player->role != $member['role']) $player->role = $member['role'];
                    if ($player->donations != $member['donations']) $player->donations = $member['donations'];
                    if ($player->donationsReceived != $member['donationsReceived']) $player->donationsReceived = $member['donationsReceived'];
                    if ($player->image != $image) $player->image = $image;
                    if ($player->inClan != 1) $player->inClan = 1;
                }
                $donations += $member['donations'];
                $donationsReceived += $member['donationsReceived'];
                
                // participaciones en guerra
                $participations = 0;
                foreach ($lists_war as $list_war){
                    $list = json_decode($list_war->list, true);
                    $period = date('Y-m', strtotime($list_war->date));
                    if($period == $idDonations && $list_war->delete_at > 0 && count($list) >= 10){
                        if(in_array($member['tag'], $list)){
                            $participations++;
                        }
                    }
                }
                if($player && $participations != $player->war_count){
                    $player->war_count = $participations;
                }
                // fin
            }
    
            if ($donation = $this->donations->find($idDonations)) {
                if ($donation->donations < $donations || $donationsReceived > $donation->donationsReceived) {
                    if ($donation->donations < $donations) $donation->donations = $donations;
                    if ($donation->donationsReceived < $donationsReceived) $donation->donationsReceived = $donationsReceived;
                    $donation->update_at = time();
                }
            } else {
                $this->donations->insert([
                    'id' => $idDonations,
                    'donations' => $donations,
                    'donationsReceived' => $donationsReceived,
                    'date_at' => time()
                ]);
            }

            // cantidad de listas de guerras creadas por usuarios
            $listCreates = [];
            $listWarGroup = (new ListWar())->groupBy('user_id', true);
            $user = new User();
            foreach ($listWarGroup as $list){
                if($user = $user->find($list['user_id'])){
                    if(!$user->delete && in_array($user->rol, [Route::ROL_PLAYER])){
                        $listCreates[] = [
                            'username' => $user->username,
                            'cant' => $list['count']
                        ];
                    }
                }
            }
            // fin

            $condition = ['inClan' => 1];
            if(isRol()) $condition['inClan'] = [0,1];
            $players = $this->player->where($condition)->get();
            Html::addVariables([
                'body' => view(
                    'home/home',
                    [
                        'listCreates' => $listCreates,
                        'members' => $claninfo['memberList'],
                        'players' => $players,
                        'max' => 1000*((int)date('d', time()))
                    ]
                ),
                'members' => count($players),
                'members_in_clan' => count($claninfo['memberList']),
                'url_get_donations' => Route::get('get.char.area.donations'),
                'url_get_performance' => Route::get('get.char.bar.performance'),
                'url_get_participation' => Route::get('get.war.participation'),
                'url_player_status_update' => Route::get('player.update.status')
            ]);
        }elseif($claninfo['reason'] == 'inMaintenance'){
            Html::addVariables([
                'body' => view('home/maintenance'),
                'URL_RELOAD' => Route::get('home.reload'),
                'MESSAGE_MAINTENANCE' => 'Hola ' . ucfirst((string)Session::getUser('username')) . ', actualmente los servidores de supercell se encuentran en mantenimiento.'
            ]);
        }

        return $this->view;
    }

    public function updatePlayerStatus(){
        $count = 0;
        if(time() > (Session::get('__UPDATE_STATUS_PLAYER__') ?? 0)){
            $players = $this->player->where(['inClan' => 1])->get();
            foreach($players as $player){
                $player_info = (new Players($player->id))->getPlayerInfo();
                if(in_array($player->status, ['active', 'wait'])){
                    if(in_array($player_info['warPreference'], ['out'])){
                        $player->status = 'break';
                        $count++;
                    }
                }elseif(in_array($player->status, ['break'])){
                    if(in_array($player_info['warPreference'], ['in'])){
                        $player->status = 'active';
                        $count++;
                    }
                }
            }
            Session::set('__UPDATE_STATUS_PLAYER__', strtotime('+30 minute'));
        }

        return Request::response([
            'status' => (($count > 0) ? 'update' : 'normal'), 
            'update' => $count, 
            'next_update' => Session::get('__UPDATE_STATUS_PLAYER__')
        ]);
    }

    public function activity()
    {
        if (!isRol()) Route::reload('home.index');
        Html::addVariable('body', view('home/option/activity', ['activity' => $this->activity->get()]));
        return $this->view;
    }

    public function setting()
    {
        Html::addVariables(['body' => view('home/option/setting')]);
        return $this->view;
    }

    public function update(Request $request)
    {
        if ($request->tokenIsValid()) {
            $validation = $request->validate([
                'username' => [
                    'empty' => false,
                    'length:max' => 50,
                    'length:min' => 5
                ], 'password' => [
                    'empty' => false,
                    'length:max' => 50,
                    'length:min' => 6,
                    'equal' => 'rpassword'
                ], 'rpassword' => [
                    'empty' => false,
                    'length:max' => 50,
                    'length:min' => 6
                ]
            ]);

            if ($validation['validation']) {
                $user = (new User())->find(Session::getUser('id'));
                $user->username = $request->username;
                $user->password = md5($request->password);
                $user->update_at = time();
                $this->activity->insert([
                    'title' => 'Actualización de perfil',
                    'description' => Session::getUser('username') . ' actualizó el usuario y/o contraseña.'
                ]);

                $content = '<ul>';
                $content .= "<li>Usuario <b>" . Session::getUser('username') . "</b> actualizado a <b>$request->username</b>.</li><li>Contraseña actualizada con exito!</li>";

                Session::updateUser($user);
            } else {
                $content = '<li>' . implode('<li>', $validation['error']);
            }

            Html::addVariables([
                'body' => view('home/option/setting'),
                'error' => alert($validation['validation'] ? 'Actualizado con Exito!' : 'Error al Actualizar', $content, $validation['validation'] ? 'success' : 'danger')
            ]);

            return $this->view;
        }
        return $this->setting();
    }

    public function reload()
    {
        Session::set('clan_info', (new Client())->getClan()->getClanInfo());
        Session::set('clan_war_log', (new Client())->getClan()->getWarLog());
        Session::set('clan_current_war', (new Client())->getClan()->getCurrentWar());
        Session::set('clan_current_war_league', (new Client())->getClan()->getCurrentWarLeagueGroup());
        if (Session::get('clan_current_war')['state'] == 'notInWar') Session::destroy('clan_current_war');
        if (Session::get('clan_current_war_league')['state'] == 'notInWar') Session::destroy('clan_current_war_league');
        Route::reload($this->redirect);
        return;
    }

    public function chartAreaDonations()
    {
        $datasets = [
            [
                'label' => 'Donadas',
                'lineTension' => 0.3,
                'backgroundColor' => "rgba(2,117,216,0.2)",
                'borderColor' => "rgba(2,117,216,1)",
                'pointRadius' => 5,
                'pointBackgroundColor' => "rgba(2,117,216,1)",
                'pointBorderColor' => "rgba(255,255,255,0.8)",
                'pointHoverRadius' => 5,
                'pointHoverBackgroundColor' => "rgba(2,117,216,1)",
                'pointHitRadius' => 50,
                'pointBorderWidth' => 2,
                'data' => []
            ],
            [
                'label' => 'Recividas',
                'lineTension' => 0.3,
                'backgroundColor' => "rgba(214,34,26,0.2)",
                'borderColor' => "rgba(214,34,26,1)",
                'pointRadius' => 5,
                'pointBackgroundColor' => "rgba(214,34,26,1)",
                'pointBorderColor' => "rgba(255,255,255,0.8)",
                'pointHoverRadius' => 5,
                'pointHoverBackgroundColor' => "rgba(214,34,26,1)",
                'pointHitRadius' => 50,
                'pointBorderWidth' => 2,
                'data' => []
            ]
        ];

        $data = [
            'label' => [],
            'datasets' => $datasets,
            'max' => 0
        ];

        $year = date('Y', time());
        for ($i = 1; $i <= 12; $i++) {
            $date = strtotime("$year-$i-1");
            $data['label'][] = date('M', $date);
            if ($donations = $this->donations->find(date('Y-m', $date))){
                $data['datasets'][0]['data'][] = (int)$donations->donations;
                $data['datasets'][1]['data'][] = (int)$donations->donationsReceived;
                if($donations->donations > $data['max']) $data['max'] = $donations->donations;
                if($donations->donationsReceived > $data['max']) $data['max'] = $donations->donationsReceived;
            }else{
                $data['datasets'][0]['data'][] = 0;
                $data['datasets'][1]['data'][] = 0;
            }
        }

        $data['max'] += 1000;

        return Request::response($data);
    }

    public function chartBarPerformance()
    {
        $datasets = [
            [
                'label' => 'Desempeño',
                'lineTension' => 0.3,
                'backgroundColor' => "rgba(2,117,216,0.2)",
                'borderColor' => "rgba(2,117,216,1)",
                'pointRadius' => 5,
                'pointBackgroundColor' => "rgba(2,117,216,1)",
                'pointBorderColor' => "rgba(255,255,255,0.8)",
                'pointHoverRadius' => 5,
                'pointHoverBackgroundColor' => "rgba(2,117,216,1)",
                'pointHitRadius' => 50,
                'pointBorderWidth' => 2,
                'data' => []
            ]
        ];

        $data = [
            'label' => [],
            'datasets' => $datasets,
            'max' => 120,
            'members' => 0
        ];

        (new CurrentWarController())->index();
        if($perfomance = Session::get('_PERFOMANCE_')){
            foreach($perfomance as $stars => $values){
                foreach($values as $value){
                    if(++$data['members'] >= 5) break;
                    $data['datasets'][0]['data'][] = ($value['destruction']/$value['attacks']);
                    $data['label'][] = $value['name'];
                }
                if($data['members'] >= 5) break;
            }
        }
        
        return Request::response($data);
    }

    public function chartBarAreaParticipation(){
        $datasets = [
            [
                'label' => 'Participación',
                'lineTension' => 0.3,
                'backgroundColor' => "rgba(2,117,216,0.2)",
                'borderColor' => "rgba(2,117,216,1)",
                'pointRadius' => 5,
                'pointBackgroundColor' => "rgba(2,117,216,1)",
                'pointBorderColor' => "rgba(255,255,255,0.8)",
                'pointHoverRadius' => 5,
                'pointHoverBackgroundColor' => "rgba(2,117,216,1)",
                'pointHitRadius' => 50,
                'pointBorderWidth' => 2,
                'data' => []
            ]
        ];

        $data = [
            'label' => [],
            'datasets' => $datasets,
            'max' => 0
        ];

        $memberList = Session::get('clan_info')['memberList'];
        $_PLAYER = [];
        foreach ($memberList as $member){
            if($player = (new Player)->find($member['tag'])){
                $_PLAYER[] = [
                    'name' => $member['name'],
                    'cant' => (int)$player->war_count
                ];
            }
        }
        usort($_PLAYER, function($arr1, $arr2){
            return ((int)$arr1['cant'] < (int)$arr2['cant']);
        });

        $_PLAYER = array_splice($_PLAYER, 0, 10);
        shuffle($_PLAYER);
        foreach ($_PLAYER as $key => $player){
            if($player['cant'] > $data['max']) $data['max'] = $player['cant'] + 5;
            $data['label'][] = $player['name'];
            $data['datasets'][0]['data'][] = $player['cant'];
        }

        return Request::response($data);
    }
}
