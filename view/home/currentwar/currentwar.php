<?php
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

$stars = function(int $stars){
    $star = '';
    for($i=0; $i<3; $i++){
        if($i<$stars){
            $star .= <<<HTML
                <i class="fas fa-star text-warning"></i>
            HTML;
        }else{
            $star .= <<<HTML
                <i class="far fa-star text-warning"></i>
            HTML;
        }
    }
    return $star;
}

?>
<i class="fas fa-star"></i>
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
                <tbody style="background-image: url('<?= asset('image/war.png') ?>');">
                    <tr class="text-center fw-bold" style="background-color: rgba(255,255,255,0.35);">
                        <td class="py-3">
                            <img src="<?= $currentWar['clan']['badgeUrls']['small'] ?>" width="50" alt=""><br>
                            <?= $currentWar['clan']['name'] ?>
                        </td>
                        <td class="fw-bold py-3 fs-4">v<span class="text-danger">s</span></td>
                        <td class="py-3">
                            <img src="<?= $currentWar['opponent']['badgeUrls']['small'] ?>" width="50" alt=""><br>
                            <?= $currentWar['opponent']['name'] ?>
                        </td>
                    </tr>
                    <?php foreach ($currentWar['clan']['members'] as $key => $member) : ?>
                        <tr>
                            <td colspan="3">
                                <div class="row p-2 text-center">
                                    <?php $clan = <<<HTML
                                        <span class="badge bg-white text-dark">{$member['mapPosition']}. {$member['name']}</span>
                                    HTML; ?>

                                    <?php if ($key == 0 || ($key % 2) == 0) : ?>
                                        <div class="col">
                                            <a href="#attack<?= $key ?>" data-bs-toggle="modal" role="button">
                                                <img src="<?= asset("image/th/th{$member['townhallLevel']}.png") ?>" width="50" alt=""><br>
                                                <?= $clan ?>
                                            </a>
                                        </div>
                                        <div class="col"></div>
                                    <?php else : ?>
                                        <div class="col"></div>
                                        <div class="col">
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
                                            <a href="#attack<?= $key . '_' . $key ?>" data-bs-toggle="modal" role="button">
                                                <img src="<?= asset("image/th/th{$currentWar['opponent']['members'][$key]['townhallLevel']}.png") ?>" width="50" alt=""><br>
                                                <?= $clan ?>
                                            </a>
                                        </div>
                                    <?php else : ?>
                                        <div class="col">
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
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="text-center">No hay guerra disponible.</div>
        <?php endif; ?>
    </div>
</div>