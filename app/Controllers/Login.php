<?php

namespace App\Controllers;

use App\Models\LoginModel;
use App\Models\BibliotecaModel;
use CodeIgniter\Controller;

class Login extends Controller
{
    protected $session;
    protected $loginModel;
    protected $bibliotecaModel;

    public function __construct()
    {
        $this->session = service('session');
        $this->loginModel = new LoginModel();
        $this->bibliotecaModel = new BibliotecaModel();
        helper(['form', 'url', 'cookie']);
    }

    public function index()
    {
        // Si ya está logueado, redirige según nivel
        $nivel = $this->session->get('nivel');
        if ($nivel) {
            return $this->redirectByNivel($nivel);
        }

        return view('login_view', ['titulo' => 'ainvar.net - Login']);
    }

    public function authenticate()
    {
        $login    = $this->request->getPost('login');
        $password = $this->request->getPost('password');
        $recordar = $this->request->getPost('recordar');

        // Validación simple
        if (!$login || !$password) {
            $this->session->setFlashdata('error', 'Login y password requeridos');
            return redirect()->to(base_url('login'));
            exit;
        }

        $user = $this->loginModel->getUserByLogin($login, $password);

        if (!$user || !password_verify($password, $user->password)) {
            $this->session->setFlashdata('error', 'Login o password incorrectos');
            return redirect()->to(base_url('login'));
        }
        // Login correcto
        $this->session->set([
            'is_logued_in' => true,
            'id_usuario'   => $user->id,
            'nivel'        => $user->nivel,
            'nombre'       => $user->nombre,
            'id_biblioteca'     => $user->id_biblioteca,  // <-- CLAVE
            'nombre_biblioteca' => '',
            'lang'         => 'esp',
        ]);

        $biblioteca = $this->bibliotecaModel->find($user->id_biblioteca);
        if ($biblioteca) {
            session()->set([
                'nombre_biblioteca' => $biblioteca['nombre'],
                'color_biblioteca'  => $biblioteca['color_tema']
            ]);
        }

        // Cookie "recuerda"
        if ($recordar === 'SI') {
            set_cookie('recuerda', "SI|{$user->id}|{$user->nivel}|{$user->nombre}", 1296000, '/');
        }

        return $this->redirectByNivel($user->nivel);
    }

    public function logout()
    {
        $this->session->destroy();
        delete_cookie('recuerda', '/', '.biblio2.test');
        return redirect()->to(base_url('login'));
    }

    private function redirectByNivel($nivel)
    {
        return match ($nivel) {
            'superadmin', 'adm' => redirect()->to('dashboard'),
            'usu' => redirect()->to('dashboard'),
            'inv' => redirect()->to('admin/invitado'),
            default => redirect()->to('login'),
        };
    }

   // ========== API para Flutter ==========

    /**
     * POST api/login
     * Body JSON: { "login": "admin", "password": "admin" }
     */
    public function apiLogin()
    {
        $json = $this->request->getJSON();
        $login = $json->login ?? '';
        $password = $json->password ?? '';

        if (empty($login) || empty($password)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Nick y password requeridos']);
        }

        // Tu modelo ya busca por login (nick)
        $user = $this->loginModel->getUserByLogin($login, $password);

        if (!$user || !password_verify($password, $user->password)) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['error' => 'Credenciales incorrectas']);
        }

        // Crear sesión
        $this->session->set([
            'is_logued_in' => true,
            'id_usuario'   => $user->id,
            'nivel'        => $user->nivel,
            'nombre'       => $user->nombre,
            'login'        => $user->login,  // Guardar nick también
        ]);

        // IMPORTANTE: regenerar ID de sesión para seguridad
        $this->session->regenerate();

        return $this->response->setJSON([
            'success' => true,
            'usuario' => [
                'id'     => $user->id,
                'nombre' => $user->nombre,
                'nivel'  => $user->nivel,
                'login'  => $user->login,
            ]
        ]);
    }

    /**
     * POST api/logout
     */
    public function apiLogout()
    {
        $this->session->destroy();
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * GET api/usuario/me
     */
    public function me()
    {
        if (!$this->session->get('is_logued_in')) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['error' => 'No autenticado']);
        }

        return $this->response->setJSON([
            'id'     => $this->session->get('id_usuario'),
            'nombre' => $this->session->get('nombre'),
            'nivel'  => $this->session->get('nivel'),
            'login'  => $this->session->get('login'),
        ]);
    }
}
