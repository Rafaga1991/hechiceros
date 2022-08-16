<?php 
    namespace core;
    use function core\{isAdmin,traslate};
?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Lista de {!!name_list!!}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Jugadores en {!!name_list!!} <span class="badge bg-primary"><?=count($players)?></span></li>
    </ol>
    {!!MESSAGE!!}
    <table class="datatablesSimple">
        <?php if(isAdmin()):?>
            <caption>
                <div class="btn-group">
                    <a href="<?=Route::get($namePathNew)?>" class="btn btn-outline-primary me-1">Agregar</a>
                    <form action="<?= Route::get($namePathDestroy) ?>" method="post" id="form" hidden>
                        <button type="submit" class="btn btn-outline-danger">Borrar (<span class="fw-bold" id="cant_selected"></span>)</button>
                    </form>
                </div>
            </caption>
        <?php endif;?>
        <thead>
            <tr>
                <th>Jugador</th>
                <?php if(isAdmin()):?>
                    <th>Acci&oacute;n</th>
                <?php endif;?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $player) : ?>
                <tr>
                    <td>
                        <div class="row">
                            <div class="col-1">
                                <input type="checkbox" onclick="onClickInput(this)" value="<?=$player->id?>" />
                                <img src="<?= $player->image ?>" width="30" alt="">
                            </div>
                            <div class="col">
                                <div class="fs-5"><b><?= $player->name ?></b> (<span class="text-success"><?= traslate($player->role) ?></span>)</div>
                                <div><?= $player->id ?></div>
                                <?php if ($player->inClan) : ?>
                                    <div class="badge bg-success">En el clan</div>
                                <?php else : ?>
                                    <div class="badge bg-danger">Fuera del clan</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <?php if(isAdmin()):?>
                        <td>
                            <form action="<?= Route::get($namePathDestroy) ?>" method="post">
                                <input type="hidden" name="id" value="<?=$player->id?>">
                                <button type="submit" class="btn btn-outline-danger">Borrar</button>
                            </form>
                        </td>
                    <?php endif;?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    let selected = 0;
    function onClickInput(element){
        var input = document.createElement('input');
        input.type = 'hidden';
        input.value = element.value;
        input.name = 'id[]';

        if(element.checked){
            selected++;
            form.appendChild(input);
        }else{
            selected--;
            form.querySelector(`input[value="${element.value}"]`).remove();
        }

        form.hidden = !(selected > 0);
        cant_selected.innerText = selected;
    }
</script>
