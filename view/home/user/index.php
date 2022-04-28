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
                <th><i class="fa-solid fa-hammer"></i> Administrador</th>
                <th><i class="fas fa-ban"></i> Bloquear</th>
                <th><i class="fas fa-key"></i> Contrase&ntilde;a</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?= $user->username ?></td>
                    <td><a href="mailto:<?= $user->email ?>"><?= $user->email ?></a></td>
                    <td class="text-center"><input type="checkbox" action="adm:<?= $user->id ?>" <?= $user->admin ? 'checked' : '' ?> <?= $user->admin ? 'disabled' : '' ?>></td>
                    <td class="text-center"><input type="checkbox" action="ban:<?= $user->id ?>" <?= $user->delete ? 'checked' : '' ?> <?= $user->admin ? 'disabled' : '' ?>></td>
                    <td><input type="button" class="btn btn-outline-danger" value="Resetear" action="reset:<?= $user->id ?>"></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    window.onload = () => {
        document.querySelectorAll('tr>td>input[action]').forEach((input) => {
            input.onclick = () => {
                let action = input.getAttribute('action').split(':');
                let data = {
                    __token: '{!!TOKEN!!}'
                };
                data[action[0]] = (input.checked) ? 1 : 0;
                data['user_id'] = action[1];

                $.post(
                    '{!!URL_UPDATE!!}',
                    data,
                    (request) => {
                        try {
                            request = JSON.parse(request);
                            if (request.type === 'error') input.checked = !input.checked;

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
        })
    }
</script>