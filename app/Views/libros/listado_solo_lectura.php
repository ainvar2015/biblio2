<div class="mainbar">
    <div class="container-fluid mt-4">

        <!-- Cabecera -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="bi bi-book"></i> Catálogo de libros</h4>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Selector de biblioteca para superadmin -->
        <?php if ($esSuperadmin && !empty($bibliotecas)): ?>
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-body d-flex align-items-center gap-3 flex-wrap">
                            <label class="fw-bold mb-0 text-primary">
                                <i class="bi bi-building me-1"></i> Biblioteca:
                            </label>
                            <form action="<?= base_url('verLibros') ?>" method="get" class="d-flex align-items-center gap-2">
                                <select name="biblioteca" class="form-select w-auto" onchange="this.form.submit()">
                                    <option value="">Todas las bibliotecas</option>
                                    <?php foreach ($bibliotecas as $bib): ?>
                                        <option value="<?= $bib['id'] ?>" <?= ($bibliotecaActiva == $bib['id']) ? 'selected' : '' ?>>
                                            <?= esc($bib['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($bibliotecaActiva): ?>
                                    <a href="<?= base_url('verLibros') ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-x-lg"></i> Ver todas
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Info de biblioteca activa para usuarios normales -->
        <?php if (!$esSuperadmin && !empty($bibliotecaUsuario)): ?>
            <div class="alert alert-info">
                <i class="bi bi-building"></i> Viendo libros de: <strong><?= esc($bibliotecaUsuario['nombre']) ?></strong>
            </div>
        <?php endif; ?>

        <!-- Alertas -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Tabla de libros -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-list"></i> Listado de libros</h6>
                <span class="badge bg-light text-primary"><?= count($libros) ?> libros</span>
            </div>
            <div class="card-body">
                <?php if (empty($libros)): ?>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> No hay libros registrados<?= $bibliotecaActiva ? ' en esta biblioteca' : '' ?>.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaLibros">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">Portada</th>
                                    <th>Título</th>
                                    <th>Autor</th>
                                    <th>Editorial</th>
                                    <th>Género</th>
                                    <th>Año</th>
                                    <th>ISBN</th>
                                    <th>Estado</th>
                                    <th class="text-center" style="width: 80px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($libros as $libro): ?>
                                    <tr>
                                        <td class="text-center">
                                            <img
                                                src="<?= getPortadaUrl($libro['portada'] ?? null) ?>"
                                                alt="<?= esc($libro['titulo']) ?>"
                                                class="img-thumbnail"
                                                style="max-height: 80px; cursor: pointer;"
                                                onclick="cargarDetalleLibro(<?= $libro['id'] ?>)"
                                                title="Clic para ver detalles">
                                        </td>
                                        <td><strong><?= esc($libro['titulo']) ?></strong> (<?= $libro['n_paginas'] ?> páginas)</td>
                                        <td>
                                            <?php if (!empty($libro['autores'])): ?>
                                                <?= esc($libro['autores_texto']) ?>
                                                <?php if ($libro['total_autores'] > 1): ?>
                                                    <span class="badge bg-info ms-1" title="<?= esc($libro['autores_texto']) ?>">+<?= $libro['total_autores'] - 1 ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Desconocido</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($libro['editorial'] ?? 'Desconocida') ?></td>
                                        <td>
                                            <?php if (!empty($libro['generos'])): ?>
                                                <span class="badge bg-secondary"><?= esc($libro['generos'][0]['nombre']) ?></span>
                                                <?php if ($libro['total_generos'] > 1): ?>
                                                    <span class="badge bg-info ms-1" title="<?= esc($libro['generos_texto']) ?>">+<?= $libro['total_generos'] - 1 ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Sin género</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($libro['ano_edicio'] ?? '-') ?></td>
                                        <td><small class="text-muted"><?= esc($libro['codigoISBN'] ?? '-') ?></small></td>
                                        <td>
                                            <?php if (!empty($libro['prestamo']) && $libro['prestamo'] == 1): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-box-arrow-right"></i> Prestado
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Disponible
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary btn-ver-detalle-libro"
                                                data-id="<?= $libro['id'] ?>"
                                                title="Ver detalles">
                                                <i class="bi bi-eye"></i>
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

<!-- Reutilizamos el mismo modal del dashboard -->
<?= view('modales/modal_detalle_libro') ?>