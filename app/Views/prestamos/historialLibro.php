<div class="container-fluid py-4" id="vistaHistorialLibro" data-libro-id="<?= $libro->id ?>">
    <!-- Header con info del libro -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="book-cover me-4">
                        <?php if (!empty($libro->portada)): ?>
                            <img src="<?= base_url('uploads/portadas/' . $libro->portada) ?>" 
                                 class="img-fluid rounded shadow" 
                                 style="max-height: 150px; width: auto;">
                        <?php else: ?>
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                 style="width: 100px; height: 150px;">
                                <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-2">
                                <li class="breadcrumb-item"><a href="<?= base_url('libros') ?>" class="text-white-50">Libros</a></li>
                                <li class="breadcrumb-item active text-white" aria-current="page">Historial</li>
                            </ol>
                        </nav>
                        <h2 class="mb-1"><?= esc($libro->titulo) ?></h2>
                        <p class="mb-2 opacity-75">
                            <i class="bi bi-person"></i> <?= esc($libro->autor ?? 'Autor desconocido') ?> | 
                            <i class="bi bi-upc"></i> ISBN: <?= esc($libro->isbn ?? 'N/A') ?>
                        </p>
                        <div class="d-flex gap-3">
                            <span class="badge bg-white text-primary">
                                <i class="bi bi-clock-history"></i> <?= count($prestamos) ?> préstamos
                            </span>
                            <span class="badge bg-white text-success">
                                <i class="bi bi-check-circle"></i> <?= $prestamosActivos ?> activos
                            </span>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="<?= base_url('prestamos') ?>" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i> Volver a préstamos</a>
                        <a href="<?= base_url('libros/editar/' . $libro->id) ?>" class="btn btn-light btn-sm">
                            <i class="bi bi-pencil"></i> Editar libro
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">Total Préstamos</div>
                    <h3 class="mb-0 text-primary"><?= count($prestamos) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">Promedio Duración</div>
                    <h3 class="mb-0 text-info">
                        <?= $promedioDias ? round($promedioDias) . ' días' : 'N/A' ?>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">Primer Préstamo</div>
                    <h3 class="mb-0 text-success">
                        <?= $primerPrestamo ? date('d/m/Y', strtotime($primerPrestamo)) : 'N/A' ?>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">Último Préstamo</div>
                    <h3 class="mb-0 text-warning">
                        <?= $ultimoPrestamo ? date('d/m/Y', strtotime($ultimoPrestamo)) : 'N/A' ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de historial -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list-ul text-primary me-2"></i>Historial de Préstamos
            </h5>
            <div class="btn-group">
                <button class="btn btn-outline-secondary btn-sm" id="btnExportarCSV">
                    <i class="bi bi-download"></i> Exportar
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaHistorial">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Prestatario</th>
                            <th>Fecha Salida</th>
                            <th>Fecha Entrada</th>
                            <th>Duración</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prestamos as $p): 
                            $estadoClass = '';
                            $estadoIcon = '';
                            $estadoText = '';
                            
                            if (!$p->fecha_entrada) {
                                $estadoClass = 'warning';
                                $estadoIcon = 'bi-clock';
                                $estadoText = 'Activo';
                            } elseif ($p->dias_prestamo && $p->dias_prestamo > 30) {
                                $estadoClass = 'danger';
                                $estadoIcon = 'bi-exclamation-triangle';
                                $estadoText = 'Retrasado (' . $p->dias_prestamo . ' días)';
                            } else {
                                $estadoClass = 'success';
                                $estadoIcon = 'bi-check-circle';
                                $estadoText = 'Devuelto' . ($p->dias_prestamo ? ' (' . $p->dias_prestamo . ' días)' : '');
                            }
                        ?>
                        <tr class="border-bottom" data-prestamo-id="<?= $p->id ?>">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-<?= $p->tipo_prestatario === 'socio' ? 'primary' : 'secondary' ?> text-white me-3" 
                                         style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                        <?= strtoupper(substr($p->usuario_nombre ?? $p->nombre_externo ?? '?', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold">
                                            <?= esc($p->usuario_nombre . ' ' . ($p->usuario_apellidos ?? '') ?: ($p->nombre_externo ?? 'Desconocido')) ?>
                                        </div>
                                        <small class="text-muted">
                                            <span class="badge bg-<?= $p->tipo_prestatario === 'socio' ? 'primary' : 'secondary' ?> bg-opacity-10 text-<?= $p->tipo_prestatario === 'socio' ? 'primary' : 'secondary' ?>">
                                                <?= ucfirst($p->tipo_prestatario) ?>
                                            </span>
                                            <?php if ($p->email_externo || $p->usuario_email): ?>
                                                <span class="ms-1"><?= esc($p->email_externo ?? $p->usuario_email) ?></span>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold"><?= date('d/m/Y', strtotime($p->fecha_prestamo)) ?></span>
                                <br><small class="text-muted"><?= date('H:i', strtotime($p->fecha_prestamo)) ?></small>
                            </td>
                            <td>
                                <?php if ($p->fecha_entrada): ?>
                                    <span class="fw-semibold"><?= date('d/m/Y', strtotime($p->fecha_entrada)) ?></span>
                                    <br><small class="text-muted"><?= date('H:i', strtotime($p->fecha_entrada)) ?></small>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p->dias_prestamo): ?>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 6px; width: 60px;">
                                            <div class="progress-bar bg-<?= $p->dias_prestamo > 30 ? 'danger' : ($p->dias_prestamo > 15 ? 'warning' : 'success') ?>" 
                                                 style="width: <?= min(100, ($p->dias_prestamo / 30) * 100) ?>%"></div>
                                        </div>
                                        <span class="small fw-semibold"><?= $p->dias_prestamo ?> días</span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $estadoClass ?> bg-opacity-10 text-<?= $estadoClass ?> border border-<?= $estadoClass ?> border-opacity-25">
                                    <i class="bi <?= $estadoIcon ?> me-1"></i><?= $estadoText ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <?php if (!$p->fecha_entrada): ?>
                                    <button class="btn btn-sm btn-success btn-devolver" data-prestamo-id="<?= $p->id ?>">
                                        <i class="bi bi-check-lg"></i> Devolver
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-info btn-detalles" data-prestamo-id="<?= $p->id ?>" title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($prestamos)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                <p class="mb-0">Este libro no tiene historial de préstamos</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Gráfico de tendencia -->
    <?php if (count($prestamos) > 1): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up text-primary me-2"></i>Tendencia de Préstamos</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartPrestamos" height="100" 
                            data-labels='<?= json_encode(array_map(function($p) { return date('M Y', strtotime($p->fecha_prestamo)); }, array_reverse($prestamos))) ?>'
                            data-dias='<?= json_encode(array_map(function($p) { return $p->dias_prestamo ?: 0; }, array_reverse($prestamos))) ?>'>
                    </canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal de detalles -->
<div class="modal fade" id="modalDetalles" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Préstamo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoDetalles">
                <!-- Se carga por AJAX -->
            </div>
        </div>
    </div>
</div>