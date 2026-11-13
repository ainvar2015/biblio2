<?php

namespace App\Controllers;

use App\Models\PrestamosModel;
use App\Models\UsuariosModel;
use App\Models\LibroModel;
use App\Models\BibliotecaModel;

class Prestamos extends BaseController
{
    protected $prestamosModel;
    protected $usuariosModel;
    protected $libroModel;
    protected $bibliotecaModel;
    protected $session;

    public function __construct()
    {
        $this->prestamosModel = new PrestamosModel();
        $this->usuariosModel = new UsuariosModel();
        $this->libroModel = new LibroModel();
        $this->bibliotecaModel = new BibliotecaModel();
        $this->session = session();
        helper(['url', 'form']);
    }

    // ============================================
    // INDEX (con filtro de biblioteca)
    // ============================================
    public function index()
    {
        $filtroBiblioteca = $this->getBibliotecaFiltro();
        $filtros = [
            'buscar' => $this->request->getGet('buscar'),
            'tipo' => $this->request->getGet('tipo'),
            'retraso' => $this->request->getGet('retraso')
        ];

        $data = [
            'titulo' => 'Gestión de Préstamos',
            'prestamos' => $this->prestamosModel->getPrestamosActivos($filtros, $filtroBiblioteca),
            'estadisticas' => $this->prestamosModel->getEstadisticas($filtroBiblioteca),
            'filtros' => $filtros,
            'esSuperadmin' => $this->rolUsuario === 'superadmin',
            'bibliotecas' => $this->rolUsuario === 'superadmin'
                ? $this->bibliotecaModel->where('activa', 1)->findAll()
                : [],
            'usuario' => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
            'js_especifico' => 'vista-prestamos.js'
        ];

        return view('layout', [
            'contenido' => view('prestamos/index', $data)
        ]);
    }

