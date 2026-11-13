<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AltasModel;
use App\Models\AdminModel;
use App\Models\UsuariosModel;
use App\Models\EditarModel;
use App\Models\EventsModel;
use App\Models\BibliotecaModel;
use CodeIgniter\API\ResponseTrait;

class Admin extends BaseController
{
    protected $altasModel;
    protected $adminModel;
    protected $usuariosModel;
    protected $editarModel;
    protected $eventsModel;
    protected $bibliotecaModel;
    protected $responseTrait;
    protected $db;

    use ResponseTrait;

    public function __construct()
    {
        $this->altasModel = new AltasModel();
        $this->adminModel = new AdminModel();
        $this->usuariosModel = new UsuariosModel();
        $this->editarModel = new EditarModel();
        $this->eventsModel = new EventsModel();
        $this->bibliotecaModel = new BibliotecaModel();
        $this->db = \Config\Database::connect();
        $this->session      = session();
        helper(['url', 'form']);
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $filtroBiblioteca = $this->getBibliotecaFiltro(); // null = superadmin sin filtro, int = admin o superadmin con filtro

        $socios = $this->usuariosModel->getUsuarios(); // Ajusta si necesitas filtrar por biblioteca

        // Datos base
        $data = [
            'usuario'     => $this->usuariosModel->dataUsuario(
                $this->session->get('id_usuario')
            ),
            'esSuperadmin' => $this->rolUsuario === 'superadmin',
            'bibliotecas' => $this->rolUsuario === 'superadmin'
                ? $this->bibliotecaModel->where('activa', 1)->findAll()
                : [],
            'bibliotecaActiva' => $filtroBiblioteca,
            'socios' => $socios,
            'js_especifico'  => 'detalle-libro.js',
        ];

        // Estadísticas globales (autores, editoriales, géneros no llevan biblioteca)
        $data['totalAutores'] = $db->table('biblio2_autores')->countAllResults();
        $data['totalEditoriales'] = $db->table('biblio2_editoriales')->countAllResults();
        $data['totalGeneros'] = $db->table('biblio2_generos')->countAllResults();

        // Estadísticas por biblioteca (filtradas o todas)
        $data['totalLibros'] = $this->getTotalLibros($filtroBiblioteca);
        $data['totalUsuarios'] = $this->getTotalUsuarios($filtroBiblioteca);
        $data['totalEventos'] = $this->getTotalEventos(); // Ajusta si tienes eventos por biblioteca

        // Estadísticas avanzadas (pasando filtro de biblioteca)
        $data['topLibrosPrestados'] = $this->getTopLibrosPrestados($filtroBiblioteca);
        $data['librosPorGenero'] = $this->getLibrosPorGenero($filtroBiblioteca);
        $data['prestamosPorMes'] = $this->getPrestamosPorMes($filtroBiblioteca);
        $data['prestamosPorTipoUsuario'] = $this->getPrestamosPorTipoUsuario($filtroBiblioteca);
        $data['prestamosActivosVsDevueltos'] = $this->getPrestamosActivosVsDevueltos($filtroBiblioteca);

        // Notificaciones (filtradas por biblioteca)
        $data['prestamosConRetraso'] = $this->getPrestamosConRetraso($filtroBiblioteca);
        $data['librosPopulaires'] = $this->getLibrosPopulaires($filtroBiblioteca);

        return view('layout', [
            'contenido' => view('dashboard/dashboard', $data)
        ]);
    }

    /**
     * Total de libros (filtrado por biblioteca si aplica)
     */
    private function getTotalLibros(?int $id_biblioteca = null): int
    {
        $builder = $this->db->table('biblio2_libros');
        if ($id_biblioteca !== null) {
            $builder->where('id_biblioteca', $id_biblioteca);
        }
        return $builder->countAllResults();
    }

    /**
     * Total de usuarios (filtrado por biblioteca si aplica)
     */
    private function getTotalUsuarios(?int $id_biblioteca = null): int
    {
        $builder = $this->db->table('biblio2_usuarios');
        if ($id_biblioteca !== null) {
            $builder->where('id_biblioteca', $id_biblioteca);
        }
        return $builder->countAllResults();
    }

