<div class="container-fluid px-4">
    <h1 class="mt-4">Inicio <a href="<?=Route::get('home.reload')?>" title="Recargar Información"><span class="fs-6"><i class="fas fa-retweet"></i></span></a></h1>
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
                                <h5 class="card-title mb-0 fs-6 fw-bold"><?=$i+1?>. <?=$members[$i]['name']?></h5>
                                <span class="h2 font-weight-bold text-muted mb-0"><strong><?=$members[$i]['donations']?></strong></span>
                                <span class="text-muted">Donaciones</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape rounded-circle shadow">
                                    <img src="#" alt="">
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-sm">
                            <span class="text-success mr-2"><?=$members[$i]['donationsReceived']?></span>
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
            Miembros del clan ({!!members!!})
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>Etiqueta</th>
                        <th>Jugador</th>
                        <th>Rol</th>
                        <th>Donaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member) : ?>
                        <tr>
                            <td><?= $member['tag'] ?></td>
                            <td><img src="<?= $member['league']['iconUrls']['small'] ?>" alt=""> <?= $member['name'] ?></td>
                            <td><?=traslate($member['role'])?></td>
                            <td>
                                <h6>Realizadas: <?=$member['donations']?></h6>
                                <h6>Donadas: <?=$member['donationsReceived']?></h6>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    window.onload = ()=>{
        $.post('{!!url_get_donations!!}', (data)=>{
            try{
                data = JSON.parse(data);
                initCharArea('myAreaChart', data);

            }catch(e){ }
        });

        $.post('{!!url_get_perfomance!!}', (data)=>{
            try{
                data = JSON.parse(data);
                console.log(data);
                initCharArea('myBarChart', data, 'bar');
            }catch(e){ }
        });
    }
</script>

