<div class="container-fluid py-4">
    
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-info text-white" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="book-cover me-4">
                        <?php if (!empty($libro->portada)): ?>
                            <img src="<?= base_url('uploads/portadas/' . $libro->portada) ?>" 
                                 class="img-fluid rounded shadow" style="max-height: 150px;">
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
                                <li class="breadcrumb-item"><a href="<?= base_url('prestamos/historial') ?>" class="text-white-50">Historial</a></li>
                                <li class="breadcrumb-item active text-white">Estadísticas</li>
                            </ol>
                        </nav>
                        <h2 class="mb-1"><?= esc($libro->titulo) ?></h2>
                        <p class="mb-2 opacity-75">
                            <i class="bi bi-person"></i> <?= esc($libro->autor ?? 'Autor desconocido') ?> | 
                            <i class="bi bi-upc"></i> ISBN: <?= esc($libro->isbn ?? 'N/A') ?>
                        </p>
                    </div>
                    <div class="text-end">
                        <a href="<?= base_url('prestamos/historialLibro/' . $libro->id) ?>" class="btn btn-light btn-sm me-2">
                            <i class="bi bi-clock-history"></i> Ver Historial
                        </a>
                        <a href="<?= base_url('prestamos/historial') ?>" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs principales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">Total Préstamos</div>
                            <h3 class="mb-0 text-primary"><?= $stats['total_prestamos'] ?></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded p-2">
                            <i class="bi bi-collection text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">Devueltos</div>
                            <h3 class="mb-0 text-success"><?= $stats['devueltos'] ?></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded p-2">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">Activos</div>
                            <h3 class="mb-0 text-warning"><?= $stats['activos'] ?></h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded p-2">
                            <i class="bi bi-clock text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">Duración Promedio</div>
                            <h3 class="mb-0 text-info"><?= $stats['dias_promedio'] ?> días</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded p-2">
                            <i class="bi bi-calendar-event text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <!-- Gráfico de préstamos por mes -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up text-primary me-2"></i>Préstamos por Mes</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartPorMes" height="120"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Distribución por tipo -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-pie-chart text-primary me-2"></i>Por Tipo</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="chartPorTipo" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de duración -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-stopwatch text-primary me-2"></i>Estadísticas de Duración</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 border-end">
                            <div class="text-muted small">Duración Mínima</div>
                            <h4 class="text-success"><?= $stats['min_dias'] ?? 'N/A' ?> días</h4>
                        </div>
                        <div class="col-md-3 border-end">
                            <div class="text-muted small">Duración Máxima</div>
                            <h4 class="text-danger"><?= $stats['max_dias'] ?> días</h4>
                        </div>
                        <div class="col-md-3 border-end">
                            <div class="text-muted small">Duración Promedio</div>
                            <h4 class="text-info"><?= $stats['dias_promedio'] ?> días</h4>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Días Totales Prestado</div>
                            <h4 class="text-primary"><?= $stats['dias_totales'] ?> días</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top prestatarios recurrentes -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Prestatarios Recurrentes</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Prestatario</th>
                                    <th class="text-center">Veces</th>
                                    <th class="pe-4">Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['prestatarios_recurrentes'] as $p): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white me-2" 
                                                 style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: bold;">
                                                <?= strtoupper(substr($p['nombre'], 0, 1)) ?>
                                            </div>
                                            <?= esc($p['nombre']) ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary"><?= $p['veces'] ?></span>
                                    </td>
                                    <td class="pe-4">
                                        <small class="text-muted"><?= esc($p['email'] ?? 'N/A') ?></small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($stats['prestatarios_recurrentes'])): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">No hay datos suficientes</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Listado de todos los préstamos -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-ul text-primary me-2"></i>Últimos Préstamos</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="verTodosLosPrestamos()">
                        Ver todos
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                        <?php foreach (array_slice($prestamos, 0, 5) as $p): 
                            $estadoClass = empty($p->fecha_entrada) ? 'warning' : 'success';
                            $estadoIcon = empty($p->fecha_entrada) ? 'clock' : 'check-circle';
                            $nombre = $p->tipo_prestatario === 'socio' 
                                ? $p->nombre_prestatario . ' ' . ($p->apellidos_prestatario ?? '')
                                : $p->nombre_prestatario;
                        ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold"><?= esc(trim($nombre)) ?></div>
                                <small class="text-muted">
                                    <?= date('d/m/Y', strtotime($p->fecha_prestamo)) ?> - 
                                    <span class="badge bg-<?= $p->tipo_prestatario === 'socio' ? 'primary' : 'secondary' ?> bg-opacity-10 text-<?= $p->tipo_prestatario === 'socio' ? 'primary' : 'secondary' ?>">
                                        <?= ucfirst($p->tipo_prestatario) ?>
                                    </span>
                                </small>
                            </div>
                            <span class="badge bg-<?= $estadoClass ?> bg-opacity-10 text-<?= $estadoClass ?>">
                                <i class="bi bi-<?= $estadoIcon ?>"></i>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal de todos los préstamos -->
<div class="modal fade" id="modalTodosPrestamos" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Todos los Préstamos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Prestatario</th>
                                <th>Tipo</th>
                                <th>Salida</th>
                                <th>Entrada</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prestamos as $p): 
                                $nombre = $p->tipo_prestatario === 'socio' 
                                    ? $p->nombre_prestatario . ' ' . ($p->apellidos_prestatario ?? '')
                                    : $p->nombre_prestatario;
                                $estado = empty($p->fecha_entrada) ? 'Activo' : 'Devuelto';
                                $estadoClass = empty($p->fecha_entrada) ? 'warning' : 'success';
                            ?>
                            <tr>
                                <td><?= esc(trim($nombre)) ?></td>
                                <td>
                                    <span class="badge bg-<?= $p->tipo_prestatario === 'socio' ? 'primary' : 'secondary' ?>">
                                        <?= ucfirst($p->tipo_prestatario) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($p->fecha_prestamo)) ?></td>
                                <td><?= !empty($p->fecha_entrada) ? date('d/m/Y', strtotime($p->fecha_entrada)) : '-' ?></td>
                                <td>
                                    <span class="badge bg-<?= $estadoClass ?>"><?= $estado ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="datosPorMes"><?= json_encode($stats['por_mes']) ?></script>
<script type="application/json" id="datosPorTipo"><?= json_encode($stats['por_tipo']) ?></script>
