<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            
            <!-- Cabecera con búsqueda -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="bi bi-search"></i> Resultados de búsqueda</h4>
                <a href="<?= base_url('usuarios/buscar') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Nueva búsqueda
                </a>
            </div>
            
            <!-- Resumen -->
            <div class="alert alert-secondary mb-4">
                <i class="bi bi-info-circle me-2"></i>
                Búsqueda: <strong><?= esc($query) ?></strong> |
                Socios: <span class="badge bg-primary"><?= count($socios) ?></span> |
                Externos: <span class="badge bg-secondary"><?= count($externos) ?></span>
            </div>
            
            <!-- RESULTADOS: SOCIOS -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-people"></i> Socios registrados</h6>
                    <span class="badge bg-light text-primary"><?= count($socios) ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($socios)): ?>
                        <div class="alert alert-light mb-0">
                            <i class="bi bi-inbox"></i> No se encontraron socios.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Apellidos</th>
                                        <th>DNI</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($socios as $socio): ?>
                                        <tr>
                                            <td><?= esc($socio['nombre']) ?></td>
                                            <td><?= esc($socio['apellidos'] ?? '-') ?></td>
                                            <td><?= esc($socio['dni'] ?? '-') ?></td>
                                            <td><?= esc($socio['email'] ?? '-') ?></td>
                                            <td><?= esc($socio['telefono'] ?? '-') ?></td>
                                            <td>
                                                <a href="<?= base_url('historial/usuario/' . ($socio['id'] ?? '')) ?>" 
                                                   class="btn btn-sm btn-outline-info" title="Ver historial">
                                                    <i class="bi bi-clock-history"></i>
                                                </a>
                                                <a href="<?= base_url('usuarios/ver/' . ($socio['id'] ?? '')) ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Ver ficha">
                                                    <i class="bi bi-eye"></i>
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
            
            <!-- RESULTADOS: EXTERNOS -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-person"></i> Prestatarios externos</h6>
                    <span class="badge bg-light text-secondary"><?= count($externos) ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($externos)): ?>
                        <div class="alert alert-light mb-0">
                            <i class="bi bi-inbox"></i> No se encontraron prestatarios externos.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>DNI</th>
                                        <th>Email</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($externos as $externo): ?>
                                        <?php
                                        // Generar ID seguro con Base64URL
                                        $externoRaw = $externo['nombre_externo'] . '|' . ($externo['dni_externo'] ?? '');
                                        $externoId = base64UrlEncode($externoRaw);
                                        ?>
                                        <tr>
                                            <td><?= esc($externo['nombre_externo']) ?></td>
                                            <td><?= esc($externo['dni_externo'] ?? '-') ?></td>
                                            <td><?= esc($externo['email_externo'] ?? '-') ?></td>
                                            <td>
                                                <a href="<?= base_url('historial/externo/' . $externoId) ?>" 
                                                   class="btn btn-sm btn-outline-info" title="Ver historial">
                                                    <i class="bi bi-clock-history"></i>
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
    </div>
</div>