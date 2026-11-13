<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class BloqueoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Si no está logueado, no aplicar filtro (dejar que AuthFilter maneje esto)
        if (! $session->get('id_usuario')) {
            return;
        }

        $uri = $request->getPath();
        $pantallaBloqueada = $session->get('pantalla_bloqueada');

        // Rutas permitidas estando bloqueado (solo estas pueden accederse)
        $rutasPermitidasBloqueado = [
            'auth/bloquear',
            'auth/lock',
            'auth/verificarBloqueo',
            'auth/pingActividad',
            'login',
            'logout',
            'assets',
        ];

        // Si está bloqueado y quiere ir a una ruta NO permitida
        if ($pantallaBloqueada) {
            // Verificar si la ruta actual está permitida
            $rutaPermitida = false;
            foreach ($rutasPermitidasBloqueado as $ruta) {
                if (strpos($uri, $ruta) !== false) {
                    $rutaPermitida = true;
                    break;
                }
            }

            // Si no es ruta permitida, forzar redirección a bloqueo
            if (! $rutaPermitida) {
                // Limpiar cualquier caché del navegador
                $response = service('response');
                $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
                $response->setHeader('Pragma', 'no-cache');
                $response->setHeader('Expires', 'Mon, 01 Jan 1990 00:00:00 GMT');

                return redirect()->to('auth/bloquear')
                    ->with('error', 'Debes desbloquear la pantalla primero');
            }
        }

        // Si NO está bloqueado pero intenta acceder a la pantalla de bloqueo
        if (! $pantallaBloqueada && $uri === 'auth/bloquear') {
            return redirect()->to('prestamos'); // o dashboard
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Añadir headers anti-caché a todas las respuestas de páginas protegidas
        if (! $request->isAJAX() && $response->getHeader('Content-Type')?->getValue() === 'text/html') {
            $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
            $response->setHeader('Pragma', 'no-cache');
            $response->setHeader('Expires', 'Mon, 01 Jan 1990 00:00:00 GMT');
        }
    }
}
