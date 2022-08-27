<?php

namespace controller\home;

use core\{Controller,Html,Session,Route};
use model\War;

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
                // $currentwar = json_decode(file_get_contents('currentwar.json'), true);
                $id = substr(md5($currentwar['endTime']), 0, 20);
                // vdump(['id' => $id, 'time' => $currentwar['endTime']]);
                if(($data = (new War())->find($id)) && $data->war != json_encode($currentwar)) $data->war = json_encode($currentwar);
                elseif(!$data) (new War())->insert(['id' => $id, 'war' => json_encode($currentwar)]);
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
        Session::set('perfomance', $data);
        return view('home/currentwar/perfomance', ['members' => $data, 'cant' => 1, 'players' => 0, 'stars' => 0]);
    }

    public function reload():void { (new HomeController('currentwar.index'))->reload(); }
}
