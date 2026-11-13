<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-people"></i>
                        <?= esc($titulo ?? 'Listado de usuarios') ?>
                    </h5>

                    <a href="<?= base_url('usuarios/nuevo') ?>"
                        class="btn btn-success btn-sm">
                        <i class="bi bi-plus-circle"></i>
                        Nuevo usuario
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">

                        <table class="table table-striped table-hover align-middle datatable tab"
                            data-tipo="usuarios">
                            <thead class="table-light">
                                <tr>
                                    <th>Login</th>
                                    <th>Nombre</th>
                                    <th>Apellidos</th>
                                    <th>Teléfono</th>
                                    <th>Nivel</th>
                                    <th>Email</th>
                                    <th>Fecha alta</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($usuarios)): ?>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td><?= esc($usuario->login) ?></td>
                                            <td><?= esc($usuario->nombre) ?></td>
                                            <td><?= esc($usuario->apellidos) ?></td>
                                            <td><?= esc($usuario->telefono) ?></td>
                                            <td align="center">
                                                <?php
                                                switch ($usuario->nivel) {
                                                    case 'adm':
                                                        echo '<i class="bi bi-shield-shaded text-danger" title="Administrador"></i>';
                                                        break;
                                                    case 'usu':
                                                        echo '<i class="bi bi-person-circle text-info" title="Usuario"></i>';
                                                        break;
                                                    case 'inv':
                                                        echo '<i class="bi bi-person-lines-fill text-secondary" title="Invitado"></i>';
                                                        break;
                                                    default:
                                                        echo '<i class="bi bi-person text-secondary" title="Usuario"></i>';
                                                }
                                                ?>
                                            </td>
                                            <td><?= esc($usuario->email) ?></td>
                                            <td><?= esc($usuario->fecha_alta) ?></td>

                                            <td class="text-end">
                                                <a href="<?= base_url('usuarios/editar/' . $usuario->id) ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger btn-borrar-usuario"
                                                    data-id="<?= $usuario->id ?>"
                                                    data-nombre="<?= esc($usuario->nombre . ' ' . $usuario->apellidos) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <a href="<?= base_url("historial/usuario/{$usuario->id}") ?>" class="btn btn-sm btn-info">
                                                    <i class="bi bi-clock-history"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            No hay usuarios dados de alta.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>