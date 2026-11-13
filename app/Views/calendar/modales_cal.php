<!-- =========================
     Modal para ver evento
============================= -->
<div class="modal fade" id="events-modal" tabindex="-1" aria-labelledby="eventsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="eventsModalLabel">Detalles del Evento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      
      <div class="modal-body">
        <div id="eventlist"></div>
      </div>
      
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="deleteViewBtn">
          <i class="fa fa-trash"></i> Eliminar
        </button>
        <button type="button" class="btn btn-primary" id="openEditBtn">
          <i class="fa fa-edit"></i> Editar
        </button> -->
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>

<!-- =========================
     Modal para añadir evento
============================= -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formAddEvent" class="modal-content">
      <input type="hidden" id="addEventCreatedAt" value="<?= date('Y-m-d') ?>">
      <div class="modal-header">
        <h5 class="modal-title">Añadir Evento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="addEventTitle" class="form-label">Título</label>
          <input type="text" class="form-control" id="addEventTitle" required>
        </div>
        <div class="mb-3">
          <label for="addEventStart" class="form-label">Inicio</label>
          <input type="text" class="form-control" id="addEventStart" required>
        </div>
        <div class="mb-3">
          <label for="addEventEnd" class="form-label">Fin</label>
          <input type="text" class="form-control" id="addEventEnd">
        </div>
        <div class="mb-3">
          <label for="addEventDesc" class="form-label">Descripción</label>
          <textarea class="form-control" id="addEventDesc"></textarea>
        </div>
        <div class="mb-3">
          <label for="addEventTipo" class="form-label">Tipo</label>
          <select class="form-select" id="addEventTipo">
            <option value="">Seleccione</option>
            <option value="reunion">Reunión</option>
            <option value="trabajo">Trabajo</option>
            <option value="cumpleanos">Cumpleaños</option>
            <option value="personal">Personal</option>
            <option value="importante">Importante</option>
            <option value="otro">Otro</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="addEventUrl" class="form-label">URL</label>
          <input type="text" class="form-control" id="addEventUrl">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- =========================
     Modal para editar evento
============================= -->
<div class="modal fade" id="editEventModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formEditEvent" class="modal-content">
      <input type="hidden" id="editEventId">
      <div class="modal-header">
        <h5 class="modal-title">Editar Evento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="editEventTitle" class="form-label">Título</label>
          <input type="text" class="form-control" id="editEventTitle" required>
        </div>
        <div class="mb-3">
          <label for="editEventStart" class="form-label">Inicio</label>
          <input type="text" class="form-control" id="editEventStart" required>
        </div>
        <div class="mb-3">
          <label for="editEventEnd" class="form-label">Fin</label>
          <input type="text" class="form-control" id="editEventEnd">
        </div>
        <div class="mb-3">
          <label for="editEventDesc" class="form-label">Descripción</label>
          <textarea class="form-control" id="editEventDesc"></textarea>
        </div>
        <div class="mb-3">
          <label for="editEventTipo" class="form-label">Tipo</label>
          <select class="form-select" id="editEventTipo">
            <option value="">Seleccione</option>
            <option value="reunion">Reunión</option>
            <option value="trabajo">Trabajo</option>
            <option value="cumpleanos">Cumpleaños</option>
            <option value="personal">Personal</option>
            <option value="importante">Importante</option>
            <option value="otro">Otro</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="editEventUrl" class="form-label">URL</label>
          <input type="text" class="form-control" id="editEventUrl">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="deleteEventBtn" class="btn btn-outline-danger">Eliminar</button>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>
