<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-people me-2"></i>Gestión de Usuarios</h2>
            <p class="text-muted mb-0">Administra los usuarios del sistema</p>
        </div>
        <a href="<?= base_url('usuarios/nuevo') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Usuario
        </a>
    </div>

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

    <!-- Selector de biblioteca para superadmin -->
    <?php if ($esSuperadmin && !empty($bibliotecas)): ?>
        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-body">
                <form action="<?= base_url('usuarios') ?>" method="get" class="d-flex align-items-center gap-3 flex-wrap">
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
                        <a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-lg me-1"></i> Ver todas
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    <?php endif; ?>

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
                            <th>Email</th>
                            <th class="text-center">Nivel</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="<?= $esSuperadmin ? 7 : 6 ?>" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                    No hay usuarios registrados
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td class="text-center fw-bold">#<?= $usuario->id ?></td>
                                    <?php if ($esSuperadmin): ?>
                                        <td>
                                            <span class="badge rounded-pill" style="background:<?= esc($usuario->color_tema ?? '#6c757d') ?>">
                                                <?= esc($usuario->biblioteca_nombre ?? 'N/A') ?>
                                            </span>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:14px;">
                                                <?= strtoupper(substr($usuario->nombre, 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?= esc($usuario->nombre . ' ' . ($usuario->apellidos ?? '')) ?></div>
                                                <?php if ($usuario->telefono): ?>
                                                    <small class="text-muted"><i class="bi bi-telephone me-1"></i><?= esc($usuario->telefono) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= esc($usuario->email) ?></td>
                                    <td class="text-center">
                                        <?php
                                        $badgeNivel = [
                                            'usu'    => 'bg-secondary',
                                            'adm'      => 'bg-info',
                                            'superadmin' => 'bg-danger',
                                            'inv' => 'bg-dark'
                                        ];
                                        ?>
                                        <span class="badge <?= $badgeNivel[$usuario->nivel] ?> rounded-pill">
                                            <?= ucfirst($usuario->nivel) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($usuario->activo): ?>
                                            <span class="badge bg-success rounded-pill">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="<?= base_url('usuarios/ver/' . $usuario->id) ?>" class="btn btn-sm btn-outline-primary" title="Ver">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= base_url('usuarios/editar/' . $usuario->id) ?>" class="btn btn-sm btn-outline-secondary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-toggle-activo btn-outline-<?= $usuario->activo ? 'warning' : 'success' ?>"
                                                data-id="<?= $usuario->id ?>"
                                                data-activo="<?= $usuario->activo ? '1' : '0' ?>"
                                                title="<?= $usuario->activo ? 'Desactivar' : 'Activar' ?>">
                                                <i class="bi bi-<?= $usuario->activo ? 'pause' : 'play' ?>-circle"></i>
                                            </button>
                                            <?php if ($esSuperadmin): ?>
                                                <a href="<?= base_url('usuarios/borrar/' . $usuario->id) ?>"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Eliminar"
                                                    onclick="return confirm('¿Eliminar permanentemente este usuario?')">
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
        <?php if (!empty($usuarios) && $pager && $pager->getPageCount() > 1): ?>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-center">
                    <?= $pager->links('usuarios', 'bootstrap5') ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>