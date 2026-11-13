<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Services\EmailService;
use App\Controllers\BaseController;
use App\Models\EditarModel;

class Api extends BaseController
{
    use ResponseTrait;
    protected $editarModel;
    protected $format = 'json';
    protected $emailService;
    protected $idBiblioteca;
    protected $session;

    public function __construct()
    {
        $this->session = session();
        $this->emailService = new EmailService();
        $this->idBiblioteca = $this->session->get('id_biblioteca');
        $this->editarModel = new EditarModel();
    }

    public function librosDatatables()
    {
        $draw = $this->request->getPost('draw') ?? 1;
        $start = $this->request->getPost('start') ?? 0;
        $length = $this->request->getPost('length') ?? 10;
        $search = $this->request->getPost('search')['value'] ?? '';
        $order = $this->request->getPost('order')[0] ?? null;

        $filtroAutor = $this->request->getPost('filtroAutor');
        $filtroEditorial = $this->request->getPost('filtroEditorial');
        $filtroGenero = $this->request->getPost('filtroGenero');
        $filtroEstado = $this->request->getPost('filtroEstado');
        $bibliotecaFiltro = $this->request->getPost('biblioteca');

        $db = \Config\Database::connect();

        // ✅ CORREGIDO: Select base sin joins de autor/género únicos
        $builder = $db->table('biblio2_libros l')
            ->select('l.*, e.nombreeditorial as nombre_editorial')
            ->join('biblio2_editoriales e', 'e.id = l.editorial', 'left');

        // Filtro biblioteca
        if ($this->rolUsuario === 'superadmin') {
            if ($bibliotecaFiltro) {
                $builder->where('l.id_biblioteca', (int)$bibliotecaFiltro);
            }
        } else {
            $builder->where('l.id_biblioteca', $this->idBiblioteca);
        }

        // Filtros personalizados (ahora por tablas pivote)
        if ($filtroAutor) {
            $builder->whereExists(function ($subquery) use ($filtroAutor) {
                $subquery->select(1)->from('biblio2_libros_autores la')
                    ->where('la.id_libro = l.id')
                    ->where('la.id_autor', $filtroAutor);
            });
        }
        if ($filtroEditorial) {
            $builder->where('l.editorial', $filtroEditorial);
        }
        if ($filtroGenero) {
            $builder->whereExists(function ($subquery) use ($filtroGenero) {
                $subquery->select(1)->from('biblio2_libros_generos lg')
                    ->where('lg.id_libro = l.id')
                    ->where('lg.id_genero', $filtroGenero);
            });
        }

        // Estado
        if ($filtroEstado === 'disponible') {
            $builder->where('NOT EXISTS (SELECT 1 FROM biblio2_prestamos p WHERE p.id_libro = l.id AND p.fecha_entrada IS NULL)', null, false);
        } elseif ($filtroEstado === 'prestamo') {
            $builder->where('EXISTS (SELECT 1 FROM biblio2_prestamos p WHERE p.id_libro = l.id AND p.fecha_entrada IS NULL)', null, false);
        }

        // Búsqueda global
        if ($search) {
            $builder->groupStart()
                ->like('l.titulo', $search)
                ->orLike('l.codigoISBN', $search)
                ->groupEnd();
        }

        $totalFiltered = $builder->countAllResults(false);

        if ($order) {
            $columns = ['', '', 'titulo', '', 'nombre_editorial', 'ano_edicio'];
            $column = $columns[$order['column']] ?? 'titulo';
            $dir = $order['dir'] ?? 'asc';
            $builder->orderBy($column, $dir);
        }

        $builder->limit($length, $start);
        $libros = $builder->get()->getResultArray();

        // ✅ CORREGIDO: Cargar autores, géneros y préstamos para cada libro
        foreach ($libros as &$libro) {
            // Autores múltiples
            $autores = $db->table('biblio2_libros_autores la')
                ->select('a.id, a.nombreautor as nombre')
                ->join('biblio2_autores a', 'a.id = la.id_autor')
                ->where('la.id_libro', $libro['id'])
                ->get()
                ->getResultArray();
            $libro['autores'] = $autores;
            $libro['nombre_autor'] = !empty($autores) ? $autores[0]['nombre'] : 'Desconocido';
            $libro['total_autores'] = count($autores);

            // Géneros múltiples
            $generos = $db->table('biblio2_libros_generos lg')
                ->select('g.id, g.genero as nombre')
                ->join('biblio2_generos g', 'g.id = lg.id_genero')
                ->where('lg.id_libro', $libro['id'])
                ->get()
                ->getResultArray();
            $libro['generos'] = $generos;
            $libro['nombre_genero'] = !empty($generos) ? $generos[0]['nombre'] : 'Sin género';
            $libro['total_generos'] = count($generos);

            // Préstamo activo
            $prestamo = $db->table('biblio2_prestamos p')
                ->select('p.*, u.nombre as usuario_nombre, u.apellidos as usuario_apellidos')
                ->where('p.id_libro', $libro['id'])
                ->where('p.fecha_entrada IS NULL')
                ->join('biblio2_usuarios u', 'u.id = p.id_usuario', 'left')
                ->orderBy('p.fecha_prestamo', 'DESC')
                ->get()
                ->getRow();

            if ($prestamo) {
                $libro['prestamo'] = [
                    'id' => $prestamo->id,
                    'tipo_prestatario' => $prestamo->tipo_prestatario,
                    'usuario_nombre' => $prestamo->usuario_nombre ? $prestamo->usuario_nombre . ' ' . ($prestamo->usuario_apellidos ?? '') : null,
                    'nombre_externo' => $prestamo->nombre_externo,
                    'fecha_prestamo' => $prestamo->fecha_prestamo,
                    'fecha_devolucion' => $prestamo->fecha_devolucion
                ];
            } else {
                $libro['prestamo'] = null;
            }
        }

        $total = $db->table('biblio2_libros')->countAllResults();

        return $this->respond([
            'draw' => intval($draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $totalFiltered,
            'data' => $libros
        ]);
    }

    public function usuariosSelect()
    {
        $usuarios = $this->db->table('biblio2_usuarios')
            ->select('id, nombre')
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResult();

        return $this->respond($usuarios);
    }

    public function usuariosBuscar()
    {
        $q = $this->request->getGet('q');

        $db = \Config\Database::connect();
        $builder = $db->table('biblio2_usuarios')
            ->select('id, nombre, apellidos, email')
            ->where('activo', 1)
            ->orderBy('nombre', 'ASC');

        // Aplicar filtros de búsqueda solo si hay término
        if (!empty($q)) {
            $builder->groupStart()
                ->like('nombre', $q)
                ->orLike('apellidos', $q)
                ->orLike('email', $q)
                ->groupEnd();
        }
        // Ejecutar la consulta
        $resultados = $builder->limit(20)->get()->getResult();

        return $this->respond($resultados);
    }

    /**
     * Guardar préstamo (socio o externo)
     */
    public function guardarPrestamo()
    {
        $db = \Config\Database::connect();

        $datos = $this->request->getPost();

        // Validaciones básicas
        if (empty($datos['id_libro']) || empty($datos['fecha_prestamo']) || empty($datos['fecha_devolucion'])) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Faltan datos obligatorios'
            ], 400);
        }

        // Verificar que el libro no esté prestado
        $prestamoActivo = $db->table('biblio2_prestamos')
            ->where('id_libro', $datos['id_libro'])
            ->where('(fecha_entrada IS NULL OR fecha_entrada = "0000-00-00")')
            ->get()
            ->getRow();

        if ($prestamoActivo) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Este libro ya está prestado'
            ], 400);
        }

        // Preparar datos para insertar
        $insertData = [
            'id_libro' => $datos['id_libro'],
            'tipo_prestatario' => $datos['tipo_prestatario'],
            'fecha_prestamo' => $datos['fecha_prestamo'],
            'fecha_devolucion' => $datos['fecha_devolucion'],
            'notas' => $datos['notas'] ?? null,
            'fecha_creacion' => date('Y-m-d H:i:s')
        ];

        if ($datos['tipo_prestatario'] === 'socio') {
            $insertData['id_usuario'] = $datos['id_usuario'];
        } else {
            $insertData['nombre_externo'] = $datos['nombre_externo'];
            $insertData['dni_externo'] = $datos['dni_externo'] ?? null;
            $insertData['email_externo'] = $datos['email_externo'];
            $insertData['telefono_externo'] = $datos['telefono_externo'] ?? null;
        }

        // Insertar préstamo
        $insertado = $db->table('biblio2_prestamos')->insert($insertData);

        if ($insertado) {
            // Marcar libro como prestado
            $db->table('biblio2_libros')
                ->where('id', $datos['id_libro'])
                ->update(['prestamo' => 1]);

            return $this->respond([
                'status' => 'ok',
                'message' => 'Préstamo registrado correctamente'
            ]);
        }

        return $this->respond([
            'status' => 'error',
            'message' => 'Error al registrar préstamo'
        ], 500);
    }

    // Métodos auxiliares para los selects
    public function librosDisponibles()
    {
        $db = \Config\Database::connect();
        $libros = $db->table('biblio2_libros')
            ->select('id, titulo, codigoISBN')
            ->where('prestamo', 0)
            ->orWhere('prestamo IS NULL')
            ->orderBy('titulo', 'ASC')
            ->get()
            ->getResult();

        return $this->respond($libros);
    }

    public function sociosActivos()
    {
        $db = \Config\Database::connect();
        $socios = $db->table('biblio2_usuarios')
            ->select('id, nombre, apellidos, email')
            ->where('activo', 1)
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResult();

        return $this->respond($socios);
    }
    /**
     * Devolver libro
     */
    public function devolverPrestamo()
    {
        $db = \Config\Database::connect();

        $idPrestamo = $this->request->getPost('id_prestamo');
        $idLibro = $this->request->getPost('id_libro');
        $fechaEntrada = $this->request->getPost('fecha_entrada') ?: date('Y-m-d');
        $notas = $this->request->getPost('notas');

        // Validar que hay ID
        if (!$idPrestamo && !$idLibro) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Debe proporcionar id_prestamo o id_libro'
            ], 400);
        }

        // Buscar préstamo activo
        $builder = $db->table('biblio2_prestamos')
            ->where('(fecha_entrada IS NULL OR fecha_entrada = "0000-00-00")');

        if ($idPrestamo) {
            $builder->where('id', $idPrestamo);
        } else {
            $builder->where('id_libro', $idLibro)
                ->orderBy('fecha_prestamo', 'DESC');
        }

        $prestamo = $builder->get()->getRow();

        if (!$prestamo) {
            return $this->respond([
                'status' => 'error',
                'message' => 'No hay préstamo activo encontrado'
            ], 404);
        }

        // Preparar datos de actualización
        $datosUpdate = [
            'fecha_entrada' => $fechaEntrada,
            'fecha_devolucion_real' => date('Y-m-d H:i:s')
        ];

        if ($notas !== null) {
            $datosUpdate['notas'] = $notas;
        }

        // Actualizar préstamo
        $actualizado = $db->table('biblio2_prestamos')
            ->where('id', $prestamo->id)
            ->update($datosUpdate);

        if ($actualizado) {
            // Liberar libro
            $db->table('biblio2_libros')
                ->where('id', $prestamo->id_libro)
                ->update(['prestamo' => 0]);

            return $this->respond([
                'status' => 'ok',
                'message' => 'Devolución registrada correctamente'
            ]);
        }

        return $this->respond([
            'status' => 'error',
            'message' => 'Error al registrar devolución'
        ], 500);
    }


    /**
     * Historial de préstamos de un libro
     */
    public function historialPrestamos($idLibro)
    {
        $db = \Config\Database::connect();

        $prestamos = $db->table('biblio2_prestamos p')
            ->select('p.*, u.nombre as usuario_nombre, u.apellidos as usuario_apellidos')
            ->where('p.id_libro', $idLibro)
            ->join('biblio2_usuarios u', 'u.id = p.id_usuario', 'left')
            ->orderBy('p.fecha_prestamo', 'DESC')
            ->get()
            ->getResult();
        // var_dump($prestamos);die();
        // Calcular días de préstamo
        foreach ($prestamos as &$p) {
            if ($p->fecha_entrada && $p->fecha_prestamo) {
                $inicio = new \DateTime($p->fecha_prestamo);
                $fin = new \DateTime($p->fecha_entrada);
                $p->dias_prestamo = $inicio->diff($fin)->days;
            } else {
                $p->dias_prestamo = null;
            }
        }

        return $this->respond([
            'status' => 'ok',
            'data' => $prestamos
        ]);
    }

    /**
     * Obtener detalles completos de un préstamo
     */
    public function detallesPrestamo($idPrestamo)
    {
        $db = \Config\Database::connect();

        $prestamo = $db->table('biblio2_prestamos p')
            ->select('
            p.*,
            l.titulo as libro_titulo,
            l.codigoISBN as libro_isbn,
            l.portada as libro_portada,
            a.nombreautor as libro_autor,
            u.nombre as usuario_nombre,
            u.apellidos as usuario_apellidos,
            u.email as usuario_email,
            u.telefono as usuario_telefono,
            u.dni as usuario_dni
        ')
            ->where('p.id', $idPrestamo)
            ->join('biblio2_libros l', 'l.id = p.id_libro')
            ->join('biblio2_autores a', 'a.id = l.autor', 'left')
            ->join('biblio2_usuarios u', 'u.id = p.id_usuario', 'left')
            ->get()
            ->getRow();

        if (!$prestamo) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Préstamo no encontrado'
            ], 404);
        }

        // Calcular días y estado - CORREGIDO
        $hoy = new \DateTime();
        $hoy->setTime(0, 0, 0);

        $fechaPrestamo = new \DateTime($prestamo->fecha_prestamo);
        $fechaPrestamo->setTime(0, 0, 0);

        // Verificar fecha_entrada correctamente (puede ser NULL o '0000-00-00')
        $tieneEntrada = !empty($prestamo->fecha_entrada) && $prestamo->fecha_entrada !== '0000-00-00';

        $diasPrestamo = null;
        $diasRetraso = 0;
        $estado = 'activo';

        if ($tieneEntrada) {
            // Ya devuelto - CORREGIDO
            $fechaEntrada = new \DateTime($prestamo->fecha_entrada);
            $fechaEntrada->setTime(0, 0, 0);

            $diasPrestamo = $fechaPrestamo->diff($fechaEntrada)->days;
            $estado = 'devuelto'; // Siempre devuelto si tiene fecha_entrada válida

        } else {
            // Aún no devuelto - calcular retraso si aplica
            if (!empty($prestamo->fecha_devolucion) && $prestamo->fecha_devolucion !== '0000-00-00') {
                $fechaDev = new \DateTime($prestamo->fecha_devolucion);
                $fechaDev->setTime(0, 0, 0);

                if ($hoy > $fechaDev) {
                    $diasRetraso = $hoy->diff($fechaDev)->days;
                    $estado = 'retrasado';
                } else {
                    $estado = 'activo';
                }
            } else {
                $estado = 'activo';
            }
        }

        // Preparar datos de respuesta - CORREGIDO para prestatario
        $nombrePrestatario = 'Desconocido';
        $emailPrestatario = null;
        $telefonoPrestatario = null;
        $dniPrestatario = null;

        if ($prestamo->tipo_prestatario === 'socio' && !empty($prestamo->usuario_nombre)) {
            $nombrePrestatario = trim($prestamo->usuario_nombre . ' ' . ($prestamo->usuario_apellidos ?? ''));
            $emailPrestatario = $prestamo->usuario_email;
            $telefonoPrestatario = $prestamo->usuario_telefono;
            $dniPrestatario = $prestamo->usuario_dni;
        } elseif ($prestamo->tipo_prestatario === 'externo') {
            // Para externos, los datos están en la tabla prestamos, no en usuarios
            $nombrePrestatario = $prestamo->nombre_externo ?? 'Externo sin nombre';
            $emailPrestatario = $prestamo->email_externo;
            $telefonoPrestatario = $prestamo->telefono_externo;
            $dniPrestatario = $prestamo->dni_externo;
        }

        $data = [
            'id' => $prestamo->id,
            'estado' => $estado,
            'tiene_entrada' => $tieneEntrada, // Para debug en JS

            // Libro
            'libro' => [
                'id' => $prestamo->id_libro,
                'titulo' => $prestamo->libro_titulo,
                'isbn' => $prestamo->libro_isbn,
                'autor' => $prestamo->libro_autor,
                'portada' => $prestamo->libro_portada
            ],

            // Prestatario - CORREGIDO
            'prestatario' => [
                'tipo' => $prestamo->tipo_prestatario ?? 'desconocido',
                'nombre' => $nombrePrestatario,
                'email' => $emailPrestatario,
                'telefono' => $telefonoPrestatario,
                'dni' => $dniPrestatario
            ],

            // Fechas
            'fecha_prestamo' => $prestamo->fecha_prestamo,
            'fecha_devolucion_prevista' => $prestamo->fecha_devolucion,
            'fecha_entrada_real' => $tieneEntrada ? $prestamo->fecha_entrada : null,
            'fecha_devolucion_real' => $prestamo->fecha_devolucion_real,

            // Cálculos
            'dias_prestamo' => $diasPrestamo,
            'dias_retraso' => $diasRetraso,
            'dias_restantes' => (!$tieneEntrada && !empty($prestamo->fecha_devolucion) && $prestamo->fecha_devolucion !== '0000-00-00')
                ? max(0, $hoy->diff(new \DateTime($prestamo->fecha_devolucion))->days)
                : null,

            // Notas
            'notas' => $prestamo->notas,

            // Metadata
            'creado' => $prestamo->fecha_creacion
        ];

        return $this->respond([
            'status' => 'ok',
            'data' => $data
        ]);
    }
    /**
     * Enviar recordatorio de devolución
     */
    public function enviarRecordatorio()
    {
        $db = \Config\Database::connect();

        $idPrestamo = $this->request->getPost('id_prestamo');
        $tipo = $this->request->getPost('tipo'); // email, whatsapp
        $mensajePersonalizado = $this->request->getPost('mensaje');

        // Solo implementamos email por ahora
        if ($tipo !== 'email') {
            return $this->respond([
                'status' => 'error',
                'message' => 'Por ahora solo está disponible el envío por email'
            ], 400);
        }

        // Obtener datos completos del préstamo
        $prestamo = $db->table('biblio2_prestamos p')
            ->select('p.*, l.titulo as titulo_libro, l.codigoISBN')
            ->where('p.id', $idPrestamo)
            ->join('biblio2_libros l', 'l.id = p.id_libro')
            ->get()
            ->getRow();

        if (!$prestamo) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Préstamo no encontrado'
            ], 404);
        }

        // Determinar destinatario según tipo
        $destinatario = [];

        if ($prestamo->tipo_prestatario === 'socio') {
            $usuario = $db->table('biblio2_usuarios')
                ->where('id', $prestamo->id_usuario)
                ->get()
                ->getRow();

            if (!$usuario || empty($usuario->email)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'El socio no tiene email registrado'
                ], 400);
            }

            $destinatario = [
                'nombre' => $usuario->nombre . ' ' . ($usuario->apellidos ?? ''),
                'email' => $usuario->email
            ];
        } else {
            // Externo
            if (empty($prestamo->email_externo)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'El usuario externo no tiene email'
                ], 400);
            }

            $destinatario = [
                'nombre' => $prestamo->nombre_externo,
                'email' => $prestamo->email_externo
            ];
        }

        // Calcular días de retraso/restantes
        $hoy = new \DateTime();
        $fechaDev = $prestamo->fecha_devolucion ? new \DateTime($prestamo->fecha_devolucion) : null;

        $diasRetraso = 0;
        $diasRestantes = 0;

        if ($fechaDev) {
            if ($hoy > $fechaDev) {
                $diasRetraso = $hoy->diff($fechaDev)->days;
            } else {
                $diasRestantes = $hoy->diff($fechaDev)->days;
            }
        }

        // Preparar datos para el email
        $datosEmail = [
            'titulo_libro' => $prestamo->titulo_libro,
            'isbn' => $prestamo->isbn,
            'fecha_prestamo' => date('d/m/Y', strtotime($prestamo->fecha_prestamo)),
            'fecha_devolucion' => $prestamo->fecha_devolucion ? date('d/m/Y', strtotime($prestamo->fecha_devolucion)) : 'No especificada',
            'dias_retraso' => $diasRetraso,
            'dias_restantes' => $diasRestantes,
            'mensaje_personalizado' => nl2br(esc($mensajePersonalizado))
        ];

        // Enviar email
        $resultado = $this->emailService->enviarRecordatorio($destinatario, $datosEmail);

        if ($resultado['success']) {
            // Guardar registro en BD
            $db->table('biblio2_recordatorios')->insert([
                'id_prestamo' => $idPrestamo,
                'tipo' => 'email',
                'fecha_envio' => date('Y-m-d H:i:s'),
                'estado' => 'enviado',
                'mensaje' => substr($mensajePersonalizado, 0, 500)
            ]);

            return $this->respond([
                'status' => 'ok',
                'message' => "Recordatorio enviado a {$destinatario['email']}"
            ]);
        }

        // Error al enviar
        // Guardar como fallido
        $db->table('biblio2_recordatorios')->insert([
            'id_prestamo' => $idPrestamo,
            'tipo' => 'email',
            'fecha_envio' => date('Y-m-d H:i:s'),
            'estado' => 'fallido',
            'mensaje' => $resultado['message']
        ]);

        return $this->respond([
            'status' => 'error',
            'message' => 'Error al enviar email: ' . $resultado['message']
        ], 500);
    }
}
