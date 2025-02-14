<?php
function create_session($id, $username, $email, $type, $languageCode = null, $time_zone = null, $remember_me = true)
{
    // Duración de la sesión: 30 días o 1 hora
    $lifetime = $remember_me ? (30 * 24 * 3600) : 3600;

    // Configuración de cookies según el entorno
    $cookieParams = [
        'lifetime' => $lifetime,
        'path' => '/', // Asegura que la cookie esté disponible en todo el dominio
        //'domain' => 'elipticnet.com', // si lo descomento no anda en produccion el login
        'secure' => ENV === 'production', // Solo se enviará por HTTPS en producción
        'httponly' => true, // Evita el acceso a la cookie desde JavaScript (previene XSS)
        'samesite' => 'Strict', // Previene CSRF en producción
        'version' => 1 // Versión de la cookie (opcional, pero útil si necesitas compatibilidad con navegadores antiguos)
    ];
    // Iniciar sesión solo si no está activa
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params($cookieParams);
        session_start();
        // Eliminar la cookie anterior solo si existe
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }

        // Regenerar el ID de sesión
        session_regenerate_id(true);
    }

    // Almacenar información del usuario en la sesión
    $_SESSION['user'] = [
        'id' => $id,
        'username' => $username,
        'email' => $email,
        'languageCode' => $languageCode,
        'time_zone' => $time_zone,
        'type' => $type
    ];
}
