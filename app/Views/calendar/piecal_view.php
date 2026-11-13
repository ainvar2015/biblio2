<footer class="bg-light py-3 mt-4">
    <div class="container text-center">
        <small class="text-muted">
            &copy; <?= date('Y') ?> |
            <a href="https://ainvar.net" target="_blank">ainvar.net</a>
        </small>
    </div>
</footer>
<script>
  const BASE_URL = "<?= base_url() ?>";
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url('assets/js/custom.js'); ?>"></script> <!-- Custom codes -->


<!-- FullCalendar -->
<script src="<?= base_url('assets/vendor/fullcalendar/core/index.global.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/fullcalendar/daygrid/index.global.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/fullcalendar/timegrid/index.global.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/fullcalendar/multimonth/index.global.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/fullcalendar/interaction/index.global.min.js') ?>"></script>
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="<?= base_url('assets/js/calendar-bs5.js') ?>"></script>
<script src="<?= base_url('assets/js/menu-lateral.js') ?>"></script>
</body>
</html>
