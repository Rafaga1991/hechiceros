<?php

namespace core;

use model\Player;

$numberOne = null;
?>
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
                        <?php if($cant == 1) $numberOne = $value['name']; ?>
                        <?= $cant++ ?>. <img src="<?= (new Player())->find($value['tag'])->image ?>" width="50" alt="">
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

<?php if($numberOne):?>
    <div class="alert alert-success fs-5">
        Felicidades a <span class="fw-bold"><?=$numberOne?></span> por tener el mejor desempe&ntilde;o en la guerra.
    </div>
<?php endif;?>