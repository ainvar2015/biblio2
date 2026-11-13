<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Services;

class AltasModel extends Model
{
    protected $db;
    protected $request;

    public function __construct()
    {
        parent::__construct();
        $this->db      = \Config\Database::connect();
        $this->request = Services::request();
    }

    /* ============================================================================
    * FUNCIÓN GENÉRICA PARA COMPROBAR SI EXISTE UN REGISTRO EN CUALQUIER TABLA
     * =============================================================================
     */
    public function existeLibro($tabla, $datos)
    {
        $builder = $this->db->table($tabla);

        $hasIsbn = !empty($datos['codigoISBN']);
        $hasTitulo = !empty($datos['titulo']);

        // Si no hay ISBN ni título, no hay nada que verificar
        if (!$hasIsbn && !$hasTitulo) {
            return false;
        }

        // Si hay ISBN, verificar por ISBN
        if ($hasIsbn) {
            $builder->where('codigoISBN', $datos['codigoISBN']);
        }

        // Si hay título, verificar por título (con OR)
        if ($hasTitulo) {
            if ($hasIsbn) {
                $builder->orWhere('titulo', $datos['titulo']);
            } else {
                $builder->where('titulo', $datos['titulo']);
            }
        }

        return $builder->countAllResults() > 0;
    }

    public function existeRegistro($tabla, $datos)
    {
        $builder = $this->db->table($tabla);

        foreach ($datos as $campo => $valor) {
            $builder->where($campo, $valor);
        }

        return $builder->countAllResults() > 0;
    }
    /* ================================
     * LIBROS
     * ================================
     */
    public function addLibro($datos)
    {
        return $this->db->table('libros')->insert($datos);
    }

    public function listaLibros()
    {
        return $this->db->table('libros')
            ->orderBy('titulo')
            ->get()
            ->getResult();
    }

    /* ================================
     * AUTORES
     * ================================
     */

    public function existeAutor($nombre)
    {
        return $this->db->table('autores')
            ->where('nombreautor', $nombre)
            ->countAllResults() > 0;
    }
    public function addAutor()
    {
        $data = [
            'nombreautor'      => $this->request->getPost('nombreautor'),
            'lugar_nacimiento' => $this->request->getPost('lugar_nacimiento'),
            'fecha_nacimiento' => $this->request->getPost('fecha_nacimiento') ?: '0000-00-00',
            'fecha_muerte'     => $this->request->getPost('fecha_muerte') ?: '0000-00-00',
            'biografia'        => $this->request->getPost('biografia')
        ];

        $this->db->table('autores')->insert($data);
        $idAutor = $this->db->insertID();

        // Consultar y devolver los datos del autor recién insertado
        return $this->db->table('autores')->where('id', $idAutor)->get()->getRowArray();
    }

    public function listaAutores()
    {
        return $this->db->table('autores')
            ->orderBy('nombreautor')
            ->get()
            ->getResult();
    }

    /* ================================
     * EDITORIALES
     * ================================
     */
    public function addEditorial()
    {
        $data = [
            'nombreeditorial' => $this->request->getPost('nombreeditorial'),
            'descripcion'     => $this->request->getPost('descripcion'),
            'fecha_alta'      => $this->request->getPost('fecha_alta'),
        ];

        $this->db->table('editoriales')->insert($data);
        $idEditorial = $this->db->insertID();

        // Consultar y devolver los datos de la editorial recién insertada
        return $this->db->table('editoriales')->where('id', $idEditorial)->get()->getRowArray();
    }

    public function listaEditoriales()
    {
        return $this->db->table('editoriales')
            ->orderBy('nombreeditorial')
            ->get()
            ->getResult();
    }

    /* ================================
     * GÉNEROS
     * ================================
     */
    public function addGenero()
    {
        $data = [
            'genero'      => $this->request->getPost('nombregenero'),
            'descripcion' => $this->request->getPost('descripcion'),
            'fecha_alta'  => $this->request->getPost('fecha_alta'),
        ];

        $this->db->table('generos')->insert($data);
        $idGenero = $this->db->insertID();

        // Consultar y devolver los datos del género recién insertado
        return $this->db->table('generos')->where('id', $idGenero)->get()->getRowArray();
    }

    public function listaGeneros()
    {
        return $this->db->table('generos')
            ->orderBy('genero')
            ->get()
            ->getResult();
    }

    /* ================================
     * PRÉSTAMOS
     * ================================
     */
    public function addPrestamo()
    {
        $idLibro      = $this->request->getPost('id_libro');
        $isbnLibro     = $this->request->getPost('isbn_libro');
        $tituloLibro   = $this->request->getPost('titulo_libro');
        $usuario    = $this->request->getPost('nombre_usuario');
        $fechaSalida  = $this->request->getPost('fecha_salida');
        $fechaDevol   = $this->request->getPost('fecha_entrada');

        // Insertar préstamo
        $insertPrestamo = $this->db->table('prestamos')->insert([
            'id_libro'      => $idLibro,
            'usuario'    => $usuario,
            'isbn'   => $isbnLibro,
            'titulo_libro' => $tituloLibro,
            'fecha_salida'  => $fechaSalida,
            'fecha_entrada' => $fechaDevol,
            'devuelto'      => 0
        ]);

        // Si falla la inserción, devolvemos false
        if (!$insertPrestamo) {
            return false;
        }

        // Marcar libro como prestado
        $updateLibro = $this->db->table('libros')
            ->where('id', $idLibro)
            ->update(['prestamo' => '1']);

        return (bool)$updateLibro; // true si se actualizó, false si no
    }


    public function verPrestamo($id_libro)
    {
        return $this->db->query(
            "SELECT * FROM biblio2_prestamos WHERE id_libro = ? AND devuelto = 0",
            [$id_libro]
        )->getRow();
    }

    public function getPrestamoByLibro(int $idLibro)
    {
        return $this->db->table('prestamos')
            ->where('id_libro', $idLibro)
            ->where('fecha_entrada IS NULL')
            ->get()
            ->getRow(); // ← devuelve stdClass o null
    }


    public function actualizaPrestamo()
    {
        $id       = $this->request->getPost('id_prestamo');
        $fecha    = $this->request->getPost('fecha_entrada');
        $id_libro = $this->request->getPost('id_libro');

        // Empezamos transacción
        $this->db->transStart();

        // Actualizamos préstamo
        $this->db->table('prestamos')
            ->where('id', $id)
            ->update([
                'fecha_entrada' => $fecha,
                'devuelto'      => 1
            ]);

        // Actualizamos estado del libro
        $this->db->table('libros')
            ->where('id', $id_libro)
            ->update(['prestamo' => '0']);

        // Finalizamos transacción
        $this->db->transComplete();

        // Devuelve true solo si ambas consultas fueron exitosas
        return $this->db->transStatus();
    }


    public function listaPrestamos($num)
    {
        $builder = $this->db->table('prestamos')->orderBy('fecha_salida');

        if ($num !== 'todos') {
            $builder->where('devuelto', 0);
        }

        return $builder->get()->getResult();
    }

    public function borrar(int $id, string $tabla): bool
    {
        return $this->db->table($tabla)
            ->where('id', $id)
            ->delete();
    }
}
