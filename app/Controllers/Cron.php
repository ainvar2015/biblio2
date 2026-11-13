<?php

namespace App\Controllers;

use App\Services\EmailService;

class Cron extends BaseController
{
    protected $emailService;

    public function __construct()
    {
        $this->emailService = new EmailService();
    }

    /**
     * Enviar recordatorios automáticos diarios
     * 
     * Ejecutar manualmente: php spark cron:recordatorios
     * Crontab (cada día a las 9:00): 0 9 * * * cd /ruta/biblio && php spark cron:recordatorios
     */
    public function recordatorios()
    {
        $db = \Config\Database::connect();
        
        echo "=== INICIANDO ENVÍO DE RECORDATORIOS ===\n";
        echo "Fecha: " . date('Y-m-d H:i:s') . "\n\n";

        // Buscar préstamos que necesitan recordatorio:
        // 1. Fecha de devolución mañana (recordatorio preventivo)
        // 2. Fecha de devolución hoy (último día)
        // 3. Fecha de devolución pasada (retraso)
        
        $hoy = date('Y-m-d');
        $manana = date('Y-m-d', strtotime('+1 day'));
        
        $prestamos = $db->table('biblio2_prestamos p')
                        ->select('p.*, l.titulo as titulo_libro')
                        ->where('p.fecha_entrada IS NULL')
                        ->where('p.fecha_devolucion IS NOT NULL')
                        ->where('p.fecha_devolucion <=', $manana)
                        ->join('biblio2_libros l', 'l.id = p.id_libro')
                        ->get()
                        ->getResult();

        $enviados = 0;
        $fallidos = 0;
        $saltados = 0;

        foreach ($prestamos as $p) {
            echo "Procesando préstamo #{$p->id} - {$p->titulo_libro}...\n";

            // Verificar si ya se envió recordatorio en las últimas 24h
            $yaEnviado = $db->table('biblio2_recordatorios')
                           ->where('id_prestamo', $p->id)
                           ->where('fecha_envio >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                           ->countAllResults();

            if ($yaEnviado) {
                echo "  → Saltado (ya enviado hoy)\n";
                $saltados++;
                continue;
            }

            // Preparar destinatario
            $destinatario = [];
            
            if ($p->tipo_prestatario === 'socio') {
                $usuario = $db->table('biblio2_usuarios')
                             ->where('id', $p->id_usuario)
                             ->get()
                             ->getRow();
                
                if (!$usuario || empty($usuario->email)) {
                    echo "  → Fallido: socio sin email\n";
                    $fallidos++;
                    continue;
                }
                
                $destinatario = [
                    'nombre' => $usuario->nombre . ' ' . ($usuario->apellidos ?? ''),
                    'email' => $usuario->email
                ];
            } else {
                if (empty($p->email_externo)) {
                    echo "  → Fallido: externo sin email\n";
                    $fallidos++;
                    continue;
                }
                
                $destinatario = [
                    'nombre' => $p->nombre_externo,
                    'email' => $p->email_externo
                ];
            }

            // Calcular días
            $fechaDev = new \DateTime($p->fecha_devolucion);
            $hoyDt = new \DateTime($hoy);
            $diasRetraso = $hoyDt > $fechaDev ? $hoyDt->diff($fechaDev)->days : 0;

            // Preparar mensaje según urgencia
            if ($diasRetraso > 0) {
                $mensaje = "Su préstamo tiene {$diasRetraso} días de retraso. Por favor, devuelva el libro lo antes posible para evitar sanciones.";
            } elseif ($p->fecha_devolucion === $hoy) {
                $mensaje = "Hoy es el último día para devolver el libro. Recuerde pasar por la biblioteca.";
            } else {
                $mensaje = "Le recordamos que mañana vence el plazo de devolución. Planifique su visita a la biblioteca.";
            }

            $datosEmail = [
                'titulo_libro' => $p->titulo_libro,
                'isbn' => $p->isbn ?? '',
                'fecha_prestamo' => date('d/m/Y', strtotime($p->fecha_prestamo)),
                'fecha_devolucion' => date('d/m/Y', strtotime($p->fecha_devolucion)),
                'dias_retraso' => $diasRetraso,
                'dias_restantes' => 0,
                'mensaje_personalizado' => $mensaje
            ];

            // Enviar
            $resultado = $this->emailService->enviarRecordatorio($destinatario, $datosEmail);

            if ($resultado['success']) {
                $db->table('biblio2_recordatorios')->insert([
                    'id_prestamo' => $p->id,
                    'tipo' => 'email',
                    'fecha_envio' => date('Y-m-d H:i:s'),
                    'estado' => 'enviado',
                    'mensaje' => 'Automático: ' . substr($mensaje, 0, 200)
                ]);
                
                echo "  → ✓ Enviado a {$destinatario['email']}\n";
                $enviados++;
            } else {
                $db->table('biblio2_recordatorios')->insert([
                    'id_prestamo' => $p->id,
                    'tipo' => 'email',
                    'fecha_envio' => date('Y-m-d H:i:s'),
                    'estado' => 'fallido',
                    'mensaje' => $resultado['message']
                ]);
                
                echo "  → ✗ Fallido: {$resultado['message']}\n";
                $fallidos++;
            }

            // Pequeña pausa para no saturar el servidor SMTP
            sleep(1);
        }

        echo "\n=== RESUMEN ===\n";
        echo "Total procesados: " . count($prestamos) . "\n";
        echo "Enviados: {$enviados}\n";
        echo "Fallidos: {$fallidos}\n";
        echo "Saltados: {$saltados}\n";
        echo "===============\n";
    }
}