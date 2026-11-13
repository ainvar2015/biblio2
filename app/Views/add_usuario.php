<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi bi-person-plus-fill me-2"></i> Alta de Usuario
                        </h4>
                        <a href="<?= base_url('usuarios') ?>" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver al listado
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <?= form_open_multipart('usuarios/add_usuario', ['id' => 'formUsuario', 'class' => 'needs-validation', 'novalidate' => true]) ?>

                    <!-- SECCIÓN: Credenciales -->
                    <div class="section-card mb-4">
                        <div class="section-header">
                            <i class="bi bi-shield-lock"></i>
                            <span>Credenciales de Acceso</span>
                            <span class="text-danger small ms-2">* Campos obligatorios</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="login" class="form-label fw-semibold">
                                    Login <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control <?= validation_show_error('login') ? 'is-invalid' : '' ?>"
                                        id="login" name="login" placeholder="usuario123"
                                        value="<?= set_value('login') ?>" required>
                                    <div class="invalid-feedback"><?= validation_show_error('login') ?></div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="password" class="form-label fw-semibold">
                                    Contraseña <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" class="form-control <?= validation_show_error('password') ? 'is-invalid' : '' ?>"
                                        id="password" name="password" placeholder="••••••••" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="invalid-feedback"><?= validation_show_error('password') ?></div>
                                </div>
                                <div class="password-strength mt-1" id="passwordStrength"></div>
                            </div>

                            <div class="col-md-3">
                                <label for="password1" class="form-label fw-semibold">
                                    Repetir Contraseña <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                    <input type="password" class="form-control <?= validation_show_error('password1') ? 'is-invalid' : '' ?>"
                                        id="password1" name="password1" placeholder="••••••••" required>
                                    <div class="invalid-feedback"><?= validation_show_error('password1') ?></div>
                                </div>
                                <div class="password-match mt-1 small" id="passwordMatch"></div>
                            </div>

                            <div class="col-md-3">
                                <label for="nivel" class="form-label fw-semibold">
                                    Nivel <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-gear"></i></span>
                                    <select name="nivel" id="nivel" class="form-select" required>
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option value="adm" <?= set_select('nivel', 'adm') ?>>Administrador</option>
                                        <option value="usu" <?= set_select('nivel', 'usu') ?>>Usuario</option>
                                        <option value="inv" <?= set_select('nivel', 'inv') ?>>Invitado</option>
                                    </select>
                                </div>
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
                            <!-- Foto de perfil -->
                            <div class="col-md-3 text-center">
                                <label class="form-label fw-semibold d-block">Fotografía</label>
                                <div class="photo-upload-wrapper">
                                    <div class="photo-preview" id="photoPreview">
                                        <img src="<?= base_url('assets/img/default-user.png') ?>" alt="Preview" id="previewImg">
                                        <div class="photo-overlay">
                                            <i class="bi bi-camera-fill"></i>
                                            <span>Cambiar foto</span>
                                        </div>
                                    </div>
                                    <input type="file" class="form-control d-none" id="foto" name="foto"
                                        accept="image/*" onchange="previewPhoto(this)">
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2 w-100" onclick="document.getElementById('foto').click()">
                                        <i class="bi bi-upload"></i> Seleccionar foto
                                    </button>
                                    <small class="text-muted d-block mt-1">Máx. 2MB (JPG, PNG)</small>
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="nombre" class="form-label fw-semibold">
                                            Nombre <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control <?= validation_show_error('nombre') ? 'is-invalid' : '' ?>"
                                            id="nombre" name="nombre" placeholder="Nombre"
                                            value="<?= set_value('nombre') ?>" required>
                                        <div class="invalid-feedback"><?= validation_show_error('nombre') ?></div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="apellidos" class="form-label fw-semibold">Apellidos</label>
                                        <input type="text" class="form-control" id="apellidos" name="apellidos"
                                            placeholder="Apellidos" value="<?= set_value('apellidos') ?>">
                                    </div>

                                    <div class="col-md-4">
                                        <label for="fecha_nacimiento" class="form-label fw-semibold">
                                            <i class="bi bi-calendar-event"></i> Fecha de nacimiento
                                        </label>
                                        <input type="date" class="form-control" id="fecha_nacimiento"
                                            name="fecha_nacimiento" value="<?= set_value('fecha_nacimiento') ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold">
                                            Email <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input type="email" class="form-control <?= validation_show_error('email') ? 'is-invalid' : '' ?>"
                                                id="email" name="email" placeholder="usuario@email.com"
                                                value="<?= set_value('email') ?>" required>
                                            <div class="invalid-feedback"><?= validation_show_error('email') ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="telefono" class="form-label fw-semibold">
                                            <i class="bi bi-telephone"></i> Teléfono
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                            <input type="tel" class="form-control" id="telefono" name="telefono"
                                                placeholder="600 000 000" value="<?= set_value('telefono') ?>">
                                        </div>
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
                                        placeholder="Calle, número, piso..." value="<?= set_value('direccion') ?>">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="poblacion" class="form-label fw-semibold">Población</label>
                                <input type="text" class="form-control" id="poblacion" name="poblacion"
                                    placeholder="Población" value="<?= set_value('poblacion') ?>">
                            </div>

                            <div class="col-md-2">
                                <label for="provincia" class="form-label fw-semibold">Provincia</label>
                                <input type="text" class="form-control" id="provincia" name="provincia"
                                    placeholder="Provincia" value="<?= set_value('provincia') ?>">
                            </div>

                            <div class="col-md-2">
                                <label for="c_postal" class="form-label fw-semibold">C.P.</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-mailbox"></i></span>
                                    <input type="text" class="form-control" id="c_postal" name="c_postal"
                                        placeholder="00000" value="<?= set_value('c_postal') ?>" maxlength="5">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Permisos y Configuración -->
                    <div class="section-card mb-4">
                        <div class="section-header">
                            <i class="bi bi-sliders"></i>
                            <span>Permisos y Configuración</span>
                        </div>

                        <div class="row g-3 align-items-center">
                            <div class="col-md-3">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" checked>
                                    <label class="form-check-label fw-semibold" for="activo">
                                        Usuario Activo
                                    </label>
                                </div>
                                <small class="text-muted ms-4">Permite el acceso al sistema</small>
                            </div>

                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="mostrar_email" name="mostrar_email" value="1">
                                    <label class="form-check-label" for="mostrar_email">
                                        <i class="bi bi-envelope-open"></i> Mostrar email público
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="mostrar_nombre_real" name="mostrar_nombre_real" value="1">
                                    <label class="form-check-label" for="mostrar_nombre_real">
                                        <i class="bi bi-person-badge"></i> Mostrar nombre real
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="permitir_contacto" name="permitir_contacto" value="1">
                                    <label class="form-check-label" for="permitir_contacto">
                                        <i class="bi bi-chat-dots"></i> Permitir contacto
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg px-5" id="btnSubmit">
                            <i class="bi bi-check-circle"></i> Crear Usuario
                        </button>
                    </div>

                    <input type="hidden" name="fecha_alta" value="<?= date('Y-m-d') ?>">
                    <input type="hidden" name="fecha_ultima_modificacion" value="<?= date('Y-m-d') ?>">

                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos personalizados */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .section-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #e3e6f0;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: #4e73df;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e3e6f0;
        font-size: 1.1rem;
    }

    .section-header i {
        font-size: 1.2rem;
    }

    /* Photo Upload */
    .photo-upload-wrapper {
        text-align: center;
    }

    .photo-preview {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid #e3e6f0;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .photo-preview:hover {
        border-color: #4e73df;
        transform: scale(1.02);
    }

    .photo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .photo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .photo-preview:hover .photo-overlay {
        opacity: 1;
    }

    .photo-overlay i {
        font-size: 2rem;
        margin-bottom: 0.25rem;
    }

    .photo-overlay span {
        font-size: 0.8rem;
    }

    /* Form switches mejorados */
    .form-check-lg .form-check-input {
        width: 3rem;
        height: 1.5rem;
        margin-right: 0.5rem;
    }

    .form-switch .form-check-input:checked {
        background-color: #1cc88a;
        border-color: #1cc88a;
    }

    /* Password strength */
    .password-strength {
        height: 4px;
        border-radius: 2px;
        background: #e3e6f0;
        overflow: hidden;
    }

    .password-strength-bar {
        height: 100%;
        border-radius: 2px;
        transition: all 0.3s;
        width: 0;
    }

    .password-match {
        font-weight: 500;
    }

    .password-match.match {
        color: #1cc88a;
    }

    .password-match.no-match {
        color: #e74a3b;
    }

    /* Validación Bootstrap 5 */
    .was-validated .form-control:invalid,
    .form-control.is-invalid {
        border-color: #e74a3b;
    }

    .was-validated .form-control:valid,
    .form-control.is-valid {
        border-color: #1cc88a;
    }
</style>

<script>
    // Preview de foto
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');

        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });

    // Validación de contraseñas en tiempo real
    document.getElementById('password').addEventListener('input', checkPasswordStrength);
    document.getElementById('password1').addEventListener('input', checkPasswordMatch);

    function checkPasswordStrength() {
        const password = document.getElementById('password').value;
        const strengthBar = document.getElementById('passwordStrength');

        let strength = 0;
        if (password.length >= 6) strength++;
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        const colors = ['#e74a3b', '#f6c23e', '#4e73df', '#1cc88a', '#1cc88a'];
        const widths = ['20%', '40%', '60%', '80%', '100%'];

        if (password.length === 0) {
            strengthBar.innerHTML = '';
        } else {
            strengthBar.innerHTML = `<div class="password-strength-bar" style="width: ${widths[strength-1] || '20%'}; background: ${colors[strength-1] || '#e74a3b'}"></div>`;
        }
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

    // Validación del formulario
    (function() {
        'use strict';

        const form = document.getElementById('formUsuario');

        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);
    })();
</script>