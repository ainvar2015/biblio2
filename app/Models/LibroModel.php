<?php

namespace App\Models;

use CodeIgniter\Model;

class LibroModel extends Model
{
    protected $table            = 'biblio2_libros';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'id_usuario',
        'tipo',
        'codigoISBN',
        'fecha_reserva',
        'portada',
        'titulo',
        'autor',
        'editorial',
        'genero',
        'n_paginas',
        'ano_edicio',
        'resen',
        'prestamo',
        'fecha_alta'
    ];

    protected $useTimestamps = false;

    /**
     * Obtener autores de un libro
     */
    public function getAutores(int $idLibro): array
    {
        return $this->db->table('biblio2_libros_autores la')
            ->select('a.id, a.nombre, a.apellidos')
            ->join('biblio2_autores a', 'a.id = la.id_autor')
            ->where('la.id_libro', $idLibro)
            ->get()
            ->getResultArray();
    }

    /**
     * Obtener géneros de un libro
     */
    public function getGeneros(int $idLibro): array
    {
        return $this->db->table('biblio2_libros_generos lg')
            ->select('g.id, g.nombre')
            ->join('biblio2_generos g', 'g.id = lg.id_genero')
            ->where('lg.id_libro', $idLibro)
            ->get()
            ->getResultArray();
    }

    /**
     * Guardar autores de un libro (reemplaza los existentes)
     */
    public function guardarAutores(int $idLibro, array $idsAutores): bool
    {
        // Eliminar actuales
        $this->db->table('biblio2_libros_autores')
            ->where('id_libro', $idLibro)
            ->delete();

        if (empty($idsAutores)) return true;

        $data = [];
        foreach ($idsAutores as $idAutor) {
            $data[] = [
                'id_libro' => $idLibro,
                'id_autor' => (int)$idAutor
            ];
        }

        return $this->db->table('biblio2_libros_autores')->insertBatch($data);
    }

    /**
     * Guardar géneros de un libro (reemplaza los existentes)
     */
    public function guardarGeneros(int $idLibro, array $idsGeneros): bool
    {
        $this->db->table('biblio2_libros_generos')
            ->where('id_libro', $idLibro)
            ->delete();

        if (empty($idsGeneros)) return true;

        $data = [];
        foreach ($idsGeneros as $idGenero) {
            $data[] = [
                'id_libro' => $idLibro,
                'id_genero' => (int)$idGenero
            ];
        }

        return $this->db->table('biblio2_libros_generos')->insertBatch($data);
    }

    /**
     * Libros con autores y géneros (para listados)
     */
    public function getLibrosConRelaciones(?int $idBiblioteca = null): array
    {
        $builder = $this->db->table('biblio2_libros l')
            ->select('l.*, e.nombre as editorial')
            ->join('biblio2_editoriales e', 'e.id = l.id_editorial', 'left');

        if ($idBiblioteca) {
            $builder->where('l.id_biblioteca', $idBiblioteca);
        }

        $libros = $builder->orderBy('l.titulo', 'ASC')->get()->getResultArray();

        foreach ($libros as &$libro) {
            $autores = $this->getAutores($libro['id']);
            $generos = $this->getGeneros($libro['id']);

            $libro['autores'] = $autores;
            $libro['autores_texto'] = implode(', ', array_column($autores, 'nombre'));
            $libro['generos'] = $generos;
            $libro['generos_texto'] = implode(', ', array_column($generos, 'nombre'));
        }

        return $libros;
    }

}