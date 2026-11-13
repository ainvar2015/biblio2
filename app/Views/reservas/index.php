<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-calendar-check me-2"></i>Gestión de Reservas</h2>
            <p class="text-muted mb-0">
                <?= $biblioteca ? esc($biblioteca['nombre']) : 'Todas las bibliotecas' ?>
            </p>
        </div>
        <?php if (session()->get('rol') !== 'usuario'): ?>
            <a href="<?= base_url('reservas/nuevo') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nueva Reserva
            </a>
        <?php endif; ?>
    </div>

    <!-- Info de normativa -->
    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Normativa:</strong> Máximo <strong><?= \App\Models\ReservaModel::MAX_RESERVAS_POR_USUARIO ?> reservas activas</strong> por usuario. 
        Plazo de préstamo: <strong><?= \App\Models\ReservaModel::DIAS_PRESTAMO ?> días</strong>.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Alertas -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('info')): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i><?= session()->getFlashdata('info') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Selector de biblioteca para superadmin -->
    <?php if ($esSuperadmin && !empty($bibliotecas)): ?>
    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-body">
            <form action="<?= base_url('reservas') ?>" method="get" class="d-flex align-items-center gap-3 flex-wrap">
                <label class="fw-bold mb-0 text-primary"><i class="bi bi-building me-1"></i> Filtrar por biblioteca:</label>
                <select name="biblioteca" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="">Todas las bibliotecas</option>
                    <?php foreach ($bibliotecas as $bib): ?>
                        <option value="<?= $bib['id'] ?>" <?= ($busqueda ?? false) || ($filtroActivo !== 'todos') ? '' : ((request()->getGet('biblioteca') == $bib['id']) ? 'selected' : '') ?>>
                            <?= esc($bib['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (request()->getGet('biblioteca')): ?>
                    <a href="<?= base_url('reservas') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg me-1"></i> Ver todas
                    </a>
                <?php endif; ?>
                <?php if ($filtroActivo !== 'todos'): ?>
                    <input type="hidden" name="estado" value="<?= $filtroActivo ?>">
                <?php endif; ?>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-warning h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-clock text-warning fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Pendientes</h6>
                        <h3 class="mb-0"><?= $estadisticas['pendiente'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-info h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-info bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-book text-info fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Prestados</h6>
                        <h3 class="mb-0"><?= $estadisticas['prestado'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-success h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-check-circle text-success fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Devueltos</h6>
                        <h3 class="mb-0"><?= $estadisticas['devuelto'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-secondary h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-secondary bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-collection text-secondary fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Total</h6>
                        <h3 class="mb-0"><?= $estadisticas['total'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros por estado y búsqueda -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="btn-group" role="group">
                        <a href="<?= base_url('reservas' . (request()->getGet('biblioteca') ? '?biblioteca=' . request()->getGet('biblioteca') : '')) ?>" 
                           class="btn btn-outline-secondary <?= $filtroActivo === 'todos' ? 'active' : '' ?>">
                            <i class="bi bi-list me-1"></i> Todos
                        </a>
                        <a href="<?= base_url('reservas?estado=pendiente' . (request()->getGet('biblioteca') ? '&biblioteca=' . request()->getGet('biblioteca') : '')) ?>" 
                           class="btn btn-outline-warning <?= $filtroActivo === 'pendiente' ? 'active' : '' ?>">
                            <i class="bi bi-clock me-1"></i> Pendientes
                        </a>
                        <a href="<?= base_url('reservas?estado=prestado' . (request()->getGet('biblioteca') ? '&biblioteca=' . request()->getGet('biblioteca') : '')) ?>" 
                           class="btn btn-outline-info <?= $filtroActivo === 'prestado' ? 'active' : '' ?>">
                            <i class="bi bi-book me-1"></i> Prestados
                        </a>
                        <a href="<?= base_url('reservas?estado=devuelto' . (request()->getGet('biblioteca') ? '&biblioteca=' . request()->getGet('biblioteca') : '')) ?>" 
                           class="btn btn-outline-success <?= $filtroActivo === 'devuelto' ? 'active' : '' ?>">
                            <i class="bi bi-check-circle me-1"></i> Devueltos
                        </a>
                        <a href="<?= base_url('reservas?estado=cancelado' . (request()->getGet('biblioteca') ? '&biblioteca=' . request()->getGet('biblioteca') : '')) ?>" 
                           class="btn btn-outline-danger <?= $filtroActivo === 'cancelado' ? 'active' : '' ?>">
                            <i class="bi bi-x-circle me-1"></i> Cancelados
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <form action="<?= base_url('reservas') ?>" method="get" class="d-flex gap-2">
                        <?php if (request()->getGet('biblioteca')): ?>
                            <input type="hidden" name="biblioteca" value="<?= request()->getGet('biblioteca') ?>">
                        <?php endif; ?>
                        <?php if ($filtroActivo !== 'todos'): ?>
                            <input type="hidden" name="estado" value="<?= $filtroActivo ?>">
                        <?php endif; ?>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="q" class="form-control" placeholder="Buscar usuario, libro, ISBN..." 
                                   value="<?= esc($busqueda ?? '') ?>">
                            <button type="submit" class="btn btn-outline-primary">Buscar</button>
                            <?php if ($busqueda): ?>
                                <a href="<?= base_url('reservas' . (request()->getGet('biblioteca') ? '?biblioteca=' . request()->getGet('biblioteca') . ($filtroActivo !== 'todos' ? '&estado=' . $filtroActivo : '') : ($filtroActivo !== 'todos' ? '?estado=' . $filtroActivo : ''))) ?>" 
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Reservas -->
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">#</th>
                            <?php if ($esSuperadmin): ?>
                                <th>Biblioteca</th>
                            <?php endif; ?>
                            <th>Usuario</th>
                            <th>Libro</th>
                            <th class="text-center">Estado</th>
                            <th>Fecha Reserva</th>
                            <th>Fecha Préstamo</th>
                            <th>Fecha Devolución</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reservas)): ?>
                            <tr>
                                <td colspan="<?= $esSuperadmin ? 9 : 8 ?>" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                    No hay reservas registradas
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reservas as $reserva): ?>
                                <tr>
                                    <td class="text-center fw-bold">#<?= $reserva['id'] ?></td>
                                    <?php if ($esSuperadmin): ?>
                                        <td>
                                            <span class="badge rounded-pill" style="background:<?= esc($reserva['color_biblioteca'] ?? '#6c757d') ?>">
                                                <?= esc($reserva['biblioteca_nombre'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:14px;">
                                                <?= strtoupper(substr($reserva['usuario_nombre'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?= esc($reserva['usuario_nombre'] . ' ' . $reserva['usuario_apellidos']) ?></div>
                                                <small class="text-muted"><?= esc($reserva['usuario_email']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?= esc($reserva['libro_titulo']) ?></div>
                                        <small class="text-muted"><?= esc($reserva['libro_autor']) ?> | ISBN: <?= esc($reserva['libro_isbn']) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $badgeClass = [
                                            'pendiente' => 'bg-warning text-dark',
                                            'prestado'  => 'bg-info',
                                            'devuelto'  => 'bg-success',
                                            'cancelado' => 'bg-danger'
                                        ];
                                        $badgeIcon = [
                                            'pendiente' => 'clock',
                                            'prestado'  => 'book',
                                            'devuelto'  => 'check-circle',
                                            'cancelado' => 'x-circle'
                                        ];
                                        ?>
                                        <span class="badge <?= $badgeClass[$reserva['estado']] ?> rounded-pill">
                                            <i class="bi bi-<?= $badgeIcon[$reserva['estado']] ?> me-1"></i>
                                            <?= ucfirst($reserva['estado']) ?>
                                        </span>
                                        <?php if ($reserva['notificado']): ?>
                                            <span class="badge bg-secondary rounded-pill ms-1" title="Notificado por email">
                                                <i class="bi bi-envelope-check"></i>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($reserva['fecha_reserva'])) ?></td>
                                    <td><?= $reserva['fecha_prestamo'] ? date('d/m/Y', strtotime($reserva['fecha_prestamo'])) : '<span class="text-muted">-</span>' ?></td>
                                    <td><?= $reserva['fecha_devolucion'] ? date('d/m/Y', strtotime($reserva['fecha_devolucion'])) : '<span class="text-muted">-</span>' ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="<?= base_url('reservas/ver/' . $reserva['id']) ?>" class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            <?php if (session()->get('rol') !== 'usuario'): ?>
                                                <?php if ($reserva['estado'] === 'pendiente'): ?>
                                                    <a href="<?= base_url('reservas/convertirAPrestamo/' . $reserva['id']) ?>" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="Convertir a préstamo"
                                                       onclick="return confirm('¿Convertir esta reserva en préstamo?')">
                                                        <i class="bi bi-arrow-right-circle"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if ($reserva['estado'] === 'prestado'): ?>
                                                    <a href="<?= base_url('reservas/devolver/' . $reserva['id']) ?>" 
                                                       class="btn btn-sm btn-outline-success" 
                                                       title="Marcar como devuelto"
                                                       onclick="return confirm('¿Marcar este préstamo como devuelto?')">
                                                        <i class="bi bi-arrow-return-left"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="<?= base_url('reservas/editar/' . $reserva['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                <?php if (in_array($reserva['estado'], ['pendiente', 'prestado'])): ?>
                                                    <a href="<?= base_url('reservas/cancelar/' . $reserva['id']) ?>" 
                                                       class="btn btn-sm btn-outline-warning" 
                                                       title="Cancelar"
                                                       onclick="return confirm('¿Cancelar esta reserva?')">
                                                        <i class="bi bi-x-lg"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="<?= base_url('reservas/eliminar/' . $reserva['id']) ?>" 
                                                   class="btn btn-sm btn-outline-danger" 
                                                   title="Eliminar"
                                                   onclick="return confirm('¿Eliminar permanentemente esta reserva?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Paginación -->
        <?php if (!empty($reservas) && $pager && $pager->getPageCount() > 1): ?>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-center">
                    <?= $pager->links('reservas', 'bootstrap5') ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>