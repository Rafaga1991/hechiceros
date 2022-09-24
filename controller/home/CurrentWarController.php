<?php

namespace controller\home;

use core\{Controller,Html,Session,Route};
use model\War;

use function core\dd;
use function core\view;

class CurrentWarController extends Controller
{
    private $view = null;

    public function __construct() { $this->view =view('home/index'); }

    public function index()
    {
        Html::addVariable('body',view('home/currentwar/index'));
        $data = [];
        if ($currentwar = Session::get('clan_current_war')) {
            if (!isset($currentwar['reason'])) {
                $id = substr(md5($currentwar['endTime']), 0, 20);
                if(($data = ((new War())->where(['id' => $id, 'clan_id' => Session::get('clan_info')['tag']])->get()[0] ?? null)) && $data->war != json_encode($currentwar)){
                    $data->war = json_encode($currentwar);
                } elseif(!$data){
                    (new War())->insert(['id' => $id, 'war' => json_encode($currentwar), 'clan_id' => Session::get('clan_info')['tag']]);
                } 
                $data = [
                    'war' =>view('home/currentwar/currentwar', ['currentWar' => $currentwar]),
                    'warname' => 'Guerra'
                ];
            }elseif($currentwar['reason'] == 'inMaintenance'){
                Html::addVariables([
                    'body' =>view('home/maintenance'),
                    'URL_RELOAD' => Route::get('currentwar.reload'),
                    'MESSAGE_MAINTENANCE' => 'Hola ' . ucfirst((string)Session::getUser('username')) . ', actualmente los servidores de supercell se encuentran en mantenimiento.'
                ]);
            }
        } elseif ($currentwar = Session::get('clan_current_war_league')) {
            if (!isset($currentwar['reason'])) {
                $data = [
                    'war' =>view('home/currentwar/currentwarleague', ['currentWar' => $currentwar]),
                    'warname' => 'Liga de Guerra de Clanes'
                ];
            }elseif($currentwar['reason'] == 'inMaintenance'){
                Html::addVariables([
                    'body' => view('home/maintenance'),
                    'URL_RELOAD' => Route::get('currentwar.reload'),
                    'MESSAGE_MAINTENANCE' => 'Hola ' . ucfirst((string)Session::getUser('username')) . ', actualmente los servidores de supercell se encuentran en mantenimiento.'
                ]);
            }
        } else {
            $data = [
                'war' => '<div class="alert alert-info text-center">No hay guerras disponibles.</div>',
                'warname' => 'En espera'
            ];
        }

        Html::addVariables($data);

        return $this->view;
    }

    public function perfomance($data){
        if(!is_array($data)) Route::reload('currentwar.index');
        $currentwar = Session::get('clan_current_war');
        Session::set('perfomance', $data);
        return view('home/currentwar/perfomance', [
            'members' => $data, 
            'cant' => 0, 
            'players' => 0, 
            'stars' => 0, 
            'endWar' => time() > (strtotime(substr($currentwar['endTime'], 0, strpos($currentwar['endTime'], '.')))-3600),
            'currentwar' => $currentwar,
            'isVictory' => (($currentwar['clan']['stars'] > $currentwar['opponent']['stars']) || ($currentwar['clan']['destructionPercentage'] > $currentwar['opponent']['destructionPercentage'] && $currentwar['clan']['stars'] > $currentwar['opponent']['stars'])),
            'isTie' => (($currentwar['clan']['stars'] == $currentwar['opponent']['stars']) && ($currentwar['clan']['destructionPercentage'] == $currentwar['opponent']['destructionPercentage']))
        ]);
    }

    public function reload():void { (new HomeController('currentwar.index'))->reload(); }
}
