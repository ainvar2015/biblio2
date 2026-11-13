<?php
$param1 = "";
$param2 = "";
$param3 = "";
$current_url = current_url(true);

// var_dump($current_url->getSegment(1));die();
foreach ($current_url->getSegments() as $i => $segment) {
    $nombreVariable = "param" . ($i + 1);
    $$nombreVariable = $segment;
}
?>
<aside class="left-aside bg-light vh-100 border-end">
    <div class="p-3">
        <!-- Perfil -->
        <div class="d-flex align-items-center mb-3">
            <img src="<?= base_url('assets/img/authors/Usuario.jpg') ?>" alt="Usuario" class="rounded-circle me-2" width="50">
            <div>
                <h6 class="mb-0"><?= esc($usuario->nombre . ' ' . $usuario->apellidos) ?></h6>
                <small class="text-muted">
                    <?php
                    switch ($usuario->nivel) {
                        case 'superadmin':
                            echo 'Super administrador';
                            break;
                        case 'adm':
                            echo 'Administrador';
                            break;
                        case 'usu':
                            echo 'Usuario';
                            break;
                        case 'inv':
                            echo 'Invitado';
                            break;
                    }
                    ?>
                </small>
            </div>
        </div>

        <!-- Menú -->
        <ul class="nav flex-column navigation">

            <?php if ($usuario->nivel !== 'inv'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= empty($param1) ? 'active' : '' ?>" href="<?= base_url() ?>">
                        <i class="bi bi-house-door-fill me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= empty($param1) ? 'active' : '' ?>" href="<?= base_url('eventos') ?>">
                        <i class="bi bi-calendar me-1"></i> Calendario
                    </a>
                </li>
                <!-- Bibliotecas -->
                <li class="nav-item">
                    <a class="nav-link <?= $param1 == 'bibliotecas' ? 'active' : '' ?>" href="<?= base_url('bibliotecas') ?>">
                        <i class="bi bi-building me-1"></i> Bibliotecas
                    </a>
                </li>
                <!-- Ver Libros -->
                <li class="nav-item">
                    <a href="<?= base_url('verLibros') ?>" class="nav-link <?= $param1 == 'verLibros' ? 'active' : '' ?>">
                        <i class="bi bi-book me-1"></i> Ver libros
                    </a>
                </li>
                <!-- Altas -->
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center <?= $param1 == 'altas' ? 'active' : '' ?>"
                        data-bs-toggle="collapse" href="#submenuAltas" role="button" aria-expanded="false" aria-controls="submenuAltas">
                        <span><i class="bi bi-plus-lg me-1"></i> Altas</span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse <?= $param1 == 'altas' ? 'show' : '' ?>" id="submenuAltas">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="<?= base_url('altas/nuevo/libro') ?>" class="nav-link"><i class="bi bi-book"></i> Nuevo libro</a></li>
                            <li><a href="<?= base_url('altas/nuevo/autor') ?>" class="nav-link"><i class="bi bi-file-earmark-person"></i> Nuevo autor</a></li>
                            <li><a href="<?= base_url('altas/nuevo/editorial') ?>" class="nav-link"><i class="bi bi-journal"></i> Nueva editorial</a></li>
                            <li><a href="<?= base_url('altas/nuevo/genero') ?>" class="nav-link"><i class="bi bi-list-task"></i> Nuevo género</a></li>
                        </ul>
                    </div>
                </li>
                <!-- Editar -->
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center <?= $param1 == 'editar' ? 'active' : '' ?>"
                        data-bs-toggle="collapse" href="#submenuEditar" role="button" aria-expanded="false" aria-controls="submenuEditar">
                        <span><i class="bi bi-pencil-square me-1"></i> Editar</span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse <?= $param1 == 'editar' ? 'show' : '' ?>" id="submenuEditar">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="<?= base_url('editar/listaLibros') ?>" class="nav-link"><i class="bi bi-book"></i> Editar libros</a></li>
                            <li><a href="<?= base_url('editar/listaAutores') ?>" class="nav-link"><i class="bi bi-file-earmark-person"></i> Editar autores</a></li>
                            <li><a href="<?= base_url('editar/listaEditoriales') ?>" class="nav-link"><i class="bi bi-journal"></i> Editar editoriales</a></li>
                            <li><a href="<?= base_url('editar/listaGeneros') ?>" class="nav-link"><i class="bi bi-list-task"></i> Editar géneros</a></li>
                        </ul>
                    </div>
                </li>

                <!-- Préstamos -->
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center <?= $param2 == 'listaPrestamos' ? 'active' : '' ?>"
                        data-bs-toggle="collapse" href="#submenuPrestamos" role="button" aria-expanded="false" aria-controls="submenuPrestamos">
                        <span><i class="bi bi-basket-fill me-1"></i> Préstamos</span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse <?= $param1 == 'prestamos' ? 'show' : '' ?>" id="submenuPrestamos">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="<?= base_url('prestamos') ?>" class="nav-link"><i class="bi bi-bag-plus-fill"></i> Préstamos activos</a></li>
                            <li><a href="<?= base_url('prestamos/historial') ?>" class="nav-link"><i class="bi bi-cart-check"></i> Historial de préstamos</a></li>
                        </ul>
                    </div>
                </li>
                <!-- Reservas -->
                <li class="nav-item">
                    <a href="<?= base_url('reservas') ?>" class="nav-link <?= $param1 == 'reservas' ? 'active' : '' ?>">
                        <i class="bi bi-journal-bookmark-fill me-1"></i> Reservas
                    </a>
                </li>


                <!-- Usuarios solo para admin -->
                <?php if ($usuario->nivel == 'adm' or $usuario->nivel == 'superadmin'): ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex justify-content-between align-items-center <?= $param1 == 'usuarios' ? 'active' : '' ?>"
                            data-bs-toggle="collapse" href="#submenuUsuarios" role="button" aria-expanded="false" aria-controls="submenuUsuarios">
                            <span><i class="bi bi-people-fill me-1"></i> Usuarios</span>
                            <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse <?= $param1 == 'usuarios' ? 'show' : '' ?>" id="submenuUsuarios">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li><a href="<?= base_url('usuarios/nuevo') ?>" class="nav-link"><i class="bi bi-person-plus"></i> Alta de Usuarios</a></li>
                                <li><a href="<?= base_url('usuarios/editar') ?>" class="nav-link"><i class="bi bi-person-vcard"></i> Editar Usuarios</a></li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= base_url('usuarios/buscar') ?>">
                                        <i class="bi bi-search"></i> Buscar usuario
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('admin/settings') ?>" class="nav-link <?= $param2 == 'settings' ? 'active' : '' ?>">
                            <i class="bi bi-gear-fill me-1"></i> Settings
                        </a>
                    </li>
                <?php endif; ?>


                <li class="nav-item">
                    <a href="<?= base_url('perfil') ?>" class="nav-link <?= $param2 == 'perfil' ? 'active' : '' ?>">
                        <i class="bi bi-person-fill me-1"></i> Perfil
                    </a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a href="<?= base_url('admin/invitado') ?>" class="nav-link <?= $param1 == 'admin' ? 'active' : '' ?>">
                        <i class="bi bi-eye me-1"></i> Ver libros
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a href="<?= base_url('ayuda/es') ?>" class="nav-link" target="_blank">
                    <i class="bi bi-question-circle me-1"></i> Ayuda
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('logout') ?>" class="nav-link">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</aside>