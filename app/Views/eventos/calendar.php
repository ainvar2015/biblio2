<!-- app/Views/eventos/calendar.php -->
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            
            <!-- Selector de biblioteca para superadmin -->
            <?php if (!empty($esSuperadmin) && !empty($bibliotecas)): ?>
            <div class="row mb-3">
                <div class="col-md-6 col-lg-4">
                    <div class="card border-primary">
                        <div class="card-body d-flex align-items-center gap-3">
                            <label class="fw-bold mb-0 text-primary">
                                <i class="bi bi-building me-1"></i> Biblioteca:
                            </label>
                            <select id="selectBibliotecaCalendario" class="form-select">
                                <option value="todas">Todas las bibliotecas</option>
                                <?php foreach ($bibliotecas as $bib): ?>
                                    <option value="<?= $bib['id'] ?>" <?= ($bibliotecaActiva == $bib['id']) ? 'selected' : '' ?>>
                                        <?= esc($bib['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Biblioteca del usuario normal (oculto) -->
            <?php if (empty($esSuperadmin) && !empty($bibliotecaActiva)): ?>
                <input type="hidden" id="bibliotecaUsuario" value="<?= $bibliotecaActiva ?>">
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-calendar"></i> Calendario de eventos</h5>
                    <div>
                        <button id="btnNuevoEvento" class="btn btn-light btn-sm me-2">
                            <i class="bi bi-plus-lg"></i> Nuevo evento
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="calendario"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar eventos -->
<div class="modal fade" id="modalEvento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-calendar-event"></i> <span id="modalEventoTitulo">Nuevo evento</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEvento">
                    <input type="hidden" id="eventoId">
                    
                    <!-- Biblioteca (solo superadmin) -->
                    <?php if (!empty($esSuperadmin) && !empty($bibliotecas)): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Biblioteca <span class="text-danger">*</span></label>
                        <select class="form-select" id="eventoBiblioteca" name="id_biblioteca" required>
                            <?php foreach ($bibliotecas as $bib): ?>
                                <option value="<?= $bib['id'] ?>" <?= ($bibliotecaActiva == $bib['id']) ? 'selected' : '' ?>>
                                    <?= esc($bib['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                        <input type="hidden" id="eventoBiblioteca" name="id_biblioteca" value="<?= $bibliotecaActiva ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="eventoTitulo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea class="form-control" id="eventoDescripcion" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha inicio <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="eventoFechaInicio" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha fin</label>
                        <input type="datetime-local" class="form-control" id="eventoFechaFin">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Color</label>
                        <input type="color" class="form-control form-control-color" id="eventoColor" value="#3788d8">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarEvento">
                    <i class="bi bi-check-lg"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para confirmar eliminación -->
<div class="modal fade" id="modalEliminarEvento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-trash"></i> Eliminar evento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este evento?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Notificación de eventos próximos -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toastEventosProximos" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-warning text-dark">
            <i class="bi bi-calendar-event me-2"></i>
            <strong class="me-auto">Eventos próximos</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Tienes <span id="numEventosProximos">0</span> eventos próximos en los próximos 7 días.
        </div>
    </div>
</div>

<script>
    window.CSRF_TOKEN = '<?= csrf_token() ?>';
    window.CSRF_HASH = '<?= csrf_hash() ?>';
    window.BASE_URL = '<?= rtrim(base_url(), '/') ?>/';
</script>