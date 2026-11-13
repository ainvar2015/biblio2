<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UsuariosModel;
use App\Models\ComunesModel;

abstract class BaseController extends Controller
{
    protected $request;
    protected $session;

    protected $usuariosModel;
    protected $comunesModel;
    protected $idBiblioteca;
    protected $rolUsuario;
    protected ?int $userID = null;
    protected string $idioma = 'esp';

    protected $data = [];

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        // Helpers globales
        helper(['url', 'form', 'uri']);

        // Sesión
        $this->session = session();

        if (session()->has('id_usuario')) {
            $this->userID = (int) session('id_usuario');
            $this->idioma = session('idioma') ?? 'esp';
            $this->rolUsuario = session()->get('nivel') ?? 'usuario';
            $this->idBiblioteca = (int) (session()->get('id_biblioteca') ?? 0);
        }

        // Modelos comunes
        $this->usuariosModel = new UsuariosModel();
        $this->comunesModel  = new ComunesModel();

        // Configuración global
        $configura = $this->comunesModel->configura();

        $this->data['settings'] = $configura;
        $this->data['titulo']   = $configura->titulo_pagina ?? '';

        // Usuario conectado (si existe)
        if ($this->session->get('is_logued_in')) {
            $this->data['usuario'] = $this->usuariosModel->dataUsuario(
                $this->session->get('id_usuario')
            );
        }
    }

    /**
     * Verifica que el usuario sea superadmin
     */
    protected function requiereSuperadmin(): void
    {
        if ($this->rolUsuario !== 'superadmin') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Acceso denegado');
        }
    }

    /**
     * Verifica que el usuario sea admin o superior
     */
    protected function requiereAdmin(): void
    {
        if (!in_array($this->rolUsuario, ['adm', 'superadmin'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Acceso denegado');
        }
    }

    private function esSuperadmin(): bool
    {
        return $this->rolUsuario === 'superadmin';
    }

    /**
     * Obtiene el id_biblioteca para filtros:
     * - superadmin: puede recibir ?biblioteca=X o ver todas
     * - admin: solo la suya
     */
    protected function getBibliotecaFiltro(): ?int
    {
        if ($this->rolUsuario === 'superadmin') {
            // Superadmin puede filtrar por query param, o null = todas
            return $this->request->getGet('biblioteca')
                ? (int) $this->request->getGet('biblioteca')
                : null;
        }
        // Admin y usuario normal: solo su biblioteca
        return $this->idBiblioteca;
    }

    /**
     * Verifica que un recurso pertenezca a la biblioteca del admin
     */
    protected function verificarBiblioteca(array $recurso, string $campo = 'id_biblioteca'): void
    {
        if ($this->rolUsuario === 'superadmin') return;

        if (($recurso[$campo] ?? null) != $this->idBiblioteca) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Recurso no encontrado');
        }
    }

    protected function validarSesionBiblioteca(): void
    {
        if ($this->rolUsuario !== 'superadmin' && !$this->idBiblioteca) {
            throw new \Exception('Sesión sin biblioteca asignada');
        }
    }
}
