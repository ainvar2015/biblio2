<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AltasModel;
use App\Models\UsuariosModel;
use App\Models\BibliotecaModel;

class Altas extends BaseController
{
    protected $altasModel;
    protected $comunesModel;
    protected $usuariosModel;
    protected $bibliotecaModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->altasModel     = new AltasModel();
        $this->usuariosModel  = new UsuariosModel();
        $this->bibliotecaModel = new BibliotecaModel();
        $this->session        = session();   // CI4 helper
        $this->db             = \Config\Database::connect();

        helper(['url', 'form']);
    }

    public function index()
    {
        $id_conectado = $this->session->get('id_usuario');
        $nivel = $this->session->get('nivel');
        $data = [
            'usuario'     => $this->usuariosModel->dataUsuario($id_conectado),
            'param3'      => '',
            'param1'      => 'altas',
            'autores'     => $this->altasModel->listaAutores(),
            'editoriales' => $this->altasModel->listaEditoriales(),
            'generos'     => $this->altasModel->listaGeneros(),
            'libros'      => $this->altasModel->listaLibros(),
            'titulo'      => 'Altas',
            'bibliotecas' => $this->bibliotecaModel->listaBibliotecas()
        ];

        return view('layout', [
            'contenido' => view('listado_general', $data)
        ]);
    }

    public function checkLibro()
    {
        try {
            $isbn = $this->request->getPost('codigoISBN');
            $titulo = $this->request->getPost('titulo');
            $idBiblioteca = $this->request->getPost('id_biblioteca');

            // Si no hay biblioteca, usar la de sesión
            if (empty($idBiblioteca)) {
                $idBiblioteca = session()->get('id_biblioteca');
            }

            $existe = false;

            if (!empty($isbn) || !empty($titulo)) {
                $builder = $this->db->table('biblio2_libros')
                    ->where('id_biblioteca', $idBiblioteca);

                $builder->groupStart();

                if (!empty($titulo)) {
                    $builder->where('titulo', $titulo);
                }

                if (!empty($isbn)) {
                    if (!empty($titulo)) {
                        $builder->orWhere('codigoISBN', $isbn);
                    } else {
                        $builder->where('codigoISBN', $isbn);
                    }
                }

                $builder->groupEnd();

                $existe = $builder->countAllResults() > 0;
            }

            return $this->response->setJSON([
                'existe' => $existe
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'error' => $e->getMessage(),
                'existe' => true
            ]);
        }
    }

    public function checkAutor()
    {
        return $this->checkRegistro('autores', 'nombreautor');
    }

    public function checkEditorial()
    {
        return $this->checkRegistro('editoriales', 'nombreeditorial');
    }

    public function checkGenero()
    {
        return $this->checkRegistro('generos', 'nombre', 'nombregenero');
    }

    private function checkRegistro($tabla, $campoTabla, $campoPost = null)
    {
        $campoPost = $campoPost ?? $campoTabla;
        $campoNombre = $this->request->getPost($campoPost);
        try {
            $nombre = $this->request->getPost($campoNombre);

            if (empty($nombre)) {
                return $this->response->setJSON([
                    'existe' => false,
                    'message' => 'El nombre está vacío.' . $nombre
                ]);
            }

            $existe = $this->altasModel->existeRegistro($tabla, [$campoNombre => $nombre]);

            return $this->response->setJSON([
                'existe' => $existe
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'error' => $e->getMessage(),
                'existe' => true // Bloquear envío por seguridad
            ]);
        }
    }

    public function guardar($tipo)
    {
        $idUsuario = session()->get('id_usuario');
        $data['usuario'] = $this->usuariosModel->dataUsuario($idUsuario);

        switch ($tipo) {
            case 'libro':
                $idBiblioteca = $this->request->getPost('id_biblioteca');
                if (empty($idBiblioteca)) {
                    $idBiblioteca = session()->get('id_biblioteca');
                }

                if (!$this->bibliotecaModel->find($idBiblioteca)) {
                    $data['titulo'] = "Error";
                    $data['contenido'] = '
                    <div class="col-lg-12">
                        <p class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            La biblioteca seleccionada no existe.
                        </p>
                    </div>';
                    return view('layout', ['contenido' => view('resultado', $data)]);
                }

                // ✅ NUEVO: Obtener arrays de autores y géneros múltiples
                $autoresSeleccionados = $this->request->getPost('autores') ?? [];
                $generosSeleccionados = $this->request->getPost('generos') ?? [];

                // Validar que haya al menos un autor y un género
                if (empty($autoresSeleccionados)) {
                    $data['titulo'] = "Error";
                    $data['contenido'] = '
                    <div class="col-lg-12">
                        <p class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            Debes seleccionar al menos un autor.
                        </p>
                    </div>';
                    return view('layout', ['contenido' => view('resultado', $data)]);
                }

                if (empty($generosSeleccionados)) {
                    $data['titulo'] = "Error";
                    $data['contenido'] = '
                    <div class="col-lg-12">
                        <p class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            Debes seleccionar al menos un género.
                        </p>
                    </div>';
                    return view('layout', ['contenido' => view('resultado', $data)]);
                }

                // ✅ NUEVO: Tomar el primer autor/género para compatibilidad temporal
                // (hasta que quites las columnas antiguas de la tabla libros)
                $primerAutor = (int)$autoresSeleccionados[0];
                $primerGenero = (int)$generosSeleccionados[0];

                $datos = [
                    'codigoISBN'      => $this->request->getPost('codigoISBN'),
                    'titulo'          => $this->request->getPost('titulo'),
                    'autor'           => $primerAutor,        // ← Temporal, para compatibilidad
                    'editorial'       => $this->request->getPost('editorial'),
                    'genero'          => $primerGenero,       // ← Temporal, para compatibilidad
                    'n_paginas'       => $this->request->getPost('n_paginas'),
                    'ano_edicio'      => $this->request->getPost('ano_edicio'),
                    'resena'          => $this->request->getPost('resena'),
                    'fecha_alta'      => date('Y-m-d'),
                    'id_usuario'      => $idUsuario,
                    'id_biblioteca'   => $idBiblioteca,
                ];

                // MANEJO DE PORTADA (igual que antes)
                $portadaUrl = $this->request->getPost('portada_url');
                $portadaFile = $this->request->getFile('portada_file');

                if ($portadaFile && $portadaFile->isValid() && !$portadaFile->hasMoved()) {
                    $uploadPath = FCPATH . 'uploads/portadas/';
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    $newName = $portadaFile->getRandomName();
                    if ($portadaFile->move($uploadPath, $newName)) {
                        $datos['portada'] = $newName;
                    }
                } elseif (!empty($portadaUrl) && str_starts_with($portadaUrl, 'http')) {
                    $datos['portada'] = $portadaUrl;
                } else {
                    $datos['portada'] = null;
                }

                // Verificar duplicado por ISBN (si tiene) o título, dentro de la MISMA biblioteca
                $db = \Config\Database::connect();
                $existe = $db->table('biblio2_libros')
                    ->where('id_biblioteca', $idBiblioteca)
                    ->groupStart()
                    ->where('titulo', $datos['titulo'])
                    ->orGroupStart()
                    ->where('codigoISBN IS NOT NULL')
                    ->where('codigoISBN !=', '')
                    ->where('codigoISBN', $datos['codigoISBN'])
                    ->groupEnd()
                    ->groupEnd()
                    ->countAllResults() > 0;

                log_message('debug', 'Biblioteca: ' . $idBiblioteca);
                log_message('debug', 'ISBN: ' . $datos['codigoISBN']);
                log_message('debug', 'Título: ' . $datos['titulo']);
                log_message('debug', 'Existe: ' . ($existe ? 'SI' : 'NO'));

                if ($existe) {
                    $data['mode'] = "add";
                    $data['titulo'] = "Libro duplicado";
                    $data['contenido'] = '
                <div class="col-lg-12">
                    <p class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        Ya existe un libro con ese ISBN o título.
                    </p>
                </div>
                <div class="col-lg-12">
                    <a class="btn btn-primary" href="' . base_url('altas/nuevo/libro') . '">Volver</a>
                </div>';
                    return view('layout', ['contenido' => view('resultado', $data)]);
                }

                // ✅ NUEVO: Transacción para guardar libro + relaciones
                $db = \Config\Database::connect();
                $db->transStart();

                // Guardar libro (usa tu modelo actual)
                $retorno = $this->altasModel->addLibro($datos);
                $idLibro = $retorno ? $db->insertID() : null;

                if ($retorno && $idLibro) {
                    // Guardar autores en tabla pivote
                    $autoresData = [];
                    foreach ($autoresSeleccionados as $idAutor) {
                        $autoresData[] = [
                            'id_libro' => $idLibro,
                            'id_autor' => (int)$idAutor
                        ];
                    }
                    $db->table('biblio2_libros_autores')->insertBatch($autoresData);

                    // Guardar géneros en tabla pivote
                    $generosData = [];
                    foreach ($generosSeleccionados as $idGenero) {
                        $generosData[] = [
                            'id_libro' => $idLibro,
                            'id_genero' => (int)$idGenero
                        ];
                    }
                    $db->table('biblio2_libros_generos')->insertBatch($generosData);
                }

                $db->transComplete();

                if ($db->transStatus() === false) {
                    $data['contenido'] = '
                    <div class="col-lg-12">
                        <p class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            Error al guardar el libro. Intenta nuevamente.
                        </p>
                    </div>';
                    return view('layout', ['contenido' => view('resultado', $data)]);
                }

                // Respuesta
                $tituloLibro = $this->request->getPost('titulo');
                if ($retorno) {
                    $data['contenido'] = '
                    <div class="col-lg-12">
                        <p class="alert alert-success">
                            <i class="fa fa-info-circle"></i>
                            El libro "<strong>' . $tituloLibro . '</strong>" se ha añadido correctamente
                        </p>
                    </div>
                    <div class="col-lg-12">
                        <a class="btn btn-primary" href="' . base_url('altas/nuevo/libro') . '">Dar de alta otro libro</a>
                    </div>';
                } else {
                    $data['contenido'] = '
                    <div class="col-lg-12">
                        <p class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            Error al guardar el libro. Intenta nuevamente.
                        </p>
                    </div>';
                }
                return view('layout', ['contenido' => view('resultado', $data)]);
                break;
            case 'autor':
                $nombre = $this->request->getPost('nombreautor');
                $existe = $this->altasModel->existeRegistro('autores', ['nombreautor' => $nombre]);

                if ($existe) {
                    $message = "Ya existe un autor con el nombre \"{$nombre}\"";

                    // Si es AJAX (modal), devolver JSON
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => 'false',
                            'message' => $message
                        ]);
                    }

                    // Si no es AJAX (página directa), usar sesión flash
                    session()->setFlashdata('toast', [
                        'type' => 'error',
                        'message' => $message
                    ]);
                    return redirect()->back();
                }

                $retorno = $this->altasModel->addAutor();
                $nuevoId = $retorno ? $retorno['id'] : null;
                $message = $retorno
                    ? "El autor \"{$nombre}\" se ha añadido correctamente"
                    : "No se pudo añadir el autor";

                // Si es AJAX (modal), devolver JSON
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success'  => $retorno ? 'true' : 'false',
                        'id'       => $nuevoId,
                        'message'  => $message
                    ]);
                }

                // Si no es AJAX (página directa), usar sesión flash
                session()->setFlashdata('toast', [
                    'type' => $retorno ? 'success' : 'error',
                    'message' => $message
                ]);
                return redirect()->to('altas/nuevo/autor');
                break;

            // --- EDITORIAL ---
            case 'editorial':
                $nombre = $this->request->getPost('nombreeditorial');
                $existe = $this->altasModel->existeRegistro('editoriales', ['nombreeditorial' => $nombre]);

                if ($existe) {
                    $message = "Ya existe una editorial con el nombre \"{$nombre}\"";

                    // Si es AJAX (modal), devolver JSON
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => 'false',
                            'message' => $message
                        ]);
                    }

                    // Si no es AJAX (página directa), usar sesión flash
                    session()->setFlashdata('toast', [
                        'type' => 'error',
                        'message' => $message
                    ]);
                    return redirect()->back();
                }

                $retorno = $this->altasModel->addGenero();
                $nuevoId = $retorno ? $retorno['id'] : null;
                $message = $retorno
                    ? "El género \"{$nombre}\" se ha añadido correctamente"
                    : "No se pudo añadir el género";

                // Si es AJAX (modal), devolver JSON
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success'  => $retorno ? 'true' : 'false',
                        'id'       => $nuevoId,
                        'message'  => $message
                    ]);
                }

                // Si no es AJAX (página directa), usar sesión flash
                session()->setFlashdata('toast', [
                    'type' => $retorno ? 'success' : 'error',
                    'message' => $message
                ]);
                return redirect()->to('altas/nuevo/editorial');
                break;
            // --- GÉNERO ---
            case 'genero':
                $nombre = $this->request->getPost('nombregenero');
                $existe = $this->altasModel->existeRegistro('generos', ['genero' => $nombre]);


                if ($existe) {
                    $message = "Ya existe un género con el nombre \"{$nombre}\"";

                    // Si es AJAX (modal), devolver JSON
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => 'false',
                            'message' => $message
                        ]);
                    }

                    // Si no es AJAX (página directa), usar sesión flash
                    session()->setFlashdata('toast', [
                        'type' => 'error',
                        'message' => $message
                    ]);
                    return redirect()->back();
                }

                $retorno = $this->altasModel->addGenero();
                $nuevoId = $retorno ? $retorno['id'] : null;
                $message = $retorno
                    ? "El género \"{$nombre}\" se ha añadido correctamente"
                    : "No se pudo añadir el género";

                // Si es AJAX (modal), devolver JSON
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success'  => $retorno ? 'true' : 'false',
                        'id'       => $nuevoId,
                        'message'  => $message
                    ]);
                }

                // Si no es AJAX (página directa), usar sesión flash
                session()->setFlashdata('toast', [
                    'type' => $retorno ? 'success' : 'error',
                    'message' => $message
                ]);
                return redirect()->to('altas/nuevo/genero');
                break;

            // ... PRÉSTAMOS

            case 'prestamo':
                $tituloLibro = $this->request->getPost('titulo_libro');
                $isbnLibro   = $this->request->getPost('isbn_libro');
                $retorno     = $this->altasModel->addPrestamo();

                // echo $isbnLibro;die();

                if ($this->request->isAJAX()) {
                    // Respuesta JSON para modal
                    return $this->response->setJSON([
                        'status'  => $retorno ? 'ok' : 'error',
                        'message' => $retorno
                            ? "El préstamo del libro \"{$tituloLibro}\" se ha añadido correctamente"
                            : "No se pudo añadir el préstamo",
                    ]);
                } else {
                    // Respuesta HTML normal (vista resultado)
                    $data['titulo'] = "Resultado Alta Préstamo";
                    if ($retorno) {
                        $data['contenido'] = "<div class=\"col-lg-12\">
                            <p class=\"alert alert-success\"><i class=\"fa fa-info-circle\"></i>
                                El préstamo del libro <strong>\"{$tituloLibro}\"</strong> se ha añadido correctamente a la base de datos
                            </p>
                            </div>
                            <div class=\"modal-footer\">
                                <button type=\"button\" class=\"btn btn-default\" onclick=\"javascript:window.location.reload()\" data-dismiss=\"modal\" aria-hidden=\"true\">Cerrar Ventana</button>
                            </div>";
                    } else {
                        $data['contenido'] = "<p class=\"alert alert-danger\">E R R O R!!!</p>";
                    }

                    echo view('resultado', $data);
                }
                break;



            case 'actualiza':
                $id          = $this->request->getPost('id_prestamo');
                $fechaEntrada = $this->request->getPost('fecha_entrada');
                $tituloLibro  = $this->request->getPost('titulo_libro');
                $idLibro      = $this->request->getPost('id_libro');
                $retorno      = false;

                // Solo intentamos actualizar si la fecha no es "0000-00-00"
                if ($fechaEntrada && $fechaEntrada !== '0000-00-00') {
                    // Llamamos al modelo
                    $retorno = $this->altasModel->actualizaPrestamo($id, $idLibro, $fechaEntrada);
                }

                if ($this->request->isAJAX()) {
                    // -------------------
                    // Respuesta AJAX JSON
                    // -------------------
                    return $this->response->setContentType('application/json')
                        ->setJSON([
                            'status'  => $retorno ? 'ok' : 'error',
                            'message' => $retorno
                                ? "El préstamo del libro \"{$tituloLibro}\" se ha actualizado correctamente"
                                : "No se pudo actualizar el préstamo"
                        ]);
                } else {
                    // -------------------
                    // Respuesta HTML normal
                    // -------------------
                    $data['titulo'] = "Resultado Actualiza Préstamo";

                    if ($retorno) {
                        $data['contenido'] = "
                            <div class='col-lg-12'>
                                <p class='alert alert-success'>
                                    <i class='fa fa-info-circle'></i>
                                    El préstamo del libro <strong>\"{$tituloLibro}\"</strong> se ha actualizado correctamente en la base de datos
                                </p>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-default' onclick='javascript:window.location.reload()' data-dismiss='modal' aria-hidden='true'>
                                    Cerrar Ventana
                                </button>
                            </div>";
                    } else {
                        $data['contenido'] = "<p class='alert alert-danger'>E R R O R!!!</p>";
                    }

                    echo view('resultado', $data);
                }
                break;



            default:
                echo "Acción no válida";
        }
    }

    public function guardarAutor()
    {
        // Obtener el nombre del autor desde la petición POST
        $nombreAutor = $this->request->getPost('nombreautor');

        // Validar que el nombre no esté vacío
        if (empty($nombreAutor)) {
            return $this->response->setJSON(['success' => false, 'message' => 'El nombre del autor es obligatorio.']);
        }

        // Guardar el autor en la base de datos y obtener su ID
        $autor = $this->altasModel->addAutor();

        $idAutor = $autor['id'];
        $nombreAutor = $autor['nombreautor'];
        $success_message = $autor['success'] ? "El autor \"{$nombreAutor}\" se ha añadido correctamente" : "No se pudo añadir el autor";

        // Devolver el ID del autor en formato JSON
        return $this->response->setJSON(['success' => true, 'id' => $idAutor, 'nombreautor' => $nombreAutor, 'message' => $success_message]);
    }

    public function guardarPrestamo()
    {
        $request = \Config\Services::request();

        $datos = [
            'id_libro' => $request->getPost('id_libro'),
            'id_usuario' => $request->getPost('id_usuario'),
            'fecha_prestamo' => $request->getPost('fecha_prestamo'),
            'fecha_devolucion' => $request->getPost('fecha_devolucion'),
        ];

        $prestamoModel = new \App\Models\PrestamosModel();

        if ($prestamoModel->insert($datos)) {
            // responder con JSON de éxito
            return $this->response->setJSON([
                'status' => 'ok',
                'message' => 'Préstamo guardado correctamente'
            ]);
        } else {
            // responder con JSON de error
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No se pudo guardar el préstamo'
            ]);
        }
    }

    public function nuevo($tipo = "")
    {
        $idUsuario = session()->get('id_usuario');
        $nivel = session()->get('nivel');
        $idBibliotecaUsuario = session()->get('id_biblioteca');

        $data['usuario'] = $this->usuariosModel->dataUsuario($idUsuario);
        $data['param1'] = 'altas';
        $data['param2'] = 'nuevo';

        switch ($tipo) {
            case 'libro':
                $data['titulo'] = "Alta Libros/CD's";
                $data['autores'] = $this->altasModel->listaAutores();
                $data['editoriales'] = $this->altasModel->listaEditoriales();
                $data['generos'] = $this->altasModel->listaGeneros();

                // Bibliotecas: superadmin ve todas, otros solo la suya
                if ($nivel == 'superadmin') {
                    $data['bibliotecas'] = $this->bibliotecaModel->findAll();
                } else {
                    $data['bibliotecas'] = $this->bibliotecaModel->where('id', $idBibliotecaUsuario)->findAll();
                }

                $data['idBibliotecaUsuario'] = $idBibliotecaUsuario;
                $data['nivel'] = $nivel;
                $data['param3'] = 'libro';
                $data['esSuperadmin'] = $this->rolUsuario === 'superadmin';

                // ✅ NUEVO: Variables para form_libro.php (modo alta)
                $data['libro'] = null;              // null indica modo alta
                $data['autoresLibro'] = [];         // Ningún autor seleccionado
                $data['generosLibro'] = [];         // Ningún género seleccionado
                $data['usuario'] = $this->usuariosModel->dataUsuario($this->session->get('id_usuario'));

                return view('layout', [
                    'contenido' => view('libros/form', $data)
                ]);
                break;

            case 'autor':
                $data['titulo'] = "Alta Autores";
                $data['param3'] = 'autor';

                return view('layout', [
                    'contenido' => view('add_autor', $data)
                ]);
                break;

            case 'editorial':
                $data['titulo'] = "Alta Editoriales";
                $data['param1'] = 'altas';
                $data['param3'] = 'editoriales';
                return view('layout', [
                    'contenido' => view('add_editorial', $data)
                ]);
                break;

            case 'genero':
                $data['titulo'] = "Alta Géneros";
                $data['param3'] = 'generos';
                return view('layout', [
                    'contenido' => view('add_genero', $data)
                ]);
                break;
        }
    }

    public function borrar()
    {
        $id          = $this->request->getPost('id');
        $tabla       = $this->request->getPost('tabla');
        $descripcion = $this->request->getPost('descripcion');

        $tablasPermitidas = ['biblio2_libros', 'biblio2_editoriales', 'biblio2_autores', 'biblio2_generos'];

        if (!in_array($tabla, $tablasPermitidas)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tabla no válida'
            ]);
        }

        $tipo = [
            "biblio2_libros"      => "El libro",
            "biblio2_editoriales" => "La editorial",
            "biblio2_autores"     => "El autor",
            "biblio2_generos"     => "El género"
        ][$tabla] ?? "Elemento";

        $ret = $this->altasModel->borrar($id, $tabla);

        if ($ret) {
            return $this->response->setJSON([
                'status'      => 'ok',
                'message'     => $tipo . ' "' . esc($descripcion) . '" se ha borrado correctamente.',
                'tipo'        => $tipo,
                'descripcion' => $descripcion
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Error al borrar el elemento.'
        ]);
    }
}
