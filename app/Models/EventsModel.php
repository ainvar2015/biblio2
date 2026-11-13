<?php namespace App\Models;

use CodeIgniter\Model;

class EventsModel extends Model
{
    protected $table = 'eventos';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'titulo', 
        'descripcion',
        'url',
        'tipoevento',
        'start',
        'end',
        'created_at'
    ];
    
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $useTimestamps = false;
}
