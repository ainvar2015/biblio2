<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuariosModel;

class HistorialController extends BaseController
{
    protected $db;
    protected $usuariosModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->usuariosModel = new UsuariosModel();
    }

    /**
     * Historial de préstamos de un socio (por ID numérico)
     */
    public function porUsuario($id)
    {
        // Validar que sea numérico
        if (!is_numeric($id)) {
            return redirect()->to('usuarios/buscar')->with('error', 'ID de usuario inválido');
        }

        $prestamos = $this->db->table('biblio2_prestamos p')
            ->select('p.*, l.titulo as titulo_libro')
            ->join('biblio2_libros l', 'l.id = p.id_libro', 'left')
            ->where('p.id_usuario', $id)
            ->orderBy('p.fecha_prestamo', 'DESC')
            ->get()
            ->getResultArray();

        $usuario = $this->usuariosModel->find($id);

        $data = [
            'titulo' => 'Historial de ' . esc($usuario['nombre'] ?? 'Usuario'),
            'prestamos' => $prestamos,
            'esExterno' => false,
            'usuario' => $usuario,
        ];

        return view('layout', [
            'contenido' => view('usuarios/historial_usuario', $data)
        ]);
    }

    /**
     * Historial de préstamos de un externo (por Base64URL)
     */
    public function porExterno(string $externoId)
    {
        // Decodificar Base64URL
        $decoded = base64UrlDecode($externoId);

        if (empty($decoded)) {
            return redirect()->to('usuarios/buscar')->with('error', 'Identificador inválido');
        }

        [$nombre, $dni] = array_pad(explode('|', $decoded, 2), 2, null);

        if (empty($nombre)) {
            return redirect()->to('usuarios/buscar')->with('error', 'Nombre no encontrado en el identificador');
        }

        // Buscar préstamos del externo
        $builder = $this->db->table('biblio2_prestamos p')
            ->select('p.*, l.titulo as titulo_libro')
            ->join('biblio2_libros l', 'l.id = p.id_libro', 'left')
            ->where('p.tipo_prestatario', 'externo')
            ->where('p.nombre_externo', $nombre);

        if (!empty($dni)) {
            $builder->where('p.dni_externo', $dni);
        }

        $prestamos = $builder->orderBy('p.fecha_prestamo', 'DESC')->get()->getResultArray();

        $data = [
            'titulo' => 'Historial de ' . esc($nombre),
            'prestamos' => $prestamos,
            'esExterno' => true,
            'nombre' => $nombre,
            'dni' => $dni,
            'usuario' => $this->usuariosModel->dataUsuario($this->userID)
        ];

        return view('layout', [
            'contenido' => view('usuarios/historial_usuario', $data)
        ]);
    }

    /**
     * Búsqueda de usuarios (socios y externos)
     */
    public function buscar()
    {
        $query = $this->request->getGet('q');

        $dataBase = [
            'usuario' => $this->usuariosModel->dataUsuario(
                $this->session->get('id_usuario')
            ),
            'query' => $query
        ];

        if (empty($query)) {
            return view('layout', [
                'contenido' => view('usuarios/buscar_usuario', $dataBase)
            ]);
        }

        // Buscar en socios
        $socios = $this->db->table('biblio2_usuarios')
            ->like('nombre', $query)
            ->orLike('apellidos', $query)
            ->orLike('dni', $query)
            ->orLike('email', $query)
            ->get()
            ->getResultArray();

        // Buscar en externos
        $externos = $this->db->table('biblio2_prestamos')
            ->select('nombre_externo, dni_externo, email_externo')
            ->where('tipo_prestatario', 'externo')
            ->groupStart()
            ->like('nombre_externo', $query)
            ->orLike('dni_externo', $query)
            ->orLike('email_externo', $query)
            ->groupEnd()
            ->groupBy('nombre_externo, dni_externo, email_externo')
            ->get()
            ->getResultArray();

        $dataResultados = array_merge($dataBase, [
            'socios' => $socios,
            'externos' => $externos,
            'usuario' => $this->usuariosModel->dataUsuario($this->userID)
        ]);

        return view('layout', [
            'contenido' => view('usuarios/resultado_busqueda_usuario', $dataResultados)
        ]);
    }
}