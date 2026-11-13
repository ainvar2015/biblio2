<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <span class="navbar-brand" <?= base_url() ?>"><img src="<?= base_url('assets/img/logo3.png') ?>" alt="Logo" width="30" height="30"> <?= esc($titulo ?? 'Biblioteca') ?></span>
        <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span> -->
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <?php if($usuario->nivel !== 'inv'): ?>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="<?= base_url('auth/lock') ?>" class="nav-link" title="Bloquear pantalla">
                        <i class="bi bi-lock-fill"></i> Bloquear
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('perfil') ?>"><i class="bi bi-person-circle"></i> Perfil de <?= esc($usuario->nombre ?? '') ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('ayuda/es') ?>"><i class="bi bi-question-circle"></i> Ayuda</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>