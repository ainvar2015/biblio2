<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <a href="<?= base_url('reservas') ?>" class="btn btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left me-1"></i> Volver al listado
            </a>

            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-check me-2"></i>
                        Reserva #<?= $reserva['id'] ?>
                    </h4>
                    <div>
                        <?php if ($reserva['notificado']): ?>
                            <span class="badge bg-secondary rounded-pill me-2">
                                <i class="bi bi-envelope-check me-1"></i> Notificado
                            </span>
                        <?php endif; ?>
                        <?php
                        $badgeClass = [
                            'pendiente' => 'bg-warning text-dark',
                            'prestado'  => 'bg-info',
                            'devuelto'  => 'bg-success',
                            'cancelado' => 'bg-danger'
                        ];
                        ?>
                        <span class="badge <?= $badgeClass[$reserva['estado']] ?> rounded-pill fs-6 px-3 py-2">
                            <?= ucfirst($reserva['estado']) ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-primary">
                                <div class="card-header bg-primary text-white">
                                    <i class="bi bi-person me-1"></i> Usuario
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?= esc($reserva['usuario_nombre'] . ' ' . $reserva['usuario_apellidos']) ?></h5>
                                    <p class="card-text text-muted mb-1">
                                        <i class="bi bi-envelope me-1"></i> <?= esc($reserva['usuario_email']) ?>
                                    </p>
                                    <p class="card-text text-muted">
                                        <i class="bi bi-person-badge me-1"></i> ID: <?= $reserva['id_usuario'] ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-success">
                                <div class="card-header bg-success text-white">
                                    <i class="bi bi-book me-1"></i> Libro
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?= esc($reserva['libro_titulo']) ?></h5>
                                    <p class="card-text text-muted mb-1">
                                        <i class="bi bi-person-lines-fill me-1"></i> <?= esc($reserva['libro_autor']) ?>
                                    </p>
                                    <p class="card-text text-muted">
                                        <i class="bi bi-upc me-1"></i> ISBN: <?= esc($reserva['libro_isbn']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info de plazo si está prestado -->
                    <?php if ($reserva['estado'] === 'prestado' && $reserva['fecha_prestamo']): ?>
                        <?php
                            $fechaLimite = date('Y-m-d', strtotime($reserva['fecha_prestamo'] . " +{$dias_prestamo} days"));
                            $diasRestantes = (strtotime($fechaLimite) - strtotime(date('Y-m-d'))) / 86400;
                            $alertClass = $diasRestantes <= 3 ? 'alert-danger' : ($diasRestantes <= 7 ? 'alert-warning' : 'alert-info');
                        ?>
                        <div class="alert <?= $alertClass ?> d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-calendar-event me-2"></i>
                                <strong>Fecha límite de devolución:</strong> <?= date('d/m/Y', strtotime($fechaLimite)) ?>
                                <span class="badge bg-white text-dark ms-2"><?= $diasRestantes ?> días restantes</span>
                            </div>
                            <a href="<?= base_url('reservas/enviarRecordatorio/' . $reserva['id']) ?>" class="btn btn-sm btn-outline-dark">
                                <i class="bi bi-envelope me-1"></i> Enviar recordatorio
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Cola de reservas para este libro -->
                    <?php if (count($colaReservas) > 1): ?>
                        <div class="alert alert-light border mb-4">
                            <h6 class="alert-heading"><i class="bi bi-people me-2"></i>Cola de reservas para este libro</h6>
                            <p class="text-muted small mb-2">Hay <?= count($colaReservas) ?> reserva(s) activa(s). Se atienden por orden de fecha.</p>
                            <div class="list-group list-group-flush">
                                <?php foreach ($colaReservas as $index => $r): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 <?= $r['id'] == $reserva['id'] ? 'bg-primary bg-opacity-10' : '' ?>">
                                        <div>
                                            <span class="badge bg-secondary me-2">#<?= $index + 1 ?></span>
                                            <span class="fw-semibold">Reserva #<?= $r['id'] ?></span>
                                            <small class="text-muted ms-2"><?= date('d/m/Y H:i', strtotime($r['fecha_reserva'])) ?></small>
                                        </div>
                                        <div>
                                            <?php if ($r['notificado']): ?>
                                                <span class="badge bg-secondary me-1"><i class="bi bi-envelope-check"></i></span>
                                            <?php endif; ?>
                                            <span class="badge <?= $badgeClass[$r['estado']] ?>">
                                                <?= ucfirst($r['estado']) ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Línea de tiempo -->
                    <h5 class="mb-3"><i class="bi bi-clock-history me-2"></i>Línea de Tiempo</h5>
                    <div class="timeline mb-4">
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                    <i class="bi bi-calendar-plus"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Reserva creada</h6>
                                <p class="text-muted mb-0"><?= date('d/m/Y \a \l\a\s H:i', strtotime($reserva['fecha_reserva'])) ?></p>
                            </div>
                        </div>

                        <?php if ($reserva['fecha_prestamo']): ?>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                        <i class="bi bi-book"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Convertido a préstamo</h6>
                                    <p class="text-muted mb-0"><?= date('d/m/Y', strtotime($reserva['fecha_prestamo'])) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($reserva['fecha_devolucion']): ?>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                        <i class="bi bi-check-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Devuelto</h6>
                                    <p class="text-muted mb-0"><?= date('d/m/Y', strtotime($reserva['fecha_devolucion'])) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($reserva['observaciones']): ?>
                        <div class="alert alert-light border">
                            <h6 class="alert-heading"><i class="bi bi-chat-left-text me-2"></i>Observaciones</h6>
                            <p class="mb-0"><?= nl2br(esc($reserva['observaciones'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Acciones -->
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= base_url('reservas/editar/' . $reserva['id']) ?>" class="btn btn-primary">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                        
                        <?php if ($reserva['estado'] === 'pendiente'): ?>
                            <a href="<?= base_url('reservas/convertirAPrestamo/' . $reserva['id']) ?>" 
                               class="btn btn-info text-white"
                               onclick="return confirm('¿Convertir esta reserva en préstamo?')">
                                <i class="bi bi-arrow-right-circle me-1"></i> Convertir a Préstamo
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($reserva['estado'] === 'prestado'): ?>
                            <a href="<?= base_url('reservas/devolver/' . $reserva['id']) ?>" 
                               class="btn btn-success"
                               onclick="return confirm('¿Marcar como devuelto?')">
                                <i class="bi bi-arrow-return-left me-1"></i> Marcar Devuelto
                            </a>
                        <?php endif; ?>
                        
                        <?php if (in_array($reserva['estado'], ['pendiente', 'prestado'])): ?>
                            <a href="<?= base_url('reservas/cancelar/' . $reserva['id']) ?>" 
                               class="btn btn-warning"
                               onclick="return confirm('¿Cancelar esta reserva?')">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?= base_url('reservas/eliminar/' . $reserva['id']) ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('¿Eliminar permanentemente?')">
                            <i class="bi bi-trash me-1"></i> Eliminar
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>