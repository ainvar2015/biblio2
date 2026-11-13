<?php

namespace App\Models;

use CodeIgniter\Model;

class EditarModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }


    /*
     * FUNCIONES COMUNES
     */
    public function data($id, $tabla)
    {
        return $this->db->table($tabla)
                        ->where('id', $id)
                        ->get()
                        ->getRow();
    }

    /*
     * PRÉSTAMOS
     */
    public function listaPrestamos($num)
    {
        $builder = $this->db->table('prestamos')->orderBy('fecha_salida');

        if ($num !== 'todos') {
            $builder->where('devuelto', 0);
        }

        return $builder->get()->getResult();
    }


    /*
     * LIBROS
     */
    public function editLibro($id,$datos)
    {
        $request = service('request');

        $tipo = ($request->getPost('tipo_item') == '1') ? 1 : 0;

        $generos = $request->getPost('genero');
        $idGeneros = is_array($generos) ? implode(',', $generos) : '';

        // $data = [
        //     'tipo'          => $tipo,
        //     'codigoISBN'    => $request->getPost('codigoISBN'),
        //     'portada_file'  => $request->getPost('portada_file'),
        //     'portada_url'   => $request->getPost('portada_url'),
        //     'titulo'        => $request->getPost('titulo'),
        //     'autor'         => $request->getPost('autor'),
        //     'editorial'     => $request->getPost('editorial'),
        //     'genero'        => $idGeneros,
        //     'n_paginas'     => $request->getPost('n_paginas'),
        //     'ano_edicio'    => $request->getPost('ano_edicio'),
        //     'fecha_alta'    => $request->getPost('fecha_alta'),
        //     'resena'        => $request->getPost('resena'),
        // ];

        return $this->db->table('libros')
                        ->where('id', $id)
                        ->update($datos);
    }

    public function listaLibros()
    {
        $query = $this->db->table('libros')
                          ->orderBy('titulo', 'ASC')
                          ->get();
        return ($query->getNumRows() > 0) ? $query->getResult() : false;
    }

    public function dataLibro($id)
    {
        return $this->db->table('libros')
                        ->where('id', $id)
                        ->get()
                        ->getRow();
    }

    /*
     * AUTORES
     */
    public function editAutor($id)
    {
        $request = service('request');

        $fecha_nac = '0000-00-00';
        $fecha_mue = '0000-00-00';

        if ($request->getPost('fecha_nacimiento')) {
            [$d, $m, $y] = explode('/', $request->getPost('fecha_nacimiento'));
            $fecha_nac = "$y-$m-$d";
        }

        if ($request->getPost('fecha_muerte')) {
            [$d, $m, $y] = explode('/', $request->getPost('fecha_muerte'));
            $fecha_mue = "$y-$m-$d";
        }

        $data = [
            'nombreautor'       => $request->getPost('nombreautor'),
            'lugar_nacimiento'  => $request->getPost('lugar_nacimiento'),
            'fecha_nacimiento'  => $fecha_nac,
            'fecha_muerte'      => $fecha_mue,
            'biografia'         => $request->getPost('biografia'),
        ];

        return $this->db->table('autores')
                        ->where('id', $id)
                        ->update($data);
    }

    public function dataAutor($id)
    {
        return $this->db->table('autores')
                        ->where('id', $id)
                        ->get()
                        ->getRow();
    }

    public function listaAutores()
    {
        $query = $this->db->table('autores')
                            ->orderBy('nombreautor', 'ASC')
                            ->get();
        return ($query->getNumRows() > 0) ? $query->getResult() : false;
    }

    /*
     * EDITORIALES
     */
    public function editEditorial($id)
    {
        $request = service('request');

        $data = [
            'nombreeditorial' => $request->getPost('nombreeditorial'),
            'descripcion'     => $request->getPost('descripcion'),
        ];

        return $this->db->table('editoriales')
                        ->where('id', $id)
                        ->update($data);
    }

    public function listaEditoriales()
    {
        $query = $this->db->table('editoriales')
                        ->orderBy('nombreeditorial', 'ASC')
                        ->get();
        return ($query->getNumRows() > 0) ? $query->getResult() : false;
    }

    public function dataEditorial($id)
    {
        return $this->db->table('editoriales')
                        ->where('id', $id)
                        ->get()
                        ->getRow();
    }

    /*
     * GÉNEROS
     */
    public function editGenero($id)
    {
        $request = service('request');

        $data = [
            'genero'      => $request->getPost('genero'),
            'descripcion' => $request->getPost('descripcion'),
        ];

        return $this->db->table('generos')
                        ->where('id', $id)
                        ->update($data);
    }

    public function listaGeneros()
    {
        $query = $this->db->table('generos')
                          ->orderBy('genero', 'ASC')
                          ->get();
        return ($query->getNumRows() > 0) ? $query->getResult() : false;
    }

    public function dataGenero($id)
    {
        return $this->db->table('generos')
                        ->where('id', $id)
                        ->get()
                        ->getRow();
    }
}
