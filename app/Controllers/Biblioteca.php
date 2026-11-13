<?php

namespace App\Controllers;

use App\Models\BibliotecaModel;


class Biblioteca extends BaseController
{
    protected $bibliotecaModel;
    protected $helpers = ['form'];

    public function __construct()
    {
        $this->bibliotecaModel = new BibliotecaModel();
    }

    // ============================================
    // LISTADO
    // ============================================
    public function index()
    {
        if ($this->rolUsuario === 'superadmin') {
            // Superadmin ve todas
            $data['bibliotecas'] = $this->bibliotecaModel->orderBy('nombre', 'ASC')->findAll();
        } else {
            // Admin solo ve la suya
            $data['bibliotecas'] = [$this->bibliotecaModel->find($this->idBiblioteca)];
        }

        $data['titulo'] = 'Bibliotecas';
        $data['esSuperadmin'] = $this->rolUsuario === 'superadmin';
        $data['usuario'] = $this->usuariosModel->dataUsuario($this->userID);

        return view('layout', [
            'contenido' => view('bibliotecas/index', $data)
        ]);
    }

    // ============================================
    // NUEVO (solo superadmin)
    // ============================================
    public function nuevo()
    {
        $this->requiereSuperadmin();

        $data['titulo'] = 'Nueva Biblioteca';
        $data['validation'] = \Config\Services::validation();
        $data['usuario'] = $this->usuariosModel->dataUsuario($this->userID);

        return view('layout', ['contenido' => view('bibliotecas/form', $data)]);
    }

    public function guardar()
    {
        $this->requiereSuperadmin();

        $rules = [
            'nombre'     => 'required|max_length[255]',
            'direccion'  => 'permit_empty|max_length[255]',
            'telefono'   => 'permit_empty|max_length[50]',
            'email'      => 'permit_empty|valid_email|max_length[100]',
            'color_tema' => 'permit_empty|max_length[7]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nombre'     => $this->request->getPost('nombre'),
            'direccion'  => $this->request->getPost('direccion'),
            'telefono'   => $this->request->getPost('telefono'),
            'email'      => $this->request->getPost('email'),
            'color_tema' => $this->request->getPost('color_tema') ?: '#0d6efd',
            'activa'     => $this->request->getPost('activa') ? 1 : 0,
        ];

        if ($this->bibliotecaModel->insert($data)) {
            return redirect()->to('/bibliotecas')->with('success', 'Biblioteca creada.');
        }

        return redirect()->back()->withInput()->with('error', 'Error al crear.');
    }

    // ============================================
    // EDITAR
    // ============================================
    public function editar($id = null)
    {
        if ($id === null) {
            return redirect()->to('/bibliotecas')->with('error', 'ID no válido.');
        }

        // Superadmin puede editar cualquiera, admin solo la suya
        if ($this->rolUsuario !== 'superadmin' && $id != $this->idBiblioteca) {
            return redirect()->to('/bibliotecas')->with('error', 'No puedes editar esta biblioteca.');
        }

        $data['biblioteca'] = $this->bibliotecaModel->find($id);
        if (!$data['biblioteca']) {
            return redirect()->to('/bibliotecas')->with('error', 'No encontrada.');
        }

        $data['titulo'] = 'Editar Biblioteca';
        $data['esSuperadmin'] = $this->rolUsuario === 'superadmin';
        $data['soloLectura'] = $this->rolUsuario !== 'superadmin';
        $data['validation'] = \Config\Services::validation();
        $data['usuario'] = $this->usuariosModel->dataUsuario($this->userID);

        return view('layout', [
            'contenido' => view('bibliotecas/form', $data)
        ]);
    }

    public function actualizar($id = null)
    {
        if ($id === null) {
            return redirect()->to('/bibliotecas')->with('error', 'ID no válido.');
        }

        if ($this->rolUsuario !== 'superadmin' && $id != $this->idBiblioteca) {
            return redirect()->to('/bibliotecas')->with('error', 'Sin permiso.');
        }

        $rules = [
            'nombre'     => 'required|max_length[255]',
            'direccion'  => 'permit_empty|max_length[255]',
            'telefono'   => 'permit_empty|max_length[50]',
            'email'      => 'permit_empty|valid_email|max_length[100]',
            'color_tema' => 'permit_empty|max_length[7]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nombre'     => $this->request->getPost('nombre'),
            'direccion'  => $this->request->getPost('direccion'),
            'telefono'   => $this->request->getPost('telefono'),
            'email'      => $this->request->getPost('email'),
            'color_tema' => $this->request->getPost('color_tema') ?: '#0d6efd',
        ];

        // Solo superadmin puede cambiar estado activa
        if ($this->rolUsuario === 'superadmin') {
            $data['activa'] = $this->request->getPost('activa') ? 1 : 0;
        }

        if ($this->bibliotecaModel->update($id, $data)) {
            return redirect()->to('/bibliotecas')->with('success', 'Biblioteca actualizada.');
        }

        return redirect()->back()->withInput()->with('error', 'Error al actualizar.');
    }

    // ============================================
    // ELIMINAR (solo superadmin)
    // ============================================
    public function eliminar($id = null)
    {
        $this->requiereSuperadmin();

        if ($id === null) {
            return redirect()->to('/bibliotecas')->with('error', 'ID no válido.');
        }

        $usuarioModel = new \App\Models\UsuariosModel();
        $tieneDependencias = $usuarioModel->where('id_biblioteca', $id)->countAllResults() > 0;

        if ($tieneDependencias) {
            return redirect()->to('/bibliotecas')->with('error', 'Tiene usuarios/libros asociados.');
        }

        if ($this->bibliotecaModel->delete($id)) {
            return redirect()->to('/bibliotecas')->with('success', 'Biblioteca eliminada.');
        }

        return redirect()->to('/bibliotecas')->with('error', 'Error al eliminar.');
    }

    // ============================================
    // SELECCIONAR (solo superadmin cambia de biblioteca)
    // ============================================
    public function seleccionar($id = null)
    {
        if ($this->rolUsuario !== 'superadmin') {
            return redirect()->back()->with('error', 'Solo superadmin puede cambiar de biblioteca.');
        }

        if ($id === null) {
            return redirect()->back()->with('error', 'ID no válido.');
        }

        $biblioteca = $this->bibliotecaModel->find($id);
        if (!$biblioteca || !$biblioteca['activa']) {
            return redirect()->back()->with('error', 'Biblioteca no válida.');
        }

        session()->set([
            'id_biblioteca'     => $id,
            'nombre_biblioteca' => $biblioteca['nombre'],
            'color_biblioteca'  => $biblioteca['color_tema']
        ]);

        return redirect()->to('/bibliotecas')->with('success', 'Biblioteca activa: ' . $biblioteca['nombre']);
    }
}