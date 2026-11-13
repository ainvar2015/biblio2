<div class="alert alert-warning">
    <strong>Debug:</strong> 
    esSuperadmin = <?= var_export($esSuperadmin, true) ?> | 
    bibliotecas count = <?= count($bibliotecas ?? []) ?> | 
    bibliotecaActiva = <?= var_export($bibliotecaActiva ?? null, true) ?>
</div>
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

<style>
  #calendar { max-width: 95%; margin: 0 auto; }
  .fc-event { cursor: grab; }
  #external-events { max-width: 200px; }
  .external-event { margin: 5px 0; padding: 5px 10px; background: #0d6efd; color: #fff; border-radius: 4px; cursor: grab; }
</style>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            
            <!-- Selector de biblioteca para superadmin -->
            <?php if ($esSuperadmin && !empty($bibliotecas)): ?>
            <div class="row mb-3">
                <div class="col-md-6 col-lg-4">
                    <div class="card border-primary">
                        <div class="card-body d-flex align-items-center gap-3">
                            <label class="fw-bold mb-0 text-primary">
                                <i class="bi bi-building me-1"></i> Biblioteca:
                            </label>
                            <select id="selectBibliotecaCalendario" class="form-select">
                                <option value="todas">Todas las bibliotecas</option>
                                <?php foreach ($bibliotecas as $bib): ?>
                                    <option value="<?= $bib['id'] ?>" <?= ($bibliotecaActiva == $bib['id']) ? 'selected' : '' ?>>
                                        <?= esc($bib['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Info de biblioteca para usuarios normales -->
            <?php if (!$esSuperadmin && !empty($bibliotecaActiva)): ?>
                <input type="hidden" id="bibliotecaActiva" value="<?= $bibliotecaActiva ?>">
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4><i class="bi bi-calendar-event"></i> Calendario de Eventos</h4>
                <div class="btn-group">
                    <button class="btn btn-outline-primary" id="btnPrev">&laquo; Anterior</button>
                    <button class="btn btn-outline-secondary" id="btnToday">Hoy</button>
                    <button class="btn btn-outline-primary" id="btnNext">Siguiente &raquo;</button>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-3 flex-wrap gap-2">
                <div class="btn-group">
                    <button class="btn btn-outline-secondary" data-view="dayGridYear">Año</button>
                    <button class="btn btn-outline-secondary active" data-view="dayGridMonth">Mes</button>
                    <button class="btn btn-outline-secondary" data-view="timeGridWeek">Semana</button>
                    <button class="btn btn-outline-secondary" data-view="timeGridDay">Día</button>
                </div>
            </div>

            <div id="calendar"></div>

        </div>
    </div>
</div>

<!-- Modal para crear/editar evento -->
<div class="modal fade" id="modalEvento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-calendar-plus"></i> <span id="modalTitulo">Nuevo Evento</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEvento">
                    <input type="hidden" id="eventoId" name="id">
                    
                    <?php if ($esSuperadmin): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Biblioteca</label>
                        <select name="id_biblioteca" id="selectBibEvento" class="form-select" required>
                            <?php foreach ($bibliotecas as $bib): ?>
                                <option value="<?= $bib['id'] ?>" <?= ($bibliotecaActiva == $bib['id']) ? 'selected' : '' ?>>
                                    <?= esc($bib['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="id_biblioteca" value="<?= $bibliotecaActiva ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tituloEvento" name="titulo" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea class="form-control" id="descEvento" name="descripcion" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Inicio <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="startEvento" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Fin</label>
                            <input type="datetime-local" class="form-control" id="endEvento" name="fecha_fin">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Color</label>
                        <input type="color" class="form-control form-control-color" id="colorEvento" name="color" value="#3788d8">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="btnEliminarEvento">Eliminar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarEvento">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const modalEvento = new bootstrap.Modal(document.getElementById('modalEvento'));
    
    // Obtener biblioteca seleccionada
    const selectBib = document.getElementById('selectBibliotecaCalendario');
    const bibliotecaActiva = document.getElementById('bibliotecaActiva')?.value || 
                             (selectBib ? selectBib.value : 'todas');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 'auto',
        editable: true,
        selectable: true,
        
        // Cargar eventos con filtro de biblioteca
        events: function(fetchInfo, successCallback, failureCallback) {
            const params = new URLSearchParams();
            const bibActual = selectBib ? selectBib.value : bibliotecaActiva;
            if (bibActual && bibActual !== 'todas') {
                params.append('biblioteca', bibActual);
            }
            
            fetch(window.BASE_URL + 'eventos/listar?' + params.toString())
                .then(res => res.json())
                .then(data => successCallback(data))
                .catch(err => failureCallback(err));
        },
        
        // Click en evento existente → editar
        eventClick: function(info) {
            const evento = info.event;
            document.getElementById('eventoId').value = evento.id;
            document.getElementById('tituloEvento').value = evento.title;
            document.getElementById('descEvento').value = evento.extendedProps.descripcion || '';
            document.getElementById('startEvento').value = formatDateTimeLocal(evento.start);
            document.getElementById('endEvento').value = evento.end ? formatDateTimeLocal(evento.end) : '';
            document.getElementById('colorEvento').value = evento.backgroundColor || '#3788d8';
            
            // Seleccionar biblioteca del evento si es superadmin
            const selectBibEvento = document.getElementById('selectBibEvento');
            if (selectBibEvento && evento.extendedProps.id_biblioteca) {
                selectBibEvento.value = evento.extendedProps.id_biblioteca;
            }
            
            document.getElementById('modalTitulo').textContent = 'Editar Evento';
            document.getElementById('btnEliminarEvento').style.display = 'inline-block';
            modalEvento.show();
        },
        
        // Seleccionar rango → crear nuevo
        select: function(info) {
            document.getElementById('formEvento').reset();
            document.getElementById('eventoId').value = '';
            document.getElementById('startEvento').value = formatDateTimeLocal(info.start);
            document.getElementById('endEvento').value = info.end ? formatDateTimeLocal(info.end) : '';
            
            document.getElementById('modalTitulo').textContent = 'Nuevo Evento';
            document.getElementById('btnEliminarEvento').style.display = 'none';
            modalEvento.show();
        },
        
        // Drag & drop → actualizar fecha
        eventDrop: function(info) {
            actualizarEvento(info.event.id, {
                fecha_inicio: info.event.start.toISOString(),
                fecha_fin: info.event.end ? info.event.end.toISOString() : null
            });
        },
        
        // Resize → actualizar duración
        eventResize: function(info) {
            actualizarEvento(info.event.id, {
                fecha_inicio: info.event.start.toISOString(),
                fecha_fin: info.event.end ? info.event.end.toISOString() : null
            });
        }
    });

    calendar.render();

    // Navegación
    document.getElementById('btnPrev').addEventListener('click', () => calendar.prev());
    document.getElementById('btnToday').addEventListener('click', () => calendar.today());
    document.getElementById('btnNext').addEventListener('click', () => calendar.next());

    // Cambio de vista
    document.querySelectorAll('[data-view]').forEach(btn => {
        btn.addEventListener('click', function() {
            calendar.changeView(this.dataset.view);
            document.querySelectorAll('[data-view]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Cambio de biblioteca → recargar eventos
    if (selectBib) {
        selectBib.addEventListener('change', function() {
            calendar.refetchEvents();
        });
    }

    // Guardar evento
    document.getElementById('btnGuardarEvento').addEventListener('click', async function() {
        const id = document.getElementById('eventoId').value;
        const formData = new FormData(document.getElementById('formEvento'));
        const datos = Object.fromEntries(formData.entries());
        
        const url = id ? window.BASE_URL + 'eventos/actualizar/' + id : window.BASE_URL + 'eventos/crear';
        const method = id ? 'POST' : 'POST';
        
        try {
            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(datos)
            });
            
            const data = await res.json();
            if (data.status === 'ok') {
                modalEvento.hide();
                calendar.refetchEvents();
                mostrarToast(data.message, 'success');
            } else {
                mostrarToast(data.message || 'Error al guardar', 'danger');
            }
        } catch (err) {
            mostrarToast('Error de conexión', 'danger');
        }
    });

    // Eliminar evento
    document.getElementById('btnEliminarEvento').addEventListener('click', async function() {
        const id = document.getElementById('eventoId').value;
        if (!id) return;
        
        if (!confirm('¿Eliminar este evento?')) return;
        
        try {
            const res = await fetch(window.BASE_URL + 'eventos/eliminar/' + id, {
                method: 'POST',
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            });
            
            const data = await res.json();
            if (data.status === 'ok') {
                modalEvento.hide();
                calendar.refetchEvents();
                mostrarToast('Evento eliminado', 'success');
            }
        } catch (err) {
            mostrarToast('Error al eliminar', 'danger');
        }
    });

    // Helpers
    function formatDateTimeLocal(date) {
        const d = new Date(date);
        d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
        return d.toISOString().slice(0, 16);
    }

    function actualizarEvento(id, datos) {
        fetch(window.BASE_URL + 'eventos/actualizar/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(datos)
        }).catch(() => mostrarToast('Error al actualizar', 'danger'));
    }

    function mostrarToast(mensaje, tipo) {
        // Usar Toastify si está disponible, o alert simple
        if (typeof Toastify !== 'undefined') {
            Toastify({
                text: mensaje,
                duration: 3000,
                gravity: 'top',
                position: 'right',
                style: { background: tipo === 'success' ? '#198754' : '#dc3545' }
            }).showToast();
        } else {
            alert(mensaje);
        }
    }
});
</script>