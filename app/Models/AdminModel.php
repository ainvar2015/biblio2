<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table      = 'settings';
    protected $primaryKey = 'id'; // ajusta si aplica

    protected $allowedFields = [
        'base_url',
        'titulo_pagina',
        'nombre_bd',
        'nom_empresa',
        'direccion',
        'poblacion',
        'provincia',
        'cod_postal',
        'pais',
        'telefono',
        'cif'
    ];

    public function dataLibro(int $id)
    {
        return $this->db->table('libros')
            ->where('id', $id)
            ->get()
            ->getRow();
    }

    /**
     * Actualiza la configuración general
     */
    public function updateSettings(): bool
    {
        $request = service('request');

        $data = [
            'base_url'       => $request->getPost('base_url'),
            'titulo_pagina'  => $request->getPost('titulo_pagina'),
            'nombre_bd'      => $request->getPost('nombre_bd'),
            'nom_empresa'    => $request->getPost('nom_empresa'),
            'direccion'      => $request->getPost('direccion'),
            'poblacion'      => $request->getPost('poblacion'),
            'provincia'      => $request->getPost('provincia'),
            'cod_postal'     => $request->getPost('cod_postal'),
            'pais'           => $request->getPost('pais'),
            'telefono'       => $request->getPost('telefono'),
            'cif'            => $request->getPost('cif'),
        ];

        return $this->builder()->update($data);
    }
}
