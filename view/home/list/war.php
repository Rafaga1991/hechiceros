<div class="container-fluid px-4">
    <h1 class="mt-4">Lista de Guerra</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Listas de Guerras Creadas <span class="badge bg-success"><?=count($listwar)?></span></li>
    </ol>
    {!!MESSAGE!!}

    <table id="datatablesSimple">
        <caption><a href="<?= Route::get('list.war.new') ?>" class="btn btn-outline-primary">Crear Lista de Guerra</a></caption>
        <thead>
            <tr>
                <th>Lista</th>
                <th>Miembros</th>
                <th>Descripci&oacute;n</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listwar as $key => $list) : ?>
                <tr>
                    <td>
                        <iframe src="<?= Route::get('list.war.show') ?>/<?=$list['id']?>" id="download<?=$list['id']?>" frameborder="0" hidden></iframe>
                        <span class="badge bg-danger" title="ver lista de guerra">#<?=$key+1?>.</span> <a href="<?= Route::get('list.war.show') ?>/<?=$list['id']?>" class="badge bg-primary" target="_blank"><?= $list['date'] ?></a>

                        <a href="<?=Route::get('list.war.destroy')?>/<?=$list['id']?>" class="fs-5 text-danger" title="Borrar Lista de Guerra"><i class="far fa-trash-alt px-1"></i></a>
                        <a href="<?=Route::get('list.war.update')?>/<?=$list['id']?>" class="fs-5 text-primary" title="Actualizar Lista de Guerra"><i class="far fa-edit px-1"></i></a>
                        <a href="javascript:downloadPDF('download<?=$list['id']?>')" title="Descargar Lista de Guerra"><i class="fas fa-download fs-5 px-1"></i></a>
                    </td>
                    <td class="text-center"><span class="badge bg-primary"><?=$list['members']?></span></td>
                    <td><?=$list['description']?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function downloadPDF(elementName){
        document.getElementById(elementName).contentWindow.print();
    }
</script>