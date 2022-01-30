<form action="<?=Route::get('home.show')?>" method="post" enctype="multipart/form-data">
    <input type="text" class="form-control" name='name' placeholder="ingresa tu nombre" required/>
    <input type="file" class="form-control" name="file[]" multiple required>
    <input type="file" class="form-control" name="file1[]" multiple required>
    <button class='btn btn-outline-primary' type="submit">Enviar</button>
</form>