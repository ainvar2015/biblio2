<?php

namespace App\Models;

use CodeIgniter\Model;

class BibliotecaModel extends Model
{
    protected $table            = 'biblio2_bibliotecas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'color_tema',
        'activa'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}