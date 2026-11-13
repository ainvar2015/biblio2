<!-- app/Views/dashboard.php -->
<!-- app/Views/dashboard.php -->
<div class="mainbar">
    <div class="container-fluid mt-4">
        <!-- Selector de biblioteca para superadmin -->
        <?php if ($esSuperadmin && !empty($bibliotecas)): ?>
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-body d-flex align-items-center gap-3 flex-wrap">
                            <label class="fw-bold mb-0 text-primary"><i class="bi bi-building me-1"></i> Biblioteca:</label>
                            <form action="<?= base_url('dashboard') ?>" method="get" class="d-flex align-items-center gap-2">
                                <select name="biblioteca" class="form-select w-auto" onchange="this.form.submit()">
                                    <option value="">Todas las bibliotecas</option>
                                    <?php foreach ($bibliotecas as $bib): ?>
                                        <option value="<?= $bib['id'] ?>" <?= ($bibliotecaActiva == $bib['id']) ? 'selected' : '' ?>>
                                            <?= esc($bib['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($bibliotecaActiva): ?>
                                    <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-x-lg"></i> Ver todas
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <!-- Préstamos con retraso -->
        <div class="row g-3">
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
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bi bi-bell"></i> Préstamos con retraso</h6>
                        <span class="badge bg-light text-danger"><?= count($prestamosConRetraso) ?></span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($prestamosConRetraso)): ?>
                            <div class="alert alert-success mb-0">
                                <i class="bi bi-check-circle"></i> No hay préstamos con retraso.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Libro</th>
                                            <th>Usuario</th>
                                            <th>Fecha devolución</th>
                                            <th>Días de retraso</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($prestamosConRetraso as $prestamo): ?>
                                            <tr>
                                                <td><?= esc($prestamo['titulo_libro']) ?></td>
                                                <td>
                                                    <?= esc($prestamo['nombre_usuario'] . ' ' . $prestamo['apellidos_usuario']) ?>
                                                    <small class="d-block text-muted">
                                                        <?= $prestamo['tipo_prestatario'] === 'externo' ? 'Externo' : 'Socio' ?>
                                                    </small>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($prestamo['fecha_devolucion'])) ?></td>
                                                <td>
                                                    <span class="badge bg-danger">
                                                        <?= $prestamo['dias_retraso'] ?> días
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url("historial/usuario/{$prestamo['id_usuario']}") ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-clock-history"></i>
                                                    </a>
                                                    <button
                                                        class="btn btn-sm btn-outline-warning btn-enviar-recordatorio"
                                                        data-id="<?= $prestamo['id'] ?>"
                                                        data-libro="<?= esc($prestamo['titulo_libro']) ?>"
                                                        data-usuario="<?= esc($prestamo['nombre_usuario'] . ' ' . $prestamo['apellidos_usuario']) ?>"
                                                        data-email="<?= esc($prestamo['email_externo'] ?? ($prestamo['email_usuario'] ?? '')) ?>">
                                                        <i class="bi bi-envelope"></i>
                                                    </button>
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
        </div>
        <!-- 5 libros más populares -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-star"></i> Libros populares</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($librosPopulaires)): ?>
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle"></i> No hay datos de libros populares.
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($librosPopulaires as $libro): ?>
                                    <div class="col-md-2 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <img
                                                    src="<?= getPortadaUrl($libro['portada']) ?>"
                                                    alt="<?= esc($libro['titulo']) ?>"
                                                    class="img-fluid mb-2"
                                                    style="max-height: 150px;">
                                                <h6 class="card-title mb-1"><?= esc($libro['titulo']) ?></h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-book"></i> <?= $libro['total_prestamos'] ?> préstamos
                                                </small>
                                            </div>
                                            <div class="card-footer bg-transparent">
                                                <div class="row">
                                                    <div class="col-sm-9 d-flex align-items-center justify-content-center">
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-primary w-100 btn-ver-detalle-libro"
                                                            data-id="<?= $libro['id'] ?>">
                                                            <i class="bi bi-eye"></i> Ver detalles
                                                        </button>
                                                    </div>
                                                    <div class="col-sm-2 d-flex align-items-center justify-content-center me-1">
                                                        <?php if ($libro['prestamo']): ?>
                                                            <span class="badge bg-danger">
                                                                <i class="bi bi-x-circle" title="Prestado"></i>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success">
                                                                <i class="bi bi-check-circle" title="Disponible"></i>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Segunda fila: Gráficos -->
        <div class="row g-3 mt-2">

            <!-- Top 5 libros más prestados -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-book"></i> Top 5 libros más prestados</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="topLibrosChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Libros por género -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-tags"></i> Libros por género</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="librosPorGeneroChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Préstamos por mes -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-calendar"></i> Préstamos por mes (últimos 12 meses)</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="prestamosPorMesChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Préstamos por tipo de usuario -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="bi bi-people"></i> Préstamos por tipo de usuario</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="prestamosPorTipoChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Préstamos activos vs. devueltos -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="bi bi-arrow-left-right"></i> Préstamos activos vs. devueltos</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="prestamosEstadoChart" height="200"></canvas>
                    </div>
                </div>
            </div>

        </div>
        <!-- Primera fila: Widgets básicos -->
        <div class="row g-3">
            <?= renderWidget([
                'title' => 'Libros',
                'icon'  => 'navicon',
                'value' => $totalLibros,
                'text'  => 'Libros registrados',
                'color' => 'blue',
                'actions' => false
            ]) ?>

            <?= renderWidget([
                'title' => 'Autores',
                'icon'  => 'image',
                'value' => $totalAutores,
                'text'  => 'Autores registrados',
                'color' => 'green',
                'actions' => false
            ]) ?>

            <?= renderWidget([
                'title' => 'Editoriales',
                'icon'  => 'globe',
                'value' => $totalEditoriales,
                'text'  => 'Editoriales registradas',
                'color' => 'orange',
                'actions' => false
            ]) ?>

            <?= renderWidget([
                'title' => 'Géneros',
                'icon'  => 'globe',
                'value' => $totalGeneros,
                'text'  => 'Géneros registrados',
                'color' => 'yellow',
                'actions' => false
            ]) ?>
            <?= renderWidget([
                'title' => 'Usuarios',
                'icon'  => 'users',
                'value' => $totalUsuarios,
                'text'  => 'Usuarios registrados',
                'color' => 'red',
                'actions' => false
            ]) ?>

            <?= renderWidget([
                'title' => 'Calendario',
                'icon'  => 'calendar',
                'value' => $totalEventos,
                'text'  => 'Eventos programados',
                'color' => 'dark',
                'actions' => false
            ]) ?>
        </div>
    </div>
    <!-- Modal para detalles del libro -->
    <div class="modal fade" id="modalDetalleLibro" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-book"></i> <span id="modalLibroTitulo">Detalles del libro</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center mia">
                            <img
                                id="modalLibroPortada"
                                src="<?= base_url('assets/img/no-portada.gif') ?>"
                                alt="Portada del libro"
                                class="img-fluid mb-3"
                                style="max-height: 300px;">
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <tr>
                                            <th>Título:</th>
                                            <td id="modalLibroTituloDetalle"></td>
                                        </tr>
                                        <tr>
                                            <th>Autor:</th>
                                            <td id="modalLibroAutor"></td>
                                        </tr>
                                        <tr>
                                            <th>Editorial:</th>
                                            <td id="modalLibroEditorial"></td>
                                        </tr>
                                        <tr>
                                            <th>Género:</th>
                                            <td id="modalLibroGenero"></td>
                                        </tr>
                                        <tr>
                                            <th>Año de edición:</th>
                                            <td id="modalLibroAno"></td>
                                        </tr>
                                        <tr>
                                            <th>ISBN:</th>
                                            <td id="modalLibroIsbn"></td>
                                        </tr>
                                        <tr>
                                            <th>Número de páginas:</th>
                                            <td id="modalLibroPaginas"></td>
                                        </tr>
                                        <tr>
                                            <th>Préstamos totales:</th>
                                            <td id="modalLibroPrestamos"></td>
                                        </tr>
                                        <tr>
                                            <th>Estado:</th>
                                            <td id="modalLibroEstado"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div>
                        <!-- Botón dinámico para prestar/devolver -->
                        <button
                            id="btnAccionLibro"
                            class="btn"
                            data-id-libro=""
                            data-estado="">
                            <i class="bi"></i> <span id="txtAccionLibro"></span>
                        </button>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para seleccionar tipo de prestatario -->
    <div class="modal fade" id="modalPrestamo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-box-arrow-right"></i> Nuevo préstamo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de prestatario</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="tipoPrestatario" id="tipoSocio" value="socio" checked>
                            <label class="btn btn-outline-primary" for="tipoSocio">
                                <i class="bi bi-person-badge"></i> Socio
                            </label>

                            <input type="radio" class="btn-check" name="tipoPrestatario" id="tipoExterno" value="externo">
                            <label class="btn btn-outline-secondary" for="tipoExterno">
                                <i class="bi bi-person"></i> Externo
                            </label>
                        </div>
                    </div>

                    <!-- Sección para socio (oculta si no es socio) -->
                    <div id="seccionSocio" class="mt-3">
                        <label class="form-label fw-bold">Seleccionar socio</label>
                        <select class="form-select" id="selectSocio">
                            <option value="">Seleccionar socio...</option>
                            <?php foreach ($socios as $socio): ?>
                                <option value="<?= $socio->id ?>">
                                    <?= esc($socio->nombre . ' ' . $socio->apellidos) ?> (DNI: <?= esc($socio->dni) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Sección para externo (oculta inicialmente) -->
                    <div id="seccionExterno" class="mt-3 d-none">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="externoNombre" placeholder="Ej: Juan García" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">DNI/NIE <span class="text-muted">(opcional)</span></label>
                            <input type="text" class="form-control" id="externoDni" placeholder="12345678A">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email <span class="text-muted">(opcional)</span></label>
                            <input type="email" class="form-control" id="externoEmail" placeholder="ejemplo@email.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Teléfono <span class="text-muted">(opcional)</span></label>
                            <input type="tel" class="form-control" id="externoTelefono" placeholder="612345678">
                        </div>
                    </div>

                    <!-- Fechas -->
                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Fecha préstamo <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="prestamoFecha" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Fecha devolución prevista</label>
                            <input type="date" class="form-control" id="prestamoDevolucion" value="<?= date('Y-m-d', strtotime('+15 days')) ?>">
                        </div>
                    </div>

                    <!-- Notas -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Notas <span class="text-muted">(opcional)</span></label>
                        <textarea class="form-control" id="prestamoNotas" rows="2" placeholder="Observaciones sobre el préstamo..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarPrestamo">
                        <i class="bi bi-check-lg"></i> Confirmar préstamo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.CSRF_TOKEN = '<?= csrf_token() ?>';
    window.CSRF_HASH = '<?= csrf_hash() ?>';
    window.BASE_URL = '<?= base_url() ?>';
</script>

<script>
    // Variables globales para los gráficos
    window.BASE_URL = '<?= base_url() ?>';
    window.CSRF_TOKEN = '<?= csrf_token() ?>';
    window.CSRF_HASH = '<?= csrf_hash() ?>';
    window.topLibrosData = <?= json_encode($topLibrosPrestados) ?>;
    window.librosPorGeneroData = <?= json_encode($librosPorGenero) ?>;
    window.prestamosPorMesData = <?= json_encode($prestamosPorMes) ?>;
    window.prestamosPorTipoData = <?= json_encode($prestamosPorTipoUsuario) ?>;
    window.prestamosEstadoData = <?= json_encode($prestamosActivosVsDevueltos) ?>;
</script>

<!-- Cargar Chart.js -->
<script src='https://cdn.jsdelivr.net/npm/chart.js'></script>

<!-- Cargar los archivos JS externos -->
<script src='/assets/js/dashboard-graficos.js'></script>
<script src='/assets/js/dashboard-recordatorios.js'></script>