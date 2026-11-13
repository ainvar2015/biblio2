<?php
// Determinar modo
// $esEdicion = !empty($libro) && is_object($libro);
$esEdicion = isset($libro) && is_object($libro) && !empty($libro->id);
$titulo = $esEdicion ? 'Editar Libro' : 'Alta de Libros';
$headerClass = $esEdicion ? 'bg-warning text-dark' : 'bg-primary text-white';
$btnText = $esEdicion ? 'Guardar Cambios' : 'Guardar Libro';
$btnClass = $esEdicion ? 'btn-warning' : 'btn-primary';
$formAction = $esEdicion ? "editar/update/libro/{$libro->id}" : 'altas/guardar/libro';

// Datos del libro (edición) o vacíos (alta)
$isbn = $esEdicion ? ($libro->isbn ?? $libro->codigoISBN ?? '') : set_value('codigoISBN');
$tituloLibro = $esEdicion ? ($libro->titulo ?? '') : set_value('titulo');
$portada = $esEdicion ? ($libro->portada_url ?? $libro->portada ?? '') : '';
$nPaginas = $esEdicion ? ($libro->n_paginas ?? $libro->paginas ?? '') : set_value('n_paginas');
$anoEdicion = $esEdicion ? ($libro->ano_edicio ?? $libro->ano_edicion ?? '') : set_value('ano_edicio');
$resena = $esEdicion ? ($libro->resena ?? '') : set_value('resena');
$fechaAlta = $esEdicion ? (isset($libro->fecha_alta) ? date('d/m/Y', strtotime($libro->fecha_alta)) : 'N/A') : date('Y-m-d');

// Autores y géneros seleccionados (arrays de IDs como enteros)
$autoresSeleccionados = $esEdicion 
    ? array_map('intval', $autoresLibro ?? []) 
    : array_map('intval', (array) (set_value('autores') ?? []));
    
$generosSeleccionados = $esEdicion 
    ? array_map('intval', $generosLibro ?? []) 
    : array_map('intval', (array) (set_value('generos') ?? []));

