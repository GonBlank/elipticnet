<?php
function create_session($id, $username, $email, $type, $languageCode = NULL, $time_zone = NULL, $remember_me = true)
{

    // Set session duration based on "remember me"
    $lifetime = $remember_me ? (30 * 24 * 3600) : (time() + 60 * 60);  // 30 días  o 1 hora

    //configuracion de cookies - Cambian dependiendo del entorno
    if (ENV == 'development') {
        $cookieParams = [
            'lifetime' => $lifetime,
            'path' => '/',
            'domain' => DOMAIN, // Deja vacío o especifica un dominio si es necesario
            'secure' => false, // No requiere HTTPS en desarrollo
            'httponly' => true, // Protege contra XSS
            'samesite' => 'Lax' // Mitiga CSRF en solicitudes de terceros
        ];
    } elseif (ENV == 'production') {
        $cookieParams = [
            'lifetime' => $lifetime,
            'path' => '/',
            'domain' => DOMAIN, // Deja vacío o especifica un dominio si es necesario
            'secure' => true, // Requiere HTTPS en producción
            'httponly' => true, // Protege contra XSS
            'samesite' => 'Strict' // Máxima protección contra CSRF
        ];
    }

    if (session_status() === PHP_SESSION_NONE) {
        // La sesión no está iniciada, iniciar la sesión
        session_set_cookie_params($cookieParams);
        session_start();
        session_regenerate_id(true); // Prevent session fixation
    }

    $_SESSION['user'] = [
        'id' => $id,
        'username' => $username,
        'email' => $email,
        'languageCode' => $languageCode,
        'time_zone' => $time_zone,
        'type' => $type
    ];
}
