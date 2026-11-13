<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('prestamos') ?>">Préstamos</a></li>
                        <li class="breadcrumb-item active">Detalles #<?= $prestamo['id'] ?></li>
                    </ol>
                </nav>
                <div>
                    <?php if ($prestamo['estado'] === 'activo' || $prestamo['estado'] === 'retrasado'): ?>
                        <button class="btn btn-success btn-sm" onclick="devolver(<?= $prestamo['id'] ?>)">
                            <i class="bi bi-check-lg"></i> Registrar Devolución
                        </button>
                    <?php endif; ?>
                    <a href="<?= base_url('prestamos') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <!-- Estado -->
            <div class="alert alert-<?= 
                $prestamo['estado'] === 'devuelto' ? 'success' : 
                ($prestamo['estado'] === 'retrasado' ? 'danger' : 'warning') 
            ?> d-flex align-items-center mb-4">
                <i class="bi bi-<?= 
                    $prestamo['estado'] === 'devuelto' ? 'check-circle' : 
                    ($prestamo['estado'] === 'retrasado' ? 'exclamation-triangle' : 'clock') 
                ?> fs-4 me-3"></i>
                <div>
                    <strong>Estado:</strong> 
                    <?= ucfirst($prestamo['estado']) ?>
                    <?php if ($prestamo['dias_retraso'] > 0): ?>
                        <br><small><?= $prestamo['dias_retraso'] ?> días de retraso</small>
                    <?php elseif ($prestamo['dias_restantes'] !== null): ?>
                        <br><small><?= $prestamo['dias_restantes'] ?> días restantes</small>
                    <?php elseif ($prestamo['dias_prestamo']): ?>
                        <br><small>Duración: <?= $prestamo['dias_prestamo'] ?> días</small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <!-- Info del Libro -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-book text-primary me-2"></i>Libro</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex">
                                <?php if (!empty($prestamo['libro']['portada'])): ?>
                                    <img src="<?= base_url('uploads/portadas/' . $prestamo['libro']['portada']) ?>" 
                                         class="img-fluid rounded me-3" style="max-height: 120px;">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                                         style="width: 80px; height: 120px;">
                                        <i class="bi bi-book text-muted fs-1"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h5 class="mb-1"><?= esc($prestamo['libro']['titulo']) ?></h5>
                                    <p class="text-muted mb-1"><?= esc($prestamo['libro']['autor'] ?? 'Autor desconocido') ?></p>
                                    <small class="text-muted">ISBN: <?= esc($prestamo['libro']['isbn'] ?? 'N/A') ?></small>
                                    <br>
                                    <a href="<?= base_url('libros/historial/' . $prestamo['libro']['id']) ?>" class="btn btn-sm btn-outline-primary mt-2">
                                        Ver historial
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info del Prestatario -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-person text-primary me-2"></i>
                                Prestatario
                            </h5>
                            <span class="badge bg-<?= $prestamo['prestatario']['tipo'] === 'socio' ? 'primary' : 'secondary' ?>">
                                <?= ucfirst($prestamo['prestatario']['tipo']) ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <h5 class="mb-2"><?= esc($prestamo['prestatario']['nombre']) ?></h5>
                            
                            <?php if ($prestamo['prestatario']['dni']): ?>
                                <p class="mb-1 text-muted">
                                    <i class="bi bi-card-text me-2"></i><?= esc($prestamo['prestatario']['dni']) ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if ($prestamo['prestatario']['email']): ?>
                                <p class="mb-1">
                                    <i class="bi bi-envelope me-2 text-muted"></i>
                                    <a href="mailto:<?= esc($prestamo['prestatario']['email']) ?>"><?= esc($prestamo['prestatario']['email']) ?></a>
                                </p>
                            <?php endif; ?>
                            
                            <?php if ($prestamo['prestatario']['telefono']): ?>
                                <p class="mb-1">
                                    <i class="bi bi-telephone me-2 text-muted"></i>
                                    <a href="tel:<?= esc($prestamo['prestatario']['telefono']) ?>"><?= esc($prestamo['prestatario']['telefono']) ?></a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline de fechas -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-event text-primary me-2"></i>Cronología</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Préstamo -->
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-box-arrow-right"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Préstamo</h6>
                                <p class="text-muted mb-0">
                                    <?= date('d/m/Y H:i', strtotime($prestamo['fecha_prestamo'])) ?>
                                </p>
                            </div>
                        </div>

                        <!-- Devolución prevista -->
                        <?php if ($prestamo['fecha_devolucion_prevista']): ?>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-<?= $prestamo['estado'] === 'retrasado' ? 'danger' : 'info' ?> rounded-circle d-flex align-items-center justify-content-center text-white" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Devolución Prevista</h6>
                                <p class="text-muted mb-0">
                                    <?= date('d/m/Y', strtotime($prestamo['fecha_devolucion_prevista'])) ?>
                                    <?php if ($prestamo['dias_retraso'] > 0): ?>
                                        <span class="badge bg-danger ms-2"><?= $prestamo['dias_retraso'] ?> días retraso</span>
                                    <?php elseif ($prestamo['dias_restantes'] !== null): ?>
                                        <span class="badge bg-info ms-2"><?= $prestamo['dias_restantes'] ?> días restantes</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Devolución real -->
                        <?php if ($prestamo['fecha_entrada_real']): ?>
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center text-white" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-box-arrow-in-left"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Devolución Real</h6>
                                <p class="text-muted mb-0">
                                    <?= date('d/m/Y H:i', strtotime($prestamo['fecha_entrada_real'])) ?>
                                    <?php if ($prestamo['dias_prestamo']): ?>
                                        <span class="badge bg-success ms-2"><?= $prestamo['dias_prestamo'] ?> días de préstamo</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Notas -->
            <?php if ($prestamo['notas']): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-sticky text-primary me-2"></i>Notas</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(esc($prestamo['notas'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
function devolver(id) {
    if (!confirm('¿Confirmar devolución?')) return;
    
    $.post(BASE_URL + 'api/prestamo/devolver', {
        id_prestamo: id,
        fecha_entrada: new Date().toISOString().split('T')[0]
    }, function(response) {
        if (response.status === 'ok') {
            location.reload();
        } else {
            alert('Error: ' + response.message);
        }
    });
}
</script>