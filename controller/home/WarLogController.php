<?php

namespace controller\home;

use core\{Controller,Session,Functions,Html,Route};
use model\War;
use function core\{view};
class WarLogController extends Controller{
    private $view = 'home/index';
    private $clanInfo;
    private $clanWarLog;

    public function __construct()
    {
        $this->view = view($this->view);
        $this->clanInfo = Session::get('clan_info');
        $this->clanWarLog = Session::get('clan_war_log');
    }

    public function index(){
        if(!isset($this->clanWarLog['reason'])){
            $warlog = array_filter($this->clanWarLog['items'], function($item){
                return $item['clan']['destructionPercentage'] <= 100;
            });

            foreach($warlog as &$log){
                $log['id'] = substr(md5($log['endTime']), 0, 20);
                if($log['details'] = (new War())->find($log['id'])) $log['details'] = $log['details']->id;
                $log['endTime'] = date('d M Y', strtotime(explode('.', $log['endTime'])[0]));
            }

            Html::addVariables([
                'body' => view('home/warlog/warlog', ['warlog' => $warlog])
            ]);
        }else{
            Html::addVariables([
                'body' => view('home/maintenance'),
                'URL_RELOAD' => Route::get('warlog.reload'),
                'MESSAGE_MAINTENANCE' => "Hola " . ucfirst((string)Session::getUser('username')) . ', actualmente los servidores de supercell se encuentran en mantenimiento.'
            ]);
        }
        return $this->view;
    }

    public function lastWar(string $id){
        if($war = (new War())->find($id)){
            Html::addVariables([
                'body' => view('home/warlog/lastwar'),
                'war' => view('home/currentwar/currentwar', ['currentWar' => json_decode($war->war, true)])
            ]);
        }
        return view('home/index');
    }

    public function reload(){ (new HomeController('warlog.index'))->reload(); }
}