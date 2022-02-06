<?php

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

        usort($claninfo['memberList'], function (array $arr1, array $arr2) {
            return ($arr1['donations'] - $arr1['donationsReceived']) < ($arr2['donations'] - $arr2['donationsReceived']);
        });

        $players = $this->player->get();

        Html::addVariables([
            'body' => view('home/home', ['members' => $claninfo['memberList'], 'players' => $players, 'max' => 1000*((int)date('d', time()))]),
            'members' => count($claninfo['memberList']),
            'url_get_donations' => HOST . '/chart-area-donations',
            'url_get_perfomance' => HOST . '/chart-bar-perfomance'
        ]);
        foreach ($players as $player) {
            $inClan = false;
            foreach ($claninfo['memberList'] as $member) {
                if ($member['tag'] == $player->id) {
                    if (!$player->inClan) {
                        $player->inClan = 1;
                        $player->cant++;
                    }
                    $inClan = true;
                    break;
                }
            }
            if (!$inClan) $player->inClan = 0;
        }

        $donations = 0;
        $donationsReceived = 0;
        $idDonations = date('Y-m', time());
        // cargando jugadores
        foreach ($claninfo['memberList'] as $member) {
            $image = $member['league']['iconUrls']['medium'] ?? $member['league']['iconUrls']['tiny'] ?? $member['league']['iconUrls']['small'] ?? '';
            if (!$player = $this->player->find($member['tag'])) {
                $this->player->insert([
                    'id' => $member['tag'],
                    'name' => $member['name'],
                    'role' => $member['role'],
                    'image' => $image,
                    'donations' => $member['donations'],
                    'donationsReceived' => $member['donationsReceived']
                ]);
            } else { // actualizando informacion de jugador 
                if ($player->name != $member['name']) $player->name = $member['name'];
                if ($player->role != $member['role']) $player->role = $member['role'];
                if ($player->donations != $member['donations']) $player->donations = $member['donations'];
                if ($player->donationsReceived != $member['donationsReceived']) $player->donationsReceived = $member['donationsReceived'];
                if ($player->image != $image) $player->image = $image;
            }
            $donations += $member['donations'];
            $donationsReceived += $member['donationsReceived'];
        }

        if ($donation = $this->donations->find($idDonations)) {
            if ($donation->donations != $donations || $donationsReceived != $donation->donationsReceived) {
                if ($donation->donations != $donations) $donation->donations = $donations;
                if ($donation->donationsReceived != $donationsReceived) $donation->donationsReceived = $donationsReceived;
                $donation->update_at = time();
            }
        } else {
            $this->donations->insert([
                'id' => $idDonations,
                'donations' => $donations,
                'date_at' => time()
            ]);
        }

        return $this->view;
    }

    public function activity()
    {
        if (isAdmin()) Route::reload('home.index');
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
                $user = (new User())->find(Session::getUser('id'))->update([
                    'username' => $request->username,
                    'password' => md5($request->password)
                ])->get(['id', 'username', 'email', 'admin']);

                $this->activity->insert([
                    'title' => 'Actualización de perfil',
                    'description' => Session::getUser('username') . ' actualizó el usuario y/o contraseña.'
                ]);

                $content = '<ul>';
                $content .= "<li>Usuario <b>" . Session::getUser('username') . "</b> actualizado a <b>$request->username</b>.</li><li>Contraseña actualizada con exito!</li>";

                Session::updateUser($user[0]);
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

        echo json_encode($data);
        return;
    }

    public function chartBarPerfomance()
    {
        $currentWar = Session::get('clan_current_war_league');
        $data = [
            'label' => [generateID(), generateID(), generateID()],
            'values' => [50, 85, 74],
            'labelName' => 'Destrucción',
            'max' => 100
        ];
        vdump($currentWar);

        echo json_encode($data);
        return;
    }
}
