    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-pencil-square"></i> <?= $titulo ?></h5>
                        <span class="badge bg-dark">ID: <?= $libro->id ?></span>
                    </div>
                    <div class="card-body">
                        <?= form_open_multipart("editar/update/libro/{$libro->id}", ['id' => 'formLibro', 'novalidate' => true]) ?>

                        <!-- ID oculto -->
                        <input type="hidden" name="id" value="<?= $libro->id ?>">

                        <!-- Sección: Información Principal -->
                        <fieldset class="mb-4 border rounded p-3">
                            <legend class="float-none w-auto px-2 h6 text-warning">
                                <i class="bi bi-book"></i> Información Principal
                            </legend>

                            <div class="row align-items-start">
                                <!-- ISBN (SOLO LECTURA) -->
                                <div class="col-md-4 mb-3">
                                    <label for="codigoISBN" class="form-label fw-bold">ISBN</label>
                                    <input id="codigoISBN" name="codigoISBN"
                                        value="<?= esc($libro->isbn ?? $libro->codigoISBN ?? ''); ?>"
                                        type="text"
                                        class="form-control bg-light"
                                        readonly>
                                    <button class="btn btn-outline-primary" type="button" id="btnBuscarISBN" title="Completar datos desde Google Books">
                                        <i class="bi bi-search"></i> Completar datos
                                    </button><hr>
                                    <small class="text-muted">El ISBN no se puede modificar</small>
                                    <div id="estadoBusqueda" class="form-text mt-1"></div>
                                </div>

                                <!-- Portada -->
                                <!-- Portada -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Portada</label>
                                    <div id="portadaWrapper" style="position: relative;">
                                        <div id="portadaContainer" style="height: 200px; border: 2px dashed #dee2e6; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; cursor: pointer; overflow: hidden;">

                                            <?php
                                            $portadaActual = $libro->portada_url ?? $libro->portada ?? '';
                                            $esUrlExterna = !empty($portadaActual) && (str_starts_with($portadaActual, 'http://') || str_starts_with($portadaActual, 'https://'));

                                            if (!empty($portadaActual)):
                                                // Si es URL externa, usar directamente
                                                // Si es archivo local, construir ruta
                                                $srcPortada = $esUrlExterna ? $portadaActual : base_url('uploads/portadas/' . $portadaActual);
                                            ?>
                                                <img id="portadaPreview" src="<?= $srcPortada ?>" alt="" style="max-height: 100%; max-width: 100%; object-fit: contain; display: block;">
                                                <div id="portadaPlaceholder" class="text-center text-muted" style="display: none;">
                                                    <i class="bi bi-image" style="font-size: 3rem;"></i>
                                                    <p class="mb-0 small">Click para cambiar imagen</p>
                                                </div>
                                            <?php else: ?>
                                                <img id="portadaPreview" src="" alt="" style="max-height: 100%; max-width: 100%; object-fit: contain; display: none;">
                                                <div id="portadaPlaceholder" class="text-center text-muted">
                                                    <i class="bi bi-image" style="font-size: 3rem;"></i>
                                                    <p class="mb-0 small">Click para subir imagen<br>o arrastra aquí</p>
                                                </div>
                                            <?php endif; ?>

                                        </div>

                                        <input type="file" id="portadaFile" name="portada_file" accept="image/*" style="display: none;">
                                        <input type="hidden" name="portada_url" id="portadaUrl" value="<?= esc($portadaActual) ?>">

                                        <div class="mt-2 d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnSubirPortada">
                                                <i class="bi bi-upload"></i> <?= !empty($portadaActual) ? 'Cambiar' : 'Subir' ?>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger <?= empty($portadaActual) ? 'd-none' : '' ?>" id="btnQuitarPortada">
                                                <i class="bi bi-trash"></i> Quitar
                                            </button>
                                        </div>

                                        <?php if (!empty($portadaActual)): ?>
                                            <small class="text-muted d-block mt-1">
                                                <?= $esUrlExterna ? '<i class="bi bi-link-45deg"></i> URL externa' : '<i class="bi bi-hdd"></i> Archivo local' ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <!-- Título -->
                                <div class="col-md-4 mb-3">
                                    <label for="titulo" class="form-label fw-bold">
                                        Título <span class="text-danger">*</span>
                                    </label>
                                    <input id="titulo" name="titulo"
                                        value="<?= esc($libro->titulo); ?>"
                                        type="text"
                                        class="form-control"
                                        required>
                                    <div class="invalid-feedback">El título es obligatorio</div>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Sección: Clasificación -->
                        <fieldset class="mb-4 border rounded p-3">
                            <legend class="float-none w-auto px-2 h6 text-warning">
                                <i class="bi bi-tags"></i> Clasificación
                            </legend>

                            <div class="row">
                                <!-- Autor -->
                                <div class="col-md-10 mb-3">
                                    <label for="autor" class="form-label fw-bold">Autor <span class="text-danger">*</span></label>
                                    <select class="form-select select2-autor" id="autor" name="autor" required>
                                        <option value=""></option>
                                        <?php foreach ($autores as $autor): ?>
                                            <option value="<?= $autor->id; ?>" <?= ($libro->autor == $autor->id) ? 'selected' : ''; ?>>
                                                <?= esc($autor->nombreautor); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Selecciona un autor</div>
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalAutor">
                                        <i class="bi bi-plus-circle"></i> Nuevo
                                    </button>
                                </div>

                                <!-- Editorial -->
                                <div class="col-md-10 mb-3">
                                    <label for="editorial" class="form-label fw-bold">Editorial <span class="text-danger">*</span></label>
                                    <select class="form-select select2-editorial" id="editorial" name="editorial" required>
                                        <option value=""></option>
                                        <?php foreach ($editoriales as $editorial): ?>
                                            <option value="<?= $editorial->id; ?>" <?= ($libro->editorial == $editorial->id) ? 'selected' : ''; ?>>
                                                <?= esc($editorial->nombreeditorial); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Selecciona una editorial</div>
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalEditorial">
                                        <i class="bi bi-plus-circle"></i> Nuevo
                                    </button>
                                </div>

                                <!-- Género (múltiple si tu BD lo soporta) -->
                                <div class="col-md-10 mb-3">
                                    <label for="genero" class="form-label fw-bold">Género</label>
                                    <select class="form-select select2-genero" id="genero" name="genero">
                                        <option value=""></option>
                                        <?php foreach ($generos as $genero): ?>
                                            <option value="<?= $genero->id; ?>"
                                                <?= (in_array($genero->id, $arrayGeneros) || $libro->genero == $genero->id) ? 'selected' : ''; ?>>
                                                <?= esc($genero->genero); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalGenero">
                                        <i class="bi bi-plus-circle"></i> Nuevo
                                    </button>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Sección: Detalles -->
                        <fieldset class="mb-4 border rounded p-3">
                            <legend class="float-none w-auto px-2 h6 text-warning">
                                <i class="bi bi-calendar"></i> Detalles de Edición
                            </legend>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="n_paginas" class="form-label fw-bold">Nº de páginas</label>
                                    <input id="n_paginas" name="n_paginas"
                                        value="<?= esc($libro->n_paginas ?? $libro->paginas ?? ''); ?>"
                                        type="number"
                                        class="form-control"
                                        min="1"
                                        placeholder="Ej: 320">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="ano_edicio" class="form-label fw-bold">Año de edición</label>
                                    <input id="ano_edicio" name="ano_edicio"
                                        type="number"
                                        value="<?= esc($libro->ano_edicio ?? $libro->ano_edicion ?? ''); ?>"
                                        class="form-control"
                                        min="1800"
                                        max="<?= date('Y') + 1; ?>"
                                        placeholder="Ej: 2017">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Fecha de alta</label>
                                    <input type="text" class="form-control bg-light" value="<?= isset($libro->fecha_alta) ? date('d/m/Y', strtotime($libro->fecha_alta)) : 'N/A' ?>" readonly>
                                </div>
                            </div>
                        </fieldset>

                        <!-- RESEÑA -->
                        <?php $tieneResena = !empty($libro->resena); ?>

                        <div class="row mb-3 <?= $tieneResena ? 'd-none' : '' ?>" id="filaBtnResena">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-outline-secondary" id="btnMostrarResena">
                                    <i class="bi bi-chat-square-text"></i> Añadir reseña del libro
                                </button>
                            </div>
                        </div>

                        <div class="row mb-3 <?= $tieneResena ? '' : 'd-none' ?>" id="filaResena">
                            <div class="col-lg-12">
                                <label for="resena" class="form-label fw-bold">Reseña del libro</label>
                                <textarea id="resena" name="resena" rows="6" class="form-control"><?= esc($libro->resena ?? '') ?></textarea>
                                <small class="text-muted"><span id="charCount"><?= strlen($libro->resena ?? '') ?></span> caracteres</small>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="btnOcultarResena">
                                    <i class="bi bi-x-circle"></i> Quitar reseña
                                </button>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <div>
                                <a href="<?= base_url('editar/listaLibros') ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver al listado
                                </a>
                                <button type="button" class="btn btn-outline-warning ms-2" id="btnRestaurar">
                                    <i class="bi bi-arrow-counterclockwise"></i> Descartar cambios
                                </button>
                            </div>
                            <button type="submit" class="btn btn-warning btn-lg" id="btnGuardar">
                                <i class="bi bi-save"></i> Guardar Cambios
                            </button>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>