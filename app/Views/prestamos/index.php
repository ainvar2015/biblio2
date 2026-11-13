<div class="container-fluid py-4">
    <!-- Header con estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Préstamos Activos</h6>
                            <h2 class="mb-0"><?= $estadisticas['activos'] ?></h2>
                        </div>
                        <i class="bi bi-box-arrow-right fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">En Retraso</h6>
                            <h2 class="mb-0"><?= $estadisticas['retraso'] ?></h2>
                        </div>
                        <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Devueltos (Mes)</h6>
                            <h2 class="mb-0"><?= $estadisticas['devueltos_mes'] ?></h2>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Distribución</h6>
                    <?php foreach ($estadisticas['por_tipo'] as $tipo): ?>
                        <div class="d-flex justify-content-between small">
                            <span><?= ucfirst($tipo->tipo_prestatario) ?>s:</span>
                            <span><?= $tipo->total ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Selector de biblioteca para superadmin -->
    <?php if ($esSuperadmin && !empty($bibliotecas)): ?>
    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-body">
            <form action="<?= base_url('prestamos') ?>" method="get" class="d-flex align-items-center gap-3 flex-wrap">
                <label class="fw-bold mb-0 text-primary"><i class="bi bi-building me-1"></i> Filtrar por biblioteca:</label>
                <select name="biblioteca" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="">Todas las bibliotecas</option>
                    <?php foreach ($bibliotecas as $bib): ?>
                        <option value="<?= $bib['id'] ?>" <?= (request()->getGet('biblioteca') == $bib['id']) ? 'selected' : '' ?>>
                            <?= esc($bib['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (request()->getGet('biblioteca')): ?>
                    <a href="<?= base_url('prestamos') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg me-1"></i> Ver todas
                    </a>
                <?php endif; ?>
                <?php if (!empty($filtros['buscar'])): ?>
                    <input type="hidden" name="buscar" value="<?= esc($filtros['buscar']) ?>">
                <?php endif; ?>
                <?php if (!empty($filtros['tipo'])): ?>
                    <input type="hidden" name="tipo" value="<?= esc($filtros['tipo']) ?>">
                <?php endif; ?>
                <?php if (!empty($filtros['retraso'])): ?>
                    <input type="hidden" name="retraso" value="1">
                <?php endif; ?>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filtros y tabla -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-box-arrow-right"></i> Préstamos Activos</h5>
            <div>
                <a href="<?= base_url('prestamos/historial' . (request()->getGet('biblioteca') ? '?biblioteca=' . request()->getGet('biblioteca') : '')) ?>" class="btn btn-outline-light btn-sm me-2">
                    <i class="bi bi-clock-history"></i> Ver Historial
                </a>
                <a href="<?= base_url('prestamos/nuevo') ?>" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg"></i> Nuevo Préstamo
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Filtros -->
            <form action="<?= base_url('prestamos') ?>" method="get" class="row g-3 mb-3">
                <?php if (request()->getGet('biblioteca')): ?>
                    <input type="hidden" name="biblioteca" value="<?= request()->getGet('biblioteca') ?>">
                <?php endif; ?>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="buscar" placeholder="Buscar por título, ISBN o persona..." value="<?= esc($filtros['buscar'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="tipo">
                        <option value="">Todos los tipos</option>
                        <option value="socio" <?= ($filtros['tipo'] ?? '') === 'socio' ? 'selected' : '' ?>>Socios</option>
                        <option value="externo" <?= ($filtros['tipo'] ?? '') === 'externo' ? 'selected' : '' ?>>Externos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check form-switch pt-2">
                        <input class="form-check-input" type="checkbox" name="retraso" value="1" id="filtroRetraso" <?= ($filtros['retraso'] ?? '') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="filtroRetraso">Solo retrasos</label>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                    <a href="<?= base_url('prestamos' . (request()->getGet('biblioteca') ? '?biblioteca=' . request()->getGet('biblioteca') : '')) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                </div>
            </form>

            <!-- Tabla -->
            <div class="table-responsive">
                <table id="tablaPrestamosActivos" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <?php if ($esSuperadmin): ?>
                                <th>Biblioteca</th>
                            <?php endif; ?>
                            <th>Libro</th>
                            <th>Prestatario</th>
                            <th>Contacto</th>
                            <th>Fecha Salida</th>
                            <th>Fecha Devolución</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prestamos as $p):
                            $hoy = new DateTime();
                            $fechaDev = new DateTime($p->fecha_devolucion);
                            $retraso = $hoy > $fechaDev;
                            $diasRetraso = $retraso ? $hoy->diff($fechaDev)->days : 0;
                            $urgente = $retraso || $hoy->diff($fechaDev)->days <= 2;
                        ?>
                            <tr class="<?= $retraso ? 'table-danger' : ($urgente ? 'table-warning' : '') ?>" data-id="<?= $p->id ?>">
                                <?php if ($esSuperadmin): ?>
                                    <td>
                                        <span class="badge rounded-pill" style="background:<?= esc($p->color_biblioteca ?? '#6c757d') ?>">
                                            <?= esc($p->biblioteca_nombre ?? 'N/A') ?>
                                        </span>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= $p->portada ? (strpos($p->portada, 'http') === 0 ? $p->portada : base_url('uploads/portadas/' . $p->portada)) : base_url('assets/img/no-portada.gif') ?>"
                                            class="img-thumbnail me-2" style="width: 40px; height: 60px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold"><?= esc($p->titulo_libro) ?></div>
                                            <small class="text-muted"><?= esc($p->autores ?? 'Desconocido') ?></small>
                                            <div><small class="text-muted">ISBN: <?= esc($p->isbn) ?></small></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $p->tipo_prestatario === 'socio' ? 'primary' : 'secondary' ?> me-1">
                                        <?= ucfirst($p->tipo_prestatario) ?>
                                    </span>
                                    <div class="fw-bold"><?= esc($p->nombre_prestatario) ?></div>
                                    <?php if ($p->dni_externo): ?>
                                        <small class="text-muted">DNI: <?= esc($p->dni_externo) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($p->email_prestatario): ?>
                                        <div><i class="bi bi-envelope text-muted"></i> <?= esc($p->email_prestatario) ?></div>
                                    <?php endif; ?>
                                    <?php if ($p->telefono_prestatario): ?>
                                        <div><i class="bi bi-telephone text-muted"></i> <?= esc($p->telefono_prestatario) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($p->fecha_prestamo)) ?></td>
                                <td>
                                    <?= date('d/m/Y', strtotime($p->fecha_devolucion)) ?>
                                    <?php if ($retraso): ?>
                                        <div class="badge bg-danger"><?= $diasRetraso ?> días de retraso</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($retraso): ?>
                                        <span class="badge bg-danger"><i class="bi bi-exclamation-circle"></i> Retrasado</span>
                                    <?php elseif ($hoy->diff($fechaDev)->days <= 2): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Por vencer</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> En plazo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn-danger" href="<?= base_url('prestamos/historialLibro/' . $p->id_libro) ?>"><i class="bi bi-card-list" title="Historial libro"></i></a>
                                        <?php if ($p->tipo_prestatario === 'socio'): ?>
                                            <a href="<?= base_url("historial/usuario/{$p->id_usuario}") ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-clock-history" title="Ver historial de este usuario"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url("historial/usuario/{$p->dni_externo}") ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-clock-history" title="Ver historial de este usuario"></i>
                                            </a>
                                        <?php endif; ?>
                                        <button class="btn btn-success btn-devolver" title="Registrar devolución">
                                            <i class="bi bi-check-lg" title="Devolver"></i>
                                        </button>
                                        <button class="btn btn-info btn-renovar" title="Renovar préstamo" data-dias="7">
                                            <i class="bi bi-arrow-repeat" title="+7 días"></i>
                                        </button>
                                        <button class="btn btn-warning btn-recordatorio" title="Enviar recordatorio">
                                            <i class="bi bi-bell"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top libros más prestados -->
    <?php if (!empty($estadisticas['top_libros'])): ?>
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="bi bi-graph-up"></i> Libros Más Prestados</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($estadisticas['top_libros'] as $i => $libro): ?>
                        <div class="col-md-2 col-4 text-center mb-3">
                            <div class="position-relative d-inline-block">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-<?= $i === 0 ? 'warning' : ($i === 1 ? 'secondary' : ($i === 2 ? 'danger' : 'light text-dark')) ?>">
                                    #<?= $i + 1 ?>
                                </span>
                                <div class="fw-bold text-primary"><?= $libro->total ?> préstamos</div>
                                <small class="text-muted d-block text-truncate" style="max-width: 150px;"><?= esc($libro->titulo) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Confirmar Devolución (CORREGIDO - IDs no coincidían) -->