    /**
     * Total de usuarios (filtrado por biblioteca si aplica)
     */
    private function getTotalEventos()
    {
        $builder = $this->db->table('biblio2_eventos');
        // if ($id_biblioteca !== null) {
        //     $builder->where('id_biblioteca', $id_biblioteca);
        // }
        return $builder->countAllResults();
    }

    // Top 5 libros más prestados
    private function getTopLibrosPrestados(?int $id_biblioteca = null)
    {
        $builder = $this->db->table('biblio2_prestamos p')
            ->select('l.titulo, COUNT(*) as total')
            ->join('biblio2_libros l', 'l.id = p.id_libro');

        if ($id_biblioteca !== null) {
            $builder->where('p.id_biblioteca', $id_biblioteca);
        }
        return $builder->groupBy('p.id_libro')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
    }

    /**
     * Libros por género (para gráfico)
     */
    private function getLibrosPorGenero(?int $id_biblioteca = null)
    {
        $builder = $this->db->table('biblio2_libros l')
            ->select('g.genero as nombre, COUNT(*) as total')
            ->join('biblio2_generos g', 'g.id = l.genero', 'left');

        if ($id_biblioteca !== null) {
            $builder->where('l.id_biblioteca', $id_biblioteca);
        }

        return $builder->groupBy('l.genero')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();
    }


