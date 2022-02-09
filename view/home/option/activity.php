<div class="container-fluid px-4">
    <h1 class="mt-4">Actividades</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Registro de Actividades</li>
    </ol>
    <table class="table table-striped" id="myTable">
        <thead>
            <tr>
                <td>ID</td>
                <td>T&iacute;tulo</td>
                <td>Descripci&oacute;n</td>
                <td>Fecha</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach($activity as $value):?>
                <tr>
                    <td><?=$value->id?></td>
                    <td><?=$value->title?></td>
                    <td><?=$value->description?></td>
                    <td><?=$value->date?></td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>