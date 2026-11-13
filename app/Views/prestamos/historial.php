<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-clock-history"></i> Historial de Préstamos</h2>
        <a href="<?= base_url('prestamos' . (request()->getGet('biblioteca') ? '?biblioteca=' . request()->getGet('biblioteca') : '')) ?>" class="btn btn-primary">
            <i class="bi bi-box-arrow-right"></i> Volver a Activos
        </a>
    </div>

    <!-- Selector de biblioteca para superadmin -->
    <?php if ($esSuperadmin && !empty($bibliotecas)): ?>
    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-body">
            <form action="<?= base_url('prestamos/historial') ?>" method="get" class="d-flex align-items-center gap-3 flex-wrap">
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
                    <a href="<?= base_url('prestamos/historial') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg me-1"></i> Ver todas
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <?php endif; ?>
    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-primary mb-1"><?= count($libros) ?></h3>
                    <small class="text-muted">Libros prestados</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-success mb-1">
                        <?= array_sum(array_column($libros, 'total_prestamos')) ?>
                    </h3>
                    <small class="text-muted">Total préstamos</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-info mb-1">
                        <?= count($libros) > 0 ? round(array_sum(array_column($libros, 'total_prestamos')) / count($libros), 1) : 0 ?>
                    </h3>
                    <small class="text-muted">Promedio por libro</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-books text-primary me-2"></i>Libros con historial</h5>
            <button class="btn btn-outline-secondary btn-sm" id="btnExportar">
                <i class="bi bi-download"></i> Exportar
            </button>
        </div>
        <div class="card-body p-0">
            <?php if (empty($libros)): ?>
                <!-- Mensaje cuando no hay datos (FUERA de la tabla) -->
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                    <p class="mb-0">No hay historial de préstamos</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table id="tablaHistorialLibros" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Libro</th>
                                <th>Autor</th>
                                <th class="text-center">Total Préstamos</th>
                                <th>Último Préstamo</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($libros as $libro): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($libro->portada)): ?>
                                            <img src="<?= getPortadaUrl($libro->portada) ?>" 
                                                 class="img-thumbnail me-3" style="width: 50px; height: 75px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 50px; height: 75px;">
                                                <i class="bi bi-book text-muted fs-4"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold"><?= esc($libro->titulo_libro) ?></div>
                                            <small class="text-muted">ISBN: <?= esc($libro->isbn ?? 'N/A') ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= esc($libro->autor ?? 'Desconocido') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-primary fs-6"><?= $libro->total_prestamos ?></span>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($libro->ultimo_prestamo)) ?>
                                    <br><small class="text-muted">
                                        Primero: <?= date('d/m/Y', strtotime($libro->primer_prestamo)) ?>
                                    </small>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-info btn-sm btn-ver-detalles" data-libro-id="<?= $libro->libro_id ?>">
                                        <i class="bi bi-eye" title="Ver Préstamos"></i> 
                                    </button>
                                    <a href="<?= base_url('prestamos/estadisticas/' . $libro->libro_id) ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-graph-up" title="Estadísticas"></i> 
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de detalles de préstamos del libro -->
<div class="modal fade" id="modalDetallesLibro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-clock-history me-2"></i>
                    Préstamos del Libro
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="infoLibroModal" class="mb-3 p-3 bg-light rounded">
                    <!-- Se carga por JS -->
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover" id="tablaDetallesPrestamos">
                        <thead class="table-light">
                            <tr>
                                <th>Prestatario</th>
                                <th>Tipo</th>
                                <th>Salida</th>
                                <th>Entrada</th>
                                <th>Días</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDetallesPrestamos">
                            <!-- Se carga por JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>