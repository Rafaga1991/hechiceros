<?php

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
        $lists = (new ListWar())->where(['delete' => 0])->get();
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
        return $this->view;
    }

    public function listWarShow($id)
    {
        if($listwar = (new ListWar())->where(['delete' => 0])->find($id)){
            $players = json_decode($listwar->list, false);
            foreach($players as &$player){
                $player = (new Player())->find($player);
            }
            newPDF(view('home/list/war-pdf', ['players' => $players, 'description' => $listwar->description]), date('d M Y', strtotime($listwar->date)));
        }else{
            Message::add("La lista de guerra con id #$id no existe.");
            Route::reload('list.war');
        }
        // foreach ($data['player'] as &$player) {
        //     $player = (new Player())->find($player);
        // }
        // ;
        vdump($id);
    }

    public function newListWar()
    {
        $players = (new Player())->where(['inClan' => 1, 'status' => ['active', 'wait']])->get();
        Html::addScript(['src' => asset('js/listwar.js')]);
        Html::addVariable('body', view('home/list/war-new', ['players' => $players]));
        Html::addVariable('url_form', Route::get('list.war.create'));
        return $this->view;
    }

    public function listWarCreate(Request $request)
    {
        if ($request->tokenIsValid()) {
            $validation = $request->validate(['player' => ['empty' => false]]);
            if ($validation['validation']) {
                (new ListWar())->insert([
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
        (new ListWar())->where(['id' => $id])->update(['delete' => 1, 'delete_at' => time()]);
        Message::add('Lista de guerra eliminada con exito!', 'success');
        (new Activity())->insert([
            'title' => 'Lista de guerra eliminada.',
            'description' => Session::getUser('username') . ' elimino una la lista de guerra #' . $id
        ]);
        Route::reload('list.war');
    }

    public function listWarUpdate($id)
    {
        if ($list = (new ListWar())->where(['delete' => 0])->find($id)) {
            $players = (new Player())->where(['inClan' => 1, 'status' => ['active', 'wait']])->get();
            Html::addScript(['src' => asset('js/listwar.js')]);
            Html::addVariable('body', view('home/list/war-update', ['listwar' => $list, 'list' => json_decode($list->list, false), 'players' => $players]));
            Html::addVariable('url_form', Route::get('list.war.change'));
            return $this->view;
        }
        Message::add('No se encontro la lista de guerra.');
        Route::reload('list.war');
    }

    public function listWarChange(Request $request)
    {
        if ($request->tokenIsValid()) {
            $validation = $request->validate(['player' => ['empty' => false], 'listId' => ['empty' => false]]);
            if ($validation['validation']) {
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
                    'players' => (new Player())->where(['status' => 'break'])->get(),
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
                'players' => (new Player())->where(['status' => ['active', 'wait']])->get(),
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
                    'players' => (new Player())->where(['status' => 'wait'])->get(),
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
                'players' => (new Player())->where(['status' => ['active', 'break']])->get(),
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
}
