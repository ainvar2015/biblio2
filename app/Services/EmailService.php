<?php

namespace App\Services;

use Config\Email as EmailConfig;
use CodeIgniter\Email\Email;

class EmailService
{
    protected $email;
    protected $config;

    public function __construct()
    {
        $this->config = new EmailConfig();
        $this->email = new Email($this->config);
    }

    // ============================================================
    // MÉTODOS PARA RESERVAS
    // ============================================================

    /**
     * Enviar confirmación de nueva reserva
     */
    public function enviarConfirmacionReserva(string $destinatario, string $nombreUsuario, array $reserva, array $libro, int $posicionCola): bool
    {
        $this->email->clear();

        $asunto = '✅ Reserva confirmada - Biblioteca';

        $mensaje = $this->generarPlantillaConfirmacionReserva($nombreUsuario, $reserva, $libro, $posicionCola);

        $this->email->setTo($destinatario);
        $this->email->setSubject($asunto);
        $this->email->setMessage($mensaje);

        return $this->email->send();
    }

    /**
     * Enviar notificación de libro disponible (siguiente en cola)
     */
    public function enviarLibroDisponible(string $destinatario, string $nombreUsuario, array $reserva, array $libro): bool
    {
        $this->email->clear();

        $asunto = '📚 ¡Tu libro está disponible! - Biblioteca';

        $mensaje = $this->generarPlantillaLibroDisponible($nombreUsuario, $reserva, $libro);

        $this->email->setTo($destinatario);
        $this->email->setSubject($asunto);
        $this->email->setMessage($mensaje);

        return $this->email->send();
    }

    /**
     * Enviar cancelación de reserva
     */
    public function enviarCancelacionReserva(string $destinatario, string $nombreUsuario, array $reserva, array $libro): bool
    {
        $this->email->clear();

        $asunto = '❌ Reserva cancelada - Biblioteca';

        $mensaje = $this->generarPlantillaCancelacionReserva($nombreUsuario, $reserva, $libro);

        $this->email->setTo($destinatario);
        $this->email->setSubject($asunto);
        $this->email->setMessage($mensaje);

        return $this->email->send();
    }

    // ============================================================
    // MÉTODOS PARA PRÉSTAMOS / RECORDATORIOS (tu código corregido)
    // ============================================================

    /**
     * Enviar recordatorio de devolución
     */
    public function enviarRecordatorio($destinatario, $datos)
    {
        $this->email->clear();

        $this->email->setTo($destinatario['email'], $destinatario['nombre']);

        $asunto = $datos['dias_retraso'] > 0
            ? "URGENTE: Devolución pendiente - {$datos['titulo_libro']}"
            : "Recordatorio: Devolución próxima - {$datos['titulo_libro']}";

        $this->email->setSubject($asunto);

        $mensaje = $this->generarPlantillaRecordatorio($destinatario, $datos);

        $this->email->setMessage($mensaje);

        if ($this->email->send()) {
            return [
                'success' => true,
                'message' => 'Email enviado correctamente'
            ];
        }

        return [
            'success' => false,
            'message' => $this->email->printDebugger(['headers'])
        ];
    }

    /**
     * Enviar email genérico
     */
    public function enviar($para, $asunto, $mensaje, $esHtml = true)
    {
        $this->email->clear();
        $this->email->setTo($para);
        $this->email->setSubject($asunto);

        if ($esHtml) {
            $this->email->setMessage($mensaje);
        } else {
            $this->email->setMessage($mensaje);
        }

        return $this->email->send();
    }

    // ============================================================
    // PLANTILLAS HTML
    // ============================================================

