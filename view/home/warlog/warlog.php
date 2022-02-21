<div class="container-fluid px-4">
    <h1 class="mt-4">Registro de Guerra <a href="<?= Route::get('warlog.reload') ?>" title="Recargar Información"><span class="fs-6"><i class="fas fa-retweet"></i></span></a></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Informaci&oacute;n Resumida</li>
    </ol>
    {!!MESSAGE!!}

    <table class="table table-striped" id="dataTableWar">
        <thead>
            <tr>
                <th class="text-center">Clan</th>
                <th class="text-center">VS</th>
                <th class="text-center">Oponente</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($warlog as $war) : ?>
                <tr class="bg-<?= inArray($war['result'], ['win', 'lose', 'tie'], ['success', 'danger', 'secondary']) ?>">
                    <td>
                        <div class="row">
                            <div class="col-2">
                                <img src="<?= $war['clan']['badgeUrls']['small'] ?>" width="75" alt="">
                            </div>
                            <div class="col">
                                <div class="row">
                                    <div class="col">
                                        <div class="text-white">
                                            <span title="Nombre del clan." role="button">
                                                <i class="fa-solid fa-chess-rook"></i> <?= $war['clan']['name'] ?? '' ?> <a href="#" class="badge bg-primary"><?= $war['clan']['tag'] ?></a>
                                            </span>
                                        </div>

                                        <div class="text-white">
                                            <span title="Ataques Realizados." role="button">
                                                <i class="fa-solid fa-dragon"></i> <?= $war['clan']['attacks'] ?? 0 ?> / <?= $war['attacksPerMember'] * $war['teamSize'] ?>
                                            </span>
                                        </div>

                                        <div class="text-white">
                                            <span role="button" title="Experiencia obtenida.">
                                                <i class="fa-solid fa-award"></i> <?= $war['clan']['expEarned'] ?>
                                            </span>
                                        </div>

                                        <div class="text-white">
                                            <span role="button" title="Destrucción total.">
                                                <i class="fa-solid fa-bomb"></i> <?= round($war['clan']['destructionPercentage'] ?? 0, 2) ?>%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col text-end">
                                        <div>
                                            <span class="text-white">
                                                [<?= $war['endTime'] ?>]
                                            </span>
                                        </div>
                                        <div>
                                            <?php if ($id = (new War())->find($war['id'])->id) : ?>
                                                <a href="<?= Route::get('warlog.last')."/$id" ?>">
                                                    <span class="badge bg-primary">
                                                        <i class="fa-solid fa-calendar-week"></i> Detalles
                                                    </span>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="text-center">
                            <span class="badge bg-primary">
                                <span><?= $war['teamSize'] ?></span> vs <span><?= $war['teamSize'] ?></span>
                            </span>
                        </div>

                        <div class="text-center py-3">
                            <span class="badge bg-white text-<?= inArray($war['result'], ['win', 'lose', 'tie'], ['success', 'danger', 'dark']) ?>"><?= traslate($war['result']) ?></span>
                        </div>

                        <div class="text-center">
                            <span class="text-warning" title="Estrellas obtenidas." role="button">
                                <div>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star fs-5"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>
                                <span class="fw-bold">
                                    <?= $war['clan']['stars'] ?> - <?= $war['opponent']['stars'] ?>
                                </span>
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="row">
                            <div class="col-2">
                                <img src="<?= $war['opponent']['badgeUrls']['small'] ?>" width="75" alt="">
                            </div>
                            <div class="col">
                                <div class="text-white">
                                    <span title="Nombre del clan." role="button">
                                        <i class="fa-solid fa-chess-rook"></i> <?= $war['opponent']['name'] ?? '' ?> <a href="#" class="badge bg-primary"><?= $war['opponent']['tag'] ?></a>
                                    </span>
                                </div>

                                <div class="text-white">
                                    <span role="button" title="Destrucción total.">
                                        <i class="fa-solid fa-bomb"></i> <?= round($war['opponent']['destructionPercentage'] ?? 0, 2) ?>%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<script>
    window.onload = () => {
        new simpleDatatables.DataTable(dataTableWar);
    }
</script>