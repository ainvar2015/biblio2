<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class ComunesModel extends Model
{
    protected $DBGroup = 'default';
    protected $returnType = 'object';
    protected $allowedFields = [];
    protected $useTimestamps = false;

    /**
     * Obtiene un registro genérico por id de una tabla
     */
    public function datos($tabla, $id = null)
    {
        $builder = $this->db->table($tabla);

        if ($id !== null) {
            $builder->where('id', $id);
        }

        $query = $builder->get();

        return ($query->getNumRows() > 0)
            ? $query->getRow()
            : null;
    }

    /**
     * Actualiza el orden de un registro
     */
    public function actOrden($id, $orden, $tabla, $tipo = 'id')
    {
        return $this->db->table($tabla)
            ->where($tipo, $id)
            ->update(['orden' => $orden]);
    }

    /**
     * Obtiene el próximo AUTO_INCREMENT de una tabla
     */
    public function nextId(string $table)
    {
        $db = Database::connect();
        $query = $db->query("SHOW TABLE STATUS LIKE ?", [$table]);
        $row = $query->getRow();

        return $row->Auto_increment;
    }

    /**
     * Idiomas activos
     */
    public function idiomas()
    {
        $query = $this->db->table('idiomas')
            ->where('activo', 1)
            ->get();

        return ($query->getNumRows() > 0)
            ? $query->getResult()
            : [];
    }

    /**
     * Módulos activos
     */
    public function modulos()
    {
        $query = $this->db->table('modulos')
            ->where('activo', 1)
            ->get();

        return ($query->getNumRows() > 0)
            ? $query->getResult()
            : [];
    }

    /**
     * Configuración general
     */
    public function configura()
    {
        $query = $this->db->query('SELECT * FROM biblio2_settings');

        return ($query->getNumRows() > 0)
            ? $query->getRow()
            : null;
    }

    /**
     * Listado de países
     */
    public function paises()
    {
        $query = $this->db->table('paises')->get();

        return ($query->getNumRows() > 0)
            ? $query->getResult()
            : [];
    }

    /**
     * Genera nombre de thumbnail
     */
    public function thumbnail(string $suffix, string $filename, string $naming = 'suffix')
    {
        $pos = strrpos($filename, '/');
        $pos = ($pos === false) ? 0 : $pos;

        $name = substr($filename, $pos, strrpos($filename, '.') - $pos);
        $ext  = substr($filename, strrpos($filename, '.'));

        if ($naming === 'suffix') {
            return $name . $suffix . $ext;
        }

        return $suffix . $name . $ext;
    }
}
