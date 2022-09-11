<?php

namespace core; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Usuarios</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Lista de Usuarios</li>
    </ol>
    {!!MESSAGE!!}
    <div for="" class="alert alert-success">Clave por defecto: <b>{!!PASSWORD!!}</b></div>
    <table class="table">
        <thead>
            <tr>
                <th><i class="fas fa-user"></i> Usuario</th>
                <th><i class="fa-solid fa-at"></i> Correo</th>
                <th><i class="fa-solid fa-hammer"></i> Roles</th>
                <th><i class="fas fa-ban"></i> Bloquear</th>
                <th><i class="fas fa-key"></i> Contrase&ntilde;a</th>
                <th><i class="fa-solid fa-right-from-bracket"></i> Sessi&oacute;n</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?= $user->username ?></td>
                    <td><a href="mailto:<?= $user->email ?>"><?= $user->email ?></a></td>

                    <td class="text-center">
                        <?php if ($user->rol == Route::ROL_ADMIN) : ?>
                            <input type="text" class="form-control" value="<?= Route::ROL[$user->rol] ?>" disabled>
                        <?php else : ?>
                            <select data-action="group" class="form-control" data-user="<?= $user->id ?>" <?= ($user->rol == Route::ROL_ADMIN) ? 'disabled' : '' ?>>
                                <?php foreach (Route::ROL as $key => $rol) : ?>
                                    <option value="<?= $key ?>" <?= ($user->rol == $key) ? 'selected' : '' ?>><?= $rol ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>

                    </td>
                    <td class="text-center">
                        <?php if ($user->rol == Route::ROL_ADMIN) : ?>
                            <input type="checkbox" disabled>
                        <?php else : ?>
                            <input type="checkbox" data-action="ban" data-user="<?= $user->id ?>" <?= $user->delete ? 'checked' : '' ?> <?= ($user->rol == Route::ROL_ADMIN) ? 'disabled' : '' ?>>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($user->rol == Route::ROL_ADMIN) : ?>
                            <input type="button" class="btn btn-outline-dark" value="Resetear" disabled>
                        <?php else : ?>
                            <input type="button" data-action="reset" class="btn btn-outline-danger" value="Resetear" data-user="<?= $user->id ?>">
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($user->rol == Route::ROL_ADMIN) : ?>
                            <input type="button" class="btn btn-outline-dark" value="Cerrar Sessión" disabled>
                        <?php else : ?>
                            <input type="button" data-action="close" data-value="1" class="btn btn-outline-danger" value="Cerrar Sessión" data-user="<?= $user->id ?>">
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    window.onload = () => {
        var url = '{!!URL_UPDATE!!}';
        var token = '{!!TOKEN!!}';

        function sedData(data) {
            data['__token'] = token;
            $.post(url, data,
                (request) => {
                    try {
                        request = JSON.parse(request);
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        })

                        Toast.fire({
                            icon: request.type,
                            title: request.message
                        })
                    } catch (e) {
                        console.log(e)
                    }
                }
            );
        }

        document.querySelectorAll('[data-action]').forEach((element) => {
            switch (element.dataset.action) {
                case 'group':
                    element.onchange = (e) => {
                        sedData({
                            group: e.target.value,
                            user_id: e.target.dataset.user
                        });
                    }
                    break;
                case 'ban':
                case 'close':
                case 'reset':
                    element.onclick = (e) => {
                        var data = {};
                        if (element.dataset.action == 'ban') {
                            data[e.target.dataset.action] = e.target.checked ? 1 : 0;
                        } else {
                            data[e.target.dataset.action] = e.target.dataset.value ?? e.target.value;
                        }
                        data['user_id'] = e.target.dataset.user;
                        sedData(data);
                    }
                    break;
            }
        })
    }
</script>
