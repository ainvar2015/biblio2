<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================
// RUTAS PÚBLICAS (sin autenticación)
// ============================================
$routes->get('login', 'Login::index');
$routes->post('authenticate', 'Login::authenticate');
$routes->get('logout', 'Login::logout');

// ============================================
// API PÚBLICA (app Android - sin auth)
// ============================================
$routes->group('api', ['namespace' => 'App\Controllers'], function ($routes) {
    // Libros
    $routes->get('libros', 'Books::index');
    $routes->get('libros/(:num)', 'Books::show/$1');
    $routes->get('libros/search', 'Books::search');
    
    // Autores
    $routes->get('autores', 'Authors::index');
    $routes->get('autores/(:num)/libros', 'Authors::books/$1');
    $routes->get('autores/search', 'Authors::search');
});

// ============================================
// API PROTEGIDA (app Android - con auth)
// ============================================
$routes->group('api', ['filter' => 'auth'], function ($routes) {
    // Usuario
    $routes->get('usuario/me', 'Login::me');
    
    // Favoritos
    $routes->get('usuario/favoritos', 'Favoritos::index');
    $routes->post('usuario/favoritos', 'Favoritos::create');
    $routes->delete('usuario/favoritos/(:num)', 'Favoritos::delete/$1');
    
    // Reservas
    $routes->get('usuario/reservas', 'ReservasApp::index');
    $routes->post('usuario/reservas', 'ReservasApp::create');
    $routes->put('usuario/reservas/(:num)/cancelar', 'ReservasApp::cancel/$1');
    
    // Login/Logout API
    $routes->post('login', 'Login::apiLogin');
    $routes->post('logout', 'Login::apiLogout');
});

// ============================================
// RUTAS DE AUTENTICACIÓN Y BLOQUEO
// ============================================
$routes->group('auth', ['filter' => 'auth'], function ($routes) {
    $routes->get('bloquear', 'Auth::bloquear');
    $routes->get('lock', 'Auth::lock');
    $routes->post('verificarBloqueo', 'Auth::verificarBloqueo');
    $routes->post('pingActividad', 'Auth::pingActividad');
    // verificarInactividad ELIMINADO - ya no se usa con actividad.js
});

