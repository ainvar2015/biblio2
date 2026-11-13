<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Alta de libros</h5>
                </div>
                <div class="card-body">
                    <form id="formLibro" method="post" action="<?= base_url(); ?>altas/guardar/libro" novalidate enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <!-- Sección: Información Principal -->
                        <fieldset class="mb-4 border rounded p-3">
                            <legend class="float-none w-auto px-2 h6 text-primary">
                                <i class="bi bi-book"></i> Información Principal
                            </legend>
                            <!-- Sección: Biblioteca -->
                            <?php if ($esSuperadmin && !empty($bibliotecas) && count($bibliotecas) > 1): ?>
                                <fieldset class="mb-4 border rounded p-3">
                                    <legend class="float-none w-auto px-2 h6 text-primary">
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
                                                    <option value="<?= $bib['id'] ?>" <?= ($idBibliotecaUsuario == $bib['id']) ? 'selected' : '' ?>>
                                                        <?= esc($bib['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback">Selecciona una biblioteca</div>
                                        </div>
                                    </div>
                                </fieldset>
                            <?php else: ?>
                                <!-- Solo una biblioteca: campo oculto -->
                                <input type="hidden" name="id_biblioteca" value="<?= $bibliotecas[0]->id ?? $idBibliotecaUsuario; ?>" />
                            <?php endif; ?>
                            <div class="row align-items-start">
                                <!-- ISBN -->
                                <div class="col-md-4 mb-3">
                                    <label for="codigoISBN" class="form-label fw-bold">
                                        ISBN <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input id="codigoISBN" name="codigoISBN"
                                            value="<?= set_value('codigoISBN'); ?>"
                                            type="text"
                                            class="form-control"
                                            placeholder="9788437604947"
                                            required>
                                        <button class="btn btn-outline-primary" type="button" id="btnBuscarISBN">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">ISBN no válido</div>
                                    <small class="text-muted">Sin guiones. Ej: 9788437604947</small>
                                </div>

                                <!-- Portada con subida manual -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Portada</label>
                                    <div id="portadaWrapper" style="position: relative;">
                                        <!-- Vista previa -->
                                        <div id="portadaContainer" style="height: 200px; border: 2px dashed #dee2e6; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; cursor: pointer; overflow: hidden;">
                                            <img id="portadaPreview" src="" alt="" style="max-height: 100%; max-width: 100%; object-fit: contain; display: none;">
                                            <div id="portadaPlaceholder" class="text-center text-muted">
                                                <i class="bi bi-image" style="font-size: 3rem;"></i>
                                                <p class="mb-0 small">Click para subir imagen<br>o arrastra aquí</p>
                                            </div>
                                        </div>

                                        <!-- Input file oculto -->
                                        <input type="file" id="portadaFile" name="portada_file" accept="image/*" style="display: none;">
                                        <input type="hidden" name="portada_url" id="portadaUrl">

                                        <!-- Botones -->
                                        <div class="mt-2 d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnSubirPortada">
                                                <i class="bi bi-upload"></i> Subir
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger d-none" id="btnQuitarPortada">
                                                <i class="bi bi-trash"></i> Quitar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Título -->
                                <div class="col-md-4 mb-3">
                                    <label for="titulo" class="form-label fw-bold">
                                        Título <span class="text-danger">*</span>
                                    </label>
                                    <input id="titulo" name="titulo"
                                        value="<?= set_value('titulo'); ?>"
                                        type="text"
                                        class="form-control"
                                        required>
                                    <div class="invalid-feedback">El título es obligatorio</div>
                                    <div id="estadoLibro" class="form-text"></div>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Sección: Clasificación -->
                        <fieldset class="mb-4 border rounded p-3">
                            <legend class="float-none w-auto px-2 h6 text-primary">
                                <i class="bi bi-tags"></i> Clasificación
                            </legend>

                            <div class="row">
                                <!-- Autor SIMPLE -->
                                <div class="col-md-10 mb-3">
                                    <label for="autor" class="form-label fw-bold">Autor <span class="text-danger">*</span></label>
                                    <select class="form-select select2-autor" id="autor" name="autor" required>
                                        <option value=""></option>
                                        <?php foreach ($autores as $autor) { ?>
                                            <option value="<?= $autor->id; ?>" <?= set_value('autor') == $autor->id ? 'selected' : ''; ?>>
                                                <?= esc($autor->nombreautor); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <div class="invalid-feedback">Selecciona un autor</div>
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalAutor">
                                        <i class="bi bi-plus-circle"></i> Nuevo
                                    </button>
                                </div>

                                <!-- Editorial SIMPLE -->
                                <div class="col-md-10 mb-3">
                                    <label for="editorial" class="form-label fw-bold">Editorial <span class="text-danger">*</span></label>
                                    <select class="form-select select2-editorial" id="editorial" name="editorial" required>
                                        <option value=""></option>
                                        <?php foreach ($editoriales as $editorial) { ?>
                                            <option value="<?= $editorial->id; ?>" <?= set_value('editorial') == $editorial->id ? 'selected' : ''; ?>>
                                                <?= esc($editorial->nombreeditorial); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <div class="invalid-feedback">Selecciona una editorial</div>
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalEditorial">
                                        <i class="bi bi-plus-circle"></i> Nuevo
                                    </button>
                                </div>

                                <!-- Género SIMPLE -->
                                <div class="col-md-10 mb-3">
                                    <label for="genero" class="form-label fw-bold">Género</label>
                                    <select class="form-select select2-genero" id="genero" name="genero">
                                        <option value=""></option>
                                        <?php foreach ($generos as $genero) { ?>
                                            <option value="<?= $genero->id; ?>" <?= set_value('genero') == $genero->id ? 'selected' : ''; ?>>
                                                <?= esc($genero->genero); ?>
                                            </option>
                                        <?php } ?>
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
                            <legend class="float-none w-auto px-2 h6 text-primary">
                                <i class="bi bi-calendar"></i> Detalles de Edición
                            </legend>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="n_paginas" class="form-label fw-bold">Nº de páginas</label>
                                    <input id="n_paginas" name="n_paginas"
                                        value="<?= set_value('n_paginas'); ?>"
                                        type="number"
                                        class="form-control"
                                        min="1"
                                        placeholder="Ej: 320">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ano_edicio" class="form-label fw-bold">Año de edición</label>
                                    <input id="ano_edicio" name="ano_edicio"
                                        type="number"
                                        value="<?= set_value('ano_edicio'); ?>"
                                        class="form-control"
                                        min="1800"
                                        max="<?= date('Y') + 1; ?>"
                                        placeholder="Ej: 2017">
                                </div>
                            </div>
                        </fieldset>

                        <!-- BOTÓN RESEÑA - SIEMPRE VISIBLE -->
                        <div class="row mb-3" id="filaBtnResena">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-outline-secondary" id="btnMostrarResena">
                                    <i class="bi bi-chat-square-text"></i> Añadir reseña del libro
                                </button>
                            </div>
                        </div>

                        <!-- RESEÑA - OCULTO INICIALMENTE -->
                        <div class="row mb-3 d-none" id="filaResena">
                            <div class="col-lg-12">
                                <label for="resena" class="form-label fw-bold">Reseña del libro</label>
                                <textarea id="resena" name="resena" rows="6" class="form-control"><?= set_value('resena'); ?></textarea>
                                <small class="text-muted"><span id="charCount">0</span> caracteres</small>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="btnOcultarResena">
                                    <i class="bi bi-x-circle"></i> Quitar reseña
                                </button>
                            </div>
                        </div>

                        <!-- Campos ocultos -->
                        <input type="hidden" name="fecha_alta" value="<?= date('Y-m-d'); ?>" />
                        <input type="hidden" name="id_usuario" value="<?= $usuario->id; ?>" />

                        <!-- Botones -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <button type="reset" class="btn btn-outline-secondary" id="btnLimpiar">
                                <i class="bi bi-x-circle"></i> Limpiar
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg" id="btnGuardar" disabled>
                                <i class="bi bi-save"></i> Guardar Libro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ESTE SCRIPT SE EJECUTARÁ DESPUÉS DE QUE jQuery ESTÉ CARGADO -->


<style>
    .is-valid {
        border-color: #198754 !important;
    }

    .is-invalid {
        border-color: #dc3545 !important;
    }

    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }

    #portadaContainer:hover {
        border-color: #0d6efd !important;
        background: #e7f1ff !important;
    }
</style>