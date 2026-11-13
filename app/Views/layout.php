<?php $current_url = current_url(true);
$i = 1;
foreach ($current_url->getSegments() as $segmento) {
    ${"segment" . $i} = $segmento;
    //echo $segment.$i;
    $i++;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?= view('head_view', $data ?? []) ?>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

    <!-- Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- SELECT2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- TOASTIFY (notificaciones) -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <!-- Bootstrap Icons (si no lo tienes ya) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tus CSS personalizados -->
    <link rel="stylesheet" href="<?= base_url('assets/css/menuMovil.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/widget-bs5.css') ?>">
    <style>
        .invalid-feedback.card {
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            border-radius: 0 !important;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .card img {
            object-fit: contain;
            width: 100%;
            height: 150px;
        }
    </style>
    <?php
    if (uri_segment(1) === 'usuarios' || uri_segment(1) === 'perfil'): ?>
        <link href="<?= base_url('assets/css/usuario-form.css') ?>" rel="stylesheet">
    <?php endif; ?>
    <script>
        const BASE_URL = "<?= base_url() ?>";
        window.BASE_URL = BASE_URL.replace(/\/+$/, '') + '/';
    </script>

    <!-- ESTILOS ESPECÍFICOS PARA EL FORMULARIO DE LIBROS -->
    <style>
        .is-invalid {
            border-color: #dc3545 !important;
        }

        .is-valid {
            border-color: #198754 !important;
        }

        .invalid-feedback {
            display: none;
            color: #dc3545;
            font-size: 0.875em;
        }

        .was-validated .invalid-feedback {
            display: block;
        }

        .isbn-loading {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="%23007bff" stroke-width="2" fill="none" stroke-dasharray="31.42" stroke-dashoffset="10"><animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/></circle></svg>');
            background-repeat: no-repeat;
            background-position: right 2.5rem center;
            background-size: 20px;
        }

        #portadaPreview {
            max-height: 150px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
        }

        /* Fix para Select2 en modales */
        .select2-container--open {
            z-index: 1060;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <div id="menuOverlay"></div>
    <?= view('cabecera_view', $data ?? []) ?>
    <button class="btn btn-outline-secondary d-md-none" id="btnMenu">
        <i class="bi bi-list"></i>
    </button>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <?= view('panel_lateral', $data ?? []) ?>
            </div>
            <div class="col-md-10">
                <?= $contenido ?? '<p>No se ha definido contenido.</p>' ?>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SCRIPTS (se cargan desde pie_view.php)    -->
    <!-- ========================================== -->
    <?= view('pie_view', $data ?? []) ?>

    <?= view('javascript.php') ?>
    <!-- Configuración global Select2 -->
    <script>
        window.CSRF_TOKEN = '<?= csrf_token() ?>';
        window.CSRF_HASH = '<?= csrf_hash() ?>';
        window.CONFIG_INACTIVIDAD = {
            TIEMPO_INACTIVIDAD: <?= (TIEMPO_INACTIVIDAD_SEGUNDOS ?? 1200) * 1000 ?>,
            TIEMPO_AVISO: 30000
        };

        $(document).ready(function() {
            if (typeof $ !== 'undefined') {
                $.ajaxSetup({
                    data: {
                        [window.CSRF_TOKEN]: window.CSRF_HASH
                    }
                });
            }
        });
    </script>
    <script>
        $.fn.select2.defaults.set('theme', 'bootstrap-5');
        $.fn.select2.defaults.set('language', 'es');
    </script>
    <script src="<?= base_url('assets/js/actividad.js') ?>"></script>

    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

</body>

</html>