// Portada
$esUrlExterna = !empty($portada) && (str_starts_with($portada, 'http://') || str_starts_with($portada, 'https://'));
$srcPortada = !empty($portada) ? ($esUrlExterna ? $portada : base_url('uploads/portadas/' . $portada)) : '';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center <?= $headerClass ?>">
                    <h5 class="mb-0">
                        <i class="bi bi-<?= $esEdicion ? 'pencil-square' : 'plus-circle' ?>"></i> 
                        <?= $titulo ?>
                    </h5>
                    <?php if ($esEdicion): ?>
                        <span class="badge bg-dark">ID: <?= $libro->id ?></span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?= form_open_multipart($formAction, ['id' => 'formLibro', 'novalidate' => true]) ?>

                    <?php if ($esEdicion): ?>
                        <input type="hidden" name="id" value="<?= $libro->id ?>">
                    <?php endif; ?>

                    <!-- Sección: Biblioteca -->
                    <?php if ($esSuperadmin && !empty($bibliotecas) && count($bibliotecas) > 1): ?>
                        <fieldset class="mb-4 border rounded p-3">
                            <legend class="float-none w-auto px-2 h6 text-<?= $esEdicion ? 'warning' : 'primary' ?>">
                                <i class="bi bi-building"></i> Biblioteca
                            </legend>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="id_biblioteca" class="form-label fw-bold">
                                        Biblioteca <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="id_biblioteca" name="id_biblioteca" required>
                                        <option value="">Selecciona una biblioteca</option>
                                        <?php foreach ($bibliotecas as $bib): ?>
                                            <option value="<?= $bib['id'] ?>" 
                                                <?= ($esEdicion ? ($libro->id_biblioteca ?? '') : ($idBibliotecaUsuario ?? '')) == $bib['id'] ? 'selected' : '' ?>>
                                                <?= esc($bib['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Selecciona una biblioteca</div>
                                </div>
                            </div>
                        </fieldset>
                    <?php else: ?>
                        <input type="hidden" name="id_biblioteca" value="<?= $esEdicion ? ($libro->id_biblioteca ?? $bibliotecas[0]->id ?? $idBibliotecaUsuario) : ($bibliotecas[0]->id ?? $idBibliotecaUsuario) ?>" />
                    <?php endif; ?>

                    <!-- Sección: Información Principal -->
                    <fieldset class="mb-4 border rounded p-3">
                        <legend class="float-none w-auto px-2 h6 text-<?= $esEdicion ? 'warning' : 'primary' ?>">
                            <i class="bi bi-book"></i> Información Principal
                        </legend>

                        <div class="row align-items-start">
                            <!-- ISBN -->
                            <div class="col-md-4 mb-3">
                                <label for="codigoISBN" class="form-label fw-bold">
                                    ISBN <?= !$esEdicion ? '<span class="text-danger">*</span>' : '' ?>
                                </label>
                                <?php if ($esEdicion): ?>
                                    <input id="codigoISBN" name="codigoISBN" value="<?= esc($isbn) ?>" type="text" class="form-control bg-light" readonly>
                                    <button class="btn btn-outline-primary mt-2" type="button" id="btnBuscarISBN" title="Completar datos desde Google Books">
                                        <i class="bi bi-search"></i> Completar datos
                                    </button>
                                    <small class="text-muted d-block">El ISBN no se puede modificar</small>
                                <?php else: ?>
                                    <div class="input-group">
                                        <input id="codigoISBN" name="codigoISBN" value="<?= esc($isbn) ?>" type="text" class="form-control" placeholder="9788437604947" required>
                                        <button class="btn btn-outline-primary" type="button" id="btnBuscarISBN">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">ISBN no válido</div>
                                    <small class="text-muted">Sin guiones. Ej: 9788437604947</small>
                                <?php endif; ?>
                                <div id="estadoBusqueda" class="form-text mt-1"></div>
                            </div>

                            <!-- Portada -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Portada</label>
                                <div id="portadaWrapper" style="position: relative;">
                                    <div id="portadaContainer" style="height: 200px; border: 2px dashed #dee2e6; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; cursor: pointer; overflow: hidden;">
                                        <?php if (!empty($srcPortada)): ?>
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
                                    <input type="hidden" name="portada_url" id="portadaUrl" value="<?= esc($portada) ?>">

                                    <div class="mt-2 d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnSubirPortada">
                                            <i class="bi bi-upload"></i> <?= !empty($portada) ? 'Cambiar' : 'Subir' ?>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger <?= empty($portada) ? 'd-none' : '' ?>" id="btnQuitarPortada">
                                            <i class="bi bi-trash"></i> Quitar
                                        </button>
                                    </div>

                                    <?php if (!empty($portada)): ?>
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
                                <input id="titulo" name="titulo" value="<?= esc($tituloLibro) ?>" type="text" class="form-control" required>
                                <div class="invalid-feedback">El título es obligatorio</div>
                                <?php if (!$esEdicion): ?>
                                    <div id="estadoLibro" class="form-text"></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Sección: Clasificación -->
                    <fieldset class="mb-4 border rounded p-3">
                        <legend class="float-none w-auto px-2 h6 text-<?= $esEdicion ? 'warning' : 'primary' ?>">
                            <i class="bi bi-tags"></i> Clasificación
                        </legend>

                        <div class="row">
                            <!-- Autores MÚLTIPLES -->
                            <div class="col-md-10 mb-3">
                                <label for="autores" class="form-label fw-bold">
                                    Autores <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2-autores" id="autores" name="autores[]" multiple="multiple" required style="width: 100%;">
                                    <?php foreach ($autores as $autor): ?>
                                        <option value="<?= $autor->id ?>" 
                                            <?= in_array((int)$autor->id, $autoresSeleccionados) ? 'selected' : '' ?>>
                                            <?= esc($autor->nombreautor . ' ' . ($autor->apellidos ?? '')) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Selecciona uno o varios autores</small>
                                <div class="invalid-feedback">Selecciona al menos un autor</div>
                            </div>
                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalAutor">
                                    <i class="bi bi-plus-circle"></i> Nuevo
                                </button>
                            </div>

                            <!-- Editorial -->
                            <div class="col-md-10 mb-3">
                                <label for="editorial" class="form-label fw-bold">
                                    Editorial <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2-editorial" id="editorial" name="editorial" required>
                                    <option value=""></option>
                                    <?php foreach ($editoriales as $editorial): ?>
                                        <option value="<?= $editorial->id ?>" 
                                            <?= ($esEdicion ? ($libro->editorial ?? '') : set_value('editorial')) == $editorial->id ? 'selected' : '' ?>>
                                            <?= esc($editorial->nombreeditorial) ?>
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

                            <!-- Géneros MÚLTIPLES -->
                            <div class="col-md-10 mb-3">
                                <label for="generos" class="form-label fw-bold">
                                    Géneros <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2-generos" id="generos" name="generos[]" multiple="multiple" required style="width: 100%;">
                                    <?php foreach ($generos as $genero): ?>
                                        <option value="<?= $genero->id ?>" 
                                            <?= in_array((int)$genero->id, $generosSeleccionados) ? 'selected' : '' ?>>
                                            <?= esc($genero->genero) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Selecciona uno o varios géneros</small>
                                <div class="invalid-feedback">Selecciona al menos un género</div>
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
                        <legend class="float-none w-auto px-2 h6 text-<?= $esEdicion ? 'warning' : 'primary' ?>">
                            <i class="bi bi-calendar"></i> Detalles de Edición
                        </legend>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="n_paginas" class="form-label fw-bold">Nº de páginas</label>
                                <input id="n_paginas" name="n_paginas" value="<?= esc($nPaginas) ?>" type="number" class="form-control" min="1" placeholder="Ej: 320">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ano_edicio" class="form-label fw-bold">Año de edición</label>
                                <input id="ano_edicio" name="ano_edicio" type="number" value="<?= esc($anoEdicion) ?>" class="form-control" min="1800" max="<?= date('Y') + 1 ?>" placeholder="Ej: 2017">
                            </div>
                            <?php if ($esEdicion): ?>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Fecha de alta</label>
                                    <input type="text" class="form-control bg-light" value="<?= $fechaAlta ?>" readonly>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="fecha_alta" value="<?= $fechaAlta ?>" />
                                <input type="hidden" name="id_usuario" value="<?= $usuario->id ?? session()->get('id_usuario') ?>" />
                            <?php endif; ?>
                        </div>
                    </fieldset>

                    <!-- RESEÑA -->
                    <?php $tieneResena = !empty($resena); ?>

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
                            <textarea id="resena" name="resena" rows="6" class="form-control"><?= esc($resena) ?></textarea>
                            <small class="text-muted"><span id="charCount"><?= strlen($resena) ?></span> caracteres</small>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="btnOcultarResena">
                                <i class="bi bi-x-circle"></i> Quitar reseña
                            </button>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <div>
                            <a href="<?= base_url($esEdicion ? 'editar/listaLibros' : 'altas') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                            <?php if ($esEdicion): ?>
                                <button type="button" class="btn btn-outline-warning ms-2" id="btnRestaurar">
                                    <i class="bi bi-arrow-counterclockwise"></i> Descartar cambios
                                </button>
                            <?php else: ?>
                                <button type="reset" class="btn btn-outline-secondary ms-2" id="btnLimpiar">
                                    <i class="bi bi-x-circle"></i> Limpiar
                                </button>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn <?= $btnClass ?> btn-lg" id="btnGuardar" <?= !$esEdicion ? 'disabled' : '' ?>>
                            <i class="bi bi-save"></i> <?= $btnText ?>
                        </button>
                    </div>

                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>