<div class="modal fade" id="modalDevolucion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle"></i> Confirmar Devolución</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Confirmar devolución del libro <strong id="devolucionLibro"></strong>?</p>
                <div class="mb-3">
                    <label class="form-label">Fecha de devolución real</label>
                    <input type="date" class="form-control" id="devolucionFecha">
                </div>
                <div class="mb-3">
                    <label class="form-label">Notas (opcional)</label>
                    <textarea class="form-control" id="devolucionNotas" rows="2"></textarea>
                </div>
                <input type="hidden" id="devolucionId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnConfirmarDevolucion">
                    <i class="bi bi-check-lg"></i> Confirmar Devolución
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Renovación (FALTABA) -->
<div class="modal fade" id="modalRenovacion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-arrow-repeat"></i> Renovar Préstamo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="renovacionId">
                <p>¿Renovar el préstamo por <strong>+15 días</strong>?</p>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Máximo 2 renovaciones permitidas.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarRenovacion">
                    <i class="bi bi-arrow-repeat"></i> Confirmar Renovación
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal nuevo préstamo -->
<div class="modal fade" id="modalNuevoPrestamo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-plus-lg"></i> Nuevo Préstamo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevoPrestamo">

                    <!-- Libro -->
                    <div class="mb-3">
                        <label class="form-label">Libro *</label>
                        <select class="form-select" id="nuevoLibro" required>
                            <option value="">Seleccione un libro...</option>
                        </select>
                    </div>

                    <!-- Tipo de Prestatario -->
                    <div class="mb-3">
                        <label class="form-label">Tipo de Prestatario *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipoPrestatario" id="tipoSocio" value="socio" checked>
                            <label class="form-check-label" for="tipoSocio">Socio</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipoPrestatario" id="tipoExterno" value="externo">
                            <label class="form-check-label" for="tipoExterno">Externo</label>
                        </div>
                    </div>

                    <!-- Sección Socio -->
                    <div id="seccionSocio" class="mb-3">
                        <label class="form-label">Seleccionar Socio *</label>
                        <select class="form-select" id="nuevoSocio">
                            <option value="">Seleccione un socio...</option>
                        </select>
                    </div>

                    <!-- Sección Externo (oculta por defecto) -->
                    <div id="seccionExterno" class="mb-3" style="display: none;">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="externoNombre" placeholder="Nombre completo">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">DNI/NIE</label>
                                <input type="text" class="form-control" id="externoDni" placeholder="12345678A">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" id="externoEmail" placeholder="email@ejemplo.com">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="externoTelefono" placeholder="600123456">
                            </div>
                        </div>
                    </div>

                    <!-- Fechas -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Préstamo *</label>
                            <input type="date" class="form-control" id="nuevoFechaPrestamo" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Devolución Prevista *</label>
                            <input type="date" class="form-control" id="nuevoFechaDevolucion" required>
                        </div>
                    </div>

                    <!-- Notas -->
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea class="form-control" id="nuevoNotas" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarNuevoPrestamo">
                    <i class="bi bi-check-lg"></i> Guardar Préstamo
                </button>
            </div>
        </div>
    </div>
</div>