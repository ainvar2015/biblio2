<?php

if (!function_exists('getPortadaUrl')) {
    /**
     * Genera la URL correcta de la portada según 3 supuestos:
     * 1. Archivo local en uploads/portadas/
     * 2. URL externa completa
     * 3. Sin portada → imagen por defecto
     *
     * @param string|null $portada Valor del campo portada en BD
     * @param string $default Ruta de la imagen por defecto
     * @return string URL completa de la portada
     */
    function getPortadaUrl(?string $portada, string $default = 'assets/img/no-portada.gif'): string
    {
        // Supuesto 3: No hay portada
        if (empty($portada)) {
            return base_url($default);
        }

        // Supuesto 2: URL externa (http:// o https://)
        if (str_starts_with($portada, 'http://') || str_starts_with($portada, 'https://')) {
            return $portada;
        }

        // Supuesto 1: Archivo local (solo nombre de archivo)
        return base_url('uploads/portadas/' . $portada);
    }
}