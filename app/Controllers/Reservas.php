<?php

namespace App\Controllers;

use App\Models\ReservaModel;
use App\Models\UsuariosModel;
use App\Models\LibroModel;
use App\Models\BibliotecaModel;
use App\Services\EmailService;
use App\Models\PrestamosModel;

class Reservas extends BaseController
{
    protected $reservaModel;
    protected $usuariosModel;
    protected $libroModel;
    protected $bibliotecaModel;
    protected $emailService;
    protected $prestamosModel;
    protected $db;
    protected $helpers = ['form'];

    public function __construct()
    {
        $this->reservaModel = new ReservaModel();
        $this->usuariosModel = new UsuariosModel();
        $this->libroModel = new LibroModel();
        $this->bibliotecaModel = new BibliotecaModel();
        $this->emailService = new EmailService();
        $this->prestamosModel = new PrestamosModel();
        $this->db = \Config\Database::connect();
    }

    // ============================================
    // LISTADO
    // ============================================
    public function index()
    {
        $estado = $this->request->getGet('estado');
        $busqueda = $this->request->getGet('q');
        $filtroBiblioteca = $this->getBibliotecaFiltro();

        if ($estado && in_array($estado, ['pendiente', 'prestado', 'devuelto', 'cancelado'])) {
            $data['reservas'] = $this->reservaModel->getReservasPorEstado($estado, $filtroBiblioteca);
            $data['filtroActivo'] = $estado;
        } else {
            $data['reservas'] = $this->reservaModel->getReservasConDetalles($filtroBiblioteca, null, null, $busqueda);
            $data['filtroActivo'] = 'todos';
        }

        $data['pager'] = $this->reservaModel->pager;
        $data['busqueda'] = $busqueda;
        $data['estadisticas'] = $this->reservaModel->getEstadisticas($filtroBiblioteca);
        $data['biblioteca'] = $this->bibliotecaModel->find($this->idBiblioteca);
        $data['esSuperadmin'] = $this->rolUsuario === 'superadmin';
        $data['bibliotecas'] = $this->rolUsuario === 'superadmin'
            ? $this->bibliotecaModel->where('activa', 1)->findAll()
            : [];
        $data['titulo'] = 'Gestión de Reservas';
        $data['usuario'] = $this->usuariosModel->find($this->userID);

        return view('layout', ['contenido' => view('reservas/index', $data)]);
    }

    // ============================================
    // VER DETALLE
    // ============================================
    public function ver($id = null)
    {
        if ($id === null) {
            return redirect()->to('/reservas')->with('error', 'ID no válido.');
        }

        $reserva = $this->reservaModel->getReservasConDetalles(
            $this->getBibliotecaFiltro(),
            $id
        );

        if (!$reserva) {
            return redirect()->to('/reservas')->with('error', 'Reserva no encontrada.');
        }

        $this->verificarBiblioteca($reserva);

        $data['reserva'] = $reserva;
        $data['colaReservas'] = $this->reservaModel->getReservasActivasPorLibro(
            $reserva['id_biblioteca'],
            $reserva['id_libro']
        );
        $data['titulo'] = 'Detalle de Reserva #' . $id;
        $data['usuario'] = $this->usuariosModel->find($this->userID);

        return view('layout', ['contenido' => view('reservas/ver', $data)]);
    }

    // ============================================
    // NUEVO
    // ============================================
    public function nuevo()
    {
        $this->requiereAdmin();

        $data['titulo'] = 'Nueva Reserva';
        $data['usuarios'] = $this->usuariosModel
            ->where('id_biblioteca', $this->idBiblioteca)
            ->orderBy('nombre', 'ASC')
            ->findAll();
        $data['libros'] = $this->libroModel
            ->where('id_biblioteca', $this->idBiblioteca)
            ->orderBy('titulo', 'ASC')
            ->findAll();
        $data['validation'] = \Config\Services::validation();
        $data['usuario'] = $this->usuariosModel->dataUsuario($this->userID);

        return view('layout', ['contenido' => view('reservas/form', $data)]);
    }

