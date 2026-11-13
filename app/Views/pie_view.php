<footer class="bg-light py-3 mt-auto">
    <div class="container text-center">
        <p class="mb-0">&copy; <?= date('Y'); ?> | <a href="http://ainvar.net" target="_blank">ainvar.net</a></p>
    </div>
    <!-- Botón fijo en la esquina inferior derecha -->
<a href="<?= base_url('auth/lock') ?>" class="btn btn-warning position-fixed bottom-0 end-0 m-3 shadow" title="Bloquear pantalla">
    <i class="bi bi-lock-fill"></i> <span class="d-none d-sm-inline">Bloquear</span>
</a>
</footer>