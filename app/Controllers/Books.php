<?php

namespace App\Controllers;

use App\Models\BookModel;
use CodeIgniter\RESTful\ResourceController;

class Books extends ResourceController
{
    protected $format = 'json';
    protected $bookModel;

    public function __construct()
    {
        $this->bookModel = new BookModel();
    }

    /**
     * Obtiene todos los libros con JOINs
     */
    // private function getAllBooks(): array
    // {
    //     $db = \Config\Database::connect();
        
    //     $sql = "SELECT 
    //         l.id,
    //         l.id_usuario,
    //         l.portada,
    //         l.titulo,
    //         a.nombreautor AS autor,
    //         e.nombreeditorial AS editorial,
    //         g.genero AS genero,
    //         l.n_paginas,
    //         l.ano_edicio,
    //         l.resena,
    //         l.prestamo
    //     FROM biblio2_libros l
    //     LEFT JOIN biblio2_autores a ON l.autor = a.id
    //     LEFT JOIN biblio2_editoriales e ON l.editorial = e.id
    //     LEFT JOIN biblio2_generos g ON l.genero = g.id
    //     ORDER BY l.titulo ASC";
        
    //     return $db->query($sql)->getResultArray();
    // }

    /**
     * GET api/libros
     */
    public function index()
    {
        $books = $this->bookModel->getLibros();
        return $this->respond($books);
    }

    /**
     * GET api/libros/search?q=termino
     * Busca en: titulo, autor, editorial, genero, resena
     */
    public function search()
    {
        $query = $this->request->getGet('q');
        
        // Si no hay término de búsqueda, devolver todos
        if (empty($query) || trim($query) === '') {
            return $this->respond($this->bookModel->getLibros());
        }

        $books = $this->bookModel->getLibros();
        $searchTerm = mb_strtolower(trim($query));
        
        // Filtrar libros que coincidan con la búsqueda
        $filtered = array_filter($books, function($book) use ($searchTerm) {
            $fieldsToSearch = [
                $book['titulo'] ?? '',
                $book['autor'] ?? '',
                $book['editorial'] ?? '',
                $book['genero'] ?? '',
                $book['resena'] ?? '',
                $book['n_paginas'] ?? '',
                $book['ano_edicio'] ?? '',
            ];
            
            foreach ($fieldsToSearch as $field) {
                if (mb_strpos(mb_strtolower($field), $searchTerm) !== false) {
                    return true;
                }
            }
            
            return false;
        });

        // Re-indexar array (array_filter mantiene keys originales)
        $filtered = array_values($filtered);
        
        return $this->respond($filtered);
    }

    /**
     * GET api/libros/(:num)
     */
    public function show($id = null)
    {
        if ($id === null) {
            return $this->failNotFound('ID no proporcionado');
        }

        $book = $this->bookModel->getDataLibro($id);

        if (!$book) {
            return $this->failNotFound('Libro no encontrado');
        }

        return $this->respond($book);
    }
}