    public function verLibros()
    {
        $data = [
            'titulo'  => 'Edición',
            'usuario' => $this->usuariosModel->dataUsuario(
                $this->session->get('id_usuario')
            ),
        ];

        return view('layout', [
            'contenido' => view('listado_general', $data)
        ]);
    }
    /**
     * Préstamos por mes (últimos 12 meses)
     */
    private function getPrestamosPorMes(?int $id_biblioteca = null)
    {
        $fechaInicio = date('Y-m-d', strtotime("-12 months"));

        $builder = $this->db->table('biblio2_prestamos')
            ->select('DATE_FORMAT(fecha_prestamo, "%Y-%m") as mes, COUNT(*) as total')
            ->where('fecha_prestamo >=', $fechaInicio);

        if ($id_biblioteca !== null) {
            $builder->where('id_biblioteca', $id_biblioteca);
        }

        return $builder->groupBy('mes')
            ->orderBy('mes', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Préstamos por tipo de usuario (socio/externo)
     */
    private function getPrestamosPorTipoUsuario(?int $id_biblioteca = null)
    {
        $builder = $this->db->table('biblio2_prestamos')
            ->select('tipo_prestatario as tipo, COUNT(*) as total');

        if ($id_biblioteca !== null) {
            $builder->where('id_biblioteca', $id_biblioteca);
        }

        return $builder->groupBy('tipo_prestatario')
            ->get()
            ->getResultArray();
    }

    /**
     * Préstamos activos vs devueltos
     */
    private function getPrestamosActivosVsDevueltos(?int $id_biblioteca = null)
    {
        $result = ['activos' => 0, 'devueltos' => 0];

        // Activos (sin fecha de entrada)
        $builder = $this->db->table('biblio2_prestamos');

        if ($id_biblioteca !== null) {
            $builder->where('id_biblioteca', $id_biblioteca);
        }

        $builder->groupStart()
            ->where('fecha_entrada IS NULL')
            ->orWhere('fecha_entrada', '0000-00-00')
            ->groupEnd();

        $result['activos'] = $builder->countAllResults();

        // Devueltos (con fecha de entrada)
        $builder = $this->db->table('biblio2_prestamos');

        if ($id_biblioteca !== null) {
            $builder->where('id_biblioteca', $id_biblioteca);
        }

        $builder->where('fecha_entrada IS NOT NULL')
            ->where('fecha_entrada !=', '0000-00-00');

        $result['devueltos'] = $builder->countAllResults();

        return $result;
    }

    /**
     * Préstamos con retraso
     */
    private function getPrestamosConRetraso(?int $id_biblioteca = null)
    {
        $builder = $this->db->table('biblio2_prestamos p')
            ->select('p.*, 
            l.titulo as titulo_libro,
            CASE 
                WHEN p.tipo_prestatario = "socio" THEN u.nombre 
                ELSE p.nombre_externo 
            END as nombre_usuario,
            CASE 
                WHEN p.tipo_prestatario = "socio" THEN u.apellidos 
                ELSE "" 
            END as apellidos_usuario,
            u.email as email_usuario,
            p.email_externo,
            DATEDIFF(CURDATE(), p.fecha_devolucion) as dias_retraso', false)
            ->join('biblio2_libros l', 'l.id = p.id_libro')
            ->join('biblio2_usuarios u', 'u.id = p.id_usuario', 'left');

        if ($id_biblioteca !== null) {
            $builder->where('p.id_biblioteca', $id_biblioteca);
        }

        // Agrupar las condiciones de fecha para que no se mezclen con el OR
        $builder->groupStart()
            ->where('p.fecha_entrada IS NULL')
            ->orWhere('p.fecha_entrada', '0000-00-00')
            ->groupEnd()
            ->where('p.fecha_devolucion <', date('Y-m-d'));

        return $builder->orderBy('dias_retraso', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Libros populares (más prestados, con info completa)
     */
    private function getLibrosPopulaires(?int $id_biblioteca = null)
    {
        $builder = $this->db->table('biblio2_prestamos p')
            ->select('l.id, l.titulo, l.portada, l.prestamo as prestamo, COUNT(*) as total_prestamos')
            ->join('biblio2_libros l', 'l.id = p.id_libro');

        if ($id_biblioteca !== null) {
            $builder->where('p.id_biblioteca', $id_biblioteca);
        }

        return $builder->groupBy('l.id')
            ->orderBy('total_prestamos', 'DESC')
            ->limit(6)
            ->get()
            ->getResultArray();
    }

    // En NotificacionesController.php o DashboardController.php
    public function enviarRecordatorio()
    {
        $prestamoId = $this->request->getPost('prestamo_id');
        $email = $this->request->getPost('email');

        // Validar datos
        if (empty($prestamoId) || empty($email)) {
            return $this->respond(['status' => 'error', 'message' => 'Faltan datos'], 400);
        }

        // Obtener datos del préstamo
        $db = \Config\Database::connect();
        $prestamo = $db->table('biblio2_prestamos')
            ->select('biblio2_prestamos.*, biblio2_libros.titulo as titulo_libro')
            ->join('biblio2_libros', 'biblio2_libros.id = biblio2_prestamos.id_libro')
            ->where('biblio2_prestamos.id', $prestamoId)
            ->get()
            ->getRowArray();

        if (!$prestamo) {
            return $this->respond(['status' => 'error', 'message' => 'Préstamo no encontrado'], 404);
        }

        // Configurar el email
        $emailService = \Config\Services::email();
        $emailService->setFrom('tubiblioteca@example.com', 'Biblioteca');
        $emailService->setTo($email);
        $emailService->setSubject('Recordatorio: Devolución de libro');

        $diasRetraso = floor((time() - strtotime($prestamo['fecha_devolucion'])) / (60 * 60 * 24));

        $message = "
        <h2>Recordatorio de devolución</h2>
        <p>Hola,</p>
        <p>Te recordamos que el libro <strong>{$prestamo['titulo_libro']}</strong> tiene una fecha de devolución prevista para el " .
            date('d/m/Y', strtotime($prestamo['fecha_devolucion'])) . ".</p>
        <p>Actualmente lleva <strong>{$diasRetraso} días de retraso</strong>.</p>
        <p>Por favor, devuélvelo a la biblioteca lo antes posible para evitar cargos adicionales.</p>
        <p>Gracias,<br>El equipo de la biblioteca</p>
    ";

        $emailService->setMessage($message);

        // Enviar el email
        if ($emailService->send()) {
            // Registrar el recordatorio en la base de datos (opcional)
            $db->table('biblio2_recordatorios')
                ->insert([
                    'prestamo_id' => $prestamoId,
                    'email' => $email,
                    'mensaje' => $message,
                    'fecha_envio' => date('Y-m-d H:i:s')
                ]);

            return $this->respond(['status' => 'ok', 'message' => 'Recordatorio enviado']);
        } else {
            return $this->respond(['status' => 'error', 'message' => 'Error al enviar el email'], 500);
        }
    }

    public function verLibrosInv()
    {
        // Listados desde el modelo
        $autores = $this->altasModel->listaAutores();
        $editoriales = $this->altasModel->listaEditoriales();
        $generos = $this->altasModel->listaGeneros();
        $libros = $this->altasModel->listaLibros();
        $usuarios = $this->usuariosModel->getUsuarios();

        // Mapas para acceso rápido (convertimos keys a string para evitar problemas)
        $autoresMap = [];
        foreach ($autores as $autor) {
            $autoresMap[(string)$autor->id] = $autor->nombreautor;
        }

        $editorialesMap = [];
        foreach ($editoriales as $editorial) {
            $editorialesMap[(string)$editorial->id] = $editorial->nombreeditorial;
        }

        $generosMap = [];
        foreach ($generos as $genero) {
            $generosMap[(string)$genero->id] = $genero->genero;
        }

        // Datos para la vista
        $data = [
            'titulo'          => $this->data['settings']->titulo_pagina,
            'libros'          => $libros,
            'usuario'         => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
            'autoresMap'      => $autoresMap,
            'editorialesMap'  => $editorialesMap,
            'generosMap'      => $generosMap
        ];

        return view('layout', [
            'contenido' => view('listado_general', $data)
        ]);
    }

    public function calendar()
    {
        $configura = $this->comunesModel->configura();
        $data = [
            'titulo'     => $configura->titulo_pagina ?? '',
            'usuarios'   => $this->usuariosModel->getUsuarios(),
            'usuario'    => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
            'id_usuario' => $this->session->get('id_usuario')
        ];

        return view('calendar/layout', [
            'contenido' => view('calendar/calendar_view', $data)
        ]);
    }

    /* ========= VER LIBRO ========= */
    public function verLibro($id = null)
    {
        $libro = $this->adminModel->dataLibro($id);

        $data = [
            'titulo'        => "Edita Libros/CD's",
            'usuario'       => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
            'libro'         => $libro,
            'autor'         => $this->editarModel->dataAutor($libro->autor),
            'editorial'     => $this->editarModel->dataEditorial($libro->editorial),
            'genero'        => $this->editarModel->dataGenero($libro->genero),
        ];
        // var_dump($data); exit;
        return view('layout', [
            'contenido' => view('ver_libro', $data)
        ]);
    }


    public function actualizaOrden()
    {
        $ordenGet = $this->request->getGet('orden');
        $tabla    = $this->request->getGet('tabla');

        $ordenado = '';

        if (is_array($ordenGet)) {
            foreach ($ordenGet as $orden => $item) {
                if ($this->comunesModel->actOrden($item, $orden, $tabla)) {
                    $ordenado = '<i class="fa fa-info"></i> Orden guardado correctamente.';
                } else {
                    $ordenado = '<i class="fa fa-info"></i> ERROR!!!.';
                }
            }
        }

        return $this->response->setBody($ordenado);
    }

    public function settings()
    {
        $idConectado = $this->session->get('id_usuario');

        $data = [
            'usuario'  => $this->usuariosModel->dataUsuario($idConectado),
            'settings' => $this->comunesModel->configura(),
            'paises'   => $this->comunesModel->paises(),
        ];

        $data['titulo'] = $data['settings']->titulo_pagina;

        return view('layout', [
            'contenido' => view('settings_view', $data)
        ]);
    }

    public function update()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'base_url'      => 'required|trim',
            'titulo_pagina' => 'required|trim',
            'nombre_bd'     => 'required',
        ], [
            'required' => 'El campo {field} es obligatorio',
        ]);

        $idConectado = $this->session->get('id_usuario');
        $data['usuario'] = $this->usuariosModel->dataUsuario($idConectado);

        if (! $validation->withRequest($this->request)->run()) {

            return $this->settings();
        } else {

            if ($this->adminModel->updateSettings()) {
                $data['contenido'] = '
                    <div class="alert alert-success">
                        <i class="fa fa-thumbs-up fa-lg"></i>
                        Las preferencias han sido modificadas correctamente.
                    </div>
                    <p>
                        <a class="btn btn-primary" href="' . base_url('admin/settings') . '">
                            Volver a ver las preferencias
                        </a>
                    </p>';
            } else {
                $data['contenido'] = '
                    <div class="alert alert-danger">
                        <i class="fa fa-thumbs-down fa-lg"></i>
                        Ha ocurrido un error. Vuelva a intentarlo más tarde.
                    </div>
                    <p>
                        <a class="btn btn-primary" href="' . base_url('admin/settings') . '">
                            Volver a ver las preferencias
                        </a>
                    </p>';
            }

            $data['titulo'] = 'Resultado Preferencias';

            return view('layout', [
                'contenido' => view('resultado', $data)
            ]);
        }
    }
}
