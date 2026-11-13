<div class="container-fluid py-4">
    <div class="row">
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-table"></i> Tabla de libros</h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">Portada</th>
                                    <th>Título</th>
                                    <th>Autor</th>
                                    <th>Género</th>
                                    <th>Estado préstamo</th>
                                    <th>Editorial</th>
                                    <th>Páginas</th>
                                    <th>Año edición</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($libros)): ?>
                                    <?php foreach ($libros as $libro): ?>
                                        <tr>
                                            <td class="text-center">
                                            <img 
                                                src="<?= getPortadaUrl($libro->portada ?? null) ?>" 
                                                alt="<?= esc($libro->titulo) ?>" 
                                                class="img-thumbnail" 
                                                style="max-height: 80px; cursor: pointer;"
                                                onclick="cargarDetalleLibro(<?= $libro->id ?>)"
                                                title="Clic para ver detalles">
                                        </td>
                                            <td><?= esc($libro->titulo) ?></td>
                                            <td><?= esc($autoresMap[(string)$libro->autor] ?? 'Desconocido') ?></td>
                                            <td>
                                                <?php
                                                $etiquetas = array_map('trim', explode(',', $libro->genero));
                                                $nombres = [];
                                                foreach ($etiquetas as $idGenero) {
                                                    if (isset($generosMap[$idGenero])) $nombres[] = $generosMap[$idGenero];
                                                }
                                                echo esc(implode(', ', $nombres));
                                                ?>
                                            </td>
                                            <td align="center">
                                                <span class="badge <?= $libro->prestamo ? 'bg-warning text-dark' : 'bg-success' ?>">
                                                    <?= $libro->prestamo ? 'En préstamo' : 'En casa' ?>
                                                </span>
                                            </td>
                                            <td><?= esc($editorialesMap[(string)$libro->editorial] ?? 'Desconocido') ?></td>
                                            <td><?= esc($libro->n_paginas) ?></td>
                                            <td><?= esc($libro->ano_edicio) ?></td>
                                            <td width="25" align="center" title="Ver <?= $libro->titulo ?>">
                                                <a class="btn btn-outline-primary btn-sm"
                                                    href="<?= base_url('admin/verLibro/' . $libro->id) ?>">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            No hay libros registrados.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div> <!-- /.table-responsive -->
                </div> <!-- /.card-body -->
            </div> <!-- /.card -->
        </div> <!-- /.col-12 -->
    </div> <!-- /.row -->
</div> <!-- /.container-fluid -->
<?= view('modales/modal_detalle_libro') ?>