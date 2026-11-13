<?php
$esSuperadmin = session()->get('nivel') == 'superadmin';
$esPropio = session()->get('id_usuario') == $usuario->id;
$puedeVerPin = $esSuperadmin || $esPropio;
?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <!-- Alertas -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-info text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi bi-person-vcard me-2"></i>
                            Ficha de Usuario
                        </h4>
                        <div class="d-flex gap-2">
                            <?php if ($esSuperadmin || $esPropio): ?>
                                <a href="<?= base_url('usuarios/editar/' . $usuario->id) ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                            <?php endif; ?>
                            <a href="<?= base_url('usuarios') ?>" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">

                    <!-- CABECERA: Info principal -->
                    <div class="row mb-4">
                        <div class="col-md-2 text-center">
                            <?php
                            $foto_src = !empty($usuario->foto) 
                                ? base_url('uploads/usuarios/' . $usuario->foto) 
                                : base_url('assets/img/default-user.png');
                            ?>
                            <img src="<?= $foto_src ?>" alt="Foto de <?= esc($usuario->nombre) ?>" 
                                class="img-thumbnail rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                        <div class="col-md-10 d-flex flex-column justify-content-center">
                            <h3 class="mb-1"><?= esc($usuario->nombre . ' ' . ($usuario->apellidos ?? '')) ?></h3>
                            <p class="text-muted mb-2">
                                <i class="bi bi-person"></i> <?= esc($usuario->login) ?>
                                <span class="badge bg-<?= $usuario->activo ? 'success' : 'secondary' ?> ms-2">
                                    <?= $usuario->activo ? 'Activo' : 'Inactivo' ?>
                                </span>
                                <span class="badge bg-primary ms-1">
                                    <?= match($usuario->nivel) {
                                        'adm' => 'Administrador',
                                        'usu' => 'Usuario',
                                        'inv' => 'Invitado',
                                        default => 'Usuario'
                                    } ?>
                                </span>
                            </p>
                            <?php if (!empty($usuario->id_biblioteca)): ?>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-building"></i> 
                                    Biblioteca: <?= esc($bibliotecaNombre ?? 'N/A') ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- DATOS DE CONTACTO -->
                    <div class="section-card mb-4">
                        <div class="section-header text-primary">
                            <i class="bi bi-person-lines-fill"></i>
                            <span>Datos de Contacto</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted">Email</label>
                                <div class="form-control bg-light">
                                    <i class="bi bi-envelope me-2"></i>
                                    <?= esc($usuario->email) ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted">Teléfono</label>
                                <div class="form-control bg-light">
                                    <i class="bi bi-telephone me-2"></i>
                                    <?= esc($usuario->telefono ?? 'No especificado') ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted">Fecha de nacimiento</label>
                                <div class="form-control bg-light">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    <?= !empty($usuario->fecha_nacimiento) ? date('d/m/Y', strtotime($usuario->fecha_nacimiento)) : 'No especificada' ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PIN DE BLOQUEO (solo si tiene PIN configurado y permisos) -->
                    <?php if (!empty($usuario->pin)): ?>
                    <div class="section-card mb-4 border-warning">
                        <div class="section-header text-warning">
                            <i class="bi bi-lock-fill"></i>
                            <span>PIN de Bloqueo</span>
                            <?php if (!$puedeVerPin): ?>
                                <small class="text-muted ms-2 fw-normal">(Información restringida)</small>
                            <?php endif; ?>
                        </div>

                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">PIN</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input 
                                        type="password" 
                                        class="form-control bg-light text-center fw-bold" 
                                        id="verPin"
                                        value="****"
                                        readonly
                                        data-real-value="<?= $puedeVerPin ? esc($usuario->pin) : '' ?>"
                                        style="font-family: monospace; letter-spacing: 0.5rem;">
                                    
                                    <?php if ($puedeVerPin): ?>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePin()" title="Mostrar/Ocultar PIN">
                                            <i class="bi bi-eye" id="icon-verPin"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="input-group-text text-muted" title="No tienes permisos para ver el PIN">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted">
                                    <?php if ($puedeVerPin): ?>
                                        Clic en el ojo para mostrar (se oculta automáticamente en <?= $tiempoAutoOcultar ?? 10 ?> segundos)
                                    <?php else: ?>
                                        Solo el propio usuario o superadmin pueden ver el PIN
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Sin PIN configurado -->
                    <div class="alert alert-secondary mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        Este usuario no tiene PIN de bloqueo configurado.
                        <?php if ($esSuperadmin || $esPropio): ?>
                            Puedes configurarlo desde <a href="<?= base_url('usuarios/editar/' . $usuario->id) ?>">editar usuario</a>.
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- PERMISOS Y CONFIGURACIÓN -->
                    <div class="section-card mb-4">
                        <div class="section-header text-success">
                            <i class="bi bi-sliders"></i>
                            <span>Permisos y Configuración</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 border rounded <?= $usuario->activo ? 'border-success bg-success-subtle' : 'border-secondary bg-light' ?>">
                                    <i class="bi bi-<?= $usuario->activo ? 'check-circle-fill text-success' : 'x-circle-fill text-secondary' ?> fs-4 me-3"></i>
                                    <div>
                                        <div class="fw-semibold">Usuario Activo</div>
                                        <small class="text-muted"><?= $usuario->activo ? 'Tiene acceso al sistema' : 'Acceso deshabilitado' ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 border rounded <?= $usuario->mostrar_email ? 'border-info bg-info-subtle' : 'border-secondary bg-light' ?>">
                                    <i class="bi bi-envelope<?= $usuario->mostrar_email ? '-open text-info' : ' text-secondary' ?> fs-4 me-3"></i>
                                    <div>
                                        <div class="fw-semibold">Email Público</div>
                                        <small class="text-muted"><?= $usuario->mostrar_email ? 'Visible para otros' : 'Oculto' ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 border rounded <?= $usuario->mostrar_nombre_real ? 'border-info bg-info-subtle' : 'border-secondary bg-light' ?>">
                                    <i class="bi bi-person-badge<?= $usuario->mostrar_nombre_real ? '-fill text-info' : ' text-secondary' ?> fs-4 me-3"></i>
                                    <div>
                                        <div class="fw-semibold">Nombre Real</div>
                                        <small class="text-muted"><?= $usuario->mostrar_nombre_real ? 'Visible públicamente' : 'Oculto' ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 border rounded <?= $usuario->permitir_contacto ? 'border-info bg-info-subtle' : 'border-secondary bg-light' ?>">
                                    <i class="bi bi-chat-dots<?= $usuario->permitir_contacto ? '-fill text-info' : ' text-secondary' ?> fs-4 me-3"></i>
                                    <div>
                                        <div class="fw-semibold">Contacto</div>
                                        <small class="text-muted"><?= $usuario->permitir_contacto ? 'Permitido' : 'No permitido' ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FECHAS DEL SISTEMA -->
                    <div class="section-card mb-4">
                        <div class="section-header text-secondary">
                            <i class="bi bi-clock-history"></i>
                            <span>Información del Sistema</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted">Fecha de alta</label>
                                <div class="form-control bg-light">
                                    <i class="bi bi-calendar-plus me-2"></i>
                                    <?= !empty($usuario->fecha_alta) ? date('d/m/Y', strtotime($usuario->fecha_alta)) : 'No registrada' ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted">Última modificación</label>
                                <div class="form-control bg-light">
                                    <i class="bi bi-calendar-check me-2"></i>
                                    <?= !empty($usuario->fecha_ultima_modificacion) ? date('d/m/Y H:i', strtotime($usuario->fecha_ultima_modificacion)) : 'Sin modificaciones' ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted">Último acceso</label>
                                <div class="form-control bg-light">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    <?= !empty($usuario->ultimo_acceso) ? date('d/m/Y H:i', strtotime($usuario->ultimo_acceso)) : 'Nunca' ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer de la card -->
                <div class="card-footer bg-light d-flex justify-content-between align-items-center py-3">
                    <a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al listado
                    </a>
                    <?php if ($esSuperadmin || $esPropio): ?>
                        <a href="<?= base_url('usuarios/editar/' . $usuario->id) ?>" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Editar usuario
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
// ============================================
// CONFIGURACIÓN: Tiempo de auto-ocultar (en milisegundos)
// ============================================
const TIEMPO_AUTO_OCULTAR = <?= ($tiempoAutoOcultar ?? 10) * 1000 ?>; // 10 segundos por defecto

