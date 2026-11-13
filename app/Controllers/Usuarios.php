<?php

namespace App\Controllers;

use App\Models\UsuariosModel;
use App\Models\BibliotecaModel;

class Usuarios extends BaseController
{
    protected $db;
    protected $usuariosModel;
    protected $bibliotecaModel;
    protected $session;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->usuariosModel = new UsuariosModel();
        $this->bibliotecaModel = new BibliotecaModel();
        $this->session = session();

        helper(['url', 'form', 'filesystem']);
    }

    // ============================================
    // HELPERS DE PERMISOS (nuevos)
    // ============================================
    private function esAdmin(): bool
    {
        return in_array($this->rolUsuario, ['adm', 'superadmin']);
    }

    private function esSuperadmin(): bool
    {
        return $this->rolUsuario === 'superadmin';
    }

    /**
     * Verifica que el usuario actual pueda gestionar al usuario objetivo
     */
    private function puedeGestionar($usuarioObjetivo): bool
    {
        // Superadmin puede todo
        if ($this->esSuperadmin()) return true;

        // Admin solo puede gestionar usuarios de su biblioteca y de nivel inferior
        if ($this->rolUsuario === 'adm') {
            if (is_array($usuarioObjetivo)) {
                $bibObjetivo = $usuarioObjetivo['id_biblioteca'] ?? null;
                $nivelObjetivo = $usuarioObjetivo['nivel'] ?? null;
            } else {
                $bibObjetivo = $usuarioObjetivo->id_biblioteca ?? null;
                $nivelObjetivo = $usuarioObjetivo->nivel ?? null;
            }

            if ($bibObjetivo != $this->idBiblioteca) return false;
            if (in_array($nivelObjetivo, ['adm', 'superadmin'])) return false;
            return true;
        }

        // Usuario normal no puede gestionar a nadie
        return false;
    }

    // ============================================
    // LISTADO
    // ============================================
    public function index()
    {
        $filtroBiblioteca = $this->getBibliotecaFiltro();

        $data = [
            'titulo'      => 'GESTIÓN DE USUARIOS',
            // 'usuarios'    => $usuarios,
            'usuario'     => $this->usuariosModel->dataUsuario($this->userID),
            'esSuperadmin' => $this->esSuperadmin(),
            'bibliotecas'  => $this->esSuperadmin() ? $this->bibliotecaModel->where('activa', 1)->findAll() : [],
            'usuarios' => $this->usuariosModel->getUsuariosPaginados($filtroBiblioteca),
            'pager' => $this->usuariosModel->pager,
        ];

        return view('layout', ['contenido' => view('usuarios/index', $data)]);
    }

    // ============================================
    // ACTIVAR/DESACTIVAR USUARIO (AJAX)
    // ============================================
    public function toggleActivo($id = null)
    {
        // Si no es AJAX, redirigir (fallback)
        if (!$this->request->isAJAX()) {
            return redirect()->to('usuarios');
        }

        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID no proporcionado'
            ]);
        }

        $usuario = $this->usuariosModel->dataUsuario($id);

        if (!$usuario) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ]);
        }

        // Verificar permisos
        if (!$this->puedeGestionar($usuario) && $id != $this->session->get('id_usuario')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No puedes modificar este usuario'
            ]);
        }

        // No permitir desactivarse a sí mismo
        if ($id == $this->session->get('id_usuario') && $usuario->activo) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No puedes desactivarte a ti mismo'
            ]);
        }

        // Cambiar estado
        $nuevoEstado = $usuario->activo ? 0 : 1;

        if ($this->usuariosModel->update($id, ['activo' => $nuevoEstado])) {
            return $this->response->setJSON([
                'status' => 'ok',
                'activo' => $nuevoEstado,
                'message' => $nuevoEstado ? 'Usuario activado' : 'Usuario desactivado'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Error al cambiar el estado'
        ]);
    }

    // ============================================
    // NUEVO USUARIO
    // ============================================
    public function nuevo()
    {
        if (!$this->esAdmin()) {
            return redirect()->to('usuarios')->with('error', 'No tienes permiso para crear usuarios.');
        }

        $data = [
            'titulo'       => 'NUEVO USUARIO',
            'usuario'      => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
            'es_admin'     => $this->esAdmin(),
            'esSuperadmin' => $this->esSuperadmin(),
            'miBiblioteca' => $this->idBiblioteca,
            'bibliotecas'  => $this->bibliotecaModel->where('activa', 1)->orderBy('nombre', 'ASC')->findAll(),
        ];

        return view('layout', ['contenido' => view('usuarios/form', $data)]);
    }

    // ============================================
    // AÑADIR USUARIO
    // ============================================
    public function add_usuario()
    {
        if (!$this->esAdmin()) {
            return redirect()->to('usuarios')->with('error', 'Sin permiso.');
        }

        $rules = [
            'login'     => 'required|is_unique[usuarios.login]',
            'password'  => 'required|min_length[5]',
            'password1' => 'required|matches[password]',
            'nombre'    => 'required',
            'email'     => 'required|valid_email|is_unique[usuarios.email]',
            'pin'       => 'permit_empty|exact_length[4]|numeric',
        ];

        $messages = [
            'pin' => [
                'exact_length' => 'El PIN debe tener exactamente 4 dígitos',
                'numeric'      => 'El PIN solo puede contener números',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->nuevo();
        }

        $nivel = $this->request->getPost('nivel') ?? 'usu';

        // Validaciones de nivel
        if (!$this->esSuperadmin() && $nivel !== 'usu') {
            return redirect()->back()->withInput()->with('error', 'Solo puedes crear usuarios normales.');
        }

        // Determinar biblioteca
        if ($this->esSuperadmin()) {
            $idBiblioteca = (int) $this->request->getPost('id_biblioteca');
            if (!$idBiblioteca) {
                return redirect()->back()->withInput()->with('error', 'Debes seleccionar una biblioteca.');
            }
        } else {
            $idBiblioteca = $this->idBiblioteca;
        }

        $data = $this->prepararDatosUsuario($nivel);
        $data['id_biblioteca'] = $idBiblioteca;

        // Añadir PIN si se proporcionó
        $pin = $this->request->getPost('pin');
        if (!empty($pin)) {
            $data['pin'] = $pin;
        }

        $data['foto'] = $this->procesarFoto();
        // echo '<pre>';var_dump($this->request->getPost('activo'));echo '</pre>'; die();

        if ($this->usuariosModel->insert($data)) {
            return redirect()->to('usuarios')->with(
                'success',
                "El usuario <strong>{$data['login']}</strong> ha sido creado."
            );
        }

        return redirect()->back()->withInput()->with('error', 'Error al crear el usuario.');
    }

    // ============================================
    // EDITAR USUARIO
    // ============================================
    public function editar($id = null)
    {
        if ($id === null) {
            return redirect()->to('usuarios');
        }

        $elusuario = $this->usuariosModel->dataUsuario($id);

        if (!$elusuario) {
            return redirect()->to('usuarios')->with('error', 'Usuario no encontrado');
        }

        // Verificar permisos
        if (!$this->puedeGestionar($elusuario) && $id != $this->session->get('id_usuario')) {
            return redirect()->to('usuarios')->with('error', 'No puedes editar este usuario.');
        }

        $data = [
            'titulo'       => 'EDITAR USUARIO',
            'usuario'      => $this->usuariosModel->dataUsuario($this->userID),
            'elusuario'    => $elusuario,
            'es_admin'     => $this->esAdmin(),
            'esSuperadmin' => $this->esSuperadmin(),
            'miBiblioteca' => $this->idBiblioteca,
            'bibliotecas'  => $this->bibliotecaModel->where('activa', 1)->orderBy('nombre', 'ASC')->findAll(),
            'esPropio'     => $id == $this->session->get('id_usuario'),
        ];

        return view('layout', ['contenido' => view('usuarios/form', $data)]);
    }

    // ============================================
    // ACTUALIZAR USUARIO
    // ============================================
    public function update($id)
    {
        $usuarioActual = $this->usuariosModel->dataUsuario($id);

        if (!$usuarioActual) {
            return redirect()->to('usuarios')->with('error', 'Usuario no encontrado');
        }

        $esPropio = ($id == $this->session->get('id_usuario'));

        // Verificar permisos
        if (!$this->puedeGestionar($usuarioActual) && !$esPropio) {
            return redirect()->to('usuarios')->with('error', 'No puedes editar este usuario.');
        }

        // Inicializar datos
        $data = [
            'nombre'                => $this->request->getPost('nombre'),
            'apellidos'             => $this->request->getPost('apellidos'),
            'telefono'              => $this->request->getPost('telefono'),
            'fecha_nacimiento'      => $this->request->getPost('fecha_nacimiento'),
            'direccion'             => $this->request->getPost('direccion'),
            'poblacion'             => $this->request->getPost('poblacion'),
            'provincia'             => $this->request->getPost('provincia'),
            'c_postal'              => $this->request->getPost('c_postal'),
            'activo'                => $this->request->getPost('activo') ? 1 : 0,
            'mostrar_email'         => $this->request->getPost('mostrar_email') ? 1 : 0,
            'mostrar_nombre_real'   => $this->request->getPost('mostrar_nombre_real') ? 1 : 0,
            'permitir_contacto'     => $this->request->getPost('permitir_contacto') ? 1 : 0,
            'fecha_ultima_modificacion' => date('Y-m-d'),
        ];

        $rules = [
            'nombre' => 'required',
        ];

        // Validación del PIN
        $pin = $this->request->getPost('pin');
        if (!empty($pin) && $pin !== '****') {
            $rules['pin'] = 'exact_length[4]|numeric';
        }

        // Validación de contraseña
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[6]';
            $rules['password1'] = 'matches[password]';
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // ADMIN/SUPERADMIN puede cambiar login, nivel y biblioteca
        if ($this->esAdmin() && !$esPropio) {
            $login = $this->request->getPost('login');
            if ($login && $login !== $usuarioActual->login) {
                $rules['login'] = 'is_unique[usuarios.login]';
                $data['login'] = $login;
            }

            $nivel = $this->request->getPost('nivel');
            if ($nivel) {
                // Validar que no asigne niveles superiores al suyo
                if (!$this->esSuperadmin() && in_array($nivel, ['adm', 'superadmin'])) {
                    return redirect()->back()->withInput()->with('error', 'No puedes asignar ese nivel.');
                }
                $rules['nivel'] = 'in_list[adm,usu,inv,superadmin,admin]';
                $data['nivel'] = $nivel;
            }

            // Superadmin puede cambiar biblioteca
            if ($this->esSuperadmin()) {
                $idBiblioteca = (int) $this->request->getPost('id_biblioteca');
                if ($idBiblioteca) {
                    $data['id_biblioteca'] = $idBiblioteca;
                }
            }
        }

        $messages = [
            'nombre' => ['required' => 'El nombre es obligatorio'],
            'password1' => ['matches' => 'Las contraseñas no coinciden'],
            'login' => ['is_unique' => 'Este login ya está en uso'],
            'nivel' => ['in_list' => 'Nivel no válido'],
            'pin' => [
                'exact_length' => 'El PIN debe tener exactamente 4 dígitos',
                'numeric' => 'El PIN debe ser numérico'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->editar($id);
        }

        // Gestionar PIN
        $eliminarPin = $this->request->getPost('eliminar_pin');
        if ($eliminarPin) {
            $data['pin'] = null;
        } elseif (!empty($pin) && $pin !== '****') {
            $data['pin'] = $pin;
        }

        // Manejar foto
        $data['foto'] = $this->procesarFoto($usuarioActual->foto);

        if ($this->usuariosModel->update($id, $data)) {
            return redirect()->to('usuarios')->with(
                'success',
                "El usuario <strong>{$usuarioActual->login}</strong> ha sido actualizado."
            );
        }

        return redirect()->back()->withInput()->with('error', 'Error al actualizar.');
    }

    // ============================================
    // MÉTODOS AUXILIARES (sin cambios)
    // ============================================
    private function prepararDatosUsuario(string $nivel): array
    {
        return [
            'login'                 => $this->request->getPost('login'),
            'password'              => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'nombre'                => $this->request->getPost('nombre'),
            'apellidos'             => $this->request->getPost('apellidos'),
            'email'                 => $this->request->getPost('email'),
            'telefono'              => $this->request->getPost('telefono'),
            'fecha_nacimiento'      => $this->request->getPost('fecha_nacimiento'),
            'direccion'             => $this->request->getPost('direccion'),
            'poblacion'             => $this->request->getPost('poblacion'),
            'provincia'             => $this->request->getPost('provincia'),
            'c_postal'              => $this->request->getPost('c_postal'),
            'nivel'                 => $nivel,
            'activo'                => $this->request->getPost('activo') ? 1 : 0,
            'mostrar_email'         => $this->request->getPost('mostrar_email') ? 1 : 0,
            'mostrar_nombre_real'   => $this->request->getPost('mostrar_nombre_real') ? 1 : 0,
            'permitir_contacto'     => $this->request->getPost('permitir_contacto') ? 1 : 0,
            'fecha_alta'            => date('Y-m-d'),
            'fecha_ultima_modificacion' => date('Y-m-d'),
        ];
    }

    private function procesarFoto(?string $fotoActual = null): ?string
    {
        $foto = $this->request->getFile('foto');
        $eliminarFoto = $this->request->getPost('eliminar_foto');

        if ($eliminarFoto && $fotoActual) {
            $this->eliminarArchivoFoto($fotoActual);
            return null;
        }

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            if ($fotoActual) {
                $this->eliminarArchivoFoto($fotoActual);
            }

            $nombreFoto = $foto->getRandomName();
            $ruta = ROOTPATH . 'public/uploads/usuarios/';

            if (!is_dir($ruta)) {
                mkdir($ruta, 0755, true);
            }

            $foto->move($ruta, $nombreFoto);
            return $nombreFoto;
        }

        return $fotoActual;
    }

    private function eliminarArchivoFoto(string $nombreFoto): void
    {
        $ruta = ROOTPATH . 'public/uploads/usuarios/' . $nombreFoto;
        if (file_exists($ruta)) {
            unlink($ruta);
        }
    }

    // ============================================
    // VER USUARIO
    // ============================================
    /**
     * Ver ficha de usuario en modo solo lectura
     */
    public function ver(int $id)
    {
        // $usuarioModel = new \App\Models\UsuariosModel();
        // $bibliotecasModel = new \App\Models\BibliotecasModel();

        $usuario = $this->usuariosModel->find($id);

        if (!$usuario) {
            return redirect()->to('usuarios')->with('error', 'Usuario no encontrado');
        }

        // Obtener nombre de biblioteca
        $bibliotecaNombre = null;
        if (!empty($usuario->id_biblioteca)) {
            $bib = $this->bibliotecaModel->find($usuario->id_biblioteca);
            $bibliotecaNombre = $bib['nombre'] ?? null;
        }

        $data = [
            'titulo' => 'Ficha de ' . esc($usuario->nombre),
            'usuario' => $usuario,
            'bibliotecaNombre' => $bibliotecaNombre,
            'tiempoAutoOcultar' => 15
        ];

        return view('layout', ['contenido' => view('usuarios/ver_usuario', $data)]);
    }
    // public function ver($id)
    // {
    //     $usuario = $this->usuariosModel->dataUsuario($id);

    //     if (!$usuario) {
    //         return redirect()->to('usuarios')->with('error', 'Usuario no encontrado');
    //     }

    //     // Verificar que pueda verlo (misma biblioteca o superadmin)
    //     if (!$this->esSuperadmin()) {
    //         $bibUsuario = is_array($usuario) ? ($usuario['id_biblioteca'] ?? null) : ($usuario->id_biblioteca ?? null);
    //         if ($bibUsuario != $this->idBiblioteca) {
    //             return redirect()->to('usuarios')->with('error', 'No puedes ver este usuario.');
    //         }
    //     }

    //     $data = [
    //         'titulo'       => 'PERFIL DE USUARIO',
    //         'data_usuario' => $usuario,
    //         'usuario'      => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
    //     ];

    //     return view('layout', ['contenido' => view('ver_usuario', $data)]);
    // }

    // ============================================
    // ACTUALIZAR PIN (AJAX)
    // ============================================
    public function actualizarPin()
    {
        $id = $this->request->getPost('id');
        $pin = $this->request->getPost('pin');
        $eliminar = $this->request->getPost('eliminar');

        if (empty($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID de usuario requerido']);
        }

        $usuario = $this->usuariosModel->dataUsuario($id);
        if (!$usuario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Usuario no encontrado']);
        }

        // Verificar permisos
        $esPropio = ($id == $this->session->get('id_usuario'));
        $esAdmin = $this->esAdmin();

        if (!$esAdmin && !$esPropio) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sin permisos']);
        }

        // Admin solo puede modificar PIN de su biblioteca
        if ($esAdmin && !$this->esSuperadmin() && !$esPropio) {
            $bibUsuario = is_array($usuario) ? ($usuario['id_biblioteca'] ?? null) : ($usuario->id_biblioteca ?? null);
            if ($bibUsuario != $this->idBiblioteca) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Usuario de otra biblioteca']);
            }
        }

        $data = ['fecha_ultima_modificacion' => date('Y-m-d')];

        if ($eliminar) {
            $data['pin'] = null;
        } else {
            if (!preg_match('/^\d{4}$/', $pin)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'El PIN debe tener 4 dígitos numéricos']);
            }
            $data['pin'] = $pin;
        }

        if ($this->usuariosModel->update($id, $data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => $eliminar ? 'PIN eliminado correctamente' : 'PIN guardado correctamente'
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Error al guardar']);
    }

    // ============================================
    // BORRAR (AJAX)
    // ============================================
    public function borrar($id = null)
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getPost('id') ?? $id;

            if (!$id) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'ID no proporcionado']);
            }

            $usuario = $this->usuariosModel->dataUsuario($id);

            if (!$usuario) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Usuario no encontrado']);
            }

            // Verificar permisos
            if (!$this->puedeGestionar($usuario)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Sin permisos']);
            }

            if ($id == $this->session->get('id_usuario')) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'No puedes eliminarte a ti mismo']);
            }

            // No eliminar el último superadmin
            $nivelUsuario = is_array($usuario) ? ($usuario['nivel'] ?? null) : ($usuario->nivel ?? null);
            if ($nivelUsuario === 'superadmin') {
                $totalSuperadmins = $this->usuariosModel->where('nivel', 'superadmin')->countAllResults();
                if ($totalSuperadmins <= 1) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'No puedes eliminar el único superadmin']);
                }
            }

            if ($usuario->foto ?? ($usuario['foto'] ?? null)) {
                $this->eliminarArchivoFoto($usuario->foto ?? $usuario['foto']);
            }

            return $this->response->setJSON(
                $this->usuariosModel->delete($id)
                    ? ['status' => 'ok']
                    : ['status' => 'error', 'message' => 'Error al eliminar']
            );
        }

        return redirect()->to('usuarios');
    }

    // ============================================
    // PERFIL PROPIO
    // ============================================
    public function perfil()
    {
        return $this->editar($this->session->get('id_usuario'));
    }

    public function updatePerfil()
    {
        $id = $this->session->get('id_usuario');

        $usuarioActual = $this->usuariosModel->dataUsuario($id);
        if (!$usuarioActual) {
            return redirect()->to('logout')->with('error', 'Sesión inválida');
        }

        $rules = [
            'nombre' => 'required',
            'pin'    => 'permit_empty|exact_length[4]|numeric',
        ];

        $messages = [
            'nombre' => ['required' => 'El nombre es obligatorio'],
            'pin' => [
                'exact_length' => 'El PIN debe tener exactamente 4 dígitos',
                'numeric'      => 'El PIN solo puede contener números',
            ],
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[6]';
            $rules['password1'] = 'matches[password]';
            $messages['password'] = ['min_length' => 'La nueva contraseña debe tener al menos 6 caracteres'];
            $messages['password1'] = ['matches' => 'Las contraseñas no coinciden'];
        }

        if (!$this->validate($rules, $messages)) {
            return $this->perfil();
        }

        $data = [
            'nombre'                => $this->request->getPost('nombre'),
            'apellidos'             => $this->request->getPost('apellidos'),
            'telefono'              => $this->request->getPost('telefono'),
            'fecha_nacimiento'      => $this->request->getPost('fecha_nacimiento'),
            'direccion'             => $this->request->getPost('direccion'),
            'poblacion'             => $this->request->getPost('poblacion'),
            'provincia'             => $this->request->getPost('provincia'),
            'c_postal'              => $this->request->getPost('c_postal'),
            'mostrar_email'         => $this->request->getPost('mostrar_email') ? 1 : 0,
            'mostrar_nombre_real'   => $this->request->getPost('mostrar_nombre_real') ? 1 : 0,
            'permitir_contacto'     => $this->request->getPost('permitir_contacto') ? 1 : 0,
            'fecha_ultima_modificacion' => date('Y-m-d'),
        ];

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $eliminarPin = $this->request->getPost('eliminar_pin');
        $pin = $this->request->getPost('pin');

        if ($eliminarPin) {
            $data['pin'] = null;
        } elseif (!empty($pin) && $pin !== '****') {
            $data['pin'] = $pin;
        }

        $data['foto'] = $this->procesarFoto($usuarioActual->foto ?? null);

        if ($this->usuariosModel->update($id, $data)) {
            return redirect()->to('usuarios/perfil')->with('success', 'Tu perfil ha sido actualizado correctamente.');
        }

        return redirect()->back()->withInput()->with('error', 'Error al actualizar tu perfil.');
    }
}
