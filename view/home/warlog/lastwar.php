<?php namespace core;?>
<div class="container-fluid p-4">
    <h1 class="mt-4">Detalle De Guerra</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?=Route::get('warlog.index')?>">Registro de Guerra</a></li>
        <li class="breadcrumb-item active">Detalle de Guerra</li>
    </ol>
    {!!MESSAGE!!}
    {!!war!!}
</div>