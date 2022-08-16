<?php

namespace core;
use function core\{traslate,asset,includes,getRoute};

usort($currentWar['clan']['members'], function ($arr1, $arr2) {
    return $arr1['mapPosition'] > $arr2['mapPosition'];
});
usort($currentWar['opponent']['members'], function ($arr1, $arr2) {
    return $arr1['mapPosition'] > $arr2['mapPosition'];
});

$filterAttacks = function (array $currentWar, string $tag, string $type = 'clan') {
    $members = [];
    foreach ($currentWar[$type]['members'] as $member) {
        if (isset($member['attacks'])) {
            foreach ($member['attacks'] as $attack) {
                if ($attack['defenderTag'] == $tag) {
                    $members[] = [
                        'tag' => $member['tag'],
                        'name' => $member['name'],
                        'level' => $member['townhallLevel'],
                        'position' => $member['mapPosition'],
                        'attack' => [
                            'duration' => $attack['duration'],
                            'destruction' => $attack['destructionPercentage'],
                            'stars' => $attack['stars']
                        ]
                    ];
                    break;
                }
            }
        }
    }
    return $members;
};

$myAttacks = function ($currentWar, string $tag, string $type = 'opponent') {
    $members = [];
    foreach ($currentWar[$type]['members'] as $key => $member) {
        if ($member['tag'] == $tag) {
            $members[] = [
                'tag' => $member['tag'],
                'name' => $member['name'],
                'level' => $member['townhallLevel'],
                'position' => $member['mapPosition']
            ];
            break;
        }
    }

    return $members;
};

$stars = function (int $stars, string $color = 'text-warning', bool $three = true, int $cant = 3) {
    $star = '';
    for ($i = 0; $i < $cant; $i++) {
        $size = "";
        if ($i == 1 && $three) $size = 'fs-5';
        if ($i < $stars) {
            $star .= <<<HTML
                <i class="fas fa-star $size $color"></i>
            HTML;
        } else {
            $star .= <<<HTML
                <i class="far fa-star $size $color"></i>
            HTML;
        }
    }
    return $star;
};

$player = function (array $war, string $tag, string $type = 'clan'): string {
    if (isset($war[$type])) {
        foreach ($war[$type]['members'] as $member) {
            if ($member['tag'] == $tag) {
                return "{$member['mapPosition']}. {$member['name']}";
            }
        }
    }
    return '';
};

$members = [];

?>

