<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    class Email {
        // Función para enviar un correo con los parámetros necesarios
        public static function enviar($destinatario, $asunto, $cuerpo) {
            $mail = new PHPMailer(true);

            try {

                // Configuración del Servidor
                $mail->isSMTP();
                $mail->Host       = EMAIL_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = EMAIL_USER;
                $mail->Password   = EMAIL_PASS;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = EMAIL_PORT;
                $mail->CharSet    = 'UTF-8';

                // Destinatarios
                $mail->setFrom(EMAIL_USER, 'Traileros');
                $mail->addAddress($destinatario);

                // Contenido
                $mail->isHTML(true);
                $mail->Subject = $asunto;
                $mail->Body    = self::getTemplate($cuerpo);

                $mail->send();
                return true;
            } catch (Exception $e) {
                error_log("Error al enviar correo: {$mail->ErrorInfo}");
                return false;
            }
        }

        // Plantilla con la cabecera y el footer reutilizable para los correos
        private static function getTemplate($contenido) {
            return "
            <div style='max-width: 600px; margin: 0 auto; font-family: sans-serif; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;'>
                <div style='background-color: #2d5a27; color: white; padding: 20px; text-align: center;'>
                    <h1>TRAILEROS</h1>
                </div>
                <div style='padding: 20px; color: #333;'>
                    $contenido
                </div>
                <div style='background-color: #f4f4f4; color: #888; padding: 10px; text-align: center; font-size: 12px;'>
                    &copy; " . date('Y') . " Traileros
                </div>
            </div>";
            
        }
    }
?>