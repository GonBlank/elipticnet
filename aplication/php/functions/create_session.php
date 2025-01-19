<?php
function create_session($id, $username, $email, $type, $languageCode = null, $time_zone = null, $remember_me = true)
{
    // Duración de la sesión: 30 días o 1 hora
    $lifetime = $remember_me ? (30 * 24 * 3600) : 3600;

    // Configuración de cookies según el entorno
    $cookieParams = [
        'lifetime' => $lifetime,
        'path' => '/',
        'domain' => DOMAIN,
        'secure' => ENV === 'production', // HTTPS solo en producción
        'httponly' => true, // Protección contra XSS
        'samesite' => ENV === 'production' ? 'Strict' : 'Lax' // CSRF según entorno
    ];

    // Iniciar sesión solo si no está activa
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params($cookieParams);
        session_start();
        session_regenerate_id(true); // Regenera el ID para mayor seguridad
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
