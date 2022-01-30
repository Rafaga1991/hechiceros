<h1>Este es el login</h1>

<?php foreach($data as $value):?>
    <a href="<?=Route::redirect('login.showData', $value)?>" class="btn btn-outline-primary"><?="$value->id. $value->name $value->last_name"?></a>
<?php endforeach;?>
