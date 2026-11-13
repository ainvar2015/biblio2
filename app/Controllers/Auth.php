<?php

namespace App\Controllers;

use App\Models\UsuariosModel;

class Auth extends BaseController
{
    protected $usuariosModel;
    protected $session;

    public function __construct()
    {
        $this->usuariosModel = new UsuariosModel();
        $this->session = session();
        helper(['url', 'form']);
    }

    /**
     * Bloqueo manual de pantalla por el usuario
     */
    public function lock()
    {
        $session = session();

        // Marcar pantalla como bloqueada
        $session->set('pantalla_bloqueada', true);

        // Guardar la URL actual para redirigir después de desbloquear (opcional)
        $session->set('url_anterior', previous_url() ?? base_url('dashboard'));

        return redirect()->to('auth/bloquear');
    }

    /**
     * Mostrar pantalla de bloqueo
     */
    public function bloquear()
    {
        // Verificar que haya sesión activa
        if (!$this->session->get('id_usuario')) {
            return redirect()->to('auth/login');
        }

        // Forzar que la sesión esté marcada como bloqueada
        $this->session->set('pantalla_bloqueada', true);

        // Guardar URL actual para redirección después de desbloqueo
        $this->session->set('url_bloqueo', previous_url() ?? base_url('prestamos'));

        // Obtener datos del usuario
        $idUsuario = $this->session->get('id_usuario');
        $usuario = $this->usuariosModel->find($idUsuario);

        $data = [
            'titulo' => 'Pantalla Bloqueada',
            'usuario_nombre' => $this->session->get('nombre'),
            'foto_usuario' => $usuario->foto ?? null,
            'token_sesion' => $this->generarTokenBloqueo()
        ];

        // Crear respuesta con headers anti-caché
        $response = $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', 'Mon, 01 Jan 1990 00:00:00 GMT');

        return $this->response->setBody(view('auth/bloqueo', $data));
    }

    /**
     * Verificar desbloqueo (PIN o Password)
     */
    public function verificarBloqueo()
    {
        $tipo = $this->request->getPost('tipo');
        $valor = $this->request->getPost('valor');
        $token = $this->request->getPost('token');


        // Verificar token
        if (!$this->verificarTokenBloqueo($token)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Sesión inválida. Recarga la página.'
            ]);
        }

        $idUsuario = $this->session->get('id_usuario');
        $usuario = $this->usuariosModel->find($idUsuario);

        if (!$usuario) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ]);
        }

        $desbloqueado = false;

        if ($tipo === 'pin') {
            // Verificar PIN (como objeto, no array)
            if (!empty($usuario->pin)) {
                $desbloqueado = ($valor === $usuario->pin);
            }
        } elseif ($tipo === 'password') {
            // Verificar contraseña (como objeto)
            $desbloqueado = password_verify($valor, $usuario->password);
        }

        if ($desbloqueado) {
            $this->session->remove('token_bloqueo');
            $this->session->remove('pantalla_bloqueada');

            return $this->response->setJSON([
                'status' => 'ok',
                'redirect' => $this->session->get('url_bloqueo') ?? base_url('prestamos')
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Credenciales incorrectas'
        ]);
    }

    /**
     * Generar token temporal para bloqueo
     */
    private function generarTokenBloqueo()
    {
        $token = bin2hex(random_bytes(16));
        $this->session->set('token_bloqueo', $token);
        return $token;
    }

    /**
     * Verificar token de bloqueo
     */
    private function verificarTokenBloqueo($token)
    {
        return $this->session->get('token_bloqueo') === $token;
    }

    /**
     * Verificar PIN del usuario
     */
    private function verificarPin($pin, $usuario)
    {
        if (!preg_match('/^\d{4}$/', $pin)) {
            return false;
        }

        if (!empty($usuario->pin)) {
            return $pin == $usuario->pin; // Comparación directa
        }

        return false;
    }

    /**
     * Auto-bloqueo por inactividad (llamado por AJAX)
     */
    public function verificarInactividad()
    {
        $ultimaActividad = $this->session->get('ultima_actividad');
        $tiempoInactividad = time() - ($ultimaActividad ?? 0);
        $limiteInactividad = 2400; // 5 minutos en segundos

        if ($tiempoInactividad > $limiteInactividad) {
            return $this->response->setJSON([
                'bloquear' => true,
                'tiempo_inactivo' => $tiempoInactividad
            ]);
        }

        return $this->response->setJSON([
            'bloquear' => false,
            'tiempo_restante' => $limiteInactividad - $tiempoInactividad
        ]);
    }

    /**
     * Actualizar actividad (llamado periódicamente)
     */
    public function pingActividad()
    {
        $this->session->set('ultima_actividad', time());
        return $this->response->setJSON(['ok' => true]);
    }

}
