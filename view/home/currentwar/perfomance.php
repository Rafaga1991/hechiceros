<?php

namespace core;

use model\Player;

$numberOne = null;
?>

<?php if ($endWar) : ?>
    <div class="row bg-<?=$color = inArray($isVictory, [true, false], ['success', inArray($isTie, [true, false], ['secondary', 'danger'])])?> p-2 py-3">
        <div class="col">
            <div class="row">
                <div class="col-2">
                    <img src="<?= $currentwar['clan']['badgeUrls']['small'] ?>" width="75" alt="">
                </div>
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <div class="text-white">
                                <span title="Nombre del clan." role="button">
                                    <i class="fa-solid fa-chess-rook"></i> <?= $currentwar['clan']['name'] ?? '' ?> <a href="#" class="badge bg-primary"><?= $currentwar['clan']['tag'] ?></a>
                                </span>
                            </div>

                            <div class="text-white">
                                <span title="Ataques Realizados." role="button">
                                    <i class="fa-solid fa-dragon"></i> <?= $currentwar['clan']['attacks'] ?? 0 ?> / <?= $currentwar['attacksPerMember'] * $currentwar['teamSize'] ?>
                                </span>
                            </div>

                            <div class="text-white">
                                <span role="button" title="Destrucción total.">
                                    <i class="fa-solid fa-bomb"></i> <?= round($currentwar['clan']['destructionPercentage'] ?? 0, 2) ?>%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="text-center">
                <span class="badge bg-primary">
                    <span><?= $currentwar['teamSize'] ?></span> vs <span><?= $currentwar['teamSize'] ?></span>
                </span>
            </div>

            <div class="text-center my-2">
                <span class="text-warning" title="Estrellas obtenidas." role="button">
                    <div>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star fs-5"></i>
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <span class="fw-bold">
                        <?= $currentwar['clan']['stars'] ?> - <?= $currentwar['opponent']['stars'] ?>
                    </span>
                </span>
            </div>

            <div class="text-center">
                <span class="p-1 badge bg-white text-<?=$color?> fw-bold">
                <?=inArray($color, ['danger', 'success', 'secondary'], ['DEROTA', 'VICTORIA', 'EMPATE'])?>
            </span>
            </div>
        </div>
        <div class="col">
            <div class="row">
                <div class="col-2">
                    <img src="<?= $currentwar['opponent']['badgeUrls']['small'] ?>" width="75" alt="">
                </div>
                <div class="col">
                    <div class="text-white">
                        <span title="Nombre del clan." role="button">
                            <i class="fa-solid fa-chess-rook"></i> <?= $currentwar['opponent']['name'] ?? '' ?> <a href="#" class="badge bg-primary"><?= $currentwar['opponent']['tag'] ?></a>
                        </span>
                    </div>

                    <div class="text-white">
                                <span title="Ataques Realizados." role="button">
                                    <i class="fa-solid fa-dragon"></i> <?= $currentwar['opponent']['attacks'] ?? 0 ?> / <?= $currentwar['attacksPerMember'] * $currentwar['teamSize'] ?>
                                </span>
                            </div>

                    <div class="text-white">
                        <span role="button" title="Destrucción total.">
                            <i class="fa-solid fa-bomb"></i> <?= round($currentwar['opponent']['destructionPercentage'] ?? 0, 2) ?>%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Jugador</th>
            <th>Estrellas</th>
            <th>Tiempo Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($members as $stars => $member) : ?>
            <?php foreach ($member as $value) : ?>
                <tr>
                    <td>
                        <?php if ($cant == 0) $numberOne = $value['name']; ?>
                        <?= ++$cant ?>. <img src="<?= (new Player())->find($value['tag'])->image ?>" width="50" alt="">
                        <?= $value['name'] ?>
                    </td>
                    <td>
                        <?= str_repeat('<i class="fas fa-star text-warning"></i>', $value['stars']) ?><br>
                        <?php $percent = round(($value['destruction'] / $value['attacks']), 1); ?>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $percent ?>%"><?= $percent ?>%</div>
                        </div>
                    </td>
                    <td><?= date('i:s', mktime(0, 0, $value['duration'])) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($numberOne && $endWar) : ?>
    <div class="alert alert-success fs-5">
        Felicidades a <span class="fw-bold"><?= $numberOne ?></span> por tener el mejor desempe&ntilde;o en la guerra.
    </div>
<?php endif; ?>