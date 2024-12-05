<?php

/*
╔════════════╗
║ Desarrollo ║
╚════════════╝
*/

function checkAuth()
{
    // Configuración de la cookie según el entorno
    $cookieParams = [
        'lifetime' => 0, // Cookie válida mientras el navegador esté abierto
        'path' => '/',
        'domain' => '', // Deja vacío o especifica un dominio si es necesario
        'secure' => false, // true en producción (HTTPS), false en desarrollo (HTTP)
        'httponly' => true, // Protege contra XSS
        'samesite' => 'Strict' // Máxima protección contra CSRF
    ];

    session_set_cookie_params($cookieParams);
    session_start();

    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['user'])) {
        // Redirige al login si no está autenticado
        header("Location: /aplication/public/login.php");
        exit();
    }

    // Retorna los datos del usuario autenticado
    return $_SESSION['user'];
}
