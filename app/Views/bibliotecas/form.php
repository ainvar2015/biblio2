<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <?php 
                $colorHeader = isset($biblioteca) ? esc($biblioteca['color_tema']) : '#0d6efd';
                $soloLectura = isset($soloLectura) && $soloLectura;
                ?>
                <div class="card-header text-white" style="background:<?= $colorHeader ?>">
                    <h4 class="mb-0">
                        <i class="bi bi-<?= isset($biblioteca) ? 'pencil-square' : 'plus-circle' ?> me-2"></i>
                        <?= isset($biblioteca) ? 'Editar Biblioteca' : 'Nueva Biblioteca' ?>
                        <?php if ($soloLectura): ?>
                            <span class="badge bg-warning text-dark ms-2">Solo lectura</span>
                        <?php endif; ?>
                    </h4>
                </div>
                <div class="card-body">

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= isset($biblioteca) ? base_url('bibliotecas/actualizar/' . $biblioteca['id']) : base_url('bibliotecas/guardar') ?>" method="post">
                        <?= csrf_field() ?>

                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="nombre" class="form-control form-control-lg" 
                                   value="<?= old('nombre', isset($biblioteca) ? $biblioteca['nombre'] : '') ?>" 
                                   placeholder="Nombre de la biblioteca" 
                                   <?= $soloLectura ? 'readonly' : 'required' ?>>
                        </div>

                        <!-- Dirección -->
                        <div class="mb-3">
                            <label for="direccion" class="form-label fw-bold">Dirección</label>
                            <input type="text" name="direccion" id="direccion" class="form-control" 
                                   value="<?= old('direccion', isset($biblioteca) ? $biblioteca['direccion'] : '') ?>" 
                                   placeholder="Calle, número, ciudad..."
                                   <?= $soloLectura ? 'readonly' : '' ?>>
                        </div>

                        <div class="row">
                            <!-- Teléfono -->
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label fw-bold">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" class="form-control" 
                                       value="<?= old('telefono', isset($biblioteca) ? $biblioteca['telefono'] : '') ?>" 
                                       placeholder="+34 123 456 789"
                                       <?= $soloLectura ? 'readonly' : '' ?>>
                            </div>
                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" name="email" id="email" class="form-control" 
                                       value="<?= old('email', isset($biblioteca) ? $biblioteca['email'] : '') ?>" 
                                       placeholder="biblioteca@ejemplo.com"
                                       <?= $soloLectura ? 'readonly' : '' ?>>
                            </div>
                        </div>

                        <?php if (!$soloLectura): ?>
                        <div class="row">
                            <!-- Color del tema -->
                            <div class="col-md-6 mb-3">
                                <label for="color_tema" class="form-label fw-bold">Color del tema</label>
                                <div class="input-group">
                                    <input type="color" name="color_tema" id="color_tema" class="form-control form-control-color" 
                                           value="<?= old('color_tema', isset($biblioteca) ? $biblioteca['color_tema'] : '#0d6efd') ?>" 
                                           style="height: 38px;">
                                    <input type="text" class="form-control" id="color_hex" 
                                           value="<?= old('color_tema', isset($biblioteca) ? $biblioteca['color_tema'] : '#0d6efd') ?>" 
                                           readonly>
                                </div>
                                <small class="text-muted">Color principal para identificar esta biblioteca</small>
                            </div>
                            <!-- Activa (solo superadmin) -->
                            <?php if (isset($esSuperadmin) && $esSuperadmin): ?>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold d-block">Estado</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="activa" id="activa" value="1" 
                                           <?= old('activa', isset($biblioteca) ? $biblioteca['activa'] : 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="activa">
                                        Biblioteca activa
                                    </label>
                                </div>
                                <small class="text-muted">Las bibliotecas inactivas no se pueden seleccionar</small>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Preview del color -->
                        <div class="mb-4 p-3 rounded" id="colorPreview" style="background:<?= old('color_tema', isset($biblioteca) ? $biblioteca['color_tema'] : '#0d6efd') ?>; color:white; text-align:center;">
                            <strong>Vista previa del color</strong>
                        </div>
                        <?php endif; ?>

                        <!-- Botones -->
                        <div class="d-flex gap-2">
                            <?php if (!$soloLectura): ?>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-lg me-1"></i> <?= isset($biblioteca) ? 'Actualizar' : 'Crear Biblioteca' ?>
                                </button>
                            <?php endif; ?>
                            <a href="<?= base_url('bibliotecas') ?>" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-1"></i> Volver
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!isset($soloLectura) || !$soloLectura): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('color_tema');
    const colorHex = document.getElementById('color_hex');
    const colorPreview = document.getElementById('colorPreview');

    function updateColor() {
        const color = colorInput.value;
        colorHex.value = color;
        colorPreview.style.background = color;
    }

    colorInput.addEventListener('input', updateColor);
});
</script>
<?php endif; ?>