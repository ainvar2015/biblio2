<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AltasModel;
use App\Models\EditarModel;
use App\Models\LibroModel;
use App\Models\BibliotecaModel;
use App\Models\PrestamosModel;

class Editar extends BaseController
{
    protected $altasModel;
    protected $editarModel;
    protected $libroModel;
    protected $bibliotecaModel;
    protected $prestamosModel;
    protected $db;

    public function __construct()
    {
        helper(['url', 'form']);

        $this->altasModel    = new AltasModel();
        $this->editarModel   = new EditarModel();
        $this->libroModel = new LibroModel();
        $this->bibliotecaModel = new BibliotecaModel();
        $this->prestamosModel = new PrestamosModel();
        $this->db = \Config\Database::connect();
    }

    /* ========= HOME ========= */
    public function index()
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

    /* ========= UPDATE ========= */
    public function update($tabla, $id)
    {
        switch ($tabla) {
            case 'autor':
            case 'autores':
                $nombre = 'Autor';
                $nombrePlural = 'Autores';
                break;
                break;
            case 'editorial':
            case 'editoriales':
                $nombre = 'Editorial';
                $nombrePlural = 'Editoriales';
                break;
            case 'genero':
            case 'generos':
                $nombre = 'Genero';
                $nombrePlural = 'Generos';
                break;
            case 'libro':
            case 'libros':
                $nombre = 'Libro';
                $nombrePlural = 'Libros';
                return $this->updateLibro($id);
                break;
            default:
                throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $varEdit = 'edit' . $nombre;

        $data = [
            'mode'    => 'edit',
            'id'      => $id,
            'titulo'  => 'Resultado Editar ' . $nombre,
            'usuario' => $this->usuariosModel->dataUsuario(
                $this->session->get('id_usuario')
            ),
            'retorno' => $this->editarModel->$varEdit($id),
        ];

        if ($data['retorno']) {
            $nombre1 = strtolower($nombre);
            $data['contenido'] = '
                <div class="alert alert-success">
                    <i class="fa fa-thumbs-up fa-lg"></i>
                    El ' . $nombre . ' ha sido modificado correctamente.
                </div>
                <p>
                    <a class="btn btn-primary" href="' . base_url("editar/lista" . $nombrePlural . "/" . $id) . '">
                        Volver a editar este ' . $nombre1 . '
                    </a>
                    <a class="btn btn-secondary" href="' . base_url("editar/lista" . $nombrePlural) . '">
                        Ir al listado de ' . $nombrePlural . '
                    </a>
                </p>';
        }

        return view('layout', [
            'contenido' => view('resultado', $data)
        ]);
    }

    private function updateLibro($id)
    {
        $db = \Config\Database::connect();

        // Datos básicos (sin autor ni género, ahora van en tablas pivote)
        $datos = [
            'titulo'        => $this->request->getPost('titulo'),
            'editorial'     => $this->request->getPost('editorial'),
            'n_paginas'     => $this->request->getPost('n_paginas'),
            'ano_edicio'    => $this->request->getPost('ano_edicio'),
            'resena'        => $this->request->getPost('resena'),
        ];

        // ✅ NUEVO: Obtener autores y géneros múltiples
        $autores = $this->request->getPost('autores');
        $generos = $this->request->getPost('generos');

        // Mantener compatibilidad temporal: primer autor/género en campos antiguos
        if (!empty($autores) && is_array($autores)) {
            $datos['autor'] = (int)$autores[0];
        }
        if (!empty($generos) && is_array($generos)) {
            $datos['genero'] = (int)$generos[0];
        }

        // MANEJO DE PORTADA (igual que antes)
        $portadaUrl = $this->request->getPost('portada_url');
        $portadaFile = $this->request->getFile('portada_file');

        if ($portadaFile && $portadaFile->isValid() && !$portadaFile->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/portadas';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $newName = $portadaFile->getRandomName();
            if ($portadaFile->move($uploadPath, $newName)) {
                $libroActual = $this->editarModel->dataLibro($id);
                if (!empty($libroActual->portada_url) && !str_starts_with($libroActual->portada_url, 'http')) {
                    $rutaAnterior = FCPATH . 'uploads/portadas/' . $libroActual->portada_url;
                    if (file_exists($rutaAnterior)) {
                        unlink($rutaAnterior);
                    }
                }
                $datos['portada'] = $newName;
            }
        } elseif ($portadaUrl === '' || $portadaUrl === null) {
            $libroActual = $this->editarModel->dataLibro($id);
            if (!empty($libroActual->portada_url) && !str_starts_with($libroActual->portada_url, 'http')) {
                $rutaAnterior = FCPATH . 'uploads/portadas/' . $libroActual->portada_url;
                if (file_exists($rutaAnterior)) {
                    unlink($rutaAnterior);
                }
            }
            $datos['portada'] = null;
        } elseif (!empty($portadaUrl) && str_starts_with($portadaUrl, 'http')) {
            $datos['portada'] = $portadaUrl;
        }

        // ✅ NUEVO: Transacción para actualizar libro + relaciones
        $db->transStart();

        // 1. Actualizar libro en tabla principal
        $actualizado = $this->editarModel->editLibro($id, $datos);

        // 2. Actualizar autores en tabla pivote
        if (!empty($autores) && is_array($autores)) {
            // Eliminar actuales
            $db->table('biblio2_libros_autores')->where('id_libro', $id)->delete();
            // Insertar nuevos
            $autoresData = [];
            foreach ($autores as $idAutor) {
                $autoresData[] = [
                    'id_libro' => $id,
                    'id_autor' => (int)$idAutor
                ];
            }
            $db->table('biblio2_libros_autores')->insertBatch($autoresData);
        }

        // 3. Actualizar géneros en tabla pivote
        if (!empty($generos) && is_array($generos)) {
            // Eliminar actuales
            $db->table('biblio2_libros_generos')->where('id_libro', $id)->delete();
            // Insertar nuevos
            $generosData = [];
            foreach ($generos as $idGenero) {
                $generosData[] = [
                    'id_libro' => $id,
                    'id_genero' => (int)$idGenero
                ];
            }
            $db->table('biblio2_libros_generos')->insertBatch($generosData);
        }

        $db->transComplete();

        // Verificar resultado
        if ($db->transStatus() === false) {
            $data = [
                'mode'    => 'edit',
                'id'      => $id,
                'titulo'  => 'Resultado Editar Libro',
                'usuario' => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
                'retorno' => false,
                'contenido' => '
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-circle fa-lg"></i>
                    Error al guardar los cambios. Intenta nuevamente.
                </div>'
            ];
            return view('layout', ['contenido' => view('resultado', $data)]);
        }

        // Preparar vista de resultado
        $data = [
            'mode'    => 'edit',
            'id'      => $id,
            'titulo'  => 'Resultado Editar Libro',
            'usuario' => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
            'retorno' => $actualizado,
        ];

        if ($actualizado) {
            $data['contenido'] = '
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up fa-lg"></i>
                El libro ha sido modificado correctamente.
            </div>
            <p>
                <a class="btn btn-primary" href="' . base_url("editar/libro/$id") . '">
                    Volver a editar este libro
                </a>
                <a class="btn btn-secondary" href="' . base_url('editar/listaLibros') . '">
                    Ir al listado
                </a>
            </p>';
        } else {
            $data['contenido'] = '
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle fa-lg"></i>
                No se pudieron guardar los cambios.
            </div>';
        }

        return view('layout', [
            'contenido' => view('resultado', $data)
        ]);
    }

    /* ========= EDITAR LIBRO ========= */
    public function libro($id)
    {
        // $libro = $this->libroModel->find($id);
        $libro = (object) $this->libroModel->where('id', $id)->first();
        if (!$libro) {
            return redirect()->to('editar/listaLibros')->with('error', 'Libro no encontrado');
        }

        // ✅ Cargar autores y géneros seleccionados desde tablas pivote
        $autoresLibro = array_map(
            'intval',
            array_column(
                $this->db->table('biblio2_libros_autores')
                    ->where('id_libro', $id)
                    ->get()
                    ->getResultArray(),
                'id_autor'
            )
        );

        $generosLibro = array_map(
            'intval',
            array_column(
                $this->db->table('biblio2_libros_generos')
                    ->where('id_libro', $id)
                    ->get()
                    ->getResultArray(),
                'id_genero'
            )
        );

        $data = [
            'titulo' => 'Editar Libro',
            'esSuperadmin' => $this->rolUsuario === 'superadmin',
            'idBibliotecaUsuario' => $this->idBiblioteca,
            'bibliotecas' => $this->bibliotecaModel->where('activa', 1)->findAll(),
            'autores' => $this->altasModel->listaAutores(),
            'editoriales' => $this->altasModel->listaEditoriales(),
            'generos' => $this->altasModel->listaGeneros(),
            'libro' => $libro,
            'autoresLibro' => $autoresLibro,      // ✅ Array de IDs
            'generosLibro' => $generosLibro,      // ✅ Array de IDs
            'usuario' => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
        ];

        return view('layout', [
            'contenido' => view('libros/form', $data)
        ]);
    }

    /* EDITAR AUTOR */

    public function autor($id = null)
    {
        $autor = $this->editarModel->dataAutor($id);

        $data = [
            'titulo'        => "Edita Autores",
            'usuario'       => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
            'autor'         => $autor,
        ];

        return view('layout', [
            'contenido' => view('edit_autor', $data)
        ]);
    }

    /* EDITAR EDITORIAL */

    public function editorial($id = null)
    {
        $editorial = $this->editarModel->dataEditorial($id);

        $data = [
            'titulo'        => "Edita Editoriales",
            'usuario'       => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
            'editorial'         => $editorial,
        ];

        return view('layout', [
            'contenido' => view('edit_editorial', $data)
        ]);
    }

    /* EDITAR GÉNERO */

    public function genero($id = null)
    {
        $genero = $this->editarModel->dataGenero($id);

        $data = [
            'titulo'        => "Edita Géneros",
            'usuario'       => $this->usuariosModel->dataUsuario($this->session->get('id_usuario')),
            'genero'         => $genero,
        ];

        return view('layout', [
            'contenido' => view('edit_genero', $data)
        ]);
    }


    /* ========= LISTADOS PÚBLICOS ========= */

    /**
     * Listado de libros en modo solo lectura (sin editar/borrar)
     */
    public function catalogo()
    {
        $session = session();
        $nivel = $session->get('nivel');
        $esSuperadmin = $nivel == 'superadmin';
        $idBibliotecaUsuario = $session->get('id_biblioteca') ?? null;

        $bibliotecaActiva = null;
        $bibliotecas = [];
        $bibliotecaUsuario = null;

        if ($esSuperadmin) {
            $bibliotecas = $this->bibliotecaModel->orderBy('nombre', 'ASC')->findAll();
            $bibGet = $this->request->getGet('biblioteca');
            if ($bibGet !== null && $bibGet !== '') {
                $bibliotecaActiva = (int)$bibGet;
            }
        } else {
            if ($idBibliotecaUsuario) {
                $bibliotecaUsuario = $this->bibliotecaModel->find($idBibliotecaUsuario);
                $bibliotecaActiva = $idBibliotecaUsuario;
            }
        }

        // ✅ CORREGIDO: Consulta base sin joins de autor/género únicos
        $builder = $this->db->table('biblio2_libros l');
        $builder->select('l.*, e.nombreeditorial as editorial');
        $builder->join('biblio2_editoriales e', 'e.id = l.editorial', 'left');

        if ($bibliotecaActiva) {
            $builder->where('l.id_biblioteca', $bibliotecaActiva);
        }

        $libros = $builder->orderBy('l.titulo', 'ASC')->get()->getResultArray();

        // ✅ CORREGIDO: Cargar autores y géneros múltiples para cada libro
        foreach ($libros as &$libro) {
            // Autores
            $autores = $this->db->table('biblio2_libros_autores la')
                ->select('a.id, a.nombreautor as nombre')
                ->join('biblio2_autores a', 'a.id = la.id_autor')
                ->where('la.id_libro', $libro['id'])
                ->get()
                ->getResultArray();
            $libro['autores'] = $autores;
            $libro['autores_texto'] = !empty($autores) ? implode(', ', array_column($autores, 'nombre')) : 'Desconocido';
            $libro['total_autores'] = count($autores);

            // Géneros
            $generos = $this->db->table('biblio2_libros_generos lg')
                ->select('g.id, g.genero as nombre')
                ->join('biblio2_generos g', 'g.id = lg.id_genero')
                ->where('lg.id_libro', $libro['id'])
                ->get()
                ->getResultArray();
            $libro['generos'] = $generos;
            $libro['generos_texto'] = !empty($generos) ? implode(', ', array_column($generos, 'nombre')) : 'Sin género';
            $libro['total_generos'] = count($generos);

            // Estado préstamo
            $libro['prestamo'] = $this->prestamosModel
                ->where('id_libro', $libro['id'])
                ->where('devuelto', '0')
                ->countAllResults() > 0 ? 1 : 0;
        }

        $data = [
            'titulo' => 'Catálogo de libros',
            'libros' => $libros,
            'usuario' => $this->usuariosModel->dataUsuario($this->userID),
            'esSuperadmin' => $esSuperadmin,
            'bibliotecas' => $bibliotecas,
            'bibliotecaActiva' => $bibliotecaActiva,
            'bibliotecaUsuario' => $bibliotecaUsuario,
        ];

        return view('layout', [
            'contenido' => view('libros/listado_solo_lectura', $data)
        ]);
    }

    public function listaLibros()
    {
        $uri = service('uri');
        $segment = $uri->getSegment(1);
        return $this->listadoComun(
            'libros',
            'Listado de Libros',
            [
                'autores'       => $this->editarModel->listaAutores(),
                'editoriales'   => $this->editarModel->listaEditoriales(),
                'generos'       => $this->editarModel->listaGeneros(),
                'esSuperadmin' => $this->rolUsuario === 'superadmin',
                'bibliotecas' => $this->rolUsuario === 'superadmin' ? $this->bibliotecaModel->where('activa', 1)->findAll()
                    : [],
            ]
        );
    }

    public function listaAutores()
    {
        return $this->listadoComun(
            'autores',
            'Listado de Autores',
            [
                'autores' => $this->editarModel->listaAutores(),
            ]
        );
    }

    public function listaEditoriales()
    {
        return $this->listadoComun(
            'editoriales',
            'Listado de Editoriales',
            [
                'editoriales' => $this->editarModel->listaEditoriales(),
            ]
        );
    }

    public function listaGeneros()
    {
        return $this->listadoComun(
            'generos',
            'Listado de Géneros',
            [
                'generos' => $this->editarModel->listaGeneros(),
            ]
        );
    }

    public function listaPrestamos()
    {
        return $this->listadoComun(
            'prestamos',
            'Listado de Préstamos',
            [
                'autores'     => $this->editarModel->listaAutores(),
                'editoriales' => $this->editarModel->listaEditoriales(),
                // 'prestamos' no se pasa - se cargan por AJAX
            ]
        );
    }

    /* ========= MÉTODO COMÚN ========= */
    private function listadoComun(string $tipo, string $titulo, array $extraData = [])
    {
        // Determinar si usa server-side processing
        $usarServerSide = in_array($tipo, ['libros', 'prestamos']);
        // =====================
        // DATOS PARA JS (todos los tipos)
        // =====================
        $jsData = [
            'tipo'           => $tipo,
            'tituloListado'  => $titulo,
            'usarServerSide' => $usarServerSide,
        ];

        // =====================
        // DATOS LOCALES (solo si NO es server-side)
        // =====================
        if (!$usarServerSide) {
            $items = $extraData[$tipo] ?? [];
            $jsData['items'] = $items;
            $jsData['totalRegistros'] = count($items);
        }
        // =====================
        // MAPAS PARA FILTROS Y REFERENCIAS
        // =====================
        $autoresMap = [];
        if (!empty($extraData['autores'])) {
            foreach ($extraData['autores'] as $autor) {
                $autoresMap[$autor->id] = $autor->nombreautor;
            }
            $jsData['autores'] = $extraData['autores']; // Para selects de filtro
        }

        $editorialesMap = [];
        if (!empty($extraData['editoriales'])) {
            foreach ($extraData['editoriales'] as $editorial) {
                $editorialesMap[$editorial->id] = $editorial->nombreeditorial;
            }
            $jsData['editoriales'] = $extraData['editoriales'];
        }

        $generosMap = [];
        if (!empty($extraData['generos'])) {
            foreach ($extraData['generos'] as $genero) {
                $generosMap[$genero->id] = $genero->genero;
            }
            $jsData['generos'] = $extraData['generos'];
        }

        // =====================
        // CONFIGURACIÓN JS ESPECÍFICA
        // =====================
        $jsConfig = [
            'libros'     => ['js' => 'listado-libros.js',     'css' => 'bg-primary'],
            'prestamos'  => ['js' => 'listado-prestamos.js',  'css' => 'bg-info'],
            'autores'    => ['js' => 'listado-generico.js',   'css' => 'bg-secondary'],
            'editoriales' => ['js' => 'listado-generico.js',   'css' => 'bg-secondary'],
            'generos'    => ['js' => 'listado-generico.js',   'css' => 'bg-secondary'],
        ];

        $configTipo = $jsConfig[$tipo] ?? ['js' => 'listado-generico.js', 'css' => 'bg-primary'];

        // =====================
        // DATA FINAL PARA LA VISTA
        // =====================
        $data = [
            'titulo'         => $titulo,
            'tituloListado'  => $titulo,
            'tipo'           => $tipo,
            'singular'       => $this->getSingular($tipo),
            'usarServerSide' => $usarServerSide,
            'headerClass'    => $configTipo['css'],

            // Datos para la vista (items solo si es local)
            'items'          => $usarServerSide ? null : ($extraData[$tipo] ?? []),
            'autoresMap'     => $autoresMap,
            'editorialesMap' => $editorialesMap,
            'generosMap'     => $generosMap,

            // JavaScript específico
            'js_especifico'  => $configTipo['js'],
            'js_data'        => $jsData, // Se pasará al JS vía window.LISTADO_CONFIG

            // Usuario
            'usuario'        => $this->usuariosModel->dataUsuario($this->userID),
        ];

        $data = array_merge($data, $extraData);

        return view('layout', [
            'contenido' => view('listado-libros', $data)
        ]);
    }

    /* ========= HELPERS ========= */

    private function getSingular(string $tipo): string
    {
        $map = [
            'libros'      => 'libro',
            'autores'     => 'autor',
            'editoriales' => 'editorial',
            'generos'     => 'genero',
            'prestamos'   => 'prestamo',
        ];
        return $map[$tipo] ?? substr($tipo, 0, -1);
    }
    /* ========= FORMULARIO PRÉSTAMO/DEVOLUCIÓN ========= */
    public function formulario($idLibro)
    {
        // Traemos el libro
        $libro = $this->editarModel->data($idLibro, 'libros');

        if (!$libro) {
            return "Libro no encontrado";
        }

        // Verificamos si está prestado
        $prestamo = null;
        if ($libro->prestamo > 0) {
            $prestamo = $this->altasModel->getPrestamoByLibro($idLibro);
        }
        // var_dump($prestamo); exit;
        // Preparamos los datos para la vista parcial
        $data = [
            'libro'    => $libro,
            'prestamo' => $prestamo
        ];

        // Cargamos la vista parcial del formulario
        return view('formularioPrestamo', $data);
    }

    public function detalleAjax()
    {
        $idLibro = $this->request->getGet('id');

        if (empty($idLibro)) {
            return $this->response
                ->setJSON(['status' => 'error', 'message' => 'ID de libro no proporcionado'])
                ->setStatusCode(400);
        }

        $db = \Config\Database::connect();

        // ✅ Datos básicos del libro (sin joins de autor/género únicos)
        $libro = $db->table('biblio2_libros l')
            ->select('l.*, e.nombreeditorial as editorial')
            ->join('biblio2_editoriales e', 'e.id = l.editorial', 'left')
            ->where('l.id', $idLibro)
            ->get()
            ->getRowArray();

        if (!$libro) {
            return $this->response
                ->setJSON(['status' => 'error', 'message' => 'Libro no encontrado'])
                ->setStatusCode(404);
        }

        // ✅ Autores múltiples
        $autores = $db->table('biblio2_libros_autores la')
            ->select('a.id, a.nombreautor as nombre')
            ->join('biblio2_autores a', 'a.id = la.id_autor')
            ->where('la.id_libro', $idLibro)
            ->get()
            ->getResultArray();
        $libro['autores'] = $autores;
        $libro['autor'] = !empty($autores) ? implode(', ', array_column($autores, 'nombre')) : 'Desconocido';

        // ✅ Géneros múltiples
        $generos = $db->table('biblio2_libros_generos lg')
            ->select('g.id, g.genero as nombre')
            ->join('biblio2_generos g', 'g.id = lg.id_genero')
            ->where('lg.id_libro', $idLibro)
            ->get()
            ->getResultArray();
        $libro['generos'] = $generos;
        $libro['genero'] = !empty($generos) ? implode(', ', array_column($generos, 'nombre')) : 'Sin género';

        // Total préstamos
        $libro['total_prestamos'] = $db->table('biblio2_prestamos')
            ->where('id_libro', $idLibro)
            ->countAllResults();

        return $this->response->setJSON([
            'status' => 'ok',
            'data' => $libro
        ]);
    }
    // En LibrosController.php
    public function prestarAjax()
    {
        // Validar CSRF
        if (!$this->validateCsrfToken()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Token CSRF inválido'
            ])->setStatusCode(403);
        }

