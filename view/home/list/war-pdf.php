<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Guerra</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="alert alert-success mb-3">Lista de Guerra de <?=count($players)?> Miembros.</div>
    <?php foreach($players as $key => $player):?>
        <div class="border-top py-3">
            <?=$key+1?>. 
            <!-- <img src="<?=$player->image?>" width="40" alt=""> -->
            <span><?=$player->name?></span> (<span class="text-muted"><?=traslate($player->role)?></span>) <br>
        </div>
    <?php endforeach;?>
    <h2 class="text-muted mt-5 bg-danger">Descripci&oacute;n</h2>
    <span><?=$description?></span>

    <div class="progress">
  <div class="progress-bar w-75" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
</div>
</body>

</html>