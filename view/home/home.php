<div class="container-fluid px-4">
    {!!MESSAGE!!}
    <h1 class="mt-4">Inicio <a href="<?= Route::get('home.reload') ?>" title="Recargar Información"><span class="fs-6"><i class="fas fa-retweet"></i></span></a></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Informaci&oacute;n Resumida</li>
    </ol>

    <div class="row mb-5">
        <?php for ($i = 0; $i < 4; $i++) : ?>
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats">
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title mb-0 fs-6 fw-bold"><?= $i + 1 ?>. <?= $members[$i]['name'] ?></h5>
                                <span class="h2 font-weight-bold text-muted mb-0"><strong><?= $members[$i]['donations'] ?></strong></span>
                                <span class="text-muted">Donaciones</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape rounded-circle shadow">
                                    <img src="#" alt="">
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-sm">
                            <span class="text-success mr-2"><?= $members[$i]['donationsReceived'] ?></span>
                            <span class="text-muted">Recividas</span>
                        </p>
                    </div>
                </div>
            </div>
        <?php endfor; ?>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-area me-1"></i>
                    Donaciones Mensuales
                </div>
                <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Guerra Actual
                </div>
                <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Miembros en el Clan ({!!members!!})
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>Jugador</th>
                        <th>Donaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($players as $player) : ?>
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-1">
                                        <img src="<?= $player->image ?>" width="40" alt="">
                                    </div>
                                    <div class="col">
                                        <div class="fs-5"><b><?= $player->name ?></b> (<span class="text-success"><?= traslate($player->role) ?></span>)</div>
                                        <div><?= $player->id ?></div>
                                        <div>
                                            <span class="badge bg-<?= $player->inClan ? 'success' : 'danger' ?>"><?= $player->inClan ? 'En el Clan' : 'Fuera del Clan' ?></span>
                                            <?php if ($player->status != 'active') : ?>
                                                <a href="<?= Route::get("list.$player->status") ?>" title="Ir a lista de <?= traslate($player->status) ?>">
                                                    <span class="badge bg-<?= $player->status == 'break' ? 'secondary' : 'primary' ?>">Lista de <?= traslate($player->status) ?></span>
                                                </a>
                                            <?php endif; ?>
                                            <span class="badge bg-warning text-dark fw-bold" role="button" title="Cantidad de veces que se ha unido al clan."><?= $player->cant ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div><b>Realizadas: </b><span><?=$player->donations?>/<?=$max?></span></div>
                                    <?php
                                        $percentDonations = round(($player->donations/$max)*100);
                                        $percentDonationsReceived = round(($player->donationsReceived/$max)*100);
                                    ?>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: <?=$percentDonations?>%;" aria-valuenow="<?=$percentDonations?>" aria-valuemin="0" aria-valuemax="100"><?=$percentDonations?>%</div>
                                    </div>
                                    <div><b>Recividas: </b><span><?=$player->donationsReceived?>/<?=$max?></span></div>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: <?=$percentDonationsReceived?>%;" aria-valuenow="<?=$percentDonationsReceived?>" aria-valuemin="0" aria-valuemax="100"><?=$percentDonationsReceived?>%</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    window.onload = () => {
        $.post('{!!url_get_donations!!}', (data) => {
            try {
                data = JSON.parse(data);
                initCharArea('myAreaChart', data);
            } catch (e) {}
        });

        $.post('{!!url_get_perfomance!!}', (data) => {
            try {
                data = JSON.parse(data);
                console.log(data);
                // initCharArea('myBarChart', data, 'bar');
            } catch (e) {}
        });
    }
</script>