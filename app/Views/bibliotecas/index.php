<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-building me-2"></i>Gestión de Bibliotecas</h2>
            <p class="text-muted mb-0">Administra las bibliotecas del sistema</p>
        </div>
        <a href="<?= base_url('bibliotecas/nuevo') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nueva Biblioteca
        </a>
    </div>

    <!-- Alertas -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Tabla de Bibliotecas -->
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Contacto</th>
                            <th class="text-center">Color</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bibliotecas)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                    No hay bibliotecas registradas
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bibliotecas as $biblioteca): ?>
                                <tr>
                                    <td class="text-center fw-bold">#<?= $biblioteca['id'] ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= esc($biblioteca['nombre']) ?></div>
                                        <?php if (session()->get('id_biblioteca') == $biblioteca['id']): ?>
                                            <span class="badge bg-success rounded-pill">
                                                <i class="bi bi-check-circle me-1"></i> Activa
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($biblioteca['direccion']) ?: '<span class="text-muted">-</span>' ?></td>
                                    <td>
                                        <?php if ($biblioteca['email']): ?>
                                            <div><i class="bi bi-envelope me-1 text-muted"></i><?= esc($biblioteca['email']) ?></div>
                                        <?php endif; ?>
                                        <?php if ($biblioteca['telefono']): ?>
                                            <div><i class="bi bi-telephone me-1 text-muted"></i><?= esc($biblioteca['telefono']) ?></div>
                                        <?php endif; ?>
                                        <?php if (!$biblioteca['email'] && !$biblioteca['telefono']): ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill px-3 py-2" style="background:<?= esc($biblioteca['color_tema']) ?>">
                                            <?= esc($biblioteca['color_tema']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($biblioteca['activa']): ?>
                                            <span class="badge bg-success rounded-pill">Activa</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill">Inactiva</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="<?= base_url('bibliotecas/seleccionar/' . $biblioteca['id']) ?>" 
                                               class="btn btn-sm btn-outline-success" 
                                               title="Seleccionar como activa"
                                               onclick="return confirm('¿Cambiar a la biblioteca <?= esc($biblioteca['nombre']) ?>?')">
                                                <i class="bi bi-check-lg"></i>
                                            </a>
                                            <a href="<?= base_url('bibliotecas/editar/' . $biblioteca['id']) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= base_url('bibliotecas/eliminar/' . $biblioteca['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               title="Eliminar"
                                               onclick="return confirm('¿Eliminar permanentemente la biblioteca <?= esc($biblioteca['nombre']) ?>?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>