<?php

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
                $log['endTime'] = date('d M Y', strtotime(explode('.', $log['endTime'])[0]));
            }

            Html::addVariables([
                'body' => view('home/warlog/warlog', ['warlog' => $warlog])
            ]);
        }else{
            Html::addVariables([
                'body' => view('home/maintenance'),
                'MESSAGE_MAINTENANCE' => "Hola " . ucfirst(Session::getUser('username')) . ', actualmente los servidores de supercell se encuentran en mantenimiento.'
            ]);
        }
        return $this->view;
    }

    public function reload(){ (new HomeController('warlog.index'))->reload(); }
}