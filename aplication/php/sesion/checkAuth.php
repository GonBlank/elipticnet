<?php

/*
╔════════════╗
║ Desarrollo ║
╚════════════╝
*/

function checkAuth()
{
    // Configuración de la cookie según el entorno, con extensión de la duración de la sesión
    $cookieParams = [
        'lifetime' => time() + 60 * 60, // Agregar 1 hora al tiempo actual
        'path' => '/',
        'domain' => '', // Deja vacío o especifica un dominio si es necesario
        'secure' => false, // true en producción (HTTPS), false en desarrollo (HTTP)
        'httponly' => true, // Protege contra XSS
        'samesite' => 'Strict' // Máxima protección contra CSRF
    ];

    // Establece los parámetros de la cookie y arranca la sesión
    session_set_cookie_params($cookieParams);
    session_start();

    // Regenera el ID de sesión para proteger contra el secuestro de sesión
    if (!isset($_SESSION['regenerated'])) {
        session_regenerate_id(true);
        $_SESSION['regenerated'] = true;
    }

    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['user'])) {
        // Redirige al login si no está autenticado
        header("Location: /aplication/public/login.php?error=auth_required");
        exit();
    }

    // Extiende el tiempo de la sesión a 1 hora más cada vez que se ejecuta la función
    setcookie(session_name(), session_id(), time() + 60 * 60, '/');

    // Retorna los datos del usuario autenticado
    return $_SESSION['user'];
}
