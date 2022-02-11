<?php

class CurrentWarController extends Controller
{
    private $view = null;

    public function __construct()
    {
        $this->view = view('home/index');
    }

    public function index()
    {
        Html::addVariable('body', view('home/currentwar/index'));
        $data = [];


        if ($currentwar = Session::get('clan_current_war')) {
            if (is_array($currentwar)) {
                $data = [
                    'war' => view('home/currentwar/currentwar', ['currentWar' => $currentwar]),
                    'warname' => 'Guerra'
                ];
            }
        } elseif ($currentwar = Session::get('clan_current_war_league')) {
            if (is_array($currentwar)) {
                $data = [
                    'war' => view('home/currentwar/currentwarleague', ['currentWar' => $currentwar]),
                    'warname' => 'Liga de Guerra de Clanes'
                ];
                // unset($currentwar['clans']);
                // file_put_contents('currentWarLeague.json', json_encode($currentwar));
                // vdump((new Client())->getClan('#2LYLQY2VQ')->getCurrentWarLeague('#2UCQUV0PR'));
            }
        } else {
            $data = [
                'war' => '<div class="alert alert-info text-center">No hay guerras disponibles.</div>',
                'warname' => '...'
            ];
        }

        Html::addVariables($data);

        return $this->view;
    }

    public function reload()
    {
        (new HomeController('currentwar.index'))->reload();
        return;
    }
}
