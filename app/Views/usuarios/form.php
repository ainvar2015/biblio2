<?php
// Determinar modo y acción del formulario
$es_alta = !isset($elusuario);
$usuario = $es_alta ? null : $elusuario;
$es_perfil_propio = !$es_alta && ($usuario->id == session()->get('id_usuario'));

// Si es perfil propio y no es admin, usar updatePerfil en lugar de update
if ($es_perfil_propio && !$es_admin) {
    $action = 'usuarios/updatePerfil';
} else {
    $action = $es_alta ? 'usuarios/add_usuario' : "usuarios/update/{$usuario->id}";
}

$titulo = $es_alta ? 'Alta de Usuario' : ($es_perfil_propio ? 'Mi Perfil' : 'Editar Usuario');
$btn_text = $es_alta ? 'Crear Usuario' : 'Guardar Cambios';
$btn_icon = $es_alta ? 'bi-person-plus' : 'bi-check-circle';
$header_class = $es_alta ? 'bg-gradient-primary' : ($es_perfil_propio ? 'bg-gradient-success' : 'bg-gradient-info');
?>

<!-- ... resto de la vista ... -->

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-lg border-0">
                <div class="card-header <?= $header_class ?> text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi <?= $es_alta ? 'bi-person-plus-fill' : 'bi-person-gear' ?> me-2"></i>
                            <?= $titulo ?>
                        </h4>
                        <div>
                            <a href="<?= base_url('usuarios') ?>" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <?= form_open_multipart($action, ['id' => 'formUsuario', 'class' => 'needs-validation', 'novalidate' => true]) ?>
                    <!-- Biblioteca (solo superadmin) -->
                        <?php if ($esSuperadmin): ?>
                            <div class="mb-3">
                                <label for="id_biblioteca" class="form-label fw-bold">Biblioteca <span class="text-danger">*</span></label>
                                <select name="id_biblioteca" id="id_biblioteca" class="form-select form-select-lg" required>
                                    <option value="">Selecciona una biblioteca...</option>
                                    <?php foreach ($bibliotecas as $bib): ?>
                                        <option value="<?= $bib['id'] ?>" 
                                            <?= (old('id_biblioteca', isset($usuario) ? $usuario->id_biblioteca : '') == $bib['id']) ? 'selected' : '' ?>>
                                            <?= esc($bib['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <!-- Admin normal: biblioteca fija (hidden) -->
                            <input type="hidden" name="id_biblioteca" value="<?= $miBiblioteca ?>">
                            <div class="alert alert-info">
                                <i class="bi bi-building me-2"></i>
                                Biblioteca: <strong><?= esc($bibliotecas[array_search($miBiblioteca, array_column($bibliotecas, 'id'))]['nombre'] ?? 'N/A') ?></strong>
                            </div>
                        <?php endif; ?>


                    <?php if (!$es_alta): ?>
                        <input type="hidden" name="id" value="<?= $usuario->id ?>">
                        <!-- Info del usuario en edición -->
                        <div class="alert alert-info d-flex align-items-center mb-4">
                            <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                            <div>
                                <strong>Editando:</strong> <?= esc($usuario->login) ?>
                                <span class="badge bg-<?= $usuario->activo ? 'success' : 'secondary' ?> ms-2">
                                    <?= $usuario->activo ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!$es_alta && $es_admin): ?>
                        <!-- SECCIÓN: Cambio de Login (SOLO ADMIN en edición) -->
                        <div class="section-card mb-4 border-warning">
                            <div class="section-header text-warning">
                                <i class="bi bi-person-badge-fill"></i>
                                <span>Cambiar Login</span>
                                <small class="text-muted ms-2 fw-normal">(Solo administradores)</small>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4 offset-md-4">
                                    <label for="login" class="form-label fw-semibold">Nuevo Login</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" id="login" name="login"
                                            value="<?= esc($usuario->login) ?>">
                                    </div>
                                    <small class="text-muted">Dejar en blanco para mantener: <strong><?= esc($usuario->login) ?></strong></small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- SECCIÓN: Credenciales (solo en alta) o Cambio de Contraseña (en edición) -->
                    <div class="section-card mb-4 <?= $es_alta ? '' : 'border-secondary' ?>">
                        <div class="section-header <?= $es_alta ? '' : 'text-secondary' ?>">
                            <i class="bi bi-shield-lock<?= $es_alta ? '' : '-fill' ?>"></i>
                            <span><?= $es_alta ? 'Credenciales de Acceso' : 'Cambiar Contraseña' ?></span>
                            <?php if ($es_alta): ?>
                                <span class="text-danger small ms-2">* Campos obligatorios</span>
                            <?php else: ?>
                                <small class="text-muted ms-2 fw-normal">(Dejar en blanco para mantener la actual)</small>
                            <?php endif; ?>
                        </div>

                        <div class="row g-3">
                            <?php if ($es_alta): ?>
                                <!-- LOGIN (solo alta) -->
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
                            <?php endif; ?>

                            <!-- PASSWORD -->
                            <div class="col-md-<?= $es_alta ? '3' : '4 offset-md-2' ?>">
                                <label for="password" class="form-label fw-semibold">
                                    <?= $es_alta ? 'Contraseña <span class="text-danger">*</span>' : 'Nueva Contraseña' ?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" class="form-control <?= validation_show_error('password') ? 'is-invalid' : '' ?>"
                                        id="password" name="password" placeholder="••••••••"
                                        <?= $es_alta ? 'required' : '' ?> autocomplete="new-password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="invalid-feedback"><?= validation_show_error('password') ?></div>
                                </div>
                                <div class="password-strength mt-1" id="passwordStrength"></div>
                            </div>

                            <!-- PASSWORD REPEAT -->
                            <div class="col-md-<?= $es_alta ? '3' : '4' ?>">
                                <label for="password1" class="form-label fw-semibold">
                                    <?= $es_alta ? 'Repetir Contraseña <span class="text-danger">*</span>' : 'Repetir Nueva Contraseña' ?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                    <input type="password" class="form-control <?= validation_show_error('password1') ? 'is-invalid' : '' ?>"
                                        id="password1" name="password1" placeholder="••••••••"
                                        <?= $es_alta ? 'required' : '' ?>>
                                    <div class="invalid-feedback"><?= validation_show_error('password1') ?></div>
                                </div>
                                <div class="password-match mt-1 small" id="passwordMatch"></div>
                            </div>

                            <?php if ($es_alta): ?>
                                <!-- NIVEL (solo alta y solo admin) -->
                                <?php if ($es_admin): ?>
                                    <div class="col-md-3">
                                        <label for="nivel" class="form-label fw-semibold">
                                            Nivel <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person-gear"></i></span>
                                            <select name="nivel" id="nivel" class="form-select" required>
                                                <option value="" disabled selected>Seleccione...</option>
                                                <?php if($esSuperadmin): ?>
                                                    <option value="superadmin" <?= set_select('nivel', 'superadmin') ?>>Super Administrador</option>
                                                <?php endif; ?>
                                                <option value="adm" <?= set_select('nivel', 'adm') ?>>Administrador</option>
                                                <option value="usu" <?= set_select('nivel', 'usu') ?>>Usuario</option>
                                                <option value="inv" <?= set_select('nivel', 'inv') ?>>Invitado</option>
                                            </select>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <input type="hidden" name="nivel" value="usu">
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- SECCIÓN: PIN de Bloqueo -->
                    <div class="section-card mb-4">
                        <div class="section-header">
                            <i class="bi bi-lock-fill"></i>
                            <span>PIN de Bloqueo (4 dígitos)</span>
                            <small class="text-muted ms-2 fw-normal">(Opcional)</small>
                        </div>

                        <div class="row g-3 align-items-end">
                            <!-- PIN -->
                            <div class="col-md-3">
                                <label for="pin" class="form-label fw-semibold">PIN</label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control text-center fw-bold"
                                        id="pin"
                                        name="pin"
                                        maxlength="4"
                                        placeholder="••••"
                                        data-user-id="<?= !$es_alta ? $usuario->id : '' ?>"
                                        value="<?= !$es_alta && !empty($usuario->pin) ? '****' : '' ?>"
                                        data-real-pin="<?= !$es_alta && !empty($usuario->pin) ? esc($usuario->pin) : '' ?>"
                                        style="font-family: monospace; font-size: 1.2rem; letter-spacing: 0.3rem;">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePin">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirmar PIN -->
                            <div class="col-md-3">
                                <label for="pin_confirmar" class="form-label fw-semibold">Confirmar PIN</label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control text-center fw-bold"
                                        id="pin_confirmar"
                                        maxlength="4"
                                        placeholder="••••"
                                        style="font-family: monospace; font-size: 1.2rem; letter-spacing: 0.3rem;">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePinConfirmar">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Botones: Generar y Guardar -->
                            <div class="col-md-6 d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary w-50" id="btnGenerarPin">
                                    <i class="bi bi-shuffle me-1"></i> Generar PIN
                                </button>
                                <button type="button" class="btn btn-primary w-50" id="btnGuardarPin">
                                    <i class="bi bi-check-lg me-1"></i> Guardar PIN
                                </button>
                            </div>
                        </div>

                        <!-- Botón para eliminar PIN (si existe) -->
                        <?php if (!$es_alta && !empty($usuario->pin)): ?>
                            <div class="mt-3">
                                <button type="button" class="btn btn-sm btn-outline-danger" id="btnEliminarPin">
                                    <i class="bi bi-trash me-1"></i> Eliminar PIN actual
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- SECCIÓN: Datos Personales -->
                    <div class="section-card mb-4">
                        <div class="section-header">
                            <i class="bi bi-person-vcard"></i>
                            <span>Datos Personales</span>
                        </div>

                        <div class="row g-3">
                            <!-- Foto -->
                            <div class="col-md-3 text-center">
                                <label class="form-label fw-semibold d-block">Fotografía</label>
                                <div class="photo-upload-wrapper">
                                    <div class="photo-preview" id="photoPreview" onclick="document.getElementById('foto').click()">
                                        <?php
                                        $foto_actual = $es_alta ? '' : ($usuario->foto ?? '');
                                        $foto_src = $foto_actual ? base_url('uploads/usuarios/' . $foto_actual) : base_url('assets/img/default-user.png');
                                        ?>
                                        <img src="<?= $foto_src ?>" alt="Preview" id="previewImg">
                                        <div class="photo-overlay">
                                            <i class="bi bi-camera-fill"></i>
                                            <span>Cambiar foto</span>
                                        </div>
                                    </div>
                                    <input type="file" class="form-control d-none" id="foto" name="foto"
                                        accept="image/*" onchange="previewPhoto(this)">
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2 w-100" onclick="event.stopPropagation(); document.getElementById('foto').click()">
                                        <i class="bi bi-upload"></i> <?= $es_alta ? 'Seleccionar foto' : 'Cambiar foto' ?>
                                    </button>
                                    <?php if (!$es_alta && $foto_actual): ?>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="eliminarFoto" name="eliminar_foto" value="1">
                                            <label class="form-check-label small text-danger" for="eliminarFoto">
                                                <i class="bi bi-trash"></i> Eliminar foto
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                    <small class="text-muted d-block mt-1">Máx. 2MB (JPG, PNG)</small>
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="row g-3">
                                    <!-- Nombre -->
                                    <div class="col-md-4">
                                        <label for="nombre" class="form-label fw-semibold">
                                            Nombre <?= $es_alta ? '<span class="text-danger">*</span>' : '' ?>
                                        </label>
                                        <input type="text" class="form-control <?= validation_show_error('nombre') ? 'is-invalid' : '' ?>"
                                            id="nombre" name="nombre" placeholder="Nombre"
                                            value="<?= $es_alta ? set_value('nombre') : esc($usuario->nombre) ?>"
                                            <?= $es_alta ? 'required' : '' ?>>
                                        <div class="invalid-feedback"><?= validation_show_error('nombre') ?></div>
                                    </div>

                                    <!-- Apellidos -->
                                    <div class="col-md-4">
                                        <label for="apellidos" class="form-label fw-semibold">Apellidos</label>
                                        <input type="text" class="form-control" id="apellidos" name="apellidos"
                                            placeholder="Apellidos"
                                            value="<?= $es_alta ? set_value('apellidos') : esc($usuario->apellidos ?? '') ?>">
                                    </div>

                                    <!-- Fecha nacimiento -->
                                    <div class="col-md-4">
                                        <label for="fecha_nacimiento" class="form-label fw-semibold">
                                            <i class="bi bi-calendar-event"></i> Fecha de nacimiento
                                        </label>
                                        <input type="date" class="form-control" id="fecha_nacimiento"
                                            name="fecha_nacimiento"
                                            value="<?= $es_alta ? set_value('fecha_nacimiento') : ($usuario->fecha_nacimiento ?? '') ?>">
                                    </div>

                                    <?php if (!$es_alta): ?>
                                        <!-- LOGIN: Solo texto, no input (seguro contra DOM manipulation) -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted">
                                                <i class="bi bi-person"></i> Login
                                            </label>
                                            <div class="form-control bg-light d-flex align-items-center" style="min-height: 38px;">
                                                <strong><?= esc($usuario->login) ?></strong>
                                            </div>
                                            <small class="text-muted">
                                                <?= $es_admin ? 'Puedes cambiarlo en la sección superior' : 'Solo el administrador puede cambiar el login' ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Email -->
                                    <?php if ($es_alta): ?>
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
                                    <?php else: ?>
                                        <!-- EMAIL en edición: Solo texto, no input (seguro contra DOM manipulation) -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted">
                                                <i class="bi bi-envelope"></i> Email
                                            </label>
                                            <div class="form-control bg-light d-flex align-items-center" style="min-height: 38px;">
                                                <?= esc($usuario->email) ?>
                                            </div>
                                            <small class="text-muted">El email no se puede modificar</small>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Teléfono -->
                                    <div class="col-md-<?= $es_alta ? '6' : '6' ?>">
                                        <label for="telefono" class="form-label fw-semibold">
                                            <i class="bi bi-telephone"></i> Teléfono
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                            <input type="tel" class="form-control" id="telefono" name="telefono"
                                                placeholder="600 000 000"
                                                value="<?= $es_alta ? set_value('telefono') : esc($usuario->telefono ?? '') ?>">
                                        </div>
                                    </div>

                                    <?php if (!$es_alta): ?>
                                        <!-- NIVEL: Select solo para admin, texto para otros (seguro contra DOM manipulation) -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted">
                                                <i class="bi bi-person-gear"></i> Nivel
                                            </label>
                                            <?php if ($es_admin): ?>
                                                <select name="nivel" id="nivel" class="form-select">
                                                    <?php if($usuario->nivel == 'superadmin'): ?>
                                                        <option value="superadmin" <?= $usuario->nivel == 'superadmin' ? 'selected' : '' ?>>Super Administrador</option>
                                                    <?php endif; ?> 
                                                    <option value="adm" <?= $usuario->nivel == 'adm' ? 'selected' : '' ?>>Administrador</option>
                                                    <option value="usu" <?= $usuario->nivel == 'usu' ? 'selected' : '' ?>>Usuario</option>
                                                    <option value="inv" <?= $usuario->nivel == 'inv' ? 'selected' : '' ?>>Invitado</option>
                                                </select>
                                            <?php else: ?>
                                                <div class="form-control bg-light d-flex align-items-center" style="min-height: 38px;">
                                                    <?php
                                                    $nivelTexto = match ($usuario->nivel) {
                                                        'superadmin' => 'Super Administrador',
                                                        'adm' => 'Administrador',
                                                        'usu' => 'Usuario',
                                                        'inv' => 'Invitado',
                                                        default => 'Usuario'
                                                    };
                                                    echo esc($nivelTexto);
                                                    ?>
                                                </div>
                                                <input type="hidden" name="nivel" value="<?= $usuario->nivel ?>">
                                                <small class="text-muted">Solo el administrador puede cambiar el nivel</small>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
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
                                        <?= ($es_alta || $usuario->activo) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-semibold" for="activo">
                                        Usuario Activo
                                    </label>
                                </div>
                                <small class="text-muted ms-4">Permite el acceso al sistema</small>
                            </div>

                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="mostrar_email" name="mostrar_email" value="1"
                                        <?= !$es_alta && $usuario->mostrar_email ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="mostrar_email">
                                        <i class="bi bi-envelope-open"></i> Mostrar email público
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="mostrar_nombre_real" name="mostrar_nombre_real" value="1"
                                        <?= !$es_alta && $usuario->mostrar_nombre_real ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="mostrar_nombre_real">
                                        <i class="bi bi-person-badge"></i> Mostrar nombre real
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="permitir_contacto" name="permitir_contacto" value="1"
                                        <?= !$es_alta && $usuario->permitir_contacto ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="permitir_contacto">
                                        <i class="bi bi-chat-dots"></i> Permitir contacto
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Botones -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="<?= $es_perfil_propio ? base_url('dashboard') : base_url('usuarios') ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <div>
                            <?php if (!$es_alta && !$es_perfil_propio): ?>
                                <button type="button" class="btn btn-danger btn-lg me-2" onclick="confirmarBorrar(<?= $usuario->id ?>)">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="btnSubmit">
                                <i class="bi <?= $btn_icon ?>"></i> <?= $btn_text ?>
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="fecha_ultima_modificacion" value="<?= date('Y-m-d') ?>">
                    <?php if ($es_alta): ?>
                        <input type="hidden" name="fecha_alta" value="<?= date('Y-m-d') ?>">
                    <?php endif; ?>

                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>