    /**
     * Plantilla: Confirmación de reserva
     */
    protected function generarPlantillaConfirmacionReserva(string $nombre, array $reserva, array $libro, int $posicionCola): string
    {
        $fechaReserva = date('d/m/Y H:i', strtotime($reserva['fecha_reserva']));
        $esPrimero = $posicionCola === 1;

        $estiloPosicion = $esPrimero
            ? 'background: #198754; color: white;'
            : 'background: #ffc107; color: #000;';

        $textoPosicion = $esPrimero
            ? '✅ ¡Eres el primero en la cola!'
            : "📋 Posición en cola: #{$posicionCola}";

        $mensajeCola = $esPrimero
            ? '<p>El libro está disponible. Puedes pasar a recogerlo cuando quieras.</p>'
            : '<p>Este libro actualmente está prestado. Te <strong>notificaremos por email</strong> cuando esté disponible para ti.</p>';

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #198754; color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .book-card { background: #f8f9fa; border-left: 4px solid #198754; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .book-card h3 { margin-top: 0; color: #198754; }
        .queue-badge { display: inline-block; padding: 10px 20px; border-radius: 20px; font-weight: bold; margin: 10px 0; {$estiloPosicion} }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .rules { background: #e7f3ff; border-left: 4px solid #0d6efd; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .rules ul { margin: 10px 0; padding-left: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Reserva Confirmada</h1>
        </div>
        <div class="content">
            <p>Hola <strong>{$this->esc($nombre)}</strong>,</p>
            <p>Tu reserva ha sido registrada correctamente en nuestro sistema.</p>

            <div class="book-card">
                <h3>{$this->esc($libro['titulo'])}</h3>
                <p><strong>Autor:</strong> {$this->esc($libro['autor'])}</p>
                <p><strong>ISBN:</strong> {$this->esc($libro['isbn'])}</p>
                <p><strong>Fecha de reserva:</strong> {$fechaReserva}</p>
            </div>

            <div class="queue-badge">{$textoPosicion}</div>

            {$mensajeCola}

            <div class="rules">
                <strong>📋 Normas de reserva:</strong>
                <ul>
                    <li>Máximo <strong>3 reservas activas</strong> simultáneas</li>
                    <li>Plazo de préstamo: <strong>15 días</strong></li>
                    <li>Si eres notificado de disponibilidad, tienes <strong>48 horas</strong> para recoger el libro</li>
                </ul>
            </div>
        </div>
        <div class="footer">
            <p>Biblioteca | Este es un email automático, por favor no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Plantilla: Libro disponible
     */
    protected function generarPlantillaLibroDisponible(string $nombre, array $reserva, array $libro): string
    {
        $fechaReserva = date('d/m/Y H:i', strtotime($reserva['fecha_reserva']));

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #0d6efd; color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .book-card { background: #f8f9fa; border-left: 4px solid #0d6efd; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .book-card h3 { margin-top: 0; color: #0d6efd; }
        .urgent { background: #fff3cd; border: 1px solid #ffc107; color: #856404; padding: 15px; border-radius: 4px; margin: 20px 0; }
        .btn { display: inline-block; background: #0d6efd; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 ¡Tu libro está disponible!</h1>
        </div>
        <div class="content">
            <p>Hola <strong>{$this->esc($nombre)}</strong>,</p>
            <p>El libro que reservaste ya está disponible para su préstamo.</p>

            <div class="book-card">
                <h3>{$this->esc($libro['titulo'])}</h3>
                <p><strong>Autor:</strong> {$this->esc($libro['autor'])}</p>
                <p><strong>ISBN:</strong> {$this->esc($libro['isbn'])}</p>
                <p><strong>Fecha de reserva original:</strong> {$fechaReserva}</p>
            </div>

            <div class="urgent">
                <strong>⏰ ¡Importante!</strong> Tienes <strong>48 horas</strong> para pasar a recoger el libro. 
                Si no lo recoges en ese plazo, la reserva pasará al siguiente usuario en la cola.
            </div>

            <p>Por favor, acude a la biblioteca con tu identificación para recoger el libro.</p>

            <p style="text-align: center;">
                <a href="mailto:biblioteca@tudominio.com" class="btn">Contactar con la Biblioteca</a>
            </p>
        </div>
        <div class="footer">
            <p>Biblioteca | Este es un email automático, por favor no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Plantilla: Cancelación de reserva
     */
    protected function generarPlantillaCancelacionReserva(string $nombre, array $reserva, array $libro): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #dc3545; color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .book-card { background: #f8f9fa; border-left: 4px solid #dc3545; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .book-card h3 { margin-top: 0; color: #dc3545; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ Reserva Cancelada</h1>
        </div>
        <div class="content">
            <p>Hola <strong>{$this->esc($nombre)}</strong>,</p>
            <p>Tu reserva ha sido cancelada en nuestro sistema.</p>

            <div class="book-card">
                <h3>{$this->esc($libro['titulo'])}</h3>
                <p><strong>Autor:</strong> {$this->esc($libro['autor'])}</p>
                <p><strong>ISBN:</strong> {$this->esc($libro['isbn'])}</p>
            </div>

            <p>Si tienes alguna duda, contacta con la biblioteca.</p>
        </div>
        <div class="footer">
            <p>Biblioteca | Este es un email automático, por favor no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Plantilla: Recordatorio de devolución (TU CÓDIGO CORREGIDO)
     */
    protected function generarPlantillaRecordatorio($destinatario, $datos)
    {
        $estado = $datos['dias_retraso'] > 0 ? 'retraso' : 'proximo';
        $color = $estado === 'retraso' ? '#dc3545' : '#ffc107';

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: {$color}; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border: 1px solid #dee2e6; }
        .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 12px; }
        .btn { display: inline-block; padding: 10px 20px; background: #0d6efd; color: white; text-decoration: none; border-radius: 5px; }
        .alert { padding: 15px; background: #fff3cd; border-left: 4px solid {$color}; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Biblioteca</h1>
            <p>Recordatorio de Devolución</p>
        </div>
        
        <div class="content">
            <p>Hola <strong>{$this->esc($destinatario['nombre'])}</strong>,</p>
            
            <p>Le recordamos que tiene pendiente la devolución del siguiente libro:</p>
            
            <div class="alert">
                <h3 style="margin-top: 0;">{$this->esc($datos['titulo_libro'])}</h3>
                <p><strong>Fecha de préstamo:</strong> {$datos['fecha_prestamo']}</p>
                <p><strong>Fecha prevista de devolución:</strong> {$datos['fecha_devolucion']}</p>
HTML;

        if ($datos['dias_retraso'] > 0) {
            $html .= <<<HTML
                <p style="color: #dc3545; font-weight: bold;">
                    ⚠️ Días de retraso: {$datos['dias_retraso']}
                </p>
HTML;
        } else {
            $dias = abs($datos['dias_restantes']);
            $html .= <<<HTML
                <p style="color: #198754;">
                    ✅ Quedan {$dias} días para la devolución
                </p>
HTML;
        }

        $html .= <<<HTML
            </div>
            
            <p>{$this->esc($datos['mensaje_personalizado'])}</p>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="mailto:biblioteca@tudominio.com?subject=Consulta sobre préstamo" class="btn">
                    Contactar con la Biblioteca
                </a>
            </p>
            
            <p>Gracias por su colaboración.</p>
        </div>
        
        <div class="footer">
            <p>Este es un mensaje automático de la Biblioteca.</p>
            <p>No responda a este email. Para consultas: biblioteca@tudominio.com</p>
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    /**
     * Escapar HTML para evitar XSS en emails
     */
    protected function esc(string $texto): string
    {
        return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
    }
}