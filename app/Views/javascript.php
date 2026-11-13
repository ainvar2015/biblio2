<!-- jQuery y Bootstrap 5 JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

<!-- DataTables y extensiones -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="<?= base_url('assets/js/detalle-libro.js') ?>"></script>

<!-- Tus scripts -->
<?php if (uri_segment(1) === 'verLibros'): ?>
    <script>
        // Inicializar DataTable para búsqueda, ordenación y paginación
        $(document).ready(function() {
            $('#tablaLibros').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [
                    [1, 'asc']
                ], // Ordenar por título
                columnDefs: [{
                        orderable: false,
                        targets: [0, 8]
                    } // No ordenar portada ni acciones
                ]
            });
        });
    </script>
<?php endif; ?>
<?php if (uri_segment(1) === 'altas'): ?>
    <script src="<?= base_url('assets/js/alta-libro.js') ?>"></script>
    <script src="<?= base_url('assets/js/modales.js') ?>"></script>
<?php endif; ?>
<?php if (uri_segment(1) === 'editar'): ?>
    <script src="<?= base_url('assets/js/editar-libro.js') ?>"></script>
    <script src="<?= base_url('assets/js/modales.js') ?>"></script>
<?php endif; ?>
<script src="<?= base_url('assets/js/dataTables.js') ?>"></script>
<script src="<?= base_url('assets/js/menu-lateral.js') ?>"></script>
<?php echo uri_segment(1); if (uri_segment(1) === 'usuarios' || uri_segment(1) === 'perfil'): ?>
    <script src="<?= base_url('assets/js/usuario-form.js') ?>"></script>
    <script src="<?= base_url('assets/js/pin.js') ?>"></script>
    <script>
        window.USUARIOS_CONFIG = {
            baseUrl: '<?= base_url() ?>',
            csrfToken: '<?= csrf_token() ?>',
            csrfHash: '<?= csrf_hash() ?>'
        };
    </script>
    <script src="<?= base_url('assets/js/vista-usuarios.js') ?>"></script>
<?php endif; ?>
<?php if (uri_segment(1) === 'prestamos' || uri_segment(1) === 'api'): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= base_url('assets/js/estadisticas.js') ?>"></script>
<?php endif; ?>
<?php if (uri_segment(1) === 'editar'): ?>
    <script src="<?= base_url('assets/js/listado-base.js') ?>"></script>
<?php endif; ?>
<?php if (uri_segment(1) == 'eventos'): ?>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <!-- SheetJS para exportar a Excel -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <!-- JavaScript externo -->
    <script src='<?= base_url('assets/js/calendario.js') ?>'></script>
<?php endif; ?>
<!-- js específico -->
<!-- Modales -->
<?php if (uri_segment(1) === 'altas' or uri_segment(1) === 'editar'): ?>
    <?= view('modales') ?>
<?php endif; ?>

<!-- SCRIPTS ADICIONALES SOLO PARA ESTA PÁGINA -->
<!-- Select2 (después de jQuery que carga pie_view.php) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>

<!-- Toastify -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<!-- Pasar datos de PHP a JS -->
<?php if (isset($js_data)): ?>
    <script>
        window.LISTADO_CONFIG = <?= json_encode($js_data) ?>;
    </script>
<?php endif; ?>
<?php if (isset($js_especifico)): ?>
    <script src="<?= base_url('assets/js/' . $js_especifico) ?>?v=<?= date('Ymd') ?>"></script>
<?php endif; ?>
<?php if (uri_segment(2) === 'nuevo' && uri_segment(3) === 'autor'): ?>
    <script>
        $(document).ready(function() {
            const formAutor = $('#formAltaAutor'); // Asegúrate de que este ID coincida con tu formulario

            if (formAutor.length) {
                formAutor.on('submit', function(e) {
                    console.log('Evento submit capturado'); // Depuraciónº
                    e.preventDefault(); // Bloquear el envío tradicional

                    const nombre = $('#nombreautor').val().trim(); // Ajusta el selector
                    const csrfToken = $(this).find('input[name="csrf_test_name"]').val();

                    // Verificar si el autor existe
                    $.ajax({
                        url: '<?= base_url('altas/checkAutor') ?>',
                        type: 'POST',
                        data: {
                            csrf_test_name: csrfToken,
                            nombreautor: nombre
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.existe) {
                                // Mostrar toast y NO enviar el formulario
                                Toastify({
                                    text: 'Ya existe un autor con ese nombre.',
                                    duration: 3000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#dc3545", // Usar backgroundColor en lugar de style
                                    stopOnFocus: true
                                }).showToast();
                            } else {
                                // Si no existe, enviar el formulario
                                formAutor.off('submit').submit();
                            }
                        },
                        error: function(xhr, status, error) {
                            // Mostrar toast de error
                            Toastify({
                                text: 'Error al verificar el autor. Intenta nuevamente.',
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#dc3545", // Usar backgroundColor en lugar de style
                                stopOnFocus: true
                            }).showToast();
                        }
                    });
                });
            } else {
                console.error('Formulario de autor no encontrado.');
            }
        });
    </script>
