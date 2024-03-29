<?php

namespace controller\home;

use api\player\Players;
use core\{Controller,Html,Session,Route,Request, Message, function view, function asset, function createImage, function strRepeat, function traslate};
use model\{Activity,Player,ListWar, User};
use controller\home\CurrentWarController;

use function core\dd;
use function core\isRol;

class ListController extends Controller
{
    private $view = null;
    private $clanInfo;

    public function __construct()
    {
        $this->view = view('home/index');
        $this->clanInfo = Session::get('clan_info');
    }

    public function index() { }

    public function listWar()
    {
        $status = ['created','generated'];
        if(isRol()) $status[] = 'delete';
        $lists = (new ListWar())->where(['status' => $status, 'clan_id' => $this->clanInfo['tag']])->get();
        $listWar = [];

        foreach ($lists as $list) {
            $listWar[] = [
                'date' => $list->date,
                'id' => $list->id,
                'description' => $list->description,
                'members' => $list->members,
                'status' => $list->status,
                'username' => (new User)->find($list->user_id)->username ?? ''
            ];
        }

        Html::addVariable('body', view('home/list/war', ['listwar' => $listWar]));
        Html::addVariable('URL_GENERATE_LIST', Route::get('list.war.generate'));
        return $this->view;
    }

    private function generateListDesing($players, $title, $attributes=[]){
        if(!$players) return '';
        $result = strRepeat(
            view('styles/listwar/row-table-pdf'),
            function($class) use($players){
                if(!$class->__STOP__ = !isset($players[$class->__ID__])){
                    $player = $players[$class->__ID__];
                    if(is_string($class->num)) $class->num = 0;
                    $class->img = createImage($player->image);
                    $class->name = $player->name;
                    $class->tag = $player->id;
                    $class->war = $player->war_count;
                    $class->rol = traslate($player->role);
                    $class->num++;
                    $class->color = ($player->war_count >= 1) ? (($player->war_count >= 3) ? 'success' : 'primary') : 'danger';
                }
                return $class;
            },
            true
        );

        return view(
            'styles/listwar/list-table-pdf',
            array_merge([
                'header' => ['Jugador', 'Guerras'],
                'body' => $result,
                'listname' => strtoupper($title),
                'members' => count($result),
            ], $attributes)
        );
    }

    public function downloadListWar($id){
        $status = ['created','generated'];
        if(isRol()) $status[] = 'delete';

        if($_listwar_session = (Session::get('listwar') ?? [])){
            if($_view = ($_listwar_session[$id] ?? false)){
                exit($_view);
            }
        }

        if($listwar = (new ListWar())->where(['status' => $status, 'clan_id' => $this->clanInfo['tag']])->find($id)){
            $players = json_decode($listwar->list, false);
            foreach($players as &$player){
                $player = (new Player())->find($player);
            }
            
            $top3 = (function(){
                $top3 = '';
                if($perfomance = Session::get('_PERFOMANCE_')){
                    $top3 = [];
                    $isBreak = false;
                    foreach($perfomance as $players){
                        foreach($players as $player){
                            $_player = (new Players($player['tag']))->getPlayerInfo();
                            $_playerDB = (new Player)->find($player['tag']);
    
                            $player['image'] = createImage($_playerDB->image);
                            $player['imageTH'] = createImage(asset("image/th/th{$_player['townHallLevel']}.png"));
                            $player['league'] = $_player['league']['name'];
                            $player['status'] = 'En ' . traslate($_playerDB->status);
                            $player['duration'] = date('i:s', mktime(0,0,$player['duration']));
                            $player['percent'] = $player['destruction'] / $player['attacks'];
    
                            $top3[] = $player;
                            if($isBreak = count($top3) >= 3){
                                break;
                            }
                        }
                        if($isBreak){
                            break;
                        }
                    }
                    $top3 = view('styles/listwar/top3-pdf', ['top' => $top3, 'count' => count($top3)]);
                }
                return $top3;
            })();

            $list_war = $this->generateListDesing(
                $players, 
                'Lista de Guerra', 
                [
                    'description' => $listwar->description,
                    'isTop3' => !empty($top3)
                ]
            );

            $list_wait = $this->generateListDesing(
                (new Player())->where(['status' => 'wait', 'inClan' => 1, 'clan_id' => $this->clanInfo['tag']])->get(),
                'Lista de Espera',
                [
                    'isTop3' => true
                ]
            );
            
            $list_break = $this->generateListDesing(
                (new Player())->where(['status' => 'break', 'inClan' => 1, 'clan_id' => $this->clanInfo['tag']])->get(),
                'Lista de Descanso',
                [
                    'isTop3' => true
                ]
            );

            $clanInfo = Session::get('clan_info');

            $players = (new Player)->where(['inClan' => 1, 'clan_id' => $this->clanInfo['tag']])->get();
            $king_war = null;
            $_players = [];
            foreach($players as $player){
                if(!$king_war) $king_war = $player;
                elseif($player->war_count > $king_war->war_count) $king_war = $player;

                $_players[$player->war_count][] = $player;
            }
            unset($_players['']);

            if(count($_players) > 1){
                $keys = array_keys($_players);
                $key = $keys[count($keys)-1];
                shuffle($_players[$key]);
                $king_war = $_players[$key][0];
            }

            if($king_war){
                $king_war = json_decode(json_encode((new Players($king_war->id))->getPlayerInfo()));
                $king_war->image = createImage($king_war->league->iconUrls->small);
                $king_war->role = traslate($king_war->role);
                $king_war->townHallLevel = createImage(asset("image/th/th$king_war->townHallLevel.png"));
            }

            $header = view(
                'styles/listwar/header-pdf',
                [
                    'date' => date('d M Y', strtotime($listwar->date)),
                    'time' => date('h:i A', strtotime($listwar->date)),
                    'logo' => createImage($clanInfo['badgeUrls']['small']),
                    'name' => $clanInfo['name'],
                    'list_war' => Route::get('list.war'),
                    'host' => HOST,
                    'listname' => 'Listas de Guerras',
                    'king' => $king_war,
                    'classofclans' => createImage(asset('image/classOfClans.png'))
                ]
            );

            $footer = view(
                'styles/listwar/footer-pdf',
                [
                    'year' => date('Y'),
                    'proyect_name' => PROYECT_NAME
                ]
            );

            $view = join(
                '',
                [
                    $header, // cabeza de página
                    $top3, // mayor desempeño en guerra
                    $list_war, // lista de guerra
                    $list_wait, // lista de espera
                    $list_break, // lista de descanso
                    $footer // pie de página
                ]
            );

            $view = <<<HTML
                <div style="width:99.25%;">$view</div>
            HTML;
            
            
            $_listwar_session[$id] = $view;
            Session::set('listwar', $_listwar_session);
            exit($view);
        } else {
            Message::add("La lista de guerra con id #$id no existe.");
            Route::reload('list.war');
        }
    }