let autoHideTimer = null;

/**
 * Alterna la visualización del PIN
 */
function togglePin() {
    const input = document.getElementById('verPin');
    const icon = document.getElementById('icon-verPin');
    const isHidden = input.type === 'password';
    
    if (isHidden) {
        // MOSTRAR PIN real
        input.type = 'text';
        input.value = input.dataset.realValue;
        input.style.letterSpacing = '0.5rem';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
        
        // Iniciar auto-ocultar
        clearTimeout(autoHideTimer);
        autoHideTimer = setTimeout(() => {
            hidePin();
        }, TIEMPO_AUTO_OCULTAR);
        
    } else {
        // OCULTAR manualmente
        hidePin();
    }
}

/**
 * Oculta el PIN (vuelve a modo password)
 */
function hidePin() {
    const input = document.getElementById('verPin');
    const icon = document.getElementById('icon-verPin');
    
    input.type = 'password';
    input.value = '****';
    icon.classList.remove('bi-eye-slash');
    icon.classList.add('bi-eye');
    
    clearTimeout(autoHideTimer);
}

// Ocultar al salir de la página por seguridad
window.addEventListener('beforeunload', hidePin);

// Ocultar si el usuario cambia de pestaña (opcional, por seguridad extra)
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        hidePin();
    }
});
</script>