<?php 
    namespace core;
    use function core\{traslate};
?>
<div class="alert alert-success mt-5"><b>Lista de <?=$typeList?></b> con <?=count($players)?> Miembros.</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Jugador</th>
            <th>Guerras</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($players as $key => $player) : ?>
            <tr>
                <td class="col-1"><?= $key + 1 ?></td>
                <td>
                    <div class="row">
                        <div class="col-1">
                            <img src="<?= $player->image ?>" width="40">
                        </div>
                        <div class="col">
                            <div>
                                <b><?= $player->name ?></b> (<span class="text-danger"><?= traslate($player->role) ?></span>)
                            </div>
                            <div><?= $player->id ?></div>
                        </div>
                    </div>
                </td>
                <td><span class="badge bg-<?=(($player->war_count>=1)?(($player->war_count>=3)?'success':'primary'):'danger')?> text-white fw-bold"><?=$player->war_count?></span></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
