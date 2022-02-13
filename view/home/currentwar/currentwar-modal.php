<div class="modal fade" id="attack<?= $key ?>" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <span><?= $member['mapPosition'] ?></span>.
                    <img src="<?= asset('image/th/th' . $member['townhallLevel'] . '.png') ?>" width="40" alt="">
                    <span class="fw-bold"><?= $member['name'] ?></span> (<span class="text-danger fw-bold">TH<?= $member['townhallLevel'] ?></span>)
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-primary">Ataques Realizados <?= isset($member['attacks']) ? count($member['attacks']) : 0 ?>/<?= $attacksPerMember ?></div>
                <table class="table border border-primary">
                    <thead>
                        <tr>
                            <th class="w-50">Jugador</th>
                            <th class="w-25">Destrucci&oacute;n</th>
                            <th class="w-25">Tiempo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attacks as $attack) : ?>
                            <tr class="border border-primary">
                                <td class="w-50">
                                    <span><?= $attack[0]['position'] ?>. </span>
                                    <img src="<?= asset('image/th/th' . $attack[0]['level'] . '.png') ?>" width="40" alt="">
                                    <span><?= $attack[0]['name'] ?> (<span class="text-danger">TH<?= $attack[0]['level'] ?></span>)</span>
                                </td>
                                <td class="w-25">
                                    <?= $stars($attack['attack']['stars']) ?>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="<?= $attack['attack']['destructionPercentage'] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $attack['attack']['destructionPercentage'] ?>%"><?= $attack['attack']['destructionPercentage'] ?>%</div>
                                    </div>
                                </td>
                                <td class="w-25"><?= date('i:s', mktime(0, 0, $attack['attack']['duration'], 0, 0, 0)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="alert alert-danger">Defensa <?= count($filterDefencesAttacks) ?>/<?= $members ?></div>
                <table class="table border border-danger">
                    <thead>
                        <tr>
                            <th class="w-50">Jugador</th>
                            <th class="w-25">Destrucci&oacute;n</th>
                            <th class="w-25">Tiempo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filterDefencesAttacks as $attack) : ?>
                            <tr class="border border-danger">
                                <td class="w-50">
                                    <span><?= $attack['position'] ?>. </span>
                                    <img src="<?= asset('image/th/th' . $attack['level'] . '.png') ?>" width="40" alt="">
                                    <span><?= $attack['name'] ?> (<span class="text-danger">TH<?= $attack['level'] ?></span>)</span>
                                </td>
                                <td class="w-25">
                                    <?= $stars($attack['attack']['stars']) ?>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="<?= $attack['attack']['destruction'] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $attack['attack']['destruction'] ?>%"><?= $attack['attack']['destruction'] ?>%</div>
                                    </div>
                                </td>
                                <td class="w-25"><?= date('i:s', mktime(0, 0, $attack['attack']['duration'], 0, 0, 0)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>