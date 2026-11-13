<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Anti-caché -->
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    
    <title><?= esc($titulo) ?> - Biblioteca</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 10px;
        }

        .lock-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 380px;
            width: 100%;
            text-align: center;
            position: relative;
        }

        /* Avatar del usuario */
        .user-avatar {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 0.75rem;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .user-avatar .status-indicator {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 16px;
            height: 16px;
            background: var(--success-color);
            border: 2px solid #fff;
            border-radius: 50%;
        }

        .user-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }

        .user-role {
            color: var(--secondary-color);
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }

        /* Indicadores del PIN */
        .pin-display {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            min-height: 24px;
        }

        .pin-dot {
            width: 14px;
            height: 14px;
            border: 2px solid #e2e8f0;
            border-radius: 50%;
            background: transparent;
            transition: all 0.3s ease;
        }

        .pin-dot.filled {
            background: var(--primary-color);
            border-color: var(--primary-color);
            transform: scale(1.1);
        }

        .pin-dot.error {
            background: var(--danger-color);
            border-color: var(--danger-color);
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }

        /* Input de contraseña */
        .password-input-container {
            display: none;
            margin-bottom: 1rem;
        }

        .password-input-container.active {
            display: block;
        }

        .password-input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            text-align: center;
        }

        .password-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        /* Teclado numérico */
        .keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            margin-bottom: 1rem;
            max-width: 280px;
            margin-left: auto;
            margin-right: auto;
        }

        .key-btn {
            aspect-ratio: 1;
            border: none;
            border-radius: 12px;
            background: #f7fafc;
            color: #2d3748;
            font-size: 1.25rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .key-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
        }

        .key-btn:active {
            transform: translateY(0);
        }

        .key-btn.action-btn {
            background: #fee;
            color: #c53030;
        }

        .key-btn.action-btn:hover {
            background: #c53030;
            color: white;
        }

        .key-btn.unlock-btn {
            background: var(--success-color);
            color: white;
        }

        .key-btn.unlock-btn:hover {
            background: #17a673;
        }

        /* Toggle método */
        .method-toggle {
            margin-bottom: 1rem;
        }

        .method-btn {
            background: none;
            border: 2px solid #e2e8f0;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            color: var(--secondary-color);
            cursor: pointer;
            transition: all 0.2s;
        }

        .method-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .method-btn.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        /* ==========================================
           SISTEMA TOAST - NOTIFICACIONES
           ========================================== */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1055;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast-custom {
            background: white;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            min-width: 300px;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideInRight 0.3s ease-out;
            border-left: 4px solid;
        }

        .toast-custom.toast-success {
            border-left-color: var(--success-color);
        }

        .toast-custom.toast-error {
            border-left-color: var(--danger-color);
        }

        .toast-custom.toast-warning {
            border-left-color: var(--warning-color);
        }

        .toast-custom.toast-info {
            border-left-color: var(--primary-color);
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        .toast-custom.hiding {
            animation: slideOutRight 0.3s ease-in forwards;
        }

        .toast-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .toast-success .toast-icon {
            background: rgba(28, 200, 138, 0.1);
            color: var(--success-color);
        }

        .toast-error .toast-icon {
            background: rgba(231, 74, 59, 0.1);
            color: var(--danger-color);
        }

        .toast-warning .toast-icon {
            background: rgba(246, 194, 62, 0.1);
            color: var(--warning-color);
        }

        .toast-info .toast-icon {
            background: rgba(78, 115, 223, 0.1);
            color: var(--primary-color);
        }

        .toast-content {
            flex: 1;
            text-align: left;
        }

        .toast-title {
            font-weight: 600;
            font-size: 0.95rem;
            color: #2d3748;
            margin-bottom: 2px;
        }

        .toast-message {
            font-size: 0.85rem;
            color: var(--secondary-color);
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .toast-close:hover {
            background: #f7fafc;
            color: #2d3748;
        }

        /* Progress bar en toast */
        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            border-radius: 0 0 0 12px;
            animation: progress 5s linear forwards;
        }

        .toast-success .toast-progress {
            background: var(--success-color);
        }

        .toast-error .toast-progress {
            background: var(--danger-color);
        }

        .toast-warning .toast-progress {
            background: var(--warning-color);
        }

        .toast-info .toast-progress {
            background: var(--primary-color);
        }

        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }

        /* Footer */
        .lock-footer {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .lock-footer a {
            color: var(--secondary-color);
            text-decoration: none;
            font-size: 0.8rem;
            transition: color 0.2s;
        }

        .lock-footer a:hover {
            color: var(--primary-color);
        }

        /* Loading overlay */
        .spinner-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.95);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .spinner-overlay.active {
            display: flex;
        }

        /* Ocultar keypad cuando se usa contraseña */
        .keypad.hidden, .pin-display.hidden {
            display: none;
        }
    </style>
