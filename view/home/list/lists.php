<?php namespace core;?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Lista de {!!name_list!!}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Jugadores en {!!name_list!!} <span class="badge bg-primary"><?=count($players)?></span></li>
    </ol>
    {!!MESSAGE!!}
    <table id="datatablesSimple">
        <caption><a href="<?=Route::get($namePathNew)?>" class="btn btn-outline-primary">Agregar</a></caption>
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
                                <?php if ($player->inClan) : ?>
                                    <div class="badge bg-success">En el clan</div>
                                <?php else : ?>
                                    <div class="badge bg-danger">Fuera del clan</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <form action="<?= Route::get($namePathDestroy) ?>" method="post">
                            <input type="hidden" name="id" value="<?=$player->id?>">
                            <button type="submit" class="btn btn-outline-danger">Borrar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>