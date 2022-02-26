<?php namespace core;?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Lista de {!!name_type_list!!}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= Route::get($namePath) ?>">Lista de {!!name_type_list!!}</a></li>
        <li class="breadcrumb-item active">Nuevo Jugador en {!!name_type_list!!}</li>
    </ol>
    {!!MESSAGE!!}
    <table id="datatablesSimple">
        <caption>
            <div><label>Seleccionados: <span id="player">0</span></label></div>
            <form action="<?= Route::get($namePathChange) ?>" id="form" method="post">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </caption>
        <thead>
            <tr>
                <th>Jugador</th>
                <th>Acci&oacute;n</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $player) : ?>
                <tr>
                    <td>
                        <div class="row">
                            <div class="col-1"><img src="<?= $player->image ?>" width="30" alt=""></div>
                            <div class="col">
                                <div class="fs-5"><b><?= $player->name ?></b> (<span class="text-success"><?= Functions::traslate($player->role) ?></span>)</div>
                                <div><?= $player->id ?></div>
                                <div>
                                    <span class="badge bg-<?= $player->inClan ? 'success' : 'danger' ?>">
                                        <?= $player->inClan ? 'En el clan' : 'Fuera del clan' ?>
                                    </span>
                                    <?php if($player->status != 'active'):?>
                                        <span class="badge bg-primary">Lista de <?=Functions::traslate($player->status)?></span>
                                    <?php endif?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" value="<?= $player->id ?>" onclick="onClickSelect(this)">
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>