<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    // Rutas permitidas para usuarios invitados (solo lectura)
    private array $rutasInvitado = [
        'admin/invitado',
        'libros/detalle',
        'libros/catalogo',
        // Añade aquí más rutas de solo lectura si las necesitas
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $path = $request->getUri()->getPath();
        $isApi = str_contains($path, 'api/');

        if (!$session->get('is_logued_in')) {
            if ($isApi) {
                return service('response')
                    ->setHeader('Access-Control-Allow-Origin', '*')
                    ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                    ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                    ->setStatusCode(401)
                    ->setJSON(['error' => 'No autenticado']);
            }
            return redirect()->to(base_url('login'));
        }

        // Verificar nivel de acceso (solo para web)
        if (!$isApi) {
            $userLevel = $session->get('nivel');
            $currentPath = uri_string();

            // Si es invitado, verificar si la ruta está permitida
            if ($userLevel === 'inv') {
                $permitido = false;
                foreach ($this->rutasInvitado as $ruta) {
                    if ($currentPath === $ruta || str_starts_with($currentPath, $ruta)) {
                        $permitido = true;
                        break;
                    }
                }

                if (!$permitido) {
                    return redirect()->to(base_url('admin/invitado'))
                        ->with('error', 'No tienes permisos para acceder a esta sección');
                }
            }
        }

        // Para API: añadir id_usuario a la request
        $request->usuario_id = $session->get('id_usuario');
        $request->usuario_nivel = $session->get('nivel');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No necesitamos nada aquí
    }
}