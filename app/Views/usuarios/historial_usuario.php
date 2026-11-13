<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            
            <!-- Cabecera -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>
                    <i class="bi bi-clock-history"></i> 
                    <?= esc($titulo) ?>
                </h4>
                <a href="<?= base_url('usuarios/buscar') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a búsqueda
                </a>
            </div>
            
            <!-- Info del usuario/externo -->
            <div class="alert alert-info mb-4">
                <i class="bi bi-person me-2"></i>
                <?php if ($esExterno): ?>
                    <strong>Externo:</strong> <?= esc($nombre) ?>
                    <?php if (!empty($dni)): ?>
                        | DNI: <?= esc($dni) ?>
                    <?php endif; ?>
                <?php else: ?>
                    <strong>Socio:</strong> <?= esc($usuario['nombre'] . ' ' . ($usuario['apellidos'] ?? '')) ?>
                    | DNI: <?= esc($usuario['dni'] ?? 'N/A') ?>
                <?php endif; ?>
            </div>
            
            <!-- Tabla de préstamos -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-book"></i> Préstamos</h6>
                    <span class="badge bg-light text-primary"><?= count($prestamos) ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($prestamos)): ?>
                        <div class="alert alert-light mb-0">
                            <i class="bi bi-inbox"></i> No hay préstamos registrados.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Libro</th>
                                        <th>Fecha préstamo</th>
                                        <th>Fecha devolución</th>
                                        <th>Estado</th>
                                        <th>Notas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($prestamos as $p): ?>
                                        <tr>
                                            <td><?= esc($p['titulo_libro'] ?? 'Desconocido') ?></td>
                                            <td><?= !empty($p['fecha_prestamo']) ? date('d/m/Y', strtotime($p['fecha_prestamo'])) : '-' ?></td>
                                            <td><?= !empty($p['fecha_devolucion']) ? date('d/m/Y', strtotime($p['fecha_devolucion'])) : '-' ?></td>
                                            <td>
                                                <?php if (!empty($p['estado']) && $p['estado'] === 'devuelto'): ?>
                                                    <span class="badge bg-success">Devuelto</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Activo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($p['notas'] ?? '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
</div>