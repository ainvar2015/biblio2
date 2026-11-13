<?php
// Determinar modo
$esEdicion = !empty($prestamo);
$prestamo = $esEdicion ? $prestamo : null;
$titulo = $esEdicion ? 'Editar Préstamo' : 'Nuevo Préstamo';
$btnText = $esEdicion ? 'Guardar Cambios' : 'Registrar Préstamo';
$headerClass = $esEdicion ? 'bg-warning' : 'bg-info';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            
            <!-- Alertas -->
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

            <div class="card shadow-lg border-0">
                <div class="card-header <?= $headerClass ?> text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            <?= $titulo ?>
                        </h4>
                        <a href="<?= base_url('prestamos') ?>" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <?= form_open($esEdicion ? 'prestamos/actualizar/' . $prestamo['id'] : 'prestamos/guardar', ['id' => 'formPrestamo', 'class' => 'needs-validation', 'novalidate' => true]) ?>
                    
                        <?php if ($esEdicion): ?>
                            <input type="hidden" name="id" value="<?= $prestamo['id'] ?>">
                        <?php endif; ?>

                        <!-- Biblioteca (solo superadmin) -->
                        <?php if ($esSuperadmin): ?>
                            <div class="mb-3">
                                <label for="id_biblioteca" class="form-label fw-bold">Biblioteca <span class="text-danger">*</span></label>
                                <select name="id_biblioteca" id="id_biblioteca" class="form-select form-select-lg" required>
                                    <option value="">Selecciona una biblioteca...</option>
                                    <?php foreach ($bibliotecas as $bib): ?>
                                        <option value="<?= $bib['id'] ?>" 
                                            <?= (old('id_biblioteca', $esEdicion ? $prestamo['id_biblioteca'] : $miBiblioteca) == $bib['id']) ? 'selected' : '' ?>>
                                            <?= esc($bib['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="id_biblioteca" value="<?= $miBiblioteca ?>">
                        <?php endif; ?>

                        <!-- Libro -->
                        <div class="mb-3">
                            <label for="id_libro" class="form-label fw-bold">Libro <span class="text-danger">*</span></label>
                            <select name="id_libro" id="id_libro" class="form-select select2-libros" required <?= $esEdicion ? 'disabled' : '' ?>>
                                <option value="">Seleccionar libro...</option>
                                <?php foreach ($libros as $libro): ?>
                                    <option value="<?= $libro['id'] ?>" 
                                        data-isbn="<?= esc($libro['codigoISBN'] ?? '') ?>"
                                        data-titulo="<?= esc($libro['titulo']) ?>"
                                        <?= (old('id_libro', $esEdicion ? $prestamo['id_libro'] : '') == $libro['id']) ? 'selected' : '' ?>>
                                        <?= esc($libro['titulo']) ?> (ISBN: <?= esc($libro['codigoISBN'] ?? 'N/A') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($esEdicion): ?>
                                <input type="hidden" name="id_libro" value="<?= $prestamo['id_libro'] ?>">
                                <small class="text-muted">El libro no se puede cambiar en edición</small>
                            <?php endif; ?>
                        </div>

                        <!-- Tipo de prestatario -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de prestatario <span class="text-danger">*</span></label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipo_prestatario" id="tipoSocio" value="socio" 
                                    <?= (old('tipo_prestatario', $esEdicion ? $prestamo['tipo_prestatario'] : 'socio') === 'socio') ? 'checked' : '' ?> 
                                    <?= $esEdicion ? 'disabled' : '' ?>>
                                <label class="btn btn-outline-primary" for="tipoSocio">
                                    <i class="bi bi-person-badge"></i> Socio
                                </label>

                                <input type="radio" class="btn-check" name="tipo_prestatario" id="tipoExterno" value="externo" 
                                    <?= (old('tipo_prestatario', $esEdicion ? $prestamo['tipo_prestatario'] : '') === 'externo') ? 'checked' : '' ?>
                                    <?= $esEdicion ? 'disabled' : '' ?>>
                                <label class="btn btn-outline-secondary" for="tipoExterno">
                                    <i class="bi bi-person"></i> Externo
                                </label>
                            </div>
                            <?php if ($esEdicion): ?>
                                <input type="hidden" name="tipo_prestatario" value="<?= $prestamo['tipo_prestatario'] ?>">
                            <?php endif; ?>
                        </div>

                        <!-- Sección SOCIO -->
                        <div id="seccionSocio" class="border rounded p-3 mb-3 bg-light">
                            <label for="id_usuario" class="form-label fw-bold">Socio <span class="text-danger">*</span></label>
                            <select name="id_usuario" id="id_usuario" class="form-select select2-socios" style="width: 100%;">
                                <option value="">Seleccionar socio...</option>
                                <?php foreach ($socios as $socio): ?>
                                    <option value="<?= $socio->id ?>" 
                                        <?= (old('id_usuario', $esEdicion ? $prestamo['id_usuario'] : '') == $socio->id) ? 'selected' : '' ?>>
                                        <?= esc($socio->nombre . ' ' . ($socio->apellidos ?? '')) ?> (DNI: <?= esc($socio->dni ?? 'N/A') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Sección EXTERNO (oculta inicialmente) -->
                        <div id="seccionExterno" class="border rounded p-3 mb-3 bg-light d-none">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre_externo" class="form-label fw-bold">Nombre completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre_externo" name="nombre_externo" 
                                        value="<?= old('nombre_externo', $esEdicion ? $prestamo['nombre_externo'] : '') ?>"
                                        placeholder="Ej: Juan García">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="dni_externo" class="form-label fw-bold">DNI/NIE <span class="text-muted">(opcional)</span></label>
                                    <input type="text" class="form-control" id="dni_externo" name="dni_externo" 
                                        value="<?= old('dni_externo', $esEdicion ? $prestamo['dni_externo'] : '') ?>"
                                        placeholder="12345678A">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email_externo" class="form-label fw-bold">Email <span class="text-muted">(opcional)</span></label>
                                    <input type="email" class="form-control" id="email_externo" name="email_externo" 
                                        value="<?= old('email_externo', $esEdicion ? $prestamo['email_externo'] : '') ?>"
                                        placeholder="ejemplo@email.com">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telefono_externo" class="form-label fw-bold">Teléfono <span class="text-muted">(opcional)</span></label>
                                    <input type="tel" class="form-control" id="telefono_externo" name="telefono_externo" 
                                        value="<?= old('telefono_externo', $esEdicion ? $prestamo['telefono_externo'] : '') ?>"
                                        placeholder="612345678">
                                </div>
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_prestamo" class="form-label fw-bold">Fecha préstamo <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_prestamo" name="fecha_prestamo" 
                                    value="<?= old('fecha_prestamo', $esEdicion ? $prestamo['fecha_prestamo'] : date('Y-m-d')) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_devolucion" class="form-label fw-bold">Fecha devolución prevista <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_devolucion" name="fecha_devolucion" 
                                    value="<?= old('fecha_devolucion', $esEdicion ? $prestamo['fecha_devolucion'] : date('Y-m-d', strtotime('+15 days'))) ?>" required>
                            </div>
                        </div>

                        <!-- Notas -->
                        <div class="mb-3">
                            <label for="notas" class="form-label fw-bold">Notas <span class="text-muted">(opcional)</span></label>
                            <textarea class="form-control" id="notas" name="notas" rows="2" placeholder="Observaciones sobre el préstamo..."><?= old('notas', $esEdicion ? $prestamo['notas'] : '') ?></textarea>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="<?= base_url('prestamos') ?>" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-check-lg"></i> <?= $btnText ?>
                            </button>
                        </div>

                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle entre Socio y Externo
    const tipoSocio = document.getElementById('tipoSocio');
    const tipoExterno = document.getElementById('tipoExterno');
    const seccionSocio = document.getElementById('seccionSocio');
    const seccionExterno = document.getElementById('seccionExterno');
    const selectSocio = document.getElementById('id_usuario');
    
    function toggleTipo() {
        if (tipoSocio.checked) {
            seccionSocio.classList.remove('d-none');
            seccionExterno.classList.add('d-none');
            selectSocio.setAttribute('required', 'required');
            document.getElementById('nombre_externo').removeAttribute('required');
        } else {
            seccionSocio.classList.add('d-none');
            seccionExterno.classList.remove('d-none');
            selectSocio.removeAttribute('required');
            document.getElementById('nombre_externo').setAttribute('required', 'required');
        }
    }
    
    tipoSocio.addEventListener('change', toggleTipo);
    tipoExterno.addEventListener('change', toggleTipo);
    
    // Estado inicial
    toggleTipo();
    
    // Inicializar Select2 para socios
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2-socios').select2({
            placeholder: 'Buscar socio...',
            allowClear: true,
            language: 'es'
        });
        
        $('.select2-libros').select2({
            placeholder: 'Buscar libro...',
            allowClear: true,
            language: 'es'
        });
    }
});
</script>