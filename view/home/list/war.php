<?php 
    namespace core;
    use function core\{isAdmin};
?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Lista de Guerra</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Listas de Guerras Creadas <span class="badge bg-success"><?=count($listwar)?></span></li>
    </ol>
    {!!MESSAGE!!}
    <div class="modal fade" id="generate" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">
                        Generar Lista de Guerra
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="players">Cantidad de Jugadores:</label>
                        <div class="input-group">
                            <input type="number" id="players" placeholder="ingresa la cantidad de jugadores" class="form-control">
                            <button class="btn btn-primary" onclick="generate()">Generar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="datatablesSimple">
        <?php if(isAdmin()):?>
            <caption>
                <div class="btn-group">
                    <a href="<?= Route::get('list.war.new') ?>" class="btn btn-outline-primary">Crear</a>
                    <a href="#generate" data-bs-toggle="modal" role="button" class="btn btn-outline-success">Generar</a>
                </div>
            </caption>
        <?php endif;?>
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
                        <span class="badge bg-danger" title="ver lista de guerra">#<?=$key+1?>.</span> <a href="<?= Route::get('list.war.show') ?>/<?=$list['id']?>" class="badge bg-primary" target="_blank"><?= $list['date'] ?></a>
                        <?php if(isAdmin()):?>
                            <a href="<?=Route::get('list.war.destroy')?>/<?=$list['id']?>" class="fs-5 text-danger" title="Borrar Lista de Guerra"><i class="far fa-trash-alt px-1"></i></a>
                            <a href="<?=Route::get('list.war.update')?>/<?=$list['id']?>" class="fs-5 text-primary" title="Actualizar Lista de Guerra"><i class="far fa-edit px-1"></i></a>
                        <?php endif;?>
                        <a href="#" onclick="downloadPDF(this)" data-id='<?=$list['id']?>' title="Descargar Lista de Guerra"><i class="fas fa-download fs-5 px-1"></i></a>
                    </td>
                    <td class="text-center"><span class="badge bg-primary"><?=$list['members']?></span></td>
                    <td><?=$list['description']?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function downloadPDF(e){
        if(!(element = document.getElementById(`document_${e.dataset.id}`))){
            loader.hidden = false;
            loader.innerText = 'Generando PDF';
            var element = document.createElement('a');
            element.id = `document_${e.dataset.id}`;
            element.hidden = true;
            var url = `<?=Route::get('list.war.download')?>/${e.dataset.id}`;
            $.get(url, () => {
                element.setAttribute('href', url);
                element.setAttribute('download', 'Lista de Guerra.pdf');
                document.body.appendChild(element);
                loader.hidden = true;
                element.click();
                element.remove();
            });
        }else{
            element.click();
        }
    }

    function generate(){
        if(players.value <= 50){
            location.href = `{!!URL_GENERATE_LIST!!}/${players.value}`;
        }else{
            players.value = '50';
        }
    }
</script>
