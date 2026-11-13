<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-info text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi bi-person-gear me-2"></i> Editar Usuario
                        </h4>
                        <div>
                            <a href="<?= base_url('usuarios/ver/' . $elusuario->id) ?>" class="btn btn-light btn-sm me-2">
                                <i class="bi bi-eye"></i> Ver perfil
                            </a>
                            <a href="<?= base_url('usuarios') ?>" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <?= form_open('usuarios/update/' . $elusuario->id, ['id' => 'formEditUsuario', 'class' => 'needs-validation', 'novalidate' => true]) ?>

                    <!-- Info del usuario -->
                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                        <div>
                            <strong>Editando:</strong> <?= esc($elusuario->login) ?>
                            <span class="badge bg-<?= $elusuario->activo ? 'success' : 'secondary' ?> ms-2">
                                <?= $elusuario->activo ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </div>
                    </div>

                    <!-- SECCIÓN: Cambio de Contraseña -->
                    <div class="section-card mb-4 border-warning">
                        <div class="section-header text-warning">
                            <i class="bi bi-shield-lock-fill"></i>
                            <span>Cambiar Contraseña</span>
                            <small class="text-muted ms-2 fw-normal">(Dejar en blanco para mantener la actual)</small>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4 offset-md-2">
                                <label for="password" class="form-label fw-semibold">Nueva Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" class="form-control <?= validation_show_error('password') ? 'is-invalid' : '' ?>"
                                        id="password" name="password" placeholder="••••••••">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="invalid-feedback"><?= validation_show_error('password') ?></div>
                                </div>
                                <div class="password-strength mt-1" id="passwordStrength"></div>
                            </div>

                            <div class="col-md-4">
                                <label for="password1" class="form-label fw-semibold">Repetir Nueva Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                    <input type="password" class="form-control <?= validation_show_error('password1') ? 'is-invalid' : '' ?>"
                                        id="password1" name="password1" placeholder="••••••••">
                                    <div class="invalid-feedback"><?= validation_show_error('password1') ?></div>
                                </div>
                                <div class="password-match mt-1 small" id="passwordMatch"></div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Datos Personales -->
                    <div class="section-card mb-4">
                        <div class="section-header">
                            <i class="bi bi-person-vcard"></i>
                            <span>Datos Personales</span>
                        </div>

                        <div class="row g-3">
                            <!-- Foto actual -->
                            <div class="col-md-3 text-center">
                                <label class="form-label fw-semibold d-block">Fotografía Actual</label>
                                <div class="photo-upload-wrapper">
                                    <div class="photo-preview" id="photoPreview">
                                        <img src="<?= !empty($elusuario->foto) ? base_url('uploads/usuarios/' . $elusuario->foto) : base_url('assets/img/default-user.png') ?>"
                                            alt="<?= esc($elusuario->nombre) ?>" id="previewImg">
                                        <div class="photo-overlay">
                                            <i class="bi bi-camera-fill"></i>
                                            <span>Cambiar foto</span>
                                        </div>
                                    </div>
                                    <?php if (!empty($elusuario->foto)): ?>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="eliminarFoto" name="eliminar_foto" value="1">
                                            <label class="form-check-label small text-danger" for="eliminarFoto">
                                                <i class="bi bi-trash"></i> Eliminar foto
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="nombre" class="form-label fw-semibold">
                                            Nombre <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control <?= validation_show_error('nombre') ? 'is-invalid' : '' ?>"
                                            id="nombre" name="nombre" value="<?= esc($elusuario->nombre) ?>" required>
                                        <div class="invalid-feedback"><?= validation_show_error('nombre') ?></div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="apellidos" class="form-label fw-semibold">Apellidos</label>
                                        <input type="text" class="form-control" id="apellidos" name="apellidos"
                                            value="<?= esc($elusuario->apellidos) ?>">
                                    </div>

                                    <div class="col-md-4">
                                        <label for="fecha_nacimiento" class="form-label fw-semibold">
                                            <i class="bi bi-calendar-event"></i> Fecha de nacimiento
                                        </label>
                                        <input type="date" class="form-control" id="fecha_nacimiento"
                                            name="fecha_nacimiento" value="<?= $elusuario->fecha_nacimiento ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">Login</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control bg-light" value="<?= esc($elusuario->login) ?>" disabled>
                                        </div>
                                        <small class="text-muted">El login no se puede modificar</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                            <input type="email" class="form-control bg-light" value="<?= esc($elusuario->email) ?>" disabled>
                                        </div>
                                        <small class="text-muted">El email no se puede modificar</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="telefono" class="form-label fw-semibold">
                                            <i class="bi bi-telephone"></i> Teléfono
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                            <input type="tel" class="form-control" id="telefono" name="telefono"
                                                value="<?= esc($elusuario->telefono) ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="nivel" class="form-label fw-semibold">
                                            <i class="bi bi-person-gear"></i> Nivel
                                        </label>
                                        <?php if ($elusuario->nivel === 'adm' && $elusuario->id == session()->get('id_usuario')): ?>
                                            <select name="nivel" id="nivel" class="form-select">
                                                <option value="adm" selected>Administrador</option>
                                                <option value="usu">Usuario</option>
                                                <option value="inv">Invitado</option>
                                            </select>
                                        <?php else: ?>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="bi bi-shield"></i></span>
                                                <input type="text" class="form-control bg-light"
                                                    value="<?php
                                                            switch ($elusuario->nivel) {
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
                                                            ?>" disabled>
                                                <input type="hidden" name="nivel" value="<?= $elusuario->nivel ?>">
                                            </div>
                                            <small class="text-muted">Solo el administrador puede cambiar niveles</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Dirección -->
                    <div class="section-card mb-4">
                        <div class="section-header">
                            <i class="bi bi-geo-alt"></i>
                            <span>Dirección</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-5">
                                <label for="direccion" class="form-label fw-semibold">Dirección</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-house"></i></span>
                                    <input type="text" class="form-control" id="direccion" name="direccion"
                                        value="<?= esc($elusuario->direccion) ?>">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="poblacion" class="form-label fw-semibold">Población</label>
                                <input type="text" class="form-control" id="poblacion" name="poblacion"
                                    value="<?= esc($elusuario->poblacion) ?>">
                            </div>

                            <div class="col-md-2">
                                <label for="provincia" class="form-label fw-semibold">Provincia</label>
                                <input type="text" class="form-control" id="provincia" name="provincia"
                                    value="<?= esc($elusuario->provincia) ?>">
                            </div>

                            <div class="col-md-2">
                                <label for="c_postal" class="form-label fw-semibold">C.P.</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-mailbox"></i></span>
                                    <input type="text" class="form-control" id="c_postal" name="c_postal"
                                        value="<?= esc($elusuario->c_postal) ?>" maxlength="5">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Permisos -->
                    <div class="section-card mb-4">
                        <div class="section-header">
                            <i class="bi bi-sliders"></i>
                            <span>Permisos y Configuración</span>
                        </div>

                        <div class="row g-3 align-items-center">
                            <div class="col-md-3">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                                        <?= $elusuario->activo ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-semibold" for="activo">
                                        Usuario Activo
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="mostrar_email" name="mostrar_email" value="1"
                                        <?= $elusuario->mostrar_email ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="mostrar_email">
                                        <i class="bi bi-envelope-open"></i> Mostrar email público
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="mostrar_nombre_real" name="mostrar_nombre_real" value="1"
                                        <?= $elusuario->mostrar_nombre_real ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="mostrar_nombre_real">
                                        <i class="bi bi-person-badge"></i> Mostrar nombre real
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="permitir_contacto" name="permitir_contacto" value="1"
                                        <?= $elusuario->permitir_contacto ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="permitir_contacto">
                                        <i class="bi bi-chat-dots"></i> Permitir contacto
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <div>
                            <button type="button" class="btn btn-danger btn-lg me-2" onclick="confirmarBorrar(<?= $elusuario->id ?>)">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-check-circle"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="fecha_ultima_modificacion" value="<?= date('Y-m-d') ?>">

                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Mismos estilos que add_usuario.php más: */
    .bg-gradient-info {
        background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
    }

    .border-warning {
        border: 2px solid #f6c23e !important;
        background: #fffbf0 !important;
    }

    .section-card .text-warning {
        color: #f6c23e !important;
    }
</style>

<script>
    // Preview de foto (igual que en add)
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Toggle password
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');

        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });

    // Validación de contraseñas
    document.getElementById('password').addEventListener('input', checkPasswordStrength);
    document.getElementById('password1').addEventListener('input', checkPasswordMatch);

    function checkPasswordStrength() {
        const password = document.getElementById('password').value;
        const strengthBar = document.getElementById('passwordStrength');

        if (password.length === 0) {
            strengthBar.innerHTML = '';
            return;
        }

        let strength = 0;
        if (password.length >= 6) strength++;
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        const colors = ['#e74a3b', '#f6c23e', '#4e73df', '#1cc88a', '#1cc88a'];
        const widths = ['20%', '40%', '60%', '80%', '100%'];

        strengthBar.innerHTML = `<div class="password-strength-bar" style="width: ${widths[strength-1]}; background: ${colors[strength-1]}"></div>`;
    }

    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const password1 = document.getElementById('password1').value;
        const matchDiv = document.getElementById('passwordMatch');

        if (password1.length === 0) {
            matchDiv.textContent = '';
            return;
        }

        if (password === password1) {
            matchDiv.innerHTML = '<i class="bi bi-check-circle-fill"></i> Las contraseñas coinciden';
            matchDiv.className = 'password-match mt-1 small match';
        } else {
            matchDiv.innerHTML = '<i class="bi bi-x-circle-fill"></i> Las contraseñas no coinciden';
            matchDiv.className = 'password-match mt-1 small no-match';
        }
    }

    // Confirmar borrado
    function confirmarBorrar(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')) {
            window.location.href = '<?= base_url('usuarios/borrar/') ?>' + id;
        }
    }

    // Validación Bootstrap
    (function() {
        'use strict';
        const form = document.getElementById('formEditUsuario');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    })();
</script>