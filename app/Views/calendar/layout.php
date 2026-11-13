<!DOCTYPE html>
<html lang="es">
<head>
    <?= view('head_view', $data ?? []) ?>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <!-- Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/fullcalendar.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/menuMovil.css') ?>">
</head>
<body class="d-flex flex-column min-vh-100">
    <div id="menuOverlay"></div>
    <!-- Cabecera -->
    <?= view('cabecera_view', $data ?? []) ?>
    <button class="btn btn-outline-secondary d-md-none" id="btnMenu">
        <i class="bi bi-list"></i>
    </button>
    <div class="container-fluid">
        <div class="row">
            <!-- Panel lateral -->
            <div class="col-md-2">
                <?= view('panel_lateral', $data ?? []) ?>
            </div>

            <!-- Contenido principal -->
            <div class="col-md-10">
                <?= $contenido ?? '<p>No se ha definido contenido.</p>' ?>
            </div>
        </div>
    </div>

    <!-- Pie de página con modales y JS -->
    <?= view('calendar/piecal_view', $data ?? []) ?>
</body>
</html>
