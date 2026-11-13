<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfiguraModel extends Model
{
    protected $table      = 'settings';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'titulo_pagina',
        'nom_empresa',
        'direccion',
        'poblacion',
        'provincia',
        'cod_postal',
        'pais',
        'telefono',
        'cif'
    ];

    public function guardar(array $data): bool
    {
        return $this->update(1, $data);
    }
}
