<!-- Calendario -->
<div class="mainbar">
  <div class="matter">
    <div class="container-fluid py-3">
      <div class="card shadow-sm">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
          <span><i class="fa fa-calendar"></i> Calendario de Eventos</span>
          <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#addEventModal">
            <i class="fa fa-plus"></i> Añadir evento
          </button>
        </div>
        <div class="card-body">
          <!-- <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
            <div class="btn-group">
              <button class="btn btn-primary" data-calendar-nav="prev">«</button>
              <button class="btn btn-outline-secondary" data-calendar-nav="today">Hoy</button>
              <button class="btn btn-primary" data-calendar-nav="next">»</button>
            </div>
            <div class="btn-group">
              <button class="btn btn-outline-secondary" data-calendar-view="dayGridMonth">Mes</button>
              <button class="btn btn-outline-secondary" data-calendar-view="timeGridWeek">Semana</button>
              <button class="btn btn-outline-secondary" data-calendar-view="timeGridDay">Día</button>
            </div>
          </div> -->
          <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">

            <!-- Navegación -->
            <div class="btn-group mb-2" role="group">
              <button type="button"
                class="btn btn-primary"
                data-calendar-nav="prev">
                <i class="bi bi-chevron-left"></i>
              </button>

              <button type="button"
                class="btn btn-outline-primdary"
                data-calendar-nav="today">
                Hoy
              </button>

              <button type="button"
                class="btn btn-primary"
                data-calendar-nav="next">
                <i class="bi bi-chevron-right"></i>
              </button>
            </div>

            <!-- Vistas -->
            <div class="btn-group mb-2" role="group">
              <button type="button"
                class="btn btn-outline-primary"
                data-calendar-view="day">
                Día
              </button>

              <button type="button"
                class="btn btn-outline-primary"
                data-calendar-view="week">
                Semana
              </button>

              <button type="button"
                class="btn btn-outline-primary active"
                data-calendar-view="month">
                Mes
              </button>

              <button type="button"
                class="btn btn-outline-primary"
                data-calendar-view="year">
                Año
              </button>
            </div>

          </div>

          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= view('calendar/modales_cal', $data ?? []) ?>