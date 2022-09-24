<?php namespace core;?>
{!!MESSAGE!!}
<div class="d-flex flex-column min-vh-100 justify-content-center align-items-center">
    <div class="card mb-5 p-5 bg-dark bg-gradient text-white col-md-4">
        <div class="card-header text-center">
            <h3>Nueva Cuenta</h3>
            {!!description!!}
        </div>
        <div class="card-body mt-3">
            <form name="login" action="<?=Route::get('register.new')?>" method="post">
                <div class="input-group form-group mt-3">
                    <input type="text" class="form-control text-center p-3" value="{!!USERNAME!!}" placeholder="Usuario" name="username" required>
                </div>
                <div class="input-group form-group mt-3">
                    <input type="password" class="form-control text-center p-3" value="{!!PASSWORD!!}" placeholder="Contraseña" name="password" required>
                </div>
                <div class="input-group form-group mt-3">
                    <input type="password" class="form-control text-center p-3" placeholder="Repetir Contraseña" name="rpassword" required>
                </div>
                <div class="input-group form-group mt-3">
                    <input type="email" class="form-control text-center p-3" placeholder="Correo Electronico" name="email" required>
                </div>
                <div class="input-group form-group mt-3">
                    <input type="text" class="form-control text-center p-3" placeholder="Etiqueta del Clan: <?=Session::get('clan_info')['tag'] ?? ''?>" name="tag" required>
                </div>
                <div class="text-center">
                    <input type="submit" value="Crear Usuario" class="btn btn-primary mt-3 w-100 p-2">
                </div>
            </form>
            <?php if (isset($message)): ?>
                <div class="text-danger"><?= $message; ?></div>
            <?php endif; ?>
        </div>
        <div class="card-footer p-3">
            <div class="d-flex justify-content-center">
                <div class="text-primary"><a href="<?=Route::get('login.index')?>">Iniciar Sesi&oacute;n</a></div>
            </div>
        </div>
    </div>
</div>
