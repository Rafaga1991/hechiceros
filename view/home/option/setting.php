<div class="container-fluid px-4">
    <h1 class="mt-4">Opciones</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Actualizaci&oacute;n de informaci&oacute;n</li>
    </ol>

    {!!error!!}

    <form action="<?=Route::get('home.setting.form')?>" method="post">
        <div class="form-group">
            <label for="" class="form-label">Nombre de usuario:</label>
            <input type="text" name="username" class="form-control" value="<?=Session::getUser('username')?>" required>
        </div>

        <div class="form-group pt-2">
            <label for="" class="form-label">Contrase&ntilde;a:</label>
            <input type="password" name="password" class="form-control" placeholder="Nueva contraseña" required>
        </div>
        <div class="form-group pt-2">
            <label for="" class="form-label">Repetir Contrase&ntilde;a:</label>
            <input type="password" name="rpassword" class="form-control" placeholder="Repetir contraseña" required>
        </div>
        <input type="submit" class="btn btn-outline-danger mt-3" value="Actualizar">
    </form>
</div>