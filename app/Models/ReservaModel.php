<?php

namespace App\Models;

use CodeIgniter\Model;

class ReservaModel extends Model
{
    protected $table            = 'biblio2_reservas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_biblioteca',
        'id_usuario',
        'id_libro',
        'estado',
        'fecha_reserva',
        'fecha_prestamo',
        'fecha_devolucion',
        'observaciones',
        'notificado'
    ];

    protected $useTimestamps = false;

    public const MAX_RESERVAS_POR_USUARIO = 3;
    public const DIAS_PRESTAMO = 15;

    // ============================================
    // LISTADO CON DETALLES + PAGINACIÓN
    // $id_biblioteca = null → superadmin ve todas
    // ============================================
    public function getReservasConDetalles(?int $id_biblioteca = null, $id = null, $estado = null, $busqueda = null, $perPage = 15)
    {
        $this->select('biblio2_reservas.*, u.nombre as usuario_nombre, u.apellidos as usuario_apellidos, u.email as usuario_email, l.titulo as libro_titulo, l.autor as libro_autor, l.codigoISBN as libro_isbn, b.nombre as biblioteca_nombre, b.color_tema as color_biblioteca');
        $this->join('biblio2_usuarios u', 'u.id = biblio2_reservas.id_usuario');
        $this->join('biblio2_libros l', 'l.id = biblio2_reservas.id_libro');
        $this->join('biblio2_bibliotecas b', 'b.id = biblio2_reservas.id_biblioteca');

        // Solo filtrar por biblioteca si se especifica (admin siempre manda su id, superadmin puede mandar null)
        if ($id_biblioteca !== null) {
            $this->where('biblio2_reservas.id_biblioteca', $id_biblioteca);
        }

        if ($estado && in_array($estado, ['pendiente', 'prestado', 'devuelto', 'cancelado'])) {
            $this->where('biblio2_reservas.estado', $estado);
        }

        if ($busqueda) {
            $this->groupStart()
                ->like('u.nombre', $busqueda)
                ->orLike('u.apellidos', $busqueda)
                ->orLike('u.email', $busqueda)
                ->orLike('l.titulo', $busqueda)
                ->orLike('l.autor', $busqueda)
                ->orLike('l.codigoISBN', $busqueda)
                ->orLike('biblio2_reservas.observaciones', $busqueda)
                ->groupEnd();
        }

        if ($id !== null) {
            $this->where('biblio2_reservas.id', $id);
            return $this->first();
        }

        $this->orderBy('biblio2_reservas.fecha_reserva', 'DESC');
        return $this->paginate($perPage, 'reservas');
    }

    public function getReservasPorEstado($estado, ?int $id_biblioteca = null, $perPage = 15)
    {
        return $this->getReservasConDetalles($id_biblioteca, null, $estado, null, $perPage);
    }

    // ============================================
    // RESERVAS ACTIVAS POR LIBRO
    // ============================================
    public function getReservasActivasPorLibro(int $id_biblioteca, $id_libro)
    {
        return $this->where('id_biblioteca', $id_biblioteca)
                    ->where('id_libro', $id_libro)
                    ->whereIn('estado', ['pendiente', 'prestado'])
                    ->orderBy('fecha_reserva', 'ASC')
                    ->findAll();
    }

    public function getReservasActivasPorUsuario(int $id_biblioteca, $id_usuario)
    {
        return $this->where('id_biblioteca', $id_biblioteca)
                    ->where('id_usuario', $id_usuario)
                    ->whereIn('estado', ['pendiente', 'prestado'])
                    ->orderBy('fecha_reserva', 'ASC')
                    ->findAll();
    }

    public function contarReservasActivasPorUsuario(int $id_biblioteca, $id_usuario)
    {
        return $this->where('id_biblioteca', $id_biblioteca)
                    ->where('id_usuario', $id_usuario)
                    ->whereIn('estado', ['pendiente', 'prestado'])
                    ->countAllResults();
    }

    public function getProximaReservaPendiente(int $id_biblioteca, $id_libro)
    {
        return $this->where('id_biblioteca', $id_biblioteca)
                    ->where('id_libro', $id_libro)
                    ->where('estado', 'pendiente')
                    ->orderBy('fecha_reserva', 'ASC')
                    ->first();
    }

    // ============================================
    // ESTADÍSTICAS
    // ============================================
    public function getEstadisticas(?int $id_biblioteca = null)
    {
        $result = [];
        foreach (['pendiente', 'prestado', 'devuelto', 'cancelado'] as $estado) {
            $this->where('estado', $estado);
            if ($id_biblioteca !== null) {
                $this->where('id_biblioteca', $id_biblioteca);
            }
            $result[$estado] = $this->countAllResults();
            $this->resetQuery(); // Limpiar where para siguiente iteración
        }
        $result['total'] = array_sum($result);
        return $result;
    }

    public function marcarNotificado($id)
    {
        return $this->update($id, ['notificado' => 1]);
    }
}