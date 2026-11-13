<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EventsModel;

class Events extends BaseController
{
    protected $eventsModel;

    public function __construct()
    {
        $this->eventsModel = new EventsModel();
    }

    /* =========================
     * Obtener eventos (GET)
     * ========================= */
    public function index()
    {
        $events = $this->eventsModel->findAll();

        // Colores por tipo
        $coloresTipo = [
        'reunion'    => '#007bff',
        'trabajo'    => '#ff8040',
        'cumpleanos' => '#28a745',
        'importante' => '#be4bdb',
        'personal'   => '#ffc107',
        'vacaciones' => '#dc3545',
    ];

        $data = [];

        foreach ($events as $event) {
            $data[] = [
                'id'    => (string) $event['id'],
                'title' => $event['titulo'], // MUY IMPORTANTE
                'start' => date('c', strtotime($event['start'])),
                'end'   => $event['end'] ? date('c', strtotime($event['end'])) : null,

                'color'     => $coloresTipo[$event['tipoevento']] ?? '#6c757d',
                'textColor' => '#fff',

                'extendedProps' => [
                    'descripcion' => $event['descripcion'] ?? '',
                    'tipoevento'  => $event['tipoevento'] ?? '',
                    'url'         => $event['url'] ?? '',
                ],
            ];
        }

        return $this->response->setJSON($data);
    }

    /* =========================
     * Añadir evento (POST)
     * ========================= */
    public function add()
    {
        $data = $this->request->getJSON(true);
        // var_dump($data);die(); // depuración

        if (!$data || empty($data['titulo']) || empty($data['start'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Datos incompletos'
            ]);
        }

        $this->eventsModel->insert([
            'titulo'       => $data['titulo'],
            'start'       => $data['start'], // Y-m-d H:i
            'end'         => $data['end'] ?? null,
            'descripcion' => $data['descripcion'] ?? '',
            'tipoevento'  => $data['tipoevento'] ?? '',
            'url'         => $data['url'] ?? '',
            'created_at'  => $data['createdat']
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    /* =========================
     * Actualizar evento (drag / resize)
     * ========================= */
    public function update($id)
    {
        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->response->setStatusCode(400);
        }

        $this->eventsModel->update($id, [
            'start' => $data['start'] ?? null,
            'end'   => $data['end'] ?? null,
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    /* =========================
     * Actualizar desde modal editar
     * ========================= */
    public function updateFull($id)
    {
        $data = $this->request->getJSON(true);

        if (!$data || empty($data['titulo']) || empty($data['start'])) {
            return $this->response->setJSON([
                'success' => false
            ]);
        }

        $this->eventsModel->update($id, [
            'titulo'       => $data['titulo'],
            'start'       => $data['start'],
            'end'         => $data['end'] ?? null,
            'descripcion' => $data['descripcion'] ?? '',
            'tipoevento'  => $data['tipoevento'] ?? '',
            'url'         => $data['url'] ?? '',
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    /* =========================
     * Eliminar evento
     * ========================= */
    public function delete($id)
    {
        $this->eventsModel->delete($id);
        return $this->response->setJSON(['success' => true]);
    }
}
