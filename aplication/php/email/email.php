<?php
require_once __DIR__ . '/../env.php';
require "vendor/autoload.php";


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
function send_email($body, $subject, $client_email)
{
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->SMTPDebug  = 0; // Cambiar a 2 para más detalles en el debug
        $mail->isSMTP();
        $mail->Host       = SMTP_SERVER;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Cambiar a SMTPS para puerto 465
        $mail->Port       = SMTP_PORT;

        // Configuración de remitente y destinatarios
        $mail->setFrom(SMTP_USERNAME, 'Elipticnet notifications'); // Remitente
        $mail->addReplyTo(SMTP_USERNAME, 'Elipticnet notifications');
        $mail->Sender = SMTP_USERNAME;
        $mail->addAddress($client_email);           // Destinatario
        $mail->isHTML(true);                        // Activar HTML
        $mail->CharSet = 'UTF-8';                   // Asegurar que el charset es UTF-8
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Enviar correo
        $mail->send();
    } catch (Exception $e) {
        // Manejo de errores
        error_log("[ERROR] " . __FILE__ . ": " . $e->getMessage());
        echo json_encode([
            "error" => true,
            "type" => "error",
            "title" => "Connection Error",
            "message" => "We are experiencing problems, please try again later or",
            "link_text" => "contact support",
            "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."
        ]);
        exit;
    }
}
