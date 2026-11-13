<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuariosModel extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'id';

    protected $returnType     = 'object';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'login',
        'password',
        'pin',
        'foto',
        'nivel',
        'activo',
        'nombre',
        'apellidos',
        'fecha_nacimiento',
        'direccion',
        'poblacion',
        'provincia',
        'c_postal',
        'telefono',
        'email',
        'activo',
        'mostrar_email',
        'mostrar_nombre_real',
        'permitir_contacto',
        'fecha_alta',
        'fecha_ultima_modificacion',
        'id_biblioteca'
    ];

    public function getUsuarios()
    {
        $contenido = $this->findAll();
        return (!empty($contenido)) ? $contenido : false;
    }

    public function dataUsuario($id)
    {
        return $this->find($id);
    }

    public function altaUsuario($post)
    {
        if (!isset($post['activo']))
            $post['activo'] = 0;
        else
            $activo = 1;
        if (!isset($post['mostrar_email']))
            $mostrarEmail = 0;
        else
            $mostrarEmail = 1;
        if (!isset($post['mostrar_nombre_real']))
            $mostrarNombreReal = 0;
        else
            $mostrarNombreReal = 1;
        if (!isset($post['permitir_contacto']))
            $permitirContacto = 0;
        else
            $permitirContacto = 1;

        $data = [
            'login'                     => $post['login'],
            'password'                  => password_hash($post['password'], PASSWORD_DEFAULT),
            'foto'                      => $post['foto'] ?? null,
            'nivel'                     => $post['nivel'],
            'activo'                    => $activo,
            'nombre'                    => $post['nombre'],
            'apellidos'                 => $post['apellidos'],
            'direccion'                 => $post['direccion'],
            'poblacion'                 => $post['poblacion'],
            'provincia'                 => $post['provincia'],
            'c_postal'                  => $post['c_postal'],
            'telefono'                  => $post['telefono'],
            'email'                     => $post['email'],
            'mostrar_email'             => $mostrarEmail,
            'mostrar_nombre_real'       => $mostrarNombreReal,
            'permitir_contacto'         => $permitirContacto,
            'fecha_alta'                => $post['fecha_alta'],
            'fecha_ultima_modificacion' => $post['fecha_ultima_modificacion'],
            'id_biblioteca'             => $post['id_biblioteca']
        ];

        return $this->insert($data);
    }

    public function editUsuario($id, $post)
    {
        if (!isset($post['activo']))
            $post['activo'] = 0;
        if (!isset($post['mostrar_email']))
            $post['mostrar_email'] = 0;
        if (!isset($post['mostrar_nombre_real']))
            $post['mostrar_nombre_real'] = 0;
        if (!isset($post['permitir_contacto']))
            $post['permitir_contacto'] = 0;

        $data = [
            'foto'                      => $post['foto'],
            'nivel'                     => $post['nivel'],
            'activo'                    => $post['activo'],
            'nombre'                    => $post['nombre'],
            'apellidos'                 => $post['apellidos'],
            'direccion'                 => $post['direccion'],
            'poblacion'                 => $post['poblacion'],
            'provincia'                 => $post['provincia'],
            'c_postal'                  => $post['c_postal'],
            'telefono'                  => $post['telefono'],
            'mostrar_email'             => $post['mostrar_email'],
            'mostrar_nombre_real'       => $post['mostrar_nombre_real'],
            'permitir_contacto'         => $post['permitir_contacto'],
            'fecha_ultima_modificacion' => $post['fecha_ultima_modificacion'],
            'id_biblioteca'             => $post['id_biblioteca']
        ];

        // Si se envió contraseña nueva
        if (!empty($post['password'])) {
            $data['password'] = sha1($post['password']);
        }

        return $this->update($id, $data);
    }

    public function password($id, $post)
    {
        $usuario = $this->find($id);

        if (!$usuario) return false;

        // Comprobar contraseña actual
        if (sha1($post['passwordold']) !== $usuario->password) {
            return false;
        }

        // Confirmación de nueva contraseña
        if ($post['password'] !== $post['password1']) {
            return false;
        }

        return $this->update($id, [
            'password' => sha1($post['password'])
        ]);
    }

    public function borraUsuario($id)
    {
        return $this->delete($id);
    }

    public function getUsuariosPaginados(?int $id_biblioteca = null, int $perPage = 15)
    {
        $this->select('usuarios.*, b.nombre as biblioteca_nombre, b.color_tema');
        $this->join('biblio2_bibliotecas b', 'b.id = usuarios.id_biblioteca', 'left');

        if ($id_biblioteca !== null) {
            $this->where('usuarios.id_biblioteca', $id_biblioteca);
        }

        $this->orderBy('usuarios.nombre', 'ASC');

        return $this->paginate($perPage, 'usuarios');
    }

    public function getUsuariosConBiblioteca()
    {
        return $this->select('usuarios.*, b.nombre as biblioteca_nombre, b.color_tema')
            ->join('biblio2_bibliotecas b', 'b.id = usuarios.id_biblioteca', 'left')
            ->orderBy('b.nombre', 'ASC')
            ->orderBy('usuarios.nombre', 'ASC')
            ->findAll();
    }

    public function getUsuariosPorBiblioteca(int $id_biblioteca)
    {
        return $this->select('usuarios.*, b.nombre as biblioteca_nombre, b.color_tema')
            ->join('biblio2_bibliotecas b', 'b.id = usuarios.id_biblioteca', 'left')
            ->where('usuarios.id_biblioteca', $id_biblioteca)
            ->orderBy('usuarios.nombre', 'ASC')
            ->findAll();
    }
}
