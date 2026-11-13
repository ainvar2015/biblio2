<!DOCTYPE html>
<html>
<head>
    <title><?= $this->renderSection('title', 'Mi Biblioteca') ?></title>
    
    <!-- CSS común -->
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <?= $this->renderSection('css') ?>
</head>
<body>
    
    <?= $this->include('partials/navbar') ?>
    
    <main class="container-fluid py-4">
        <?= $this->renderSection('content') ?>
    </main>
    
    <?= $this->include('partials/footer') ?>
    
    <!-- JS común -->
    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/datatables.min.js') ?>"></script>
    
    <!-- ✅ JS específico de cada página -->
    <?= $this->renderSection('js') ?>
    
</body>
</html>