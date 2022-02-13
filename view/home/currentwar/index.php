<div class="container-fluid px-4">
    <h1 class="mt-4">Guerra Actual (<span class="fs-2 text-danger">{!!warname!!}</span>) <a href="<?= Route::get('currentwar.reload') ?>" title="Recargar Información"><span class="fs-6"><i class="fas fa-retweet"></i></span></a></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Informaci&oacute;n de la guerra actual.</li>
    </ol>
    {!!war!!}
</div>