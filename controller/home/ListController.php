<?php

namespace controller\home;

use core\{Controller,Html,Session,Route,Request, Message};
use model\{Activity,Player,ListWar};
use controller\home\CurrentWarController;
use function core\{view,asset,dd};

class ListController extends Controller
{
    private $view = null;

    public function __construct()
    {
        $this->view = view('home/index');
    }

    public function index() { }

    public function listWar()
    {
        $lists = (new ListWar())->where(['status' => ['created','generated']])->get();
        $listWar = [];

        foreach ($lists as $list) {
            $listWar[] = array_merge(
                [
                    'player' => json_decode($list->list)
                ],
                [
                    'date' => $list->date,
                    'id' => $list->id,
                    'description' => $list->description,
                    'members' => $list->members
                ]
            );
        }

        Html::addVariable('body', view('home/list/war', ['listwar' => $listWar]));
        Html::addVariable('URL_GENERATE_LIST', Route::get('list.war.generate'));
        return $this->view;
    }

    public function listWarShow($id)
    {
        if($listwar = (new ListWar())->where(['status' => ['created','generated']])->find($id)){
            $players = json_decode($listwar->list, false);
            foreach($players as &$player){
                $player = (new Player())->find($player);
                // if($player->status == 'active') $player->status = 'war';
            }
            
            // dd($players);
            Html::addVariables([
                'members_war' => view('home/list/list-table', [
                    'players' => $players,
                    'typeList' => 'Guerra'
                ]),
                'members_wait' => view('home/list/list-table', [
                    'players' => (new Player())->where(['status' => 'wait', 'inClan' => 1])->get(),
                    'typeList' => 'Espera'
                ]),
                'members_break' => view('home/list/list-table', [
                    'players' => (new Player())->where(['status' => 'break', 'inClan' => 1])->get(),
                    'typeList' => 'Descanso'
                ]),
                '_HOST_' => HOST,
                '_DATE_' => date('d M Y', strtotime($listwar->date)),
                '__PROYECT_NAME__' => PROYECT_NAME,
                '_ICON_URL' => Session::get('clan_info')['badgeUrls']['small']
            ]);

            return view('home/list/war-pdf', [
                'description' => $listwar->description,
            ]);
        }else{
            Message::add("La lista de guerra con id #$id no existe.");
            Route::reload('list.war');
        }
    }

    public function newListWar()
    {
        $players = (new Player())->where(['inClan' => 1, 'status' => ['active', 'wait', 'war']])->get();
        Html::addScript(['src' => asset('js/listwar.js')]);
        Html::addVariables([
            'body' => view('home/list/war-new', ['players' => $players]),
            'url_form' => Route::get('list.war.create'),
            'cant_members_wait' => (new Player())->where(['status' => 'wait', 'inClan' => 1])->count()
        ]);
        
        return $this->view;
    }

    public function listWarCreate(Request $request)
    {
        if ($request->tokenIsValid()) {
            $validation = $request->validate(['player' => ['empty' => false]]);
            if ($validation['validation']) {
                (new Player())->where(['status' => 'war'])->update(['status' => 'active']);
                foreach($request->player as $tag) (new Player())->find($tag)->status = 'war';

                (new ListWar())->insert([
                    'user_id' => Session::getUser('id'),
                    'list' => json_encode($request->player),
                    'description' => $request->description,
                    'members' => count($request->player)
                ]);

                Message::add('Lista de Guerra Creada con Exito!', 'success');

                (new Activity())->insert([
                    'title' => 'Lista de guerra creada.',
                    'description' => Session::getUser('username') . ' creo una lista de guerra.'
                ]);
            } else {
                if (count($validation['error']) == 1) Message::add($validation['error'][0], 'danger');
            }
        }
        Route::reload('list.war');
    }

    public function listWarDestroy($id)
    {
        // if($listWar = (new ListWar())->where(['id' => $id])->get()){
        //     $listWar = json_decode($listWar[0]->list);
        // }
        (new ListWar())->where(['id' => $id])->update(['status' => 'delete', 'delete_at' => time()]);
        Message::add('Lista de guerra eliminada con exito!', 'success');
        (new Activity())->insert([
            'title' => 'Lista de guerra eliminada.',
            'description' => Session::getUser('username') . ' elimino una la lista de guerra #' . $id
        ]);
        Route::reload('list.war');
    }

