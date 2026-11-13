    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-eye"></i> Ver libro: <strong><?= esc($libro->titulo) ?></strong></h5>
                        <a href="<?= base_url('admin/invitado') ?>" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver al listado de libros</a>
                    </div>
                    <div class="card-body">
                        <!-- Tipo libro/CD y ISBN -->
                        <div class="row mb-3">
                            <div class="col-lg-5">
                                <label for="codigoISBN" class="form-label"><strong>ISBN: </strong></label>
                                <span><?= esc($libro->codigoISBN) ?></span>
                            </div>
                            <div class="col-lg-5 d-flex align-items-end">
                                <div id="estado-libro" class="col-md-3">
                                    <label>Estado: </label>
                                    <span class="badge <?= $libro->prestamo > 0 ? 'bg-warning' : 'bg-success' ?>">
                                        <?= $libro->prestamo > 0 ? 'EN PRÉSTAMO' : 'EN CASA' ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Título -->
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <label for="titulo" class="form-label red"><strong>Título: </strong></label>
                                <span><?= esc($libro->titulo) ?></span>
                            </div>
                        </div>

                        <!-- Autor -->
                        <div class="row mb-3">
                            <div class="col-lg-10">
                                <label for="autor" class="form-label"><strong>Autor: </strong></label>
                                <span><?= $autor->nombreautor ?? 'Desconocido' ?></span>
                            </div>
                            <div class="col-lg-2 d-flex align-items-end">
                            </div>
                        </div>

                        <!-- Editorial -->
                        <div class="row mb-3">
                            <div class="col-lg-10">
                                <label for="editorial" class="form-label"><strong>Editorial: </strong></label>
                                <span><?= esc($editorial->nombreeditorial) ?></span>
                            </div>
                            <div class="col-lg-2 d-flex align-items-end">
                            </div>
                        </div>

                        <!-- Género -->
                        <div class="row mb-3">
                            <div class="col-lg-10">
                                <label for="genero" class="form-label"><strong>Género: </strong></label>
                                <span><?= $genero->genero ?? 'No definido' ?></span>
                            </div>
                            <div class="col-lg-2 d-flex align-items-end">
                            </div>
                        </div>

                        <!-- Páginas y año -->
                        <div class="row mb-3">
                            <div class="col-lg-2">
                                <label for="n_paginas" class="form-label"><strong>Nº de páginas: </strong></label>
                                <span><?= esc($libro->n_paginas) ?></span>
                            </div>
                            <div class="col-lg-4">
                                <label for="ano_edicio" class="form-label"><strong>Año de edición: </strong></label>
                                <span><?= esc($libro->ano_edicio) ?></span>
                            </div>
                        </div>

                        <!-- Reseña -->
                        <div class="row mb-3 resena-animada
                                <?= empty($libro->resena) ? 'resena-oculta' : 'resena-visible' ?>"
                            id="resenaRow">
                            <div class="col-lg-12">
                                <label for="resena" class="form-label">Reseña del libro</label>
                                <textarea id="resena" name="resena" rows="12" class="form-control"><?= esc($libro->resena) ?></textarea>
                            </div>
                        </div>
                        <?php if (empty($libro->resena)): ?>
                            <div class="row mb-3" id="btnMostrarResena">
                                <div class="col-lg-12">
                                    <button type="button" class="btn btn-outline-secondary" id="muestraResena">Ver reseña del libro</button>
                                </div>
                            </div>
                        <?php endif; ?>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>