<div class="container-fluid px-4">
    <h1 class="mt-4">Nueva Lista de Guerra</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= Route::get('list.war') ?>" title="Volver">Lista de Guerra</a></li>
        <li class="breadcrumb-item active">Nueva Lista</li>
    </ol>
    {!!MESSAGE!!}
    <h5 class="text-muted">Jugadores En Espera ({!!cant_members_wait!!})</h5>
    <table class="table table-striped" id="dataTableMembersWait">
        <thead>
            <tr>
                <th>Jugador</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $player) : ?>
                <?php if ($player->status == 'wait') : ?>
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-1">
                                    <img src="<?= $player->image ?>" width="25">
                                </div>
                                <div class="col">
                                    <?= $player->name ?> (<span class="text-success"><?= traslate($player->role) ?></span>)<br>
                                    <?= $player->id ?>
                                </div>
                            </div>
                        </td>
                        <td><?= traslate($player->status) ?></td>
                        <td class="text-center">
                            <input type="checkbox" value="<?= $player->id ?>" onclick="onClickSelect(this)">
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <hr class="dropdown-divider">
    <h5 class="text-muted">Jugadores disponibles (<?= count($players) ?>)</h5>
    <table class="table table-striped" id="datatablesSimple">
        <caption>
            <div><label>Seleccionados: <span id="player">0</span></label></div>
        </caption>
        <thead>
            <tr>
                <th>Jugador</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $player) : ?>
                <?php if ($player->status == 'active' || $player->status == 'war') : ?>
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-1">
                                    <img src="<?= $player->image ?>" width="25">
                                </div>
                                <div class="col">
                                    <?= $player->name ?> (<span class="text-success"><?= traslate($player->role) ?></span>)<br>
                                    <?= $player->id ?>
                                </div>
                            </div>
                        </td>
                        <td><?= traslate($player->status) ?></td>
                        <td class="text-center">
                            <input type="checkbox" value="<?= $player->id ?>" onclick="onClickSelect(this)">
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form action="{!!url_form!!}" class="my-2" id="form" method="post">
        <div class="form-group my-4">
            <label for="">Descripci&oacute;n (opcional):</label>
            <textarea name="description" cols="30" rows="10" class="form-control" placeholder="Breve descripción"></textarea>
        </div>
        <button type="submit" class="btn btn-outline-primary">Crear Lista</button>
    </form>
</div>

<script>
    new simpleDatatables.DataTable(dataTableMembersWait);
</script>