</head>
<body>

    <!-- Contenedor de Toasts -->
    <div class="toast-container" id="toastContainer"></div>

    <div class="lock-container">
        <!-- Avatar del Usuario -->
        <div class="user-avatar">
            <?php if (!empty($foto_usuario)): ?>
                <img src="<?= base_url('uploads/usuarios/' . esc($foto_usuario)) ?>" 
                     alt="<?= esc($usuario_nombre) ?>" 
                     onerror="this.src='<?= base_url('assets/img/default-user.png') ?>'">
            <?php else: ?>
                <img src="<?= base_url('assets/img/default-user.png') ?>" 
                     alt="Usuario">
            <?php endif; ?>
            <div class="status-indicator"></div>
        </div>

        <!-- Información del usuario -->
        <h2 class="user-name"><?= esc($usuario_nombre) ?></h2>
        <p class="user-role">Sesión bloqueada</p>

        <!-- Toggle método -->
        <div class="method-toggle">
            <button type="button" class="method-btn active" id="btnModoPin" onclick="cambiarModo('pin')">
                <i class="bi bi-grid-3x3"></i> PIN
            </button>
            <button type="button" class="method-btn" id="btnModoPass" onclick="cambiarModo('password')">
                <i class="bi bi-keyboard"></i> Contraseña
            </button>
        </div>

        <!-- Indicadores del PIN -->
        <div class="pin-display" id="pinDisplay">
            <div class="pin-dot" data-index="0"></div>
            <div class="pin-dot" data-index="1"></div>
            <div class="pin-dot" data-index="2"></div>
            <div class="pin-dot" data-index="3"></div>
        </div>

        <!-- Input de contraseña (oculto inicialmente) -->
        <div class="password-input-container" id="passwordContainer">
            <input type="password" 
                   class="password-input" 
                   id="passwordInput" 
                   placeholder="Ingresa tu contraseña"
                   maxlength="50">
        </div>

        <!-- Teclado Numérico -->
        <div class="keypad" id="keypad">
            <button type="button" class="key-btn" data-num="1">1</button>
            <button type="button" class="key-btn" data-num="2">2</button>
            <button type="button" class="key-btn" data-num="3">3</button>
            <button type="button" class="key-btn" data-num="4">4</button>
            <button type="button" class="key-btn" data-num="5">5</button>
            <button type="button" class="key-btn" data-num="6">6</button>
            <button type="button" class="key-btn" data-num="7">7</button>
            <button type="button" class="key-btn" data-num="8">8</button>
            <button type="button" class="key-btn" data-num="9">9</button>
            <button type="button" class="key-btn action-btn" id="btnBorrar" title="Borrar">
                <i class="bi bi-backspace"></i>
            </button>
            <button type="button" class="key-btn" data-num="0">0</button>
            <button type="button" class="key-btn unlock-btn" id="btnDesbloquear" title="Desbloquear">
                <i class="bi bi-unlock-fill"></i>
            </button>
        </div>

        <!-- Botón desbloquear para contraseña -->
        <button type="button" 
                class="btn btn-success w-100" 
                id="btnDesbloquearPass" 
                style="display: none; border-radius: 12px; padding: 0.75rem;">
            <i class="bi bi-unlock-fill"></i> Desbloquear
        </button>

        <!-- Footer -->
        <div class="lock-footer">
            <a href="<?= base_url('logout') ?>">
                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
            </a>
        </div>
    </div>

    <!-- Loading overlay -->
    <div class="spinner-overlay" id="spinnerOverlay">
        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="text-muted">Verificando...</p>
    </div>

    <!-- CSRF Token para JS -->
    <script>
        const CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
        const CSRF_HASH = '<?= csrf_hash() ?>';
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Variables globales
        let modoActual = 'pin';
        let pin = '';
        const maxDigits = 4;
        
        // URLs y tokens
        const urlVerificar = '<?= base_url('auth/verificarBloqueo') ?>';
        const tokenSesion = '<?= $token_sesion ?>';

        // Elementos DOM
        const pinDisplay = document.getElementById('pinDisplay');
        const keypad = document.getElementById('keypad');
        const passwordContainer = document.getElementById('passwordContainer');
        const passwordInput = document.getElementById('passwordInput');
        const btnDesbloquearPass = document.getElementById('btnDesbloquearPass');
        const toastContainer = document.getElementById('toastContainer');
        const spinnerOverlay = document.getElementById('spinnerOverlay');

        // ==========================================
        // SISTEMA TOAST
        // ==========================================

        function mostrarToast(mensaje, tipo = 'error', titulo = null, duracion = 5000) {
            // Iconos según tipo
            const iconos = {
                success: 'bi-check-circle-fill',
                error: 'bi-x-circle-fill',
                warning: 'bi-exclamation-triangle-fill',
                info: 'bi-info-circle-fill'
            };

            // Títulos por defecto
            const titulos = {
                success: '¡Éxito!',
                error: 'Error',
                warning: 'Advertencia',
                info: 'Información'
            };

            const icono = iconos[tipo] || iconos.error;
            const tituloFinal = titulo || titulos[tipo] || 'Mensaje';

            // Crear elemento toast
            const toast = document.createElement('div');
            toast.className = `toast-custom toast-${tipo}`;
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="bi ${icono}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${tituloFinal}</div>
                    <div class="toast-message">${mensaje}</div>
                </div>
                <button class="toast-close" onclick="cerrarToast(this.parentElement)">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="toast-progress"></div>
            `;

            // Agregar al contenedor
            toastContainer.appendChild(toast);

            // Auto-cerrar después de la duración
            const timeoutId = setTimeout(() => {
                cerrarToast(toast);
            }, duracion);

            // Guardar referencia al timeout para poder cancelarlo si se cierra manualmente
            toast.dataset.timeoutId = timeoutId;

            // Efecto de entrada
            requestAnimationFrame(() => {
                toast.style.opacity = '1';
            });
        }

        function cerrarToast(toast) {
            if (!toast) return;
            
            // Cancelar timeout si existe
            if (toast.dataset.timeoutId) {
                clearTimeout(parseInt(toast.dataset.timeoutId));
            }

            // Animación de salida
            toast.classList.add('hiding');
            
            // Remover del DOM después de la animación
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.parentElement.removeChild(toast);
                }
            }, 300);
        }

        // Función helper para mensajes comunes
        function mostrarError(mensaje) {
            mostrarToast(mensaje, 'error');
        }

        function mostrarExito(mensaje) {
            mostrarToast(mensaje, 'success');
        }

        function mostrarAdvertencia(mensaje) {
            mostrarToast(mensaje, 'warning', 'Atención');
        }

        function mostrarInfo(mensaje) {
            mostrarToast(mensaje, 'info', 'Información');
        }

        // ==========================================
        // FUNCIONES DE UI
        // ==========================================

        function cambiarModo(modo) {
            modoActual = modo;
            
            // Actualizar botones
            document.getElementById('btnModoPin').classList.toggle('active', modo === 'pin');
            document.getElementById('btnModoPass').classList.toggle('active', modo === 'password');
            
            // Mostrar/ocultar elementos
            if (modo === 'pin') {
                pinDisplay.classList.remove('hidden');
                keypad.classList.remove('hidden');
                passwordContainer.classList.remove('active');
                btnDesbloquearPass.style.display = 'none';
                pin = '';
                actualizarDisplayPin();
            } else {
                pinDisplay.classList.add('hidden');
                keypad.classList.add('hidden');
                passwordContainer.classList.add('active');
                btnDesbloquearPass.style.display = 'block';
                setTimeout(() => passwordInput.focus(), 100);
            }
        }

        function actualizarDisplayPin() {
            const dots = pinDisplay.querySelectorAll('.pin-dot');
            dots.forEach((dot, index) => {
                dot.classList.toggle('filled', index < pin.length);
                dot.classList.remove('error');
            });
        }

        function mostrarErrorPin() {
            const dots = pinDisplay.querySelectorAll('.pin-dot');
            dots.forEach(dot => dot.classList.add('error'));
            setTimeout(() => {
                pin = '';
                actualizarDisplayPin();
            }, 800);
        }

        // ==========================================
        // LÓGICA DEL PIN
        // ==========================================

        function agregarNumero(num) {
            if (pin.length < maxDigits) {
                pin += num;
                actualizarDisplayPin();
                
                if (pin.length === maxDigits) {
                    setTimeout(() => verificarDesbloqueo('pin', pin), 200);
                }
            }
        }

        function borrarUltimo() {
            if (pin.length > 0) {
                pin = pin.slice(0, -1);
                actualizarDisplayPin();
            }
        }

        // ==========================================
        // VERIFICACIÓN AJAX
        // ==========================================

        async function verificarDesbloqueo(tipo, valor) {
            if (!valor || valor.length === 0) {
                mostrarError('Ingresa un valor válido');
                return;
            }

            spinnerOverlay.classList.add('active');

            try {
                const formData = new URLSearchParams();
                formData.append('tipo', tipo);
                formData.append('valor', valor);
                formData.append('token', tokenSesion);
                formData.append(CSRF_TOKEN_NAME, CSRF_HASH);

                const response = await fetch(urlVerificar, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData.toString()
                });

                const data = await response.json();

                if (data.status === 'ok') {
                    mostrarExito('Desbloqueando...');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 500);
                } else {
                    spinnerOverlay.classList.remove('active');
                    mostrarError(data.message || 'Credenciales incorrectas');
                    
                    if (tipo === 'pin') {
                        mostrarErrorPin();
                    } else {
                        passwordInput.value = '';
                        passwordInput.focus();
                    }
                }
            } catch (error) {
                spinnerOverlay.classList.remove('active');
                mostrarError('Error de conexión. Intenta de nuevo.');
                console.error('Error:', error);
            }
        }

        // ==========================================
        // EVENT LISTENERS
        // ==========================================

        // Teclado numérico
        document.querySelectorAll('.key-btn[data-num]').forEach(btn => {
            btn.addEventListener('click', () => agregarNumero(btn.dataset.num));
        });

        document.getElementById('btnBorrar').addEventListener('click', borrarUltimo);
        
        document.getElementById('btnDesbloquear').addEventListener('click', () => {
            if (pin.length === maxDigits) {
                verificarDesbloqueo('pin', pin);
            } else {
                mostrarAdvertencia('Completa los 4 dígitos del PIN');
            }
        });

        // Botón desbloquear para contraseña
        btnDesbloquearPass.addEventListener('click', () => {
            const pass = passwordInput.value.trim();
            if (pass.length > 0) {
                verificarDesbloqueo('password', pass);
            } else {
                mostrarAdvertencia('Ingresa tu contraseña');
            }
        });

        // Enter en input de contraseña
        passwordInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const pass = passwordInput.value.trim();
                if (pass.length > 0) {
                    verificarDesbloqueo('password', pass);
                }
            }
        });

        // Teclado físico para PIN
        document.addEventListener('keydown', (e) => {
            if (modoActual !== 'pin') return;
            
            if (e.key >= '0' && e.key <= '9') {
                agregarNumero(e.key);
            } else if (e.key === 'Backspace') {
                borrarUltimo();
            } else if (e.key === 'Enter' && pin.length === maxDigits) {
                verificarDesbloqueo('pin', pin);
            }
        });

        // Anti-navegación
        history.pushState(null, null, location.href);
        window.onpopstate = function() {
            history.pushState(null, null, location.href);
        };
    </script>
</body>
</html>