    // ============================================
    // GUARDAR
    // ============================================
    public function guardar()
    {
        $this->requiereAdmin();

        $rules = [
            'id_usuario' => 'required|integer',
            'id_libro'   => 'required|integer',
            'observaciones' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id_usuario = $this->request->getPost('id_usuario');
        $id_libro = $this->request->getPost('id_libro');

        // Verificar que usuario y libro pertenecen a la biblioteca del admin
        $usuario = $this->usuariosModel
            ->where('id', $id_usuario)
            ->where('id_biblioteca', $this->idBiblioteca)
            ->first();
        $libro = $this->libroModel
            ->where('id', $id_libro)
            ->where('id_biblioteca', $this->idBiblioteca)
            ->first();

        if (!$usuario || !$libro) {
            return redirect()->back()->withInput()->with('error', 'Usuario o libro no válidos.');
        }

        // Límite de reservas
        $reservasActivas = $this->reservaModel->contarReservasActivasPorUsuario(
            $this->idBiblioteca,
            $id_usuario
        );
        if ($reservasActivas >= ReservaModel::MAX_RESERVAS_POR_USUARIO) {
            return redirect()->back()->withInput()->with(
                'error',
                'Límite de ' . ReservaModel::MAX_RESERVAS_POR_USUARIO . ' reservas alcanzado.'
            );
        }

        // Duplicado
        $yaReservado = $this->reservaModel
            ->where('id_biblioteca', $this->idBiblioteca)
            ->where('id_usuario', $id_usuario)
            ->where('id_libro', $id_libro)
            ->whereIn('estado', ['pendiente', 'prestado'])
            ->first();

        if ($yaReservado) {
            return redirect()->back()->withInput()->with('error', 'Ya tiene reserva para este libro.');
        }

        $data = [
            'id_biblioteca'  => $this->idBiblioteca,
            'id_usuario'     => $id_usuario,
            'id_libro'       => $id_libro,
            'estado'         => 'pendiente',
            'fecha_reserva'  => date('Y-m-d H:i:s'),
            'observaciones'  => $this->request->getPost('observaciones'),
            'notificado'     => 0
        ];

        $nuevaReservaId = $this->reservaModel->insert($data);

        if ($nuevaReservaId) {
            $reserva = $this->reservaModel->find($nuevaReservaId);
            $cola = $this->reservaModel->getReservasActivasPorLibro($this->idBiblioteca, $id_libro);
            $posicion = 1;
            foreach ($cola as $c) {
                if ($c['id'] == $nuevaReservaId) break;
                $posicion++;
            }

            try {
                $this->emailService->enviarConfirmacionReserva(
                    $usuario->email,
                    $usuario->nombre . ' ' . ($usuario->apellidos ?? ''),
                    $reserva,
                    $libro,
                    $posicion
                );
            } catch (\Exception $e) {
                log_message('error', 'Email: ' . $e->getMessage());
            }

            $msg = $posicion > 1
                ? "Reserva #{$nuevaReservaId} creada. Posición #{$posicion} en cola."
                : "Reserva #{$nuevaReservaId} creada. ¡Libro disponible!";

            return redirect()->to('/reservas')->with('success', $msg);
        }

        return redirect()->back()->withInput()->with('error', 'Error al crear.');
    }

    // ============================================
    // EDITAR
    // ============================================
    public function editar($id = null)
    {
        $this->requiereAdmin();

        if ($id === null) {
            return redirect()->to('/reservas')->with('error', 'ID no válido.');
        }

        $data['reserva'] = $this->reservaModel->find($id);
        if (!$data['reserva']) {
            return redirect()->to('/reservas')->with('error', 'No encontrada.');
        }

        $this->verificarBiblioteca($data['reserva']);

        $data['titulo'] = 'Editar Reserva #' . $id;
        $data['usuario'] = $this->usuariosModel->find($this->userID);
        $data['usuarios'] = $this->usuariosModel
            ->where('id_biblioteca', $this->idBiblioteca)
            ->orderBy('nombre', 'ASC')
            ->findAll();
        $data['libros'] = $this->libroModel
            ->where('id_biblioteca', $this->idBiblioteca)
            ->orderBy('titulo', 'ASC')
            ->findAll();
        $data['validation'] = \Config\Services::validation();

        return view('layout', ['contenido' => view('reservas/form', $data)]);
    }

    // ============================================
    // ACTUALIZAR
    // ============================================
    public function actualizar($id = null)
    {
        $this->requiereAdmin();

        if ($id === null) {
            return redirect()->to('/reservas')->with('error', 'ID no válido.');
        }

        $reserva = $this->reservaModel->find($id);
        if (!$reserva) {
            return redirect()->to('/reservas')->with('error', 'No encontrada.');
        }

        $this->verificarBiblioteca($reserva);

        $rules = [
            'id_usuario' => 'required|integer',
            'id_libro'   => 'required|integer',
            'estado'     => 'required|in_list[pendiente,prestado,devuelto,cancelado]',
            'fecha_prestamo'    => 'permit_empty|valid_date[Y-m-d]',
            'fecha_devolucion'  => 'permit_empty|valid_date[Y-m-d]',
            'observaciones'     => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'id_usuario'       => $this->request->getPost('id_usuario'),
            'id_libro'         => $this->request->getPost('id_libro'),
            'estado'           => $this->request->getPost('estado'),
            'fecha_prestamo'   => $this->request->getPost('fecha_prestamo') ?: null,
            'fecha_devolucion' => $this->request->getPost('fecha_devolucion') ?: null,
            'observaciones'    => $this->request->getPost('observaciones')
        ];

        if ($this->reservaModel->update($id, $data)) {
            return redirect()->to('/reservas')->with('success', 'Reserva actualizada.');
        }

        return redirect()->back()->withInput()->with('error', 'Error al actualizar.');
    }

    // ============================================
    // CONVERTIR A PRÉSTAMO
    // ============================================
    // ============================================
    // CONVERTIR RESERVA A PRÉSTAMO
    // ============================================
    public function convertirAPrestamo($id = null)
    {
        $this->requiereAdmin();

        if ($id === null) {
            return redirect()->to('/reservas')->with('error', 'ID no válido.');
        }

        $reserva = $this->reservaModel->find($id);
        if (!$reserva) {
            return redirect()->to('/reservas')->with('error', 'Reserva no encontrada.');
        }

        $this->verificarBiblioteca($reserva);

        if ($reserva['estado'] !== 'pendiente') {
            return redirect()->to('/reservas')->with('error', 'Solo reservas pendientes pueden convertirse.');
        }

        // Verificar que el libro no esté ya prestado
        $prestamoActivo = $this->prestamosModel
            ->where('id_libro', $reserva['id_libro'])
            ->where('(fecha_entrada IS NULL OR fecha_entrada = "0000-00-00")')
            ->first();

        if ($prestamoActivo) {
            return redirect()->to('/reservas')->with('error', 'El libro ya tiene un préstamo activo.');
        }

        $this->db->transStart();

        // 1. Actualizar reserva a "prestado"
        $this->reservaModel->update($id, [
            'estado'         => 'prestado',
            'fecha_prestamo' => date('Y-m-d')
        ]);

        // 2. Insertar en biblio2_prestamos
        $prestamoData = [
            'id_biblioteca'       => $reserva['id_biblioteca'],
            'id_libro'            => $reserva['id_libro'],
            'tipo_prestatario'    => 'socio', // Las reservas son siempre de socios
            'id_usuario'          => $reserva['id_usuario'],
            'titulo_libro'        => $reserva['titulo_libro'] ?? $this->libroModel->find($reserva['id_libro'])['titulo'],
            'fecha_prestamo'      => date('Y-m-d'),
            'fecha_devolucion'    => $reserva['fecha_devolucion'] ?? date('Y-m-d', strtotime('+15 days')),
            'notas'               => 'Convertido desde reserva #' . $id,
            'devuelto'            => 0,
            'fecha_creacion'      => date('Y-m-d H:i:s')
        ];

        $this->db->table('biblio2_prestamos')->insert($prestamoData);
        $idPrestamo = $this->db->insertID();

        // 3. Marcar libro como prestado
        $this->libroModel->update($reserva['id_libro'], ['prestamo' => 1]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->to('/reservas')->with('error', 'Error al convertir reserva a préstamo.');
        }

        return redirect()->to('/reservas')->with('success', 'Reserva convertida a préstamo correctamente. ID préstamo: ' . $idPrestamo);
    }

    // ============================================
    // DEVOLVER PRÉSTAMO (desde reservas)
    // ============================================
    public function devolver($id = null)
    {
        $this->requiereAdmin();

        if ($id === null) {
            return redirect()->to('/reservas')->with('error', 'ID no válido.');
        }

        $reserva = $this->reservaModel->find($id);
        if (!$reserva) {
            return redirect()->to('/reservas')->with('error', 'Reserva no encontrada.');
        }

        $this->verificarBiblioteca($reserva);

        if ($reserva['estado'] !== 'prestado') {
            return redirect()->to('/reservas')->with('error', 'Solo préstamos activos pueden devolverse.');
        }

        $this->db->transStart();

        // 1. Actualizar reserva a "devuelto"
        $this->reservaModel->update($id, [
            'estado'           => 'devuelto',
            'fecha_devolucion' => date('Y-m-d')
        ]);

        // 2. Actualizar préstamo en biblio2_prestamos
        $this->db->table('biblio2_prestamos')
            ->where('id_libro', $reserva['id_libro'])
            ->where('id_usuario', $reserva['id_usuario'])
            ->where('(fecha_entrada IS NULL OR fecha_entrada = "0000-00-00")')
            ->update([
                'fecha_entrada'    => date('Y-m-d'),
                'devuelto'         => 1
            ]);

        // 3. Marcar libro como disponible
        $this->libroModel->update($reserva['id_libro'], ['prestamo' => 0]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->to('/reservas')->with('error', 'Error al procesar la devolución.');
        }

        // Notificar siguiente reserva pendiente
        $mensajes = ['Préstamo devuelto correctamente.'];

        $siguiente = $this->reservaModel->getProximaReservaPendiente(
            $this->idBiblioteca,
            $reserva['id_libro']
        );

        if ($siguiente) {
            $usuarioSig = $this->usuariosModel->find($siguiente['id_usuario']);
            $libro = $this->libroModel->find($reserva['id_libro']);

            if ($usuarioSig && $libro) {
                try {
                    $enviado = $this->emailService->enviarLibroDisponible(
                        $usuarioSig->email,
                        $usuarioSig->nombre . ' ' . ($usuarioSig->apellidos ?? ''),
                        $siguiente,
                        $libro
                    );

                    if ($enviado) {
                        $this->reservaModel->marcarNotificado($siguiente['id']);
                        $mensajes[] = 'Notificado siguiente reserva: ' . $usuarioSig->email;
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Notificación reserva: ' . $e->getMessage());
                }
            }
        }

        return redirect()->to('/reservas')->with('success', implode(' ', $mensajes));
    }
    // ============================================
    // CANCELAR
    // ============================================
    public function cancelar($id = null)
    {
        $this->requiereAdmin();

        if ($id === null) {
            return redirect()->to('/reservas')->with('error', 'ID no válido.');
        }

        $reserva = $this->reservaModel->find($id);
        if (!$reserva) {
            return redirect()->to('/reservas')->with('error', 'No encontrada.');
        }

        $this->verificarBiblioteca($reserva);

        if (!in_array($reserva['estado'], ['pendiente', 'prestado'])) {
            return redirect()->to('/reservas')->with('error', 'No se puede cancelar.');
        }

        $data = ['estado' => 'cancelado'];

        if ($this->reservaModel->update($id, $data)) {
            return redirect()->to('/reservas')->with('success', 'Reserva cancelada.');
        }

        return redirect()->to('/reservas')->with('error', 'Error al cancelar.');
    }

    // ============================================
    // ELIMINAR
    // ============================================
    public function eliminar($id = null)
    {
        $this->requiereAdmin();

        if ($id === null) {
            return redirect()->to('/reservas')->with('error', 'ID no válido.');
        }

        $reserva = $this->reservaModel->find($id);
        if (!$reserva) {
            return redirect()->to('/reservas')->with('error', 'No encontrada.');
        }

        $this->verificarBiblioteca($reserva);

        if ($this->reservaModel->delete($id)) {
            return redirect()->to('/reservas')->with('success', 'Reserva eliminada.');
        }

        return redirect()->to('/reservas')->with('error', 'Error al eliminar.');
    }

    // ============================================
    // RECORDATORIO MANUAL
    // ============================================
    public function enviarRecordatorio($id = null)
    {
        $this->requiereAdmin();

        if ($id === null) {
            return redirect()->to('/reservas')->with('error', 'ID no válido.');
        }

        $reserva = $this->reservaModel->getReservasConDetalles(
            $this->getBibliotecaFiltro(),
            $id
        );

        if (!$reserva || $reserva['estado'] !== 'prestado') {
            return redirect()->to('/reservas')->with('error', 'No válida.');
        }

        $this->verificarBiblioteca($reserva);

        $fechaLimite = date('Y-m-d', strtotime($reserva['fecha_prestamo'] . " +" . ReservaModel::DIAS_PRESTAMO . " days"));
        $diasRestantes = (strtotime($fechaLimite) - strtotime(date('Y-m-d'))) / 86400;

        $datos = [
            'titulo_libro' => $reserva['libro_titulo'],
            'fecha_prestamo' => date('d/m/Y', strtotime($reserva['fecha_prestamo'])),
            'fecha_devolucion' => date('d/m/Y', strtotime($fechaLimite)),
            'dias_retraso' => $diasRestantes < 0 ? abs($diasRestantes) : 0,
            'dias_restantes' => $diasRestantes >= 0 ? $diasRestantes : 0,
            'mensaje_personalizado' => 'Por favor, devuelva el libro cuanto antes.'
        ];

        $destinatario = [
            'email' => $reserva['usuario_email'],
            'nombre' => $reserva['usuario_nombre'] . ' ' . $reserva['usuario_apellidos']
        ];

        $resultado = $this->emailService->enviarRecordatorio($destinatario, $datos);

        if ($resultado['success']) {
            return redirect()->to('/reservas/ver/' . $id)->with('success', 'Recordatorio enviado.');
        }

        return redirect()->to('/reservas/ver/' . $id)->with('error', 'Error al enviar.');
    }
}
