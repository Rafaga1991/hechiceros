<div class="container-fluid px-4">
    <h1 class="mt-4">Usuarios</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Lista de Usuarios</li>
    </ol>
    {!!MESSAGE!!}
    <table class="table">
        <thead>
            <tr>
                <th><i class="fas fa-user"></i> Usuario</th>
                <th><i class="fa-solid fa-at"></i> Correo</th>
                <th><i class="fa-solid fa-hammer"></i> Administrador</th>
                <th><i class="fas fa-ban"></i> Bloquear</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?=$user->username?></td>
                    <td><a href="#"><?=$user->email?></a></td>
                    <td class="text-center"><input type="checkbox" id="admin" <?=$user->admin?'checked':''?>></td>
                    <td class="text-center"><input type="checkbox" id="ban" <?=$user->delete?'checked':''?>></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>