    public function listWarUpdate($id)
    {
        if ($list = (new ListWar())->where(['status' => ['created','generated']])->find($id)) {
            $players = (new Player())->where(['inClan' => 1, 'status' => ['active', 'wait', 'war']])->get();
            Html::addScript(['src' => asset('js/listwar.js')]);
            Html::addVariable(
                'body',
                view(
                    'home/list/war-update',
                    [
                        'listwar' => $list,
                        'list' => json_decode($list->list, true),
                        'players' => $players]
                )
            );
            Html::addVariable('url_form', Route::get('list.war.change'));
            return $this->view;
        }
        Message::add('No se encontro la lista de guerra.');
        Route::reload('list.war');
    }

    private function getPlayers($list1, $list2){
        return array_filter($list1, function($value) use($list2){
            return !in_array($value, $list2);
        });
    }

    public function listWarChange(Request $request)
    {
        if ($request->tokenIsValid()) {
            $validation = $request->validate([
                'player' => ['empty' => false],
                'listId' => ['empty' => false]
            ]);
            if ($validation['validation']) {
                $data = (new ListWar())->find($request->listId);
                $last_list = json_decode($data->list, true);
                $new_list = $request->player;

                $list = [
                    'active' => $this->getPlayers($last_list, $new_list),
                    'war' => $this->getPlayers($new_list, $last_list)
                ];

                foreach($list as $status => $players){ // actualizando estado de jugadores
                    foreach($players as $player_id){
                        if($player = (new Player)->find($player_id)){
                            if($player->status != $status) $player->status = $status;
                        }
                    }
                }

                (new ListWar())->where(['id' => $request->listId])->update([
                    'list' => json_encode($request->player),
                    'description' => $request->description,
                    'update_at' => time(),
                    'members' => count($request->player)
                ]);

                Message::add('Lista de Guerra Actualizada con Exito!', 'success');

                (new Activity())->insert([
                    'title' => 'Lista de guerra actualizada.',
                    'description' => Session::getUser('username') . ' actualizó la lista de guerra #' . $request->listId
                ]);
            } else {
                Message::add($validation['error']);
            }
            Route::reload('list.war');
        }
        return $this->listWarUpdate($request->listId);
    }

    public function listBreak()
    {
        Html::addVariables([
            'body' => view(
                'home/list/lists',
                [
                    'players' => (new Player())->where(['status' => 'break', 'inClan' => 1])->get(),
                    'namePathNew' => 'list.break.new',
                    'namePathDestroy' => 'list.break.destroy'
                ]
            ),
            'name_list' => 'Descanso'
        ]);
        return $this->view;
    }

    public function listBreakNew()
    {
        Html::addScript(['src' => asset('js/listwar.js')]);
        Html::addVariables([
            'body' => view('home/list/list-new', [
                'players' => (new Player())->where(['inClan' => 1,'status' => ['active', 'wait', 'war']])->get(),
                'namePath' => 'list.break',
                'namePathChange' => 'list.break.change'
            ]),
            'name_type_list' => 'Descanso',
        ]);
        return $this->view;
    }

    public function listBreakChange(Request $request)
    {
        if ($request->tokenIsValid()) {
            $validation = $request->validate([
                'player' => [
                    'empty' => false
                ]
            ]);
            if ($validation['validation']) {
                foreach ($request->player as $id) (new Player())->where(['id' => $id])->update(['status' => 'break']);
                Message::add('Miembros agregados con exito!', 'success');
                (new Activity())->insert([
                    'title' => 'Nuevo miembro',
                    'description' => Session::getUser('username') . ' agrego un nuevo miembro a las lista de descanso.'
                ]);
            } else {
                Message::add($validation['error']);
            }
        }
        Route::reload('list.break');
        return;
    }

    public function listBreakDestroy(Request $request)
    {
        if ($request->tokenIsValid()) {
            (new Player())->where([
                'id' => $request->id
            ])->update(['status' => 'active']);
            Message::add('Lista de Descanso Actualizada con Exito!', 'success');
            (new Activity())->insert([
                'title' => 'Lista Actualizada',
                'description' => Session::getUser('username') . ' actualizó la lista de descanso.'
            ]);
            Route::reload('list.break');
        }

        return $this->listBreak();
    }

    public function listWait()
    {
        Html::addVariables([
            'body' => view(
                'home/list/lists',
                [
                    'players' => (new Player())->where(['status' => 'wait', 'inClan' => 1])->get(),
                    'namePathNew' => 'list.wait.new',
                    'namePathDestroy' => 'list.wait.destroy'
                ]
            ),
            'name_list' => 'Espera'
        ]);
        return $this->view;
    }

    public function listWaitNew()
    {
        Html::addScript(['src' => asset('js/listwar.js')]);
        Html::addVariables([
            'body' => view('home/list/list-new', [
                'players' => (new Player())->where(['inClan' => 1,'status' => ['active', 'break', 'war']])->get(),
                'namePath' => 'list.wait',
                'namePathChange' => 'list.wait.change'
            ]),
            'name_type_list' => 'Espera',
        ]);
        return $this->view;
    }

