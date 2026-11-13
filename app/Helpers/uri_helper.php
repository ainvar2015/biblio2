<?php

/**
 * Helper de URI para CodeIgniter 4
 * 
 * Proporciona funciones seguras para obtener segmentos de URI
 */

if (!function_exists('uri_segment')) {
    /**
     * Obtener segmento de URI de forma segura
     * 
     * @param int $n Número de segmento (1-based)
     * @param mixed $default Valor por defecto si no existe
     * @return mixed
     */
    function uri_segment(int $n, $default = '')
    {
        $uri = service('request')->getUri();

        return $uri->getTotalSegments() >= $n
            ? $uri->getSegment($n)
            : $default;
    }
}

if (!function_exists('uri_segment_int')) {
    /**
     * Obtener segmento de URI como entero
     * Útil para IDs en URLs
     * 
     * @param int $n Número de segmento
     * @param int $default Valor por defecto
     * @return int
     */
    function uri_segment_int(int $n, int $default = 0): int
    {
        $segment = uri_segment($n, '');
        return is_numeric($segment) ? (int)$segment : $default;
    }
}

if (!function_exists('uri_has_segment')) {
    /**
     * Verificar si existe un segmento de URI
     * 
     * @param int $n Número de segmento
     * @return bool
     */
    function uri_has_segment(int $n): bool
    {
        return service('request')->getUri()->getTotalSegments() >= $n;
    }
}

if (!function_exists('uri_total_segments')) {
    /**
     * Obtener el total de segmentos de la URI
     * 
     * @return int
     */
    function uri_total_segments(): int
    {
        return service('request')->getUri()->getTotalSegments();
    }
}

// ============================================
// FUNCIONES BASE64 URL-SAFE
// ============================================

if (!function_exists('base64UrlEncode')) {
    /**
     * Codifica datos en Base64 seguro para URLs (RFC 4648)
     * Reemplaza + → -, / → _, y quita el padding =
     */
    function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

if (!function_exists('base64UrlDecode')) {
    /**
     * Decodifica datos en Base64 URL-safe
     */
    function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
}
