<?php if ($top) : ?>
    <div class="text-dark">
        <hr>
    </div>
    <h3>TOP <?= $count ?>: Destacados en Guerra</h3>
    <div class="text-dark">
        <hr>
    </div>
    <div class="row py-2">
        <?php foreach ($top as $key => $player) : ?>
            <div class="col">
                <div class="card p-3 position-relative" style="width: 100%;">
                    <img src="<?= $player['imageTH'] ?>" class="card-img-top" alt="">
                    <div class="card-body">
                        <h5 class="card-title"><?= $player['name'] ?></h5>
                        <p class="card-text"><?= $player['tag'] ?></p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Duraci&oacute;n: <span class="text-success"><?=$player['duration']?></span> m</li>
                        <li class="list-group-item">Ataques: <span class="badge bg-primary"><?=$player['attacks']?></span></li>
                        <li class="list-group-item">Estado: <span class="badge bg-secondary"><?=$player['status']?></span></li>
                    </ul>
                    <div class="card-body text-center">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-label="Example with label" style="width: <?=$player['percent']?>%;" aria-valuenow="<?=$player['percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$player['percent']?>%</div>
                        </div>
                        <div><?=str_repeat('⭐', $player['stars'])?></div>
                    </div>
                    <span class="badge position-absolute top-0 end-0 rounded-circle p-2">
                        <img src="<?=$player['image']?>" style="width: 3rem;">
                    </span>
                    <span class="position-absolute top-0 start-0 badge bg-primary"><?=$player['league']?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <br><br><br><br><br>
    <br><br><br><br><br>
    <br><br><br><br>
<?php endif; ?>