    public function listWaitChange(Request $request)
    {
        if ($request->tokenIsValid()) {
            $validation = $request->validate([
                'player' => [
                    'empty' => false
                ]
            ]);
            if ($validation['validation']) {
                foreach ($request->player as $id) (new Player())->where(['id' => $id])->update(['status' => 'wait']);
                Message::add('Miembros agregados con exito!', 'success');
                (new Activity())->insert([
                    'title' => 'Nuevo miembro',
                    'description' => Session::getUser('username') . ' agrego nuevos miembros a las lista de espera.'
                ]);
            } else {
                Message::add($validation['error']);
            }
        }
        Route::reload('list.wait');
        return;
    }

    public function listWaitDestroy(Request $request)
    {
        if ($request->tokenIsValid()) {
            (new Player())->where([
                'id' => $request->id
            ])->update(['status' => 'active']);
            Message::add('Lista de Espera Actualizada con Exito!', 'success');
            (new Activity())->insert([
                'title' => 'Lista de Espera Actualizada',
                'description' => Session::getUser('username') . " eliminó al jugador $request->id de la lista de espera."
            ]);
            Route::reload('list.wait');
        }

        return $this->listWait();
    }

    public function listWarGenerate($players=0){
        (new CurrentWarController())->index();// cargando desempeño
        $Players = (new Player)->where(['inClan' => 1, 'status' => 'active'])->get(['id','war_count']);
        $listWait = (new Player)->where(['inClan' => 1, 'status' => 'wait'])->get();
        $data = $listWar = [];
        $lists = (new ListWar)->get(['date']);
        $exists = false;
        $date = '';
        foreach($lists as $list){
            if($exists = !(time() > strtotime(($date = $list->date) . ' +1 day'))) break;
        }
        
        if($exists){
            $date = time() - strtotime($date);
            $format = 'i \m s \s';
            if($date > 3600) $format = "h \h $format";
            $date = date($format, mktime(0,0, $date));
            Message::add('No puedes generar lista de guerra.');
            Message::add('Debes esperar 24 horas para volver a generarla.');
            Message::add("Tiempo trascurrido: $date desde la última lista creada.");
            Route::reload('list.war');
            exit;
        }

        foreach($Players as $key => $player){
            $data[] = [
                'player_id' => $player->id,
                'war_count' => $player->war_count
            ];
        }

        if(count($listWait) >= $players){
            for($i=$players; $i>0; $i--){
                $listWar[] = $listWait[$i-1]->id;
            }
        }else{
            for($i=count($listWait); $i>0; $i--){
                $listWar[$listWait[$i-1]->id] = '';
            }
            
            usort($data, function($arr, $next_arr){
                return $arr['war_count'] > $next_arr['war_count'] ? 1 : -1;
            });

            foreach($data as $key => $value){
                unset($data[$key]);
                $data[$value['player_id']] = '';
            }

            $listWar = array_merge($listWar, $data);
            
            if($perfomance = Session::get('_PERFOMANCE_')){
                $perfomanceList= [];
                foreach($perfomance as $_players){
                    foreach($_players as $player){
                        $perfomanceList[$player['tag']] = '';
                    }
                }
                $listWar = array_merge($listWar, $perfomanceList);
            }
            $listWar = array_keys(array_slice($listWar, 0, $players));
        }

        if(count($listWar) < $players){
            Message::add('No se puede generar la lista de guerra.');
            Message::add("Cantidad de jugadores no disponibles.");
            Message::add("Solo hay disponible <b>" . count($listWar) . "/$players</b> jugadores.");
            Message::add("<b>RECOMENDACION:</b> agrega más jugadores a la lista de espera o sacalos de la lista de descanso.");
        }else{
            (new Player())->where(['status' => 'war'])->update(['status' => 'active']);
            foreach($listWar as $tag) (new Player())->find($tag)->status = 'war';

            (new ListWar())->insert([
                'user_id' => Session::getUser('id'),
                'list' => json_encode($listWar),
                'description' => 'Lista de guerra generada por ' . Session::getUser('username') . '.',
                'members' => $players,
                'status' => 'generated'
            ]);

            Message::add('Lista de Guerra Creada con Exito!', 'success');

            (new Activity())->insert([
                'title' => 'Lista de guerra generada.',
                'description' => Session::getUser('username') . ' generó una lista de guerra.'
            ]);
        }        
        Route::reload('list.war');
    }
}
