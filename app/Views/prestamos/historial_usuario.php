<!-- app/Views/historial_usuario.php -->
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history"></i>
                        Historial de préstamos de <?= esc($usuarioPrestamo->nombre) ?>
                        <span class="badge bg-<?= $esSocio ? 'info' : 'secondary' ?> ms-2">
                            <?= $usuarioPrestamo->tipo === 'socio' ? 'Socio' : 'Externo' ?>
                        </span>
                    </h5>
                    <a href="<?= base_url('prestamos') ?>" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Volver a préstamos
                    </a>
                </div>
                <div class="card-body">
                    <!-- Información del usuario -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-person"></i> Información del usuario</h6>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <strong>Nombre:</strong> <?= esc($usuarioPrestamo->nombre) ?>
                                        </li>
                                        <?php if ($esSocio): ?>
                                            <li class="list-group-item">
                                                <strong>ID de socio:</strong> <?= esc($usuarioPrestamo->id) ?>
                                            </li>
                                        <?php else: ?>
                                            <li class="list-group-item">
                                                <strong>DNI/NIE:</strong> <?= esc($usuarioPrestamo->dni ?? 'No especificado') ?>
                                            </li>
                                            <li class="list-group-item">
                                                <strong>Email:</strong> <?= esc($usuarioPrestamo->email ?? 'No especificado') ?>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de préstamos -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaHistorialUsuario">
                            <thead class="table-light">
                                <tr>
                                    <th>Libro</th>
                                    <th>Fecha préstamo</th>
                                    <th>Fecha devolución</th>
                                    <th>Estado</th>
                                    <th>Días</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial as $prestamo): ?>
                                    <tr class="<?= !$prestamo['fecha_entrada'] ? 'table-warning' : '' ?>">
                                        <td><?= esc($prestamo['titulo_libro'] ?? 'Libro desconocido') ?></td>
                                        <td><?= date('d/m/Y', strtotime($prestamo['fecha_prestamo'])) ?></td>
                                        <td>
                                            <?= $prestamo['fecha_entrada']
                                                ? date('d/m/Y', strtotime($prestamo['fecha_entrada']))
                                                : ($prestamo['fecha_devolucion']
                                                    ? 'Previsto: ' . date('d/m/Y', strtotime($prestamo['fecha_devolucion']))
                                                    : 'Sin fecha') ?>
                                        </td>
                                        <td>
                                            <span class="badge <?= $prestamo['fecha_entrada'] ? 'bg-success' : 'bg-warning text-dark' ?>">
                                                <i class="bi bi-<?= $prestamo['fecha_entrada'] ? 'check-circle' : 'hourglass-split' ?>"></i>
                                                <?= $prestamo['fecha_entrada'] ? 'Devuelto' : 'En préstamo' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= $prestamo['dias_prestamo'] ?? '-' ?>
                                        </td>
                                        <td><?= esc($prestamo['notas'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables -->
<script>
$(document).ready(function() {
    $('#tablaHistorialUsuario').DataTable({
        language: {
            url: 'https//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        responsive: true,
        order: [[1, 'desc']] // Ordenar por fecha de préstamo (más reciente primero)
    });
});
</script>