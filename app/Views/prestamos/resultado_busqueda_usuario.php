<!-- app/Views/resultados_busqueda_usuario.php -->
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-search"></i> Resultados para "<?= esc($query) ?>"</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($socios) && empty($externos)): ?>
                        <div class="alert alert-info">
                            No se encontraron usuarios con ese criterio de búsqueda.
                        </div>
                    <?php else: ?>
                        <?php if (!empty($socios)): ?>
                            <h6 class="mb-3"><i class="bi bi-people"></i> Socios</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>DNI</th>
                                            <th>Email</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($socios as $socio): ?>
                                            <tr>
                                                <td><?= esc($socio['nombre'] . ' ' . $socio['apellidos']) ?></td>
                                                <td><?= esc($socio['dni']) ?></td>
                                                <td><?= esc($socio['email']) ?></td>
                                                <td>
                                                    <a href="<?= base_url("historial/usuario/{$socio['id']}") ?>" class="btn btn-sm btn-info">
                                                        <i class="bi bi-clock-history"></i> Ver historial
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($externos)): ?>
                            <h6 class="mb-3 mt-4"><i class="bi bi-person"></i> Usuarios externos</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>DNI</th>
                                            <th>Email</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($externos as $externo): ?>
                                            <tr>
                                                <td><?= esc($externo['nombre_externo']) ?></td>
                                                <td><?= esc($externo['dni_externo']) ?></td>
                                                <td><?= esc($externo['email_externo']) ?></td>
                                                <td>
                                                    <a href="<?= base_url("historial/usuario/{$externo['dni_externo']}") ?>" class="btn btn-sm btn-info">
                                                        <i class="bi bi-clock-history"></i> Ver historial
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>