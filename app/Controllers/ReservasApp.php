<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class ReservasApp extends ResourceController
{
    protected $format = 'json';

    private function getUserId(): int
    {
        return $this->request->usuario_id ?? 0;
    }

    public function index()
    {
        $userId = $this->getUserId();
        if ($userId === 0) return $this->failUnauthorized();

        $db = \Config\Database::connect();

        $sql = "SELECT 
            r.id AS id_reserva,
            r.estado,
            r.fecha_reserva,
            r.fecha_prestamo,
            r.fecha_devolucion,
            r.observaciones,
            l.id AS id_libro,
            l.portada,
            l.titulo,
            l.prestamo,
            a.nombreautor AS autor,
            e.nombreeditorial AS editorial
        FROM biblio2_reservas r
        INNER JOIN biblio2_libros l ON r.id_libro = l.id
        LEFT JOIN biblio2_autores a ON l.autor = a.id
        LEFT JOIN biblio2_editoriales e ON l.editorial = e.id
        WHERE r.id_usuario = ?
        ORDER BY r.fecha_reserva DESC";

        $reservas = $db->query($sql, [$userId])->getResultArray();
        return $this->respond($reservas);
    }

    /**
     * Crear reserva — SIEMPRE permitido (lista de espera)
     */
    public function create()
    {
        $userId = $this->getUserId();
        $json = $this->request->getJSON();
        $bookId = $json->id_libro ?? 0;
        $obs = $json->observaciones ?? null;

        if ($userId === 0) return $this->failUnauthorized();
        if (empty($bookId)) return $this->failValidationErrors('id_libro requerido');

        $db = \Config\Database::connect();

        // Verificar si el usuario YA tiene una reserva activa de este libro
        $existing = $db->table('biblio2_reservas')
            ->where('id_usuario', $userId)
            ->where('id_libro', $bookId)
            ->whereIn('estado', ['pendiente', 'prestado'])
            ->get()
            ->getRow();

        if ($existing) {
            return $this->fail([
                'message' => 'Ya tienes una reserva activa de este libro',
            ], 409);
        }

        // Crear reserva (siempre permitido, incluso si está prestado)
        $db->table('biblio2_reservas')->insert([
            'id_usuario'    => $userId,
            'id_libro'      => $bookId,
            'estado'        => 'pendiente',
            'observaciones' => $obs,
        ]);

        return $this->respondCreated([
            'message' => 'Reserva creada. Te avisaremos cuando esté disponible.'
        ]);
    }

    public function cancel($id = null)
    {
        $userId = $this->getUserId();
        if ($userId === 0) return $this->failUnauthorized();
        if ($id === null) return $this->failNotFound('ID requerido');

        $db = \Config\Database::connect();
        $db->table('biblio2_reservas')
            ->where('id', $id)
            ->where('id_usuario', $userId)
            ->where('estado', 'pendiente')
            ->update(['estado' => 'cancelado']);

        return $this->respond(['message' => 'Reserva cancelada']);
    }
}