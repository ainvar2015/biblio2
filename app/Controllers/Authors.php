<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Authors extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('autores');
        $authors = $builder->orderBy('nombreautor', 'ASC')->get()->getResultArray();
        
        return $this->respond($authors);
    }

    public function search()
    {
        $query = $this->request->getGet('q');
        
        if (empty($query)) {
            return $this->respond([]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('autores');
        $builder->like('nombreautor', $query, 'both');
        $builder->orLike('biografia', $query, 'both');
        $builder->orderBy('nombreautor', 'ASC');
        
        $authors = $builder->get()->getResultArray();
        
        return $this->respond($authors);
    }

    public function books($id = null)
    {
        if ($id === null) {
            return $this->failNotFound('ID de autor no proporcionado');
        }

        $db = \Config\Database::connect();

        $sql = "SELECT 
             l.id,
             l.id_usuario,
             l.portada,
             l.titulo,
             a.nombreautor AS autor,
             e.nombreeditorial AS editorial,
             g.genero AS genero,
             l.n_paginas,
             l.ano_edicio,
             l.resena,
             l.prestamo
        FROM biblio2_libros l
        LEFT JOIN biblio2_autores a ON l.autor = a.id
        LEFT JOIN biblio2_editoriales e ON l.editorial = e.id
        LEFT JOIN biblio2_generos g ON l.genero = g.id
        WHERE l.autor = $id
        ORDER BY l.titulo ASC";
        $books = $db->query($sql)->getResultArray();
        
        return $this->respond($books);
    }
}