<?php endif; ?>
<?php if (uri_segment(2) === 'nuevo' && uri_segment(3) === 'editorial'): ?>
    <script>
        $(document).ready(function() {
            const formAutor = $('#formAltaEditorial'); // Asegúrate de que este ID coincida con tu formulario

            if (formAutor.length) {
                formAutor.on('submit', function(e) {
                    console.log('Evento submit capturado'); // Depuraciónº
                    e.preventDefault(); // Bloquear el envío tradicional

                    const nombre = $('#nombreeditorial').val().trim(); // Ajusta el selector
                    const csrfToken = $(this).find('input[name="csrf_test_name"]').val();

                    // Verificar si el autor existe
                    $.ajax({
                        url: '<?= base_url('altas/checkEditorial') ?>',
                        type: 'POST',
                        data: {
                            csrf_test_name: csrfToken,
                            nombreeditorial: nombre
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.existe) {
                                // Mostrar toast y NO enviar el formulario
                                Toastify({
                                    text: 'Ya existe una editorial con ese nombre.',
                                    duration: 3000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#dc3545", // Usar backgroundColor en lugar de style
                                    stopOnFocus: true
                                }).showToast();
                            } else {
                                // Si no existe, enviar el formulario
                                formAutor.off('submit').submit();
                            }
                        },
                        error: function(xhr, status, error) {
                            // Mostrar toast de error
                            Toastify({
                                text: 'Error al verificar la editorial. Intenta nuevamente.',
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#dc3545", // Usar backgroundColor en lugar de style
                                stopOnFocus: true
                            }).showToast();
                        }
                    });
                });
            } else {
                console.error('Formulario de editorial no encontrado.');
            }
        });
    </script>
<?php endif; ?>
<?php if (uri_segment(2) === 'nuevo' && uri_segment(3) === 'genero'): ?>
    <script>
        $(document).ready(function() {
            const formGenero = $('#formAltaGenero'); // Asegúrate de que este ID coincida con tu formulario

            if (formGenero.length) {
                formGenero.on('submit', function(e) {
                    console.log('Evento submit capturado'); // Depuraciónº
                    e.preventDefault(); // Bloquear el envío tradicional

                    const nombre = $('#genero').val().trim(); // Ajusta el selector
                    const csrfToken = $(this).find('input[name="csrf_test_name"]').val();

                    // Verificar si el género existe
                    $.ajax({
                        url: '<?= base_url('altas/checkGenero') ?>',
                        type: 'POST',
                        data: {
                            csrf_test_name: csrfToken,
                            nombregenero: nombre
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.existe) {
                                // Mostrar toast y NO enviar el formulario
                                Toastify({
                                    text: 'Ya existe un género con ese nombre.',
                                    duration: 3000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#dc3545", // Usar backgroundColor en lugar de style
                                    stopOnFocus: true
                                }).showToast();
                            } else {
                                // Si no existe, enviar el formulario
                                formGenero.off('submit').submit();
                            }
                        },
                        error: function(xhr, status, error) {
                            // Mostrar toast de error
                            Toastify({
                                text: 'Error al verificar el género. Intenta nuevamente.',
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#dc3545", // Usar backgroundColor en lugar de style
                                stopOnFocus: true
                            }).showToast();
                        }
                    });
                });
            } else {
                console.error('Formulario de género no encontrado.');
            }
        });
    </script>
<?php endif; ?>
<?php if (!($usarServerSide ?? false) && !empty($items)): ?>
    <script>
        // Pasar datos locales al JS
        window.LISTADO_TIPO = '<?= $tipo ?>';
        window.LISTADO_DATOS = <?= json_encode($items) ?>;
    </script>
<?php endif; ?>