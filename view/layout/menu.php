<div class="bg-dark py-3 w-100" text-color="white" position="fixed">
    <div class="row px-3">
        <div class="col-2">
            <a href="<?=Route::get('home')?>" class="" text-color="white" clear-style>
                <img src="<?=$badgeUrls['small']?>" width="25" rel="img" /> <?=PROYECT_NAME?>
            </a>
        </div>
        <div class="col" text-align="right">
            <a href="<?=Route::get('')?>" option="<?=Route::isView('') ? 'selected' : 'item'?>">Registro</a>
            <a href="<?=Route::get('')?>" option="<?=Route::isView('') ? 'selected' : 'item'?>">Administrador</a>
            <a href="<?=Route::get('list-members')?>" option="<?=Route::isView('listmembers') ? 'selected' : 'item'?>">Miembros</a>
            <a href="<?=Route::get('list')?>" option="<?=Route::isView('list') ? 'selected' : 'item'?>">Listas</a>
            <a href="javascript:exit()" option="close" title="cerrar sesion">
                <?=strtoupper(Session::user())?> <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</div>
