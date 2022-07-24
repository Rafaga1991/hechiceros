<?php namespace core;?>
<div class="container-fuid px-4">
    <h1 class="mt-4">Lista de Guerra <span class="badge bg-danger">( <?=$listwar->date?> )</span></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?=Route::get('list.war')?>" title="Volver">Lista de Guerra</a></li>
        <li class="breadcrumb-item active">Actualizar Lista</li>
    </ol>
    {!!MESSAGE!!}
    
    <h5 class="text-muted">Jugadores disponibles (<?= count($players) ?>)</h5>
    <table class="datatablesSimple">
        <caption>
            <div><label>Seleccionados: <span id="player"><?=count($list)?></span></label></div>
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
                <tr>
                    <td>
                        <div class="row">
                            <div class="col-1">
                                <img src="<?= $player->image ?>" width="25">
                            </div>
                            <div class="col">
                                <?= $player->name ?> (<span class="text-success"><?= Functions::traslate($player->role) ?></span>)<br>
                                <?= $player->id ?>
                            </div>
                        </div>
                    </td>
                    <td><?= Functions::traslate($player->status) ?></td>
                    <td class="text-center">
                        <input type="checkbox" value="<?= $player->id ?>" <?=in_array($player->id, $list)?'checked':''?> onclick="onClickSelect(this)">
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form action="{!!url_form!!}" class="my-2" id="form" method="post">
        <input type="hidden" name="listId" value="<?=$listwar->id?>">
        <div class="form-group my-4">
            <label for="">Descripci&oacute;n (opcional):</label>
            <textarea name="description" cols="30" rows="10" class="form-control" placeholder="Breve descripción"><?=$listwar->description?></textarea>
        </div>
        <button type="submit" class="btn btn-outline-primary">Actualizar Lista</button>
        <?php foreach($list as $player):?>
            <input type="hidden" name="player[]" value="<?=$player?>">
        <?php endforeach;?>
    </form>
</div>
