<!-- Modal Autor -->
<div class="modal fade" id="modalAutor" tabindex="-1" aria-labelledby="modalAutorLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <?= form_open('altas/guardar/autor', ['id' => 'formAutor', 'class' => 'form_alta', 'data-target-select' => 'autor']) ?>
        <div class="modal-header">
          <h5 class="modal-title" id="modalAutorLabel">Añadir Autor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="nombreautor" class="form-label">Nombre del Autor</label>
            <input type="text" class="form-control" id="nombreautor" name="nombreautor" required>
          </div>
          <div class="mb-3">
            <label for="lugar_nacimiento" class="form-label">Lugar de Nacimiento</label>
            <input type="text" class="form-control" id="lugar_nacimiento" name="lugar_nacimiento">
          </div>
          <div class="row g-3">
            <div class="col">
              <label for="fecha_nacimiento" class="form-label">Fecha Nacimiento</label>
              <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
            </div>
            <div class="col">
              <label for="fecha_muerte" class="form-label">Fecha Fallecimiento</label>
              <input type="date" class="form-control" id="fecha_muerte" name="fecha_muerte">
            </div>
          </div>
          <div class="mb-3 mt-3">
            <label for="biografia" class="form-label">Biografía</label>
            <textarea class="form-control" id="biografia" name="biografia" rows="4"></textarea>
          </div>
          <input type="hidden" name="fecha_alta" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar Autor</button>
        </div>
      <?= form_close() ?>
    </div>
  </div>
</div>
<!-- Modal Editorial -->
<div class="modal fade" id="modalEditorial" tabindex="-1" aria-labelledby="modalEditorialLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <?= form_open('altas/guardar/editorial', ['id' => 'formEditorial', 'class' => 'form_alta', 'data-target-select' => 'editorial']) ?>
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditorialLabel">Añadir Editorial</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="nombreeditorial" class="form-label">Nombre Editorial</label>
            <input type="text" class="form-control" id="nombreeditorial" name="nombreeditorial" required>
          </div>
          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
          </div>
            <input type="hidden" class="form-control" id="fecha_alta" name="fecha_alta" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar Editorial</button>
        </div>
      <?= form_close() ?>
    </div>
  </div>
</div>
<!-- Modal Género -->
<div class="modal fade" id="modalGenero" tabindex="-1" aria-labelledby="modalGeneroLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <?= form_open('altas/guardar/genero', ['id' => 'formGenero', 'class' => 'form_alta', 'data-target-select' => 'genero']) ?>
      <input type="hidden" name="fecha_alta" value="<?= date('Y-m-d') ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="modalGeneroLabel">Añadir Género</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="nombregenero" class="form-label">Nombre del Género</label>
            <input type="text" class="form-control" id="nombregenero" name="nombregenero" required>
          </div>
          <div class="mb-3">
            <label for="descripcionGen" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcionGen" name="descripcionGen" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar Género</button>
        </div>
      <?= form_close() ?>
    </div>
  </div>
</div>
<!-- Modal Préstamo -->
<div id="modalPrestamo" class="modal fade" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title" id="prestamoTitulo"></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body" id="prestamoBody">
        <div class="text-center py-5">
          <i class="fa fa-spinner fa-spin fa-2x"></i>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- Modal Borrar Usuario -->
<div class="modal fade" id="modalBorrarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger"></i>
                    Confirmar borrado
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>
                    ¿Seguro que deseas borrar al usuario
                    <strong id="nombreUsuario"></strong>?
                </p>
                <p class="text-muted mb-0">
                    Esta acción no se puede deshacer.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="button"
                        class="btn btn-danger"
                        id="btnConfirmarBorrado">
                    <i class="bi bi-trash"></i>
                    Borrar
                </button>
            </div>

        </div>
    </div>
</div>
<!-- Modal Confirmación Borrado -->
<div class="modal fade" id="modalConfirmBorrar" tabindex="-1" aria-labelledby="modalConfirmBorrarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalConfirmBorrarLabel">Confirmar Borrado</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p id="textoBorrar">¿Seguro que quieres borrar este elemento?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmaBorrar">Borrar</button>
      </div>
    </div>
  </div>
</div>


