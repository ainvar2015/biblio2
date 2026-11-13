<?php
// test_cors.php
header('Content-Type: application/json');

// Intentar usar header() de Apache (solo funciona si mod_headers está activo)
// Pero como no podemos saberlo directamente, hacemos una prueba de respuesta

$response = [
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'desconocido',
    'php_version' => phpversion(),
    'headers_sent' => headers_list(),
    'mod_headers_check' => function_exists('apache_get_modules') 
        ? (in_array('mod_headers', apache_get_modules()) ? 'INSTALADO' : 'NO INSTALADO')
        : 'NO VERIFICABLE (función no disponible)',
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>