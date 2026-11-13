<?php

/** @var string $tipo */
switch ($tipo) {
    case 'autores':
        $singular = 'autor';
        $tablaId = 'tableAutores';
        $jsFile = 'listado-generico.js';
        break;
    case 'editoriales':
        $singular = 'editorial';
        $tablaId = 'tableEditoriales';
        $jsFile = 'listado-generico.js';
        break;
    case 'generos':
        $singular = 'genero';
        $tablaId = 'tableGeneros';
        $jsFile = 'listado-generico.js';
        break;
    case 'libros':
        $singular = 'libro';
        $tablaId = 'tableLibros';
        $jsFile = 'listado-libros.js';
        break;
    case 'prestamos':
        $singular = 'prestamo';
        $tablaId = 'tablePrestamos';
        $jsFile = 'listado-prestamos.js';
        break;
    default:
        $singular = '';
        $tablaId = 'datatable';
        $jsFile = 'listado-generico.js';
        break;
}

// Para libros y préstamos: usar server-side
// Para otros: datos locales
$usarServerSide = in_array($tipo, ['libros', 'prestamos']);
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-table"></i> <?= $tituloListado ?></h5>
                    <?php if ($tipo !== 'prestamos' && $singular): ?>
                        <a href="<?= base_url("altas/nuevo/$singular") ?>" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-lg"></i> Nuevo <?= ucfirst($singular) ?>
                        </a>
                    <?php endif; ?>
                </div>
                <!-- Después del card-header, antes del panel de filtros de libros -->
                <?php if ($tipo === 'libros' && $esSuperadmin && !empty($bibliotecas)): ?>
                    <div class="card-body border-bottom bg-light">
                        <form action="<?= base_url('editar/listaLibros') ?>" method="get" class="d-flex align-items-center gap-3 flex-wrap" id="formBiblioteca">
                            <label class="fw-bold mb-0 text-primary"><i class="bi bi-building me-1"></i> Biblioteca:</label>
                            <select name="biblioteca" class="form-select w-auto" id="selectBiblioteca" onchange="this.form.submit()">
                                <option value="">Todas las bibliotecas</option>
                                <?php foreach ($bibliotecas as $bib): ?>
                                    <option value="<?= $bib['id'] ?>" <?= (request()->getGet('biblioteca') == $bib['id']) ? 'selected' : '' ?>>
                                        <?= esc($bib['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (request()->getGet('biblioteca')): ?>
                                <a href="<?= base_url('editar/listaLibros') ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-x-lg me-1"></i> Ver todas
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                <?php endif; ?>
                <!-- Filtros solo para libros -->
                <?php if ($tipo === 'libros'): ?>
                    <div class="card-body border-bottom" id="panelFiltros">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small">Autor</label>
                                <select id="filtroAutor" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    <?php foreach ($autoresMap as $id => $nombre): ?>
                                        <option value="<?= $id ?>"><?= esc($nombre) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Editorial</label>
                                <select id="filtroEditorial" class="form-select form-select-sm">
                                    <option value="">Todas</option>
                                    <?php foreach ($editorialesMap as $id => $nombre): ?>
                                        <option value="<?= $id ?>"><?= esc($nombre) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Género</label>
                                <select id="filtroGenero" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    <?php foreach ($generosMap as $id => $nombre): ?>
                                        <option value="<?= $id ?>"><?= esc($nombre) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Estado</label>
                                <select id="filtroEstado" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    <option value="disponible">Disponible</option>
                                    <option value="prestado">Prestado</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-outline-secondary btn-sm w-100" id="btnLimpiarFiltros">
                                    <i class="bi bi-x-circle"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="<?= $tablaId ?>"
                            class="table table-striped table-hover align-middle"
                            style="width:100%"
                            data-tipo="<?= $tipo ?>"
                            <?= !$usarServerSide ? 'data-local="true"' : '' ?>>
                            <thead class="table-dark">
                                <tr>
                                    <?php if ($tipo === 'libros'): ?>
                                        <th width="40"><input type="checkbox" class="form-check-input" id="selectAll"></th>
                                        <th width="60">Portada</th>
                                        <th>Título</th>
                                        <th>Autor</th>
                                        <th>Editorial</th>
                                        <th>Año</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    <?php elseif ($tipo === 'autores'): ?>
                                        <th>Nombre</th>
                                        <th>Lugar Nacimiento</th>
                                        <th>Fecha Nacimiento</th>
                                        <th class="text-center">Acciones</th>
                                    <?php elseif ($tipo === 'editoriales'): ?>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th class="text-center">Acciones</th>
                                    <?php elseif ($tipo === 'generos'): ?>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th class="text-center">Acciones</th>
                                    <?php elseif ($tipo === 'prestamos'): ?>
                                        <th>Libro</th>
                                        <th>Usuario</th>
                                        <th>Fecha Salida</th>
                                        <th>Fecha Devolución</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <?php if (!$usarServerSide && !empty($items)): ?>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <?php if ($tipo === 'autores'): ?>
                                                <td><?= esc($item->nombreautor) ?></td>
                                                <td><?= esc($item->lugar_nacimiento) ?></td>
                                                <td><?= $item->fecha_nacimiento ? date('d/m/Y', strtotime($item->fecha_nacimiento)) : '-' ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?= base_url("editar/autor/{$item->id}") ?>" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                                                        <button class="btn btn-danger btn-borrar" data-id="<?= $item->id ?>" data-nombre="<?= esc($item->nombreautor) ?>" data-tipo="autores"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                </td>
                                            <?php elseif ($tipo === 'editoriales'): ?>
                                                <td><?= esc($item->nombreeditorial) ?></td>
                                                <td><?= esc($item->descripcion) ?></td>
                                                <td class="text-center">

                                                </td>
                                            <?php elseif ($tipo === 'generos'): ?>
                                                <td><?= esc($item->genero) ?></td>
                                                <td><?= esc($item->descripcion) ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?= base_url("editar/genero/{$item->id}") ?>" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                                                        <button class="btn btn-danger btn-borrar" data-id="<?= $item->id ?>" data-nombre="<?= esc($item->genero) ?>" data-tipo="generos"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Borrar (común) -->
<div class="modal fade" id="modalBorrar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirmar borrado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Seguro que deseas borrar <strong id="tituloBorrar"></strong>?</p>
                <input type="hidden" id="tipoBorrar">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarBorrar"><i class="bi bi-trash"></i> Borrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Préstamo (solo libros) -->
<?php if ($tipo === 'libros'): ?>
    <!-- Modal Préstamo Mejorado -->
    <div class="modal fade" id="modalPrestamo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="bi bi-box-arrow-right"></i> Nuevo Préstamo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formPrestamoRapido">
                        <input type="hidden" id="prestamoLibroId">

                        <!-- Info del libro -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Libro</label>
                            <input type="text" class="form-control bg-light" id="prestamoLibroTitulo" readonly>
                        </div>

                        <!-- Tipo de prestatario -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de prestatario</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipoPrestatario" id="tipoSocio" value="socio" checked>
                                <label class="btn btn-outline-primary" for="tipoSocio">
                                    <i class="bi bi-person-badge"></i> Socio
                                </label>

                                <input type="radio" class="btn-check" name="tipoPrestatario" id="tipoExterno" value="externo">
                                <label class="btn btn-outline-secondary" for="tipoExterno">
                                    <i class="bi bi-person"></i> Externo
                                </label>
                            </div>
                        </div>

                        <!-- Sección SOCIO -->
                        <div id="seccionSocio" class="border rounded p-3 mb-3 bg-light">
                            <label class="form-label fw-bold">Seleccionar Socio <span class="text-danger">*</span></label>
                            <select class="form-select select2-usuarios" id="prestamoUsuario" style="width: 100%;">
                                <option value="">Buscar socio...</option>
                            </select>
                            <small class="text-muted">Escribe para buscar por nombre</small>
                        </div>

                        <!-- Sección EXTERNO (oculta inicialmente) -->
                        <div id="seccionExterno" class="border rounded p-3 mb-3 bg-light d-none">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nombre completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="externoNombre" placeholder="Ej: Juan García">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">DNI/NIE <span class="text-muted">(opcional)</span></label>
                                    <input type="text" class="form-control" id="externoDni" placeholder="12345678A">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Email <span class="text-muted">(opcional)</span></label>
                                    <input type="email" class="form-control" id="externoEmail" placeholder="ejemplo@email.com">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Teléfono <span class="text-muted">(opcional)</span></label>
                                    <input type="tel" class="form-control" id="externoTelefono" placeholder="612345678">
                                </div>
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha préstamo <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="prestamoFecha" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha devolución prevista</label>
                                <input type="date" class="form-control" id="prestamoDevolucion">
                            </div>
                        </div>

                        <!-- Notas -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Notas <span class="text-muted">(opcional)</span></label>
                            <textarea class="form-control" id="prestamoNotas" rows="2" placeholder="Observaciones sobre el préstamo..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-info" id="btnGuardarPrestamo">
                        <i class="bi bi-check-lg"></i> Registrar Préstamo
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Historial de Préstamos -->
    <div class="modal fade" id="modalHistorial" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title"><i class="bi bi-clock-history"></i> Historial de Préstamos</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 id="historialTituloLibro" class="mb-3 fw-bold text-primary"></h6>

                    <div class="table-responsive">
                        <table class="table table-sm table-striped" id="tablaHistorial">
                            <thead>
                                <tr>
                                    <th>Fecha Salida</th>
                                    <th>Prestatario</th>
                                    <th>Tipo</th>
                                    <th>Fecha Devolución</th>
                                    <th>Estado</th>
                                    <th>Días</th>
                                </tr>
                            </thead>
                            <tbody id="historialBody">
                                <!-- Se carga por AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <div id="historialVacio" class="text-center text-muted py-4 d-none">
                        <i class="bi bi-inbox fs-1"></i>
                        <p>No hay préstamos registrados para este libro</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Enviar Recordatorio -->
    <div class="modal fade" id="modalRecordatorio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="bi bi-bell"></i> Recordatorio de Devolución</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="recordatorioPrestamoId">

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Enviar recordatorio a: <strong id="recordatorioDestinatario"></strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de notificación</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="tipoRecordatorio" id="recEmail" value="email" checked>
                            <label class="btn btn-outline-primary" for="recEmail">
                                <i class="bi bi-envelope"></i> Email
                            </label>

                            <input type="radio" class="btn-check" name="tipoRecordatorio" id="recWhatsapp" value="whatsapp">
                            <label class="btn btn-outline-success" for="recWhatsapp">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mensaje</label>
                        <textarea class="form-control" id="recordatorioMensaje" rows="4">Le recordamos que tiene pendiente la devolución del libro. Por favor, pase por la biblioteca lo antes posible. Gracias.</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i>
                            Fecha prevista: <span id="recordatorioFechaPrevista" class="fw-bold"></span>
                        </small>
                        <span class="badge bg-danger" id="recordatorioDiasRetraso"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning" id="btnEnviarRecordatorio">
                        <i class="bi bi-send"></i> Enviar Recordatorio
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>