<div class="card">
    <div class="card-header">
        <div class="row">
            <?php if (isset($currentWar) && count($currentWar) > 0) : ?>
                <div class="col">Guerra Actual (<span class="fw-bold text-<?= $currentWar['state'] == 'inWar' ? 'success' : ($currentWar['state'] == 'preparation' ? 'secondary' : 'danger') ?>"><?= traslate($currentWar['state']) ?></span>)</div>
                <div class="col">
                    <?php
                    $start = time() - strtotime(explode('.', $currentWar['preparationStartTime'])[0]);
                    $end = strtotime(explode('.', $currentWar['endTime'])[0]) - strtotime(explode('.', $currentWar['preparationStartTime'])[0]);
                    $percent = round((($start / $end) * 100), 2);
                    $percent = ($percent <= 100) ? $percent : 100;
                    ?>
                    Completada
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $percent ?>%"><?= $percent ?>%</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <?php if (isset($currentWar) && count($currentWar) > 0) : ?>
            <table class="table">
                <thead>
                    <tr class="bg-dark text-white">
                        <th>Preparaci&oacute;n</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                    </tr>
                    <tr class="fw-bold bg-dark text-white">
                        <td class="bg-<?= strtotime(explode('.', $currentWar['preparationStartTime'])[0]) < time() ? 'primary' : 'dark' ?>"><?= date('d M Y h:i:s A', strtotime(explode('.', $currentWar['preparationStartTime'])[0])) ?></td>
                        <td class="bg-<?= strtotime(explode('.', $currentWar['startTime'])[0]) < time() ? 'primary' : 'dark' ?>"><?= date('d M Y h:i:s A', strtotime(explode('.', $currentWar['startTime'])[0])) ?></td>
                        <td class="bg-<?= strtotime(explode('.', $currentWar['endTime'])[0]) < time() ? 'primary' : 'dark' ?>"><?= date('d M Y h:i:s A', strtotime(explode('.', $currentWar['endTime'])[0])) ?></td>
                    </tr>
                </thead>
                <tbody style="background-image: url('<?= asset('image/war.png') ?>');background-position: center;background-repeat: no-repeat;background-size: cover;">
                    <tr class="text-center fw-bold" style="background-color: rgba(255,255,255,0.35);">
                        <td class="py-3">
                            <div class="row">
                                <div class="col-5">
                                    <img src="<?= $currentWar['clan']['badgeUrls']['small'] ?>" width="50" alt=""><br>
                                    <?= $currentWar['clan']['name'] ?>
                                </div>
                                <div class="col text-start">
                                    <div style="font-size: 15px;">
                                        <i class="fa-solid fa-fire text-danger"></i> Destrucci&oacute;n: <?= $currentWar['clan']['destructionPercentage'] ?>%
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?= $currentWar['clan']['destructionPercentage'] ?>%;" aria-valuenow="<?= $currentWar['clan']['destructionPercentage'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="pt-1">
                                            <span title="Ataques realizados" role="button">
                                                <i class="fa-solid fa-dragon"></i> <?= $currentWar['clan']['attacks'] ?> / <?= count($currentWar['clan']['members']) * $currentWar['attacksPerMember'] ?>
                                            </span>
                                        </div>
                                        <div class="pt-1">
                                            <span title="Estrellas Obtenidas" role="button">
                                                <i class="fas fa-star text-warning"></i> <?= $currentWar['clan']['stars'] ?> / <?= count($currentWar['clan']['members']) * 3 ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="fw-bold py-3 fs-4">
                            <span class="badge bg-white text-dark">
                                <?= count($currentWar['clan']['members']) ?>
                                <span class="text-dark">v</span><span class="text-danger">s</span>
                                <?= count($currentWar['clan']['members']) ?>
                            </span>
                        </td>
                        <td class="py-3">
                            <div class="row">
                                <div class="col text-start">
                                    <div style="font-size: 15px;">
                                        <i class="fa-solid fa-fire text-danger"></i> Destrucci&oacute;n: <?= $currentWar['opponent']['destructionPercentage'] ?>%
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?= $currentWar['opponent']['destructionPercentage'] ?>%;" aria-valuenow="<?= $currentWar['opponent']['destructionPercentage'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="pt-1">
                                            <span title="Ataques realizados" role="button">
                                                <i class="fa-solid fa-dragon"></i> <?= $currentWar['opponent']['attacks'] ?> / <?= count($currentWar['opponent']['members']) * $currentWar['attacksPerMember'] ?>
                                            </span>
                                        </div>
                                        <div class="pt-1">
                                            <span title="Estrellas Obtenidas" role="button">
                                                <i class="fas fa-star text-warning"></i> <?= $currentWar['opponent']['stars'] ?> / <?= count($currentWar['opponent']['members']) * 3 ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <img src="<?= $currentWar['opponent']['badgeUrls']['small'] ?>" width="50" alt=""><br>
                                    <?= $currentWar['opponent']['name'] ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php foreach ($currentWar['clan']['members'] as $key => $member) : ?>
                        <tr style="border: 1px solid transparent;">
                            <td colspan="3">
                                <div class="row p-2 text-center">
                                    <?php $clan = <<<HTML
                                        <span class="badge bg-white text-dark">{$member['mapPosition']}. {$member['name']}</span>
                                    HTML; ?>

                                    <?php if ($key == 0 || ($key % 2) == 0) : ?>
                                        <div class="col">
                                            <?php if (isset($member['bestOpponentAttack'])) : ?>
                                                <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $player($currentWar, $member['bestOpponentAttack']['attackerTag'], 'opponent') ?> (<?= $member['bestOpponentAttack']['destructionPercentage'] ?>%)">
                                                    <?= $stars($member['bestOpponentAttack']['stars'], 'text-danger') ?> <br>
                                                </span>
                                            <?php endif; ?>
                                            <a href="#attack<?= $key ?>" data-bs-toggle="modal" role="button">
                                                <img src="<?= asset("image/th/th{$member['townhallLevel']}.png") ?>" width="50" alt=""><br>
                                                <?= $clan ?>
                                            </a>
                                        </div>
                                        <div class="col"></div>
                                    <?php else : ?>
                                        <div class="col"></div>
                                        <div class="col">
                                            <?php if (isset($member['bestOpponentAttack'])) : ?>
                                                <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $player($currentWar, $member['bestOpponentAttack']['attackerTag'], 'opponent') ?> (<?= $member['bestOpponentAttack']['destructionPercentage'] ?>%)">
                                                    <?= $stars($member['bestOpponentAttack']['stars'], 'text-danger') ?> <br>
                                                </span>
                                            <?php endif; ?>
                                            <a href="#attack<?= $key ?>" data-bs-toggle="modal" role="button">
                                                <img src="<?= asset("image/th/th{$member['townhallLevel']}.png") ?>" width="50" alt=""><br>
                                                <?= $clan ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <div class="col">
                                        <div>
                                            <span class="badge bg-primary text-white">
                                                <?= isset($member['attacks']) ? count($member['attacks']) : 0 ?> - <?= isset($currentWar['opponent']['members'][$key]['attacks']) ? count($currentWar['opponent']['members'][$key]['attacks']) : 0 ?>
                                            </span>
                                        </div>
                                    </div>

                                    <?php $clan = <<<HTML
                                        <span class="badge bg-white text-dark">{$currentWar['opponent']['members'][$key]['mapPosition']}. {$currentWar['opponent']['members'][$key]['name']}</span>
                                    HTML; ?>

                                    <?php if ($key == 0 || ($key % 2) == 0) : ?>
                                        <div class="col"></div>
                                        <div class="col">
                                            <?php if (isset($currentWar['opponent']['members'][$key]['bestOpponentAttack'])) : ?>
                                                <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $player($currentWar, $currentWar['opponent']['members'][$key]['bestOpponentAttack']['attackerTag']) ?> (<?= $currentWar['opponent']['members'][$key]['bestOpponentAttack']['destructionPercentage'] ?>%)">
                                                    <?= $stars($currentWar['opponent']['members'][$key]['bestOpponentAttack']['stars'], 'text-danger') ?> <br>
                                                </span>
                                            <?php endif; ?>
                                            <a href="#attack<?= $key . '_' . $key ?>" data-bs-toggle="modal" role="button">
                                                <img src="<?= asset("image/th/th{$currentWar['opponent']['members'][$key]['townhallLevel']}.png") ?>" width="50" alt=""><br>
                                                <?= $clan ?>
                                            </a>
                                        </div>
                                    <?php else : ?>
                                        <div class="col">
                                            <?php if (isset($currentWar['opponent']['members'][$key]['bestOpponentAttack'])) : ?>
                                                <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $player($currentWar, $currentWar['opponent']['members'][$key]['bestOpponentAttack']['attackerTag']) ?> (<?= $currentWar['opponent']['members'][$key]['bestOpponentAttack']['destructionPercentage'] ?>%)">
                                                    <?= $stars($currentWar['opponent']['members'][$key]['bestOpponentAttack']['stars'], 'text-danger') ?> <br>
                                                </span>
                                            <?php endif; ?>
                                            <a href="#attack<?= $key . '_' . $key ?>" data-bs-toggle="modal" role="button">
                                                <img src="<?= asset("image/th/th{$currentWar['opponent']['members'][$key]['townhallLevel']}.png") ?>" width="50" alt=""><br>
                                                <?= $clan ?>
                                            </a>
                                        </div>
                                        <div class="col"></div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                $attacks = [];
                                if (isset($member['attacks'])) {
                                    foreach ($member['attacks'] as $attack) {
                                        if ($attack_ = $myAttacks($currentWar, $attack['defenderTag'])) {
                                            $attacks[] = array_merge($attack_, ['attack' => $attack]);
                                        }
                                    }
                                }
                                $attacksOpponents = [];
                                if (isset($currentWar['opponent']['members'][$key]['attacks'])) {
                                    foreach ($currentWar['opponent']['members'][$key]['attacks'] as $item) {
                                        if ($attack_ = $myAttacks($currentWar, $item['defenderTag'], 'clan')) {
                                            $attacksOpponents[] = array_merge($attack_, ['attack' => $item]);
                                        }
                                    }
                                }
                                includes(
                                    [
                                        getRoute('view/home/currentwar/currentwar-modal.php'),
                                        getRoute('view/home/currentwar/currentwar-modal.php')
                                    ],
                                    [[
                                        'member' => $member,
                                        'key' => $key,
                                        'attacksPerMember' => $currentWar['attacksPerMember'],
                                        'filterDefencesAttacks' => $filterAttacks($currentWar, $member['tag'], 'opponent'),
                                        'attacks' => $attacks,
                                        'members' => $currentWar['teamSize'],
                                        'stars' => $stars
                                    ], [
                                        'member' => $currentWar['opponent']['members'][$key],
                                        'key' => $key . "_$key",
                                        'attacksPerMember' => $currentWar['attacksPerMember'],
                                        'filterDefencesAttacks' => $filterAttacks($currentWar, $currentWar['opponent']['members'][$key]['tag']),
                                        'attacks' => $attacksOpponents,
                                        'members' => $currentWar['teamSize'],
                                        'stars' => $stars
                                    ]]
                                );
                                ?>
                            </td>
                        </tr>
                        <?php
                        if (isset($member['attacks'])) {
                            $data = [
                                'name' => $member['name'],
                                'tag' => $member['tag'],
                                'stars' => 0,
                                'duration' => 0,
                                'destruction' => 0,
                                'attacks' => $currentWar['attacksPerMember']
                            ];
                            foreach ($member['attacks'] as $attack) {
                                $data['stars'] += $attack['stars'];
                                $data['duration'] += $attack['duration'];
                                $data['destruction'] += $attack['destructionPercentage'];
                            }
                            $members[] = $data;
                        }
                        ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="text-center">No hay guerra disponible.</div>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <?php
        usort($members, function ($arr1, $arr2) {
            return $arr1['stars'] < $arr2['stars'];
        });

        $data = [];
        foreach ($members as $member) {
            $data[$member['stars']][] = $member;
        }

        $members = $data;
        $data = [];
        foreach ($members as $stars => $member) {
            usort($member, function ($arr1, $arr2) {
                if($arr1['destruction'] == $arr2['destruction']) return $arr1['duration'] > $arr2['duration'];
                return $arr1['destruction'] < $arr2['destruction'];
            });
            $data[] = $member;
        }
        Session::set('_PERFOMANCE_', $members);
        ?>
        <div>
            <div class="row">
                <div class="col">
                    <span class="text-muted fs-3">Desempeño</span>
                </div>
                <div class="col text-end">
                    <?php if (!empty($members)) : ?>
                        <span class="btn-group">
                            <i class="fa-solid fa-download btn btn-outline-success" role="button" title="Descargar Desempeño" id="download"></i>
                            <a href="<?= Route::redirect('currentwar.perfomance', $data) ?>" class="btn btn-outline-primary" title="Ver Desempeño" target="_blank">
                                <i class="fas fa-eye"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            </viv>
            <iframe src="<?= Route::redirect('currentwar.perfomance', $data) ?>" id="iPerfomance" frameborder="0" width="100%" height="500"></iframe>
            <table class="table table-striped mt-4">
                <thead>
                    <tr>
                        <th>Detalles de la Guerra</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $players = $stars = 0; ?>
                    <?php foreach ($members as $index => $member) : ?>
                        <tr>
                            <td><?= count($member) . ' ' . (count($member) > 1 ? 'jugadores obtuvieron' : 'jugador obtuvo') . " $index estrellas." ?></td>
                        </tr>
                        <?php $players += count($member);
                        $stars += count($member) * $index ?>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th><?= $players . ' ' . ($players == 1 ? 'jugador obtuvo' : 'jugadores obtuvieron') . " un total de $stars " . (($stars == 1) ? 'estrella' : 'estrellas') . '.' ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script>
        download.onclick = () => {
            iPerfomance.contentWindow.print();
        };
    </script>
