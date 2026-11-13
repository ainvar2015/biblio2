<?php if ($libro->prestamo == 0): ?>
    <!-- Formulario PRÉSTAMO -->
     <?= form_open('altas/guardar/prestamo/mod', 'id="guardaPrestamo"') ?>
        <input type="hidden" name="id_libro" value="<?= $libro->id ?>">
        <input type="hidden" name="titulo_libro" value="<?= esc($libro->titulo) ?>">
        <input type="hidden" name="isbn_libro" value="<?= esc($libro->codigoISBN) ?>">

        <div class="mb-3">
            <label for="nombreusuario">Nombre del Usuario</label>
            <input type="text" name="nombre_usuario" id="nombreusuario" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="fecha_salida">Fecha de préstamo</label>
            <input type="date" name="fecha_salida" id="fecha_salida" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Préstamo</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <?= form_close() ?>

<?php else: ?>
    <!-- Formulario DEVOLUCIÓN -->
     <?= form_open('altas/guardar/actualiza/mod', 'id="guardadevolucion"') ?>

     <input type="hidden" name="id_prestamo" value="<?= $prestamo->id ?>">
        <input type="hidden" name="id_libro" value="<?= $libro->id ?>">
        <input type="hidden" name="titulo_libro" value="<?= esc($libro->titulo) ?>">

        <div class="mb-3">
            <label for="nombreusuario">Nombre del Usuario</label>
            <input type="text" name="nombreusuario" id="nombreusuario" class="form-control" value="<?= esc($prestamo->usuario) ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="fecha_salida">Fecha de préstamo</label>
            <input type="date" name="fecha_salida" id="fecha_salida" class="form-control" value="<?= esc($prestamo->fecha_salida) ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="fecha_entrada">Fecha de devolución</label>
            <input type="date" name="fecha_entrada" id="fecha_entrada" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Préstamo</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <?= form_close() ?>
<?php endif; ?>