    // ============================================
    // HISTORIAL (con filtro de biblioteca)
    // ============================================
    public function historial()
    {
        $filtroBiblioteca = $this->getBibliotecaFiltro();
        $db = \Config\Database::connect();

        $builder = $db->table('biblio2_prestamos p')
            ->select('
                l.id as libro_id,
                l.titulo as titulo_libro,
                l.codigoISBN,
                l.portada,
                a.nombreautor as autor,
                COUNT(p.id) as total_prestamos,
                MAX(p.fecha_prestamo) as ultimo_prestamo,
                MIN(p.fecha_prestamo) as primer_prestamo
            ')
            ->join('biblio2_libros l', 'l.id = p.id_libro')
            ->join('biblio2_autores a', 'a.id = l.autor', 'left')
            ->where('p.fecha_entrada IS NOT NULL')
            ->where('p.fecha_entrada !=', '0000-00-00');

        if ($filtroBiblioteca !== null) {
            $builder->where('p.id_biblioteca', $filtroBiblioteca);
        }

        $libros = $builder->groupBy('l.titulo, l.codigoISBN')
            ->orderBy('total_prestamos', 'DESC')
            ->orderBy('ultimo_prestamo', 'DESC')
            ->get()
            ->getResult();

        $data = [
            'titulo' => 'Historial de Préstamos',
            'libros' => $libros,
            'esSuperadmin' => $this->rolUsuario === 'superadmin',
            'bibliotecas' => $this->rolUsuario === 'superadmin'
                ? $this->bibliotecaModel->where('activa', 1)->findAll()
                : [],
            'usuario' => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
            'js_especifico' => 'historial-prestamos.js'
        ];

        return view('layout', [
            'contenido' => view('prestamos/historial', $data)
        ]);
    }

    // ============================================
    // DETALLES LIBRO (AJAX) - sin cambios funcionales
    // ============================================
    public function detallesLibro($idLibro)
    {
        $db = \Config\Database::connect();

        $prestamos = $db->table('biblio2_prestamos p')
            ->select('
                p.*,
                CASE 
                    WHEN p.tipo_prestatario = "socio" THEN u.nombre
                    ELSE p.nombre_externo
                END as nombre_prestatario,
                CASE 
                    WHEN p.tipo_prestatario = "socio" THEN u.apellidos
                    ELSE ""
                END as apellidos_prestatario,
                CASE 
                    WHEN p.tipo_prestatario = "socio" THEN u.email
                    ELSE p.email_externo
                END as email_prestatario,
                l.titulo as titulo_libro,
                l.codigoISBN', false)
            ->where('p.id_libro', $idLibro)
            ->where('p.fecha_entrada IS NOT NULL')
            ->where('p.fecha_entrada !=', '0000-00-00')
            ->join('biblio2_libros l', 'l.id = p.id_libro')
            ->join('biblio2_usuarios u', 'u.id = p.id_usuario', 'left')
            ->orderBy('p.fecha_prestamo', 'DESC')
            ->get()
            ->getResult();

        foreach ($prestamos as &$p) {
            if ($p->fecha_entrada && $p->fecha_prestamo) {
                $inicio = new \DateTime($p->fecha_prestamo);
                $fin = new \DateTime($p->fecha_entrada);
                $p->dias_prestamo = $inicio->diff($fin)->days;
            } else {
                $p->dias_prestamo = null;
            }
        }

        return $this->response->setJSON([
            'status' => 'ok',
            'data' => $prestamos
        ]);
    }

    // ============================================
    // HISTORIAL PRÉSTAMOS POR LIBRO
    // ============================================
    public function historialPrestamos($idLibro)
    {
        $db = \Config\Database::connect();

        $libro = $db->table('biblio2_libros')
            ->select('biblio2_libros.*, biblio2_autores.nombreautor as autor')
            ->join('biblio2_autores', 'biblio2_autores.id = biblio2_libros.autor', 'left')
            ->where('biblio2_libros.id', $idLibro)
            ->get()
            ->getRow();

        if (!$libro) {
            return redirect()->to('libros')->with('error', 'Libro no encontrado');
        }

        $prestamos = $db->table('biblio2_prestamos p')
            ->select('p.*, u.nombre as usuario_nombre, u.apellidos as usuario_apellidos, u.email as usuario_email')
            ->where('p.id_libro', $idLibro)
            ->join('biblio2_usuarios u', 'u.id = p.id_usuario', 'left')
            ->orderBy('p.fecha_prestamo', 'DESC')
            ->get()
            ->getResult();

        // ... resto del método sin cambios ...
        $totalPrestamos = count($prestamos);
        $prestamosActivos = 0;
        $diasTotales = 0;
        $conteoDias = 0;
        $primerPrestamo = null;
        $ultimoPrestamo = null;

        foreach ($prestamos as &$p) {
            if (empty($p->tipo_prestatario)) {
                $p->tipo_prestatario = $p->id_usuario ? 'socio' : 'externo';
            }

            if ($p->fecha_entrada && $p->fecha_prestamo) {
                $inicio = new \DateTime($p->fecha_prestamo);
                $fin = new \DateTime($p->fecha_entrada);
                $p->dias_prestamo = $inicio->diff($fin)->days;
                $diasTotales += $p->dias_prestamo;
                $conteoDias++;
            } else {
                $p->dias_prestamo = null;
                $prestamosActivos++;
            }

            if (!$primerPrestamo || $p->fecha_prestamo < $primerPrestamo) {
                $primerPrestamo = $p->fecha_prestamo;
            }
            if (!$ultimoPrestamo || $p->fecha_prestamo > $ultimoPrestamo) {
                $ultimoPrestamo = $p->fecha_prestamo;
            }
        }

        $data = [
            'titulo' => 'Historial: ' . $libro->titulo,
            'libro' => $libro,
            'prestamos' => $prestamos,
            'prestamosActivos' => $prestamosActivos,
            'promedioDias' => $conteoDias > 0 ? $diasTotales / $conteoDias : 0,
            'primerPrestamo' => $primerPrestamo,
            'ultimoPrestamo' => $ultimoPrestamo,
            'usuario' => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
            'js_especifico' => 'historial-libro.js'
        ];

        return view('layout', [
            'contenido' => view('prestamos/historialLibro', $data)
        ]);
    }

    // ============================================
    // ESTADÍSTICAS LIBRO - sin cambios
    // ============================================
    public function estadisticasLibro($idLibro)
    {
        // ... tu código actual sin cambios ...
        $db = \Config\Database::connect();

        $libro = $db->table('biblio2_libros')
            ->select('biblio2_libros.*, biblio2_autores.nombreautor as autor')
            ->join('biblio2_autores', 'biblio2_autores.id = biblio2_libros.autor', 'left')
            ->where('biblio2_libros.id', $idLibro)
            ->get()
            ->getRow();

        if (!$libro) {
            return redirect()->to('prestamos/historial')->with('error', 'Libro no encontrado');
        }

        // Todos los préstamos del libro
        $prestamos = $db->table('biblio2_prestamos p')
            ->select('
            p.*,
            CASE WHEN p.tipo_prestatario = "socio" THEN u.nombre ELSE p.nombre_externo END as nombre_prestatario,
            CASE WHEN p.tipo_prestatario = "socio" THEN u.apellidos ELSE "" END as apellidos_prestatario,
            u.email as usuario_email,
            p.email_externo', false)
            ->where('p.id_libro', $idLibro)
            ->join('biblio2_usuarios u', 'u.id = p.id_usuario', 'left')
            ->orderBy('p.fecha_prestamo', 'DESC')
            ->get()
            ->getResult();

        // Calcular estadísticas
        $stats = [
            'total_prestamos' => count($prestamos),
            'activos' => 0,
            'devueltos' => 0,
            'dias_totales' => 0,
            'dias_promedio' => 0,
            'max_dias' => 0,
            'min_dias' => null,
            'por_tipo' => ['socio' => 0, 'externo' => 0],
            'por_mes' => [],
            'prestatarios_recurrentes' => []
        ];

        $prestatarios = [];

        foreach ($prestamos as $p) {
            // Contar por estado
            if (empty($p->fecha_entrada) || $p->fecha_entrada === '0000-00-00') {
                $stats['activos']++;
            } else {
                $stats['devueltos']++;
            }

            // Contar por tipo
            $stats['por_tipo'][$p->tipo_prestatario]++;

            // Calcular días si está devuelto
            if (!empty($p->fecha_entrada) && $p->fecha_entrada !== '0000-00-00' && $p->fecha_prestamo) {
                $inicio = new \DateTime($p->fecha_prestamo);
                $fin = new \DateTime($p->fecha_entrada);
                $dias = $inicio->diff($fin)->days;

                $stats['dias_totales'] += $dias;
                $stats['max_dias'] = max($stats['max_dias'], $dias);
                $stats['min_dias'] = $stats['min_dias'] === null ? $dias : min($stats['min_dias'], $dias);

                // Agrupar por mes
                $mes = date('Y-m', strtotime($p->fecha_prestamo));
                if (!isset($stats['por_mes'][$mes])) {
                    $stats['por_mes'][$mes] = 0;
                }
                $stats['por_mes'][$mes]++;

                // Contar prestatarios recurrentes
                $nombre = $p->nombre_prestatario . ' ' . ($p->apellidos_prestatario ?? '');
                if (!isset($prestatarios[$nombre])) {
                    $prestatarios[$nombre] = [
                        'nombre' => trim($nombre),
                        'email' => $p->usuario_email ?? $p->email_externo,
                        'veces' => 0
                    ];
                }
                $prestatarios[$nombre]['veces']++;
            }
        }

        // Calcular promedio
        if ($stats['devueltos'] > 0) {
            $stats['dias_promedio'] = round($stats['dias_totales'] / $stats['devueltos'], 1);
        }

        // Top prestatarios recurrentes
        usort($prestatarios, function ($a, $b) {
            return $b['veces'] <=> $a['veces'];
        });
        $stats['prestatarios_recurrentes'] = array_slice($prestatarios, 0, 5);

        // Ordenar meses cronológicamente
        ksort($stats['por_mes']);

        $data = [
            'titulo' => 'Estadísticas: ' . $libro->titulo,
            'usuario' => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
            'libro' => $libro,
            'prestamos' => $prestamos,
            'stats' => $stats,
            // 'js_especifico' => 'estadisticas-libro.js'
        ];

        return view('layout', [
            'contenido' => view('prestamos/estadisticas_libro', $data)
        ]);
    }

    // ============================================
    // DETALLES PRÉSTAMO - sin cambios
    // ============================================
    public function detalles($idPrestamo)
    {
        $prestamo = $this->prestamosModel->getPrestamosConDetalle($idPrestamo);

        if (!$prestamo) {
            return redirect()->to('prestamos')->with('error', 'Préstamo no encontrado');
        }

        $hoy = new \DateTime();
        $fechaDev = $prestamo->fecha_devolucion ? new \DateTime($prestamo->fecha_devolucion) : null;
        $fechaEntrada = $prestamo->fecha_entrada ? new \DateTime($prestamo->fecha_entrada) : null;
        $fechaPrestamo = new \DateTime($prestamo->fecha_prestamo);

        $prestamo->dias_prestamo = null;
        $prestamo->dias_retraso = 0;
        $prestamo->estado = 'activo';

        if ($fechaEntrada) {
            $prestamo->dias_prestamo = $fechaPrestamo->diff($fechaEntrada)->days;
            $prestamo->estado = $prestamo->dias_prestamo > 30 ? 'retrasado' : 'devuelto';
        } elseif ($fechaDev && $hoy > $fechaDev) {
            $prestamo->dias_retraso = $hoy->diff($fechaDev)->days;
            $prestamo->estado = 'retrasado';
        }

        return view('layout', [
            'titulo' => 'Detalles del Préstamo #' . $idPrestamo,
            'contenido' => view('prestamos/detalles', ['prestamo' => $prestamo])
        ]);
    }

    // ============================================
    // DATATABLES (con filtro de biblioteca)
    // ============================================
    public function datatables()
    {
        $filtroBiblioteca = $this->getBibliotecaFiltro();
        $filtros = [
            'buscar' => $this->request->getPost('search')['value'] ?? '',
            'tipo' => $this->request->getPost('tipo') ?? '',
            'retraso' => $this->request->getPost('retraso') ?? ''
        ];

        $prestamos = $this->prestamosModel->getPrestamosActivos($filtros, $filtroBiblioteca);
        $total = $this->prestamosModel->countAll();

        return $this->response->setJSON([
            'draw' => intval($this->request->getPost('draw') ?? 1),
            'recordsTotal' => $total,
            'recordsFiltered' => count($prestamos),
            'data' => $prestamos
        ]);
    }

    // ============================================
    // DEVOLVER (API)
    // ============================================
    public function devolver()
    {
        $this->requiereAdmin();

        $id = $this->request->getPost('id');

        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID de préstamo no proporcionado'
            ]);
        }

        // Verificar que el préstamo pertenece a la biblioteca del admin
        if (!$this->esSuperadmin()) {
            $prestamo = $this->prestamosModel->find($id);
            if (!$prestamo || $prestamo['id_biblioteca'] != $this->idBiblioteca) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No tienes permiso para este préstamo'
                ]);
            }
        }

        $resultado = $this->prestamosModel->registrarDevolucion($id);

        return $this->response->setJSON([
            'status' => $resultado ? 'ok' : 'error',
            'message' => $resultado ? 'Devolución registrada correctamente' : 'Error al registrar devolución'
        ]);
    }

    // ============================================
    // RENOVAR (API)
    // ============================================
    public function renovar()
    {
        $this->requiereAdmin();

        $id = $this->request->getPost('id');
        $dias = intval($this->request->getPost('dias') ?? 7);

        $prestamo = $this->prestamosModel->find($id);

        if (!$prestamo) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Préstamo no encontrado'
            ]);
        }

        // Verificar biblioteca
        if (!$this->esSuperadmin() && $prestamo['id_biblioteca'] != $this->idBiblioteca) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No tienes permiso para este préstamo'
            ]);
        }

        $fechaActual = new \DateTime($prestamo['fecha_devolucion']);
        $fechaActual->add(new \DateInterval("P{$dias}D"));

        $this->prestamosModel->update($id, [
            'fecha_devolucion' => $fechaActual->format('Y-m-d')
        ]);

        return $this->response->setJSON([
            'status' => 'ok',
            'message' => "Préstamo renovado por {$dias} días"
        ]);
    }

    // ============================================
    // NUEVO PRÉSTAMO (vista)
    // ============================================
    public function nuevo()
    {
        $this->requiereAdmin();

        $data = [
            'titulo' => 'Nuevo Préstamo',
            'esSuperadmin' => $this->esSuperadmin(),
            'miBiblioteca' => $this->idBiblioteca,
            'bibliotecas' => $this->bibliotecaModel->where('activa', 1)->findAll(),
            'libros' => $this->libroModel
                ->where('id_biblioteca', $this->idBiblioteca)
                ->where('prestamo', 0)
                ->orderBy('titulo', 'ASC')
                ->findAll(),
            'socios' => $this->usuariosModel
                ->where('id_biblioteca', $this->idBiblioteca)
                ->where('activo', 1)
                ->orderBy('nombre', 'ASC')
                ->findAll(),
            'usuario' => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
        ];

        return view('layout', [
            'contenido' => view('prestamos/form', $data)
        ]);
    }

    public function editar($id = null)
    {
        $this->requiereAdmin();

        if (!$id) {
            return redirect()->to('prestamos')->with('error', 'ID de préstamo no válido');
        }

        $prestamo = $this->prestamosModel->find($id);
        if (!$prestamo) {
            return redirect()->to('prestamos')->with('error', 'Préstamo no encontrado');
        }

        $data = [
            'titulo' => 'Editar Préstamo',
            'esSuperadmin' => $this->esSuperadmin(),
            'miBiblioteca' => $this->idBiblioteca,
            'bibliotecas' => $this->bibliotecaModel->where('activa', 1)->findAll(),
            'libros' => $this->libroModel->findAll(), // Todos los libros para mostrar el actual
            'socios' => $this->usuariosModel
                ->where('activo', 1)
                ->orderBy('nombre', 'ASC')
                ->findAll(),
            'prestamo' => $prestamo,
            'usuario' => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
        ];

        return view('layout', [
            'contenido' => view('prestamos/form', $data)
        ]);
    }

    private function esSuperadmin(): bool
    {
        return $this->rolUsuario === 'superadmin';
    }

    // ============================================
    // GUARDAR PRÉSTAMO
    // ============================================
    public function guardar()
    {
        $this->requiereAdmin();

        $rules = [
            'id_libro'          => 'required|integer',
            'tipo_prestatario'  => 'required|in_list[socio,externo]',
            'fecha_prestamo'    => 'required|valid_date[Y-m-d]',
            'fecha_devolucion'  => 'required|valid_date[Y-m-d]',
        ];

        if ($this->request->getPost('tipo_prestatario') === 'socio') {
            $rules['id_usuario'] = 'required|integer';
        } else {
            $rules['nombre_externo'] = 'required|max_length[255]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Determinar biblioteca
        if ($this->esSuperadmin()) {
            $idBiblioteca = (int) $this->request->getPost('id_biblioteca');
            if (!$idBiblioteca) {
                return redirect()->back()->withInput()->with('error', 'Selecciona una biblioteca.');
            }
        } else {
            $idBiblioteca = $this->idBiblioteca;
        }

        $idLibro = $this->request->getPost('id_libro');
        $libro = $this->libroModel->where('id', $idLibro)
            ->where('id_biblioteca', $idBiblioteca)
            ->first();

        if (!$libro || ($libro->prestado ?? $libro['prestado'] ?? 0) == 1) {
            return redirect()->back()->withInput()->with('error', 'El libro no está disponible.');
        }

        $data = [
            'id_biblioteca'     => $idBiblioteca,
            'id_libro'          => $idLibro,
            'tipo_prestatario'  => $this->request->getPost('tipo_prestatario'),
            'fecha_prestamo'    => $this->request->getPost('fecha_prestamo'),
            'fecha_devolucion'  => $this->request->getPost('fecha_devolucion'),
            'notas'             => $this->request->getPost('notas'),
            'devuelto'          => 0,
            'fecha_creacion'    => date('Y-m-d H:i:s'),
        ];

        if ($this->request->getPost('tipo_prestatario') === 'socio') {
            $idUsuario = $this->request->getPost('id_usuario');
            $socio = $this->usuariosModel->find($idUsuario);
            $data['id_usuario'] = $idUsuario;
            $data['usuario'] = ($socio->nombre ?? $socio['nombre'] ?? '') . ' ' . ($socio->apellidos ?? $socio['apellidos'] ?? '');
        } else {
            $data['nombre_externo'] = $this->request->getPost('nombre_externo');
            $data['email_externo'] = $this->request->getPost('email_externo');
            $data['telefono_externo'] = $this->request->getPost('telefono_externo');
            $data['dni_externo'] = $this->request->getPost('dni_externo');
            $data['usuario'] = $data['nombre_externo'];
        }

        $data['isbn'] = $libro->isbn ?? $libro['isbn'] ?? null;
        $data['titulo_libro'] = $libro->titulo ?? $libro['titulo'] ?? null;

        $prestamoId = $this->prestamosModel->insert($data);

        if ($prestamoId) {
            $this->libroModel->update($idLibro, ['prestamo' => 1]);
            return redirect()->to('prestamos')->with('success', 'Préstamo registrado correctamente.');
        }

        return redirect()->back()->withInput()->with('error', 'Error al registrar el préstamo.');
    }
}
