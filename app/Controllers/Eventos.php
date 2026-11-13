<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Eventos extends BaseController
{
    protected $db;
    protected $bibliotecaModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->bibliotecaModel = new \App\Models\BibliotecaModel();
    }

    public function index()
    {
        $session = session();

        $esSuperadmin = ($session->get('nivel') ?? '') === 'superadmin';
        $idBibliotecaUsuario = $session->get('id_biblioteca');

        $bibliotecas = [];
        if ($esSuperadmin) {
            $bibliotecas = $this->bibliotecaModel->orderBy('nombre', 'ASC')->findAll();
        }

        $data = [
            'usuario' => $this->usuariosModel->dataUsuario($this->userID),
            'esSuperadmin' => $esSuperadmin,
            'bibliotecas' => $bibliotecas,
            'bibliotecaActiva' => $idBibliotecaUsuario,
        ];

        return view('layout', [
            'contenido' => view('eventos/calendar', $data)
        ]);
    }

    public function listar()
    {
        $idBiblioteca = $this->request->getGet('biblioteca');
        $session = session();

        $esSuperadmin = ($session->get('nivel') ?? '') === 'superadmin';
        $idBibliotecaUsuario = $session->get('id_biblioteca');
        
        $builder = $this->db->table('biblio2_eventos')
            ->select('id, titulo, descripcion, start, end, color, id_biblioteca');

        // Filtrar por biblioteca
        if ($esSuperadmin) {
            if (!empty($idBiblioteca) && $idBiblioteca !== 'todas') {
                $builder->where('id_biblioteca', (int)$idBiblioteca);
            }
            // Si es 'todas' o vacío, no filtrar (ver todas)
        } else {
            // Usuario normal: solo su biblioteca
            if (!empty($idBibliotecaUsuario)) {
                $builder->where('id_biblioteca', $idBibliotecaUsuario);
            }
        }

        $eventos = $builder->get()->getResultArray();

        $formattedEvents = [];
        foreach ($eventos as $evento) {
            $formattedEvents[] = [
                'id' => $evento['id'],
                'title' => $evento['titulo'],
                'start' => $evento['start'],
                'end' => $evento['end'],
                'color' => $evento['color'],
                'extendedProps' => [
                    'descripcion' => $evento['descripcion'],
                    'id_biblioteca' => $evento['id_biblioteca']
                ]
            ];
        }

        return $this->response->setJSON($formattedEvents);
    }

    public function crear()
    {
        $datos = $this->request->getPost();
        $session = session();
        $usuario = $session->get('usuario');
        $idBibliotecaUsuario = $usuario['id_biblioteca'] ?? null;

        if (empty($datos['titulo']) || empty($datos['fecha_inicio'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Faltan datos obligatorios'
            ])->setStatusCode(400);
        }

        // Determinar biblioteca del evento
        $idBiblioteca = !empty($datos['id_biblioteca']) ? (int)$datos['id_biblioteca'] : $idBibliotecaUsuario;

        $this->db->table('biblio2_eventos')->insert([
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'] ?? null,
            'start' => $datos['fecha_inicio'],
            'end' => $datos['fecha_fin'] ?? null,
            'color' => $datos['color'] ?? '#3788d8',
            'id_biblioteca' => $idBiblioteca,
            'creado_por' => $session->get('id_usuario'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status' => 'ok',
            'message' => 'Evento creado correctamente'
        ]);
    }

    public function actualizar($id)
    {
        $datos = $this->request->getPost();

        if (empty($datos['titulo']) || empty($datos['fecha_inicio'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Faltan datos obligatorios'
            ])->setStatusCode(400);
        }

        $updateData = [
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'] ?? null,
            'start' => $datos['fecha_inicio'],
            'end' => $datos['fecha_fin'] ?? null,
            'color' => $datos['color'] ?? '#3788d8',
        ];

        // Solo superadmin puede cambiar biblioteca
        $session = session();
        $usuario = $session->get('usuario');
        $esSuperadmin = ($usuario['nivel'] ?? '') === 'superadmin' && ($usuario['id'] ?? 0) == 1;
        if ($esSuperadmin && !empty($datos['id_biblioteca'])) {
            $updateData['id_biblioteca'] = (int)$datos['id_biblioteca'];
        }

        $this->db->table('biblio2_eventos')
            ->where('id', $id)
            ->update($updateData);

        return $this->response->setJSON([
            'status' => 'ok',
            'message' => 'Evento actualizado correctamente'
        ]);
    }

    public function eliminar($id)
    {
        $this->db->table('biblio2_eventos')
            ->where('id', $id)
            ->delete();

        return $this->response->setJSON([
            'status' => 'ok',
            'message' => 'Evento eliminado correctamente'
        ]);
    }

    public function eventosProximos()
    {
        $session = session();
        $usuario = $session->get('usuario');
        $idBibliotecaUsuario = $usuario['id_biblioteca'] ?? null;

        $hoy = date('Y-m-d', strtotime('-1 day'));
        $proximosDias = date('Y-m-d', strtotime('+7 days'));

        $builder = $this->db->table('biblio2_eventos')
            ->where('start >=', $hoy)
            ->where('start <=', $proximosDias);

        if (!empty($idBibliotecaUsuario)) {
            $builder->where('id_biblioteca', $idBibliotecaUsuario);
        }

        $eventos = $builder->countAllResults();

        return $this->response->setJSON([
            'count' => $eventos
        ]);
    }
}
