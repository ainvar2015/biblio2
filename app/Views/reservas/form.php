<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-<?= isset($reserva) ? 'pencil-square' : 'plus-circle' ?> me-2"></i>
                        <?= isset($reserva) ? 'Editar Reserva #' . $reserva['id'] : 'Nueva Reserva' ?>
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

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= isset($reserva) ? base_url('reservas/actualizar/' . $reserva['id']) : base_url('reservas/guardar') ?>" method="post">
                        <?= csrf_field() ?>

                        <!-- Usuario con Choices.js -->
                        <div class="mb-3">
                            <label for="id_usuario" class="form-label fw-bold">Usuario <span class="text-danger">*</span></label>
                            <select name="id_usuario" id="id_usuario" class="form-select" required>
                                <option value="">Selecciona un usuario...</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= $usuario->id ?>" 
                                        <?= (old('id_usuario', isset($reserva) ? $reserva['id_usuario'] : '') == $usuario->id) ? 'selected' : '' ?>>
                                        <?= esc($usuario->nombre . ' ' . ($usuario->apellidos ?? '') . ' | ' . $usuario->email) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Libro con Choices.js -->
                        <div class="mb-3">
                            <label for="id_libro" class="form-label fw-bold">Libro <span class="text-danger">*</span></label>
                            <select name="id_libro" id="id_libro" class="form-select" required>
                                <option value="">Selecciona un libro...</option>
                                <?php foreach ($libros as $libro): ?>
                                    <option value="<?= $libro['id'] ?>" 
                                        <?= (old('id_libro', isset($reserva) ? $reserva['id_libro'] : '') == $libro['id']) ? 'selected' : '' ?>>
                                        <?= esc($libro['titulo'] . ' | ' . $libro['autor'] . ' | ISBN: ' . $libro['codigoISBN']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Estado (solo en edición) -->
                        <?php if (isset($reserva)): ?>
                            <div class="mb-3">
                                <label for="estado" class="form-label fw-bold">Estado</label>
                                <select name="estado" id="estado" class="form-select">
                                    <option value="pendiente" <?= $reserva['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                    <option value="prestado" <?= $reserva['estado'] === 'prestado' ? 'selected' : '' ?>>Prestado</option>
                                    <option value="devuelto" <?= $reserva['estado'] === 'devuelto' ? 'selected' : '' ?>>Devuelto</option>
                                    <option value="cancelado" <?= $reserva['estado'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_prestamo" class="form-label fw-bold">Fecha de Préstamo</label>
                                    <input type="date" name="fecha_prestamo" id="fecha_prestamo" 
                                           class="form-control" 
                                           value="<?= old('fecha_prestamo', $reserva['fecha_prestamo']) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_devolucion" class="form-label fw-bold">Fecha de Devolución</label>
                                    <input type="date" name="fecha_devolucion" id="fecha_devolucion" 
                                           class="form-control" 
                                           value="<?= old('fecha_devolucion', $reserva['fecha_devolucion']) ?>">
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Observaciones -->
                        <div class="mb-4">
                            <label for="observaciones" class="form-label fw-bold">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" rows="3" class="form-control" 
                                      placeholder="Notas adicionales sobre la reserva..."><?= old('observaciones', isset($reserva) ? $reserva['observaciones'] : '') ?></textarea>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-lg me-1"></i> <?= isset($reserva) ? 'Actualizar' : 'Crear Reserva' ?>
                            </button>
                            <a href="<?= base_url('reservas') ?>" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Choices.js para Usuarios
    const usuarioSelect = new Choices('#id_usuario', {
        searchEnabled: true,
        searchPlaceholderValue: 'Buscar usuario por nombre o email...',
        itemSelectText: 'Seleccionar',
        noResultsText: 'No se encontraron usuarios',
        noChoicesText: 'No hay más opciones',
        placeholder: true,
        placeholderValue: 'Selecciona un usuario...',
        allowHTML: false,
        shouldSort: true,
        fuseOptions: {
            threshold: 0.3,
            distance: 100,
            includeScore: true
        }
    });

    // Inicializar Choices.js para Libros
    const libroSelect = new Choices('#id_libro', {
        searchEnabled: true,
        searchPlaceholderValue: 'Buscar libro por título, autor o ISBN...',
        itemSelectText: 'Seleccionar',
        noResultsText: 'No se encontraron libros',
        noChoicesText: 'No hay más opciones',
        placeholder: true,
        placeholderValue: 'Selecciona un libro...',
        allowHTML: false,
        shouldSort: true,
        fuseOptions: {
            threshold: 0.3,
            distance: 100,
            includeScore: true
        }
    });
});
</script>