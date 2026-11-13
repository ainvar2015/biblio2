<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Verificar si hay sesión activa
        if (!$session->get('id_usuario')) {
            return redirect()->to('login')->with('error', 'Debes iniciar sesión');
        }
        
        // Verificar si es administrador
        if ($session->get('nivel') !== 'adm') {
            // Si no es admin, redirigir a su perfil
            return redirect()->to('')->with('error', 'No tienes permisos para acceder a esta sección');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se necesita nada después
    }
}