// ============================================
// RUTAS PROTEGIDAS (auth + bloqueo)
// ============================================
$routes->group('', ['filter' => 'auth'], function ($routes) {
    
    // --- ADMINISTRACIÓN ---
    $routes->get('/', 'Admin::index');
    $routes->get('dashboard', 'Admin::index');
    $routes->get('admin', 'Admin::index');
    $routes->get('admin/invitado', 'Admin::verLibrosInv');
    $routes->get('admin/calendar', 'Admin::calendar');
    $routes->get('admin/settings', 'Admin::settings');
    $routes->get('admin/verLibro/(:num)', 'Admin::verLibro/$1');
    $routes->post('admin/update/settings', 'Admin::update/settings');
    
    // --- BIBLIOTECAS ---
    $routes->get('bibliotecas', 'Biblioteca::index');
    $routes->get('bibliotecas/nuevo', 'Biblioteca::nuevo');
    $routes->post('bibliotecas/guardar', 'Biblioteca::guardar');
    $routes->get('bibliotecas/editar/(:num)', 'Biblioteca::editar/$1');
    $routes->post('bibliotecas/actualizar/(:num)', 'Biblioteca::actualizar/$1');
    $routes->get('bibliotecas/eliminar/(:num)', 'Biblioteca::eliminar/$1');
    $routes->get('bibliotecas/seleccionar/(:num)', 'Biblioteca::seleccionar/$1');
    
    // --- ALTAS (libros, autores, editoriales, géneros) ---
    $routes->get('altas', 'Altas::index');
    $routes->get('altas/nuevo/(:any)', 'Altas::nuevo/$1');
    $routes->post('altas/check', 'Altas::checkLibro');
    $routes->post('altas/checkAutor', 'Altas::checkAutor');
    $routes->post('altas/checkEditorial', 'Altas::checkEditorial');
    $routes->post('altas/checkGenero', 'Altas::checkGenero');
    $routes->get('altas/check/(:segment)', 'Altas::checkLibro/$1');
    $routes->post('altas/guardar/(:any)', 'Altas::guardar/$1');
    $routes->post('altas/guardarAutor', 'Altas::guardarAutor');
    $routes->get('altas/guardarAutor', 'Altas::guardarAutor');
    $routes->post('altas/borrar/genero/(:num)', 'Altas::borrar/genero/$1');
    $routes->post('altas/borrar', 'Altas::borrar');
    
    // --- EDICIÓN / CATÁLOGO (Editar.php maneja todo) ---
    $routes->get('verLibros', 'Editar::catalogo');
    $routes->get('editar/listaLibros', 'Editar::listaLibros');
    $routes->get('editar/listaAutores', 'Editar::listaAutores');
    $routes->get('editar/listaEditoriales', 'Editar::listaEditoriales');
    $routes->get('editar/listaGeneros', 'Editar::listaGeneros');
    $routes->get('editar/listaPrestamos', 'Editar::listaPrestamos');
    $routes->get('editar/listaPrestamos/todos', 'Editar::listaPrestamos/todos');
    $routes->get('editar/libro/(:num)', 'Editar::libro/$1');
    $routes->get('editar/autor/(:num)', 'Editar::autor/$1');
    $routes->get('editar/editorial/(:num)', 'Editar::editorial/$1');
    $routes->get('editar/genero/(:num)', 'Editar::genero/$1');
    $routes->get('editar/formulario/(:num)', 'Editar::formulario/$1');
    $routes->post('editar/update/libro/(:num)', 'Editar::update/libro/$1');
    $routes->post('editar/update/autores/(:num)', 'Editar::update/autores/$1');
    $routes->post('editar/update/editorial/(:num)', 'Editar::update/editorial/$1');
    $routes->post('editar/update/genero/(:num)', 'Editar::update/genero/$1');
    
    // Detalle, préstamo y devolución AJAX (Editar.php)
    $routes->get('libros/detalle', 'Editar::detalleAjax');
    $routes->post('libros/prestar', 'Editar::prestarAjax');
    $routes->post('libros/devolver', 'Editar::devolverAjax');
    
    // --- RESERVAS ---
    $routes->get('reservas', 'Reservas::index');
    $routes->get('reservas/nuevo', 'Reservas::nuevo');
    $routes->post('reservas/guardar', 'Reservas::guardar');
    $routes->get('reservas/ver/(:num)', 'Reservas::ver/$1');
    $routes->get('reservas/editar/(:num)', 'Reservas::editar/$1');
    $routes->post('reservas/actualizar/(:num)', 'Reservas::actualizar/$1');
    $routes->get('reservas/convertirAPrestamo/(:num)', 'Reservas::convertirAPrestamo/$1');
    $routes->get('reservas/devolver/(:num)', 'Reservas::devolver/$1');
    $routes->get('reservas/cancelar/(:num)', 'Reservas::cancelar/$1');
    $routes->get('reservas/eliminar/(:num)', 'Reservas::eliminar/$1');
    $routes->get('reservas/enviarRecordatorio/(:num)', 'Reservas::enviarRecordatorio/$1');
    
    // --- EVENTOS ---
    $routes->get('eventos', 'Eventos::index');
    $routes->get('eventos/listar', 'Eventos::listar');
    $routes->post('eventos/crear', 'Eventos::crear');
    $routes->post('eventos/actualizar/(:num)', 'Eventos::actualizar/$1');
    $routes->post('eventos/eliminar/(:num)', 'Eventos::eliminar/$1');
    $routes->get('eventos/proximos', 'Eventos::eventosProximos');
    
    // --- API INTERNA (DataTables, Select2, etc.) ---
    $routes->post('api/libros/datatables', 'Api::librosDatatables');
    $routes->get('api/usuarios/select', 'Api::usuariosSelect');
    $routes->get('api/usuarios/buscar', 'Api::usuariosBuscar');
    $routes->post('api/prestamos/guardar', 'Api::guardarPrestamo');
    $routes->post('api/devolverPrestamo', 'Api::devolverPrestamo');
    $routes->get('api/detallesPrestamo/(:num)', 'Api::detallesPrestamo/$1');
    $routes->get('api/librosDisponibles', 'Api::librosDisponibles');
    $routes->get('api/sociosActivos', 'Api::sociosActivos');
    $routes->get('api/prestamos/historial/(:num)', 'Api::historialPrestamos/$1');
    $routes->post('api/recordatorios/enviar', 'Api::enviarRecordatorio');
    
    // --- PRÉSTAMOS ---
    $routes->get('prestamos', 'Prestamos::index');
    $routes->get('prestamos/detallesLibro/(:num)', 'Prestamos::detallesLibro/$1');
    $routes->get('prestamos/estadisticas/(:num)', 'Prestamos::estadisticasLibro/$1');
    $routes->get('prestamos/nuevo', 'Prestamos::nuevo');
    $routes->post('prestamos/devolver/(:num)', 'Prestamos::devolver/$1');
    $routes->post('prestamos/renovar/(:num)', 'Prestamos::renovar/$1');
    $routes->post('prestamos/eliminar/(:num)', 'Prestamos::eliminar/$1');
    $routes->get('detalles/(:num)', 'Prestamos::detalles/$1');
    $routes->post('prestamos/guardar', 'Prestamos::guardar');
    $routes->get('prestamos/exportar', 'Prestamos::exportar');
    $routes->get('prestamos/historial', 'Prestamos::historial');
    $routes->get('prestamos/historialLibro/(:num)', 'Prestamos::historialPrestamos/$1');
    
    // --- HISTORIAL ---
    $routes->get('historial/usuario/(:segment)', 'HistorialController::porUsuario/$1');
    $routes->get('historial/externo/(:segment)', 'HistorialController::porExterno/$1');
    $routes->get('usuarios/buscar', 'HistorialController::buscar');
    
    // --- USUARIOS ---
    $routes->get('perfil', 'Usuarios::perfil');
    
    $routes->group('usuarios', function ($routes) {
        $routes->get('', 'Usuarios::index');
        $routes->get('nuevo', 'Usuarios::nuevo');
        $routes->get('editar', 'Usuarios::editar');
        $routes->get('editar/(:num)', 'Usuarios::editar/$1');
        $routes->post('update/(:num)', 'Usuarios::update/$1');
        $routes->post('update/perfil/(:num)', 'Usuarios::update/perfil/$1');
        $routes->post('add_usuario', 'Usuarios::add_usuario');
        $routes->post('editar', 'Usuarios::editar');
        $routes->get('borrar/(:num)', 'Usuarios::borrar/$1');
        $routes->post('borrar/(:num)', 'Usuarios::borrar/$1');
        $routes->post('actualizar-pin', 'Usuarios::actualizarPin');
        $routes->post('toggleActivo/(:num)', 'Usuarios::toggleActivo/$1');
        $routes->get('ver/(:num)', 'Usuarios::ver/$1');
    });
});