    public function listWarShow($id)
    {
        $status = ['created','generated'];
        if(isRol()) $status[] = 'delete';
        if($listwar = (new ListWar())->where(['status' => $status, 'clan_id' => $this->clanInfo['tag']])->find($id)){
            $players = json_decode($listwar->list, false);
            foreach($players as &$player){
                $player = (new Player())->where(['clan_id' => $this->clanInfo['tag']])->find($player);
                // if($player->status == 'active') $player->status = 'war';
            }
            
            // dd($players);
            Html::addVariables([
                'members_war' => view('home/list/list-table', [
                    'players' => $players,
                    'typeList' => 'Guerra'
                ]),
                'members_wait' => view('home/list/list-table', [
                    'players' => (new Player())->where(['status' => 'wait', 'inClan' => 1, 'clan_id' => $this->clanInfo['tag']])->get(),
                    'typeList' => 'Espera'
                ]),
                'members_break' => view('home/list/list-table', [
                    'players' => (new Player())->where(['status' => 'break', 'inClan' => 1, 'clan_id' => $this->clanInfo['tag']])->get(),
                    'typeList' => 'Descanso'
                ]),
                '_HOST_' => HOST,
                '_DATE_' => date('d M Y', strtotime($listwar->date)),
                '__PROYECT_NAME__' => $this->clanInfo['name'],
                '_ICON_URL' => $this->clanInfo['badgeUrls']['small']
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
        $players = (new Player())->where(['inClan' => 1, 'status' => ['active', 'wait', 'war'], 'clan_id' => $this->clanInfo['tag']])->get();
        Html::addScript(['src' => asset('js/listwar.js')]);
        Html::addVariables([
            'body' => view('home/list/war-new', ['players' => $players]),
            'url_form' => Route::get('list.war.create'),
            'cant_members_wait' => (new Player())->where(['status' => 'wait', 'inClan' => 1,  'clan_id' => $this->clanInfo['tag']])->count()
        ]);
        
        return $this->view;
    }

    public function listWarCreate(Request $request)
    {
        if ($request->tokenIsValid()) {
            $validation = $request->validate(['player' => ['empty' => false]]);
            if ($validation['validation']) {
                (new Player())->where(['status' => 'war', 'clan_id' => $this->clanInfo['tag']])->update(['status' => 'active']);
                foreach($request->player as $tag) (new Player())->where(['clan_id' => $this->clanInfo['tag']])->find($tag)->status = 'war';

                (new ListWar())->insert([
                    'user_id' => Session::getUser('id'),
                    'list' => json_encode($request->player),
                    'description' => $request->description,
                    'members' => count($request->player),
                    'clan_id' => $this->clanInfo['tag']
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
        (new ListWar())->where(['id' => $id, 'clan_id' => $this->clanInfo['tag']])->update(['status' => 'delete', 'delete_at' => time()]);
        Message::add('Lista de guerra eliminada con exito!', 'success');
        (new Activity())->insert([
            'title' => 'Lista de guerra eliminada.',
            'description' => Session::getUser('username') . ' elimino una la lista de guerra #' . $id
        ]);
        Route::reload('list.war');
    }

    public function listWarUpdate($id)
    {
        $status = ['created','generated'];
        if(isRol()) $status[] = 'delete';
        if ($list = (new ListWar())->where(['status' => $status, 'clan_id' => $this->clanInfo['tag']])->find($id)) {
            $players = (new Player())->where(['inClan' => 1, 'status' => ['active', 'wait', 'war'], 'clan_id' => $this->clanInfo['tag']])->get();
            Html::addScript(['src' => asset('js/listwar.js')]);
            Html::addVariable(
                'body',
                view('home/list/war-update',[
                    'listwar' => $list,
                    'list' => json_decode($list->list, true),
                    'players' => $players
                ])
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
                $data = (new ListWar())->where(['clan_id' => $this->clanInfo['tag']])->find($request->listId);
                $last_list = json_decode($data->list, true);
                $new_list = $request->player;

                $list = [
                    'active' => $this->getPlayers($last_list, $new_list),
                    'war' => $this->getPlayers($new_list, $last_list)
                ];

                foreach($list as $status => $players){ // actualizando estado de jugadores
                    foreach($players as $player_id){
                        if($player = (new Player)->where(['clan_id' => $this->clanInfo['tag']])->find($player_id)){
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
        $conditions = ['status' => 'break', 'inClan' => 1, 'clan_id' => $this->clanInfo['tag']];
        if(isRol()) unset($conditions['inClan']);
        Html::addVariables([
            'body' => view(
                'home/list/lists',
                [
                    'players' => (new Player())->where($conditions)->get(),
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
                'players' => (new Player())->where(['inClan' => 1, 'status' => ['active', 'wait', 'war'], 'clan_id' => $this->clanInfo['tag']])->get(),
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
                foreach ($request->player as $id) (new Player())->where(['id' => $id, 'clan_id' => $this->clanInfo['tag']])->update(['status' => 'break']);
                Message::add('Miembros agregados con exito!', 'success');
                (new Activity())->insert([
                    'title' => 'Nuevo miembro',
                    'description' => Session::getUser('username') . ' agrego ' . count($request->player) . ' miembros a las lista de descanso.'
                ]);
            } else {
                Message::multiAdd($validation['error']);
            }
        }
        Route::reload('list.break');
    }

    public function listBreakDestroy(Request $request)
    {
        if ($request->tokenIsValid()) {
            (new Player())->where([
                'id' => $request->id,
                'clan_id' => $this->clanInfo['tag']
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
        $conditions = ['status' => 'wait', 'inClan' => 1, 'clan_id' => $this->clanInfo['tag']];
        if(isRol()) unset($conditions['inClan']);
        Html::addVariables([
            'body' => view(
                'home/list/lists',
                [
                    'players' => (new Player())->where($conditions)->get(),
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
                'players' => (new Player())->where(['inClan' => 1,'status' => ['active', 'break', 'war'], 'clan_id' => $this->clanInfo['tag']])->get(),
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
                foreach ($request->player as $id) (new Player())->where(['id' => $id, 'clan_id' => $this->clanInfo['tag']])->update(['status' => 'wait']);
                Message::add('Miembros agregados con exito!', 'success');
                (new Activity())->insert([
                    'title' => 'Nuevo miembro',
                    'description' => Session::getUser('username') . ' agrego ' . count($request->player) . ' miembros a las lista de espera.'
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
                'id' => $request->id,
                'clan_id' => $this->clanInfo['tag']
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
        $Players = (new Player)->where(['inClan' => 1, 'status' => 'active', 'clan_id' => $this->clanInfo['tag']])->get(['id','war_count']);
        $listWait = (new Player)->where(['inClan' => 1, 'status' => 'wait', 'clan_id' => $this->clanInfo['tag']])->get();
        $data = $listWar = [];
        $lists = (new ListWar)->where(['clan_id' => $this->clanInfo['tag']])->get(['date']);
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
            (new Player())->where(['status' => 'war', 'clan_id' => $this->clanInfo['tag']])->update(['status' => 'active']);
            foreach($listWar as $tag) (new Player())->where(['clan_id' => $this->clanInfo['tag']])->find($tag)->status = 'war';

            (new ListWar())->insert([
                'user_id' => Session::getUser('id'),
                'list' => json_encode($listWar),
                'description' => 'Lista de guerra generada por ' . Session::getUser('username') . '.',
                'members' => $players,
                'status' => 'generated',
                'clan_id' => $this->clanInfo['tag']
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
