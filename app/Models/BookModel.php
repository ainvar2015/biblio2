<?php

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    protected $table      = 'libros';
    protected $primaryKey = 'id'; // ajusta si aplica

    protected $allowedFields = [
        'id',
        'id_usuario',
        'tipo',
        'codigoISBN',
        'porftada',
        'titulo',
        'autor',
        'editorial',
        'genero',
        'n_paginas',
        'ano_edicion',
        'resena',
        'prestamo',
        'fevcha_alta'
    ];

    public function getLibros()
    {
        $query = $this->db->query("SELECT 
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
        ORDER BY l.titulo ASC");
        return $query->getResultArray();
    }

    public function getDataLibro($id)
    {
        $query = $this->db->query("SELECT 
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
        WHERE l.id = ?", [$id]);
        return $query->getRowArray();
    }

    public function searchBooks($title, $author, $genre)
    {
        $builder = $this->builder();

        if ($title) {
            $builder->like('titulo', $title);
        }
        if ($author) {
            $builder->like('autor', $author);
        }
        if ($genre) {
            $builder->like('genero', $genre);
        }

        return $builder->get()->getResultArray();
    }

    public function autores()
    {
        $builder = $this->db->table('autores')->orderBy('nombreautor', 'ASC');
        return $builder->get()->getResultArray();
    }
}