        $datos = $this->request->getPost();
        $idLibro = $datos['id_libro'] ?? null;
        $tipoPrestatario = $datos['tipo_prestatario'] ?? null;

        if (empty($idLibro) || empty($tipoPrestatario)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Faltan datos obligatorios'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();

        // Verificar que el libro no esté ya prestado
        $libro = $db->table('biblio2_libros')->where('id', $idLibro)->get()->getRow();
        if (!$libro) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Libro no encontrado'
            ])->setStatusCode(404);
        }

        if ($libro->prestamo == 1) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Este libro ya está prestado'
            ])->setStatusCode(400);
        }

        // Validar datos según el tipo de prestatario
        if ($tipoPrestatario === 'socio') {
            if (empty($datos['id_usuario'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Selecciona un socio'
                ])->setStatusCode(400);
            }
        } else {
            if (empty($datos['nombre_externo'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'El nombre del prestatario externo es obligatorio'
                ])->setStatusCode(400);
            }
        }

        // Marcar el libro como prestado
        $db->table('biblio2_libros')
            ->where('id', $idLibro)
            ->update(['prestamo' => 1]);

        // Registrar el préstamo en biblio2_prestamos
        $prestamoData = [
            'id_libro' => $idLibro,
            'tipo_prestatario' => $tipoPrestatario,
            'fecha_prestamo' => $datos['fecha_prestamo'] ?? date('Y-m-d'),
            'fecha_devolucion' => $datos['fecha_devolucion'] ?? date('Y-m-d', strtotime('+15 days')),
            'notas' => $datos['notas'] ?? null,
            'fecha_creacion' => date('Y-m-d H:i:s')
        ];

        if ($tipoPrestatario === 'socio') {
            $prestamoData['id_usuario'] = $datos['id_usuario'];
        } else {
            $prestamoData['nombre_externo'] = $datos['nombre_externo'];
            $prestamoData['dni_externo'] = $datos['dni_externo'] ?? null;
            $prestamoData['email_externo'] = $datos['email_externo'] ?? null;
            $prestamoData['telefono_externo'] = $datos['telefono_externo'] ?? null;
        }

        $db->table('biblio2_prestamos')->insert($prestamoData);

        return $this->response->setJSON([
            'status' => 'ok',
            'message' => 'Libro prestado correctamente'
        ]);
    }

    /**
     * Valida el token CSRF de la petición
     * 
     * @return bool True si el token es válido, false en caso contrario
     */
    private function validateCsrfToken(): bool
    {
        $security = \Config\Services::security();

        try {
            $security->verify($this->request);
            return true;
        } catch (\CodeIgniter\Security\Exceptions\SecurityException $e) {
            // Token inválido o expirado
            log_message('warning', 'CSRF validation failed: ' . $e->getMessage());
            return false;
        }
    }

    public function devolverAjax()
    {
        $idLibro = $this->request->getPost('id_libro');
        if (empty($idLibro)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID de libro no proporcionado'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();

        // Verificar que el libro esté prestado
        $libro = $db->table('biblio2_libros')->where('id', $idLibro)->get()->getRow();
        if (!$libro) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Libro no encontrado'
            ])->setStatusCode(404);
        }

        if ($libro->prestamo == 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Este libro no está prestado'
            ])->setStatusCode(400);
        }

        // Marcar el libro como disponible
        $db->table('biblio2_libros')
            ->where('id', $idLibro)
            ->update(['prestamo' => 0]);

        return $this->response->setJSON([
            'status' => 'ok',
            'message' => 'Libro devuelto correctamente'
        ]);
    }
}
