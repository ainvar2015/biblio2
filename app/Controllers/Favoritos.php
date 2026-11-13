<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Favoritos extends ResourceController
{
    protected $format = 'json';

    private function getUserId(): int
    {
        return $this->request->usuario_id ?? 0;
    }

    public function index()
    {
        $userId = $this->getUserId();
        if ($userId === 0) {
            return $this->failUnauthorized('No autenticado');
        }

        $db = \Config\Database::connect();
        $sql = "SELECT 
            l.id, l.portada, l.titulo,
            a.nombreautor AS autor,
            e.nombreeditorial AS editorial,
            g.genero AS genero,
            l.n_paginas, l.ano_edicio, l.prestamo,
            f.fecha_alta AS fecha_favorito
        FROM biblio2_favoritos f
        INNER JOIN biblio2_libros l ON f.id_libro = l.id
        LEFT JOIN biblio2_autores a ON l.autor = a.id
        LEFT JOIN biblio2_editoriales e ON l.editorial = e.id
        LEFT JOIN biblio2_generos g ON l.genero = g.id
        WHERE f.id_usuario = ?
        ORDER BY f.fecha_alta DESC";

        $favoritos = $db->query($sql, [$userId])->getResultArray();
        return $this->respond($favoritos);
    }

    public function create()
    {
        $userId = $this->getUserId();
        $json = $this->request->getJSON();
        $bookId = $json->id_libro ?? 0;

        if ($userId === 0){
            return $this->failUnauthorized();
        }
        if (empty($bookId)) return $this->failValidationErrors('id_libro requerido');

        $db = \Config\Database::connect();
        $builder = $db->table('biblio2_favoritos');

        // Verificar si ya existe
        $exists = $builder
            ->where('id_usuario', $userId)
            ->where('id_libro', $bookId)
            ->get()->getRow();

        if ($exists) {
            return $this->respond(['message' => 'Ya está en favoritos']);
        }

        $builder->insert(['id_usuario' => $userId, 'id_libro' => $bookId]);

        return $this->respondCreated(['message' => 'Añadido a favoritos']);
    }

    public function delete($id = null)
    {
        $userId = $this->getUserId();
        if ($userId === 0) return $this->failUnauthorized();
        if ($id === null) return $this->failNotFound('ID requerido');

        $db = \Config\Database::connect();
        $db->table('biblio2_favoritos')
            ->where('id_usuario', $userId)
            ->where('id_libro', $id)
            ->delete();

        return $this->respondDeleted(['message' => 'Eliminado de favoritos']);
    }
}