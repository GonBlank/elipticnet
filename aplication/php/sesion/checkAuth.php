<?php

/*
╔═════════════════╗
║ Para desarrollo ║
╚═════════════════╝
*/

function checkAuth()
{
    //Login with secure cookie parameters
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();

    if (!isset($_SESSION['user'])) {
        // Redirige al usuario a la página de inicio de sesión si no está autenticado
        header("Location: ../../aplication/public/login.html");
        exit();
    }

    return $_SESSION['user'];
}

/*
╔═════════════════╗
║ Para producción ║
╚═════════════════╝

<?php
function checkAuth()
{
    // Configuración segura de cookies de sesión
    session_set_cookie_params([
        'lifetime' => 0, // La sesión finaliza al cerrar el navegador
        'path' => '/',
        'domain' => '', // Vacío para que funcione en el dominio actual
        'secure' => true, // Solo se envía por HTTPS
        'httponly' => true, // No accesible desde JavaScript
        'samesite' => 'Strict' // Protege contra ataques CSRF
    ]);
    
    // Iniciar la sesión
    session_start();

    // Verificar si el usuario está autenticado y si el User-Agent coincide
    if (!isset($_SESSION['user']) || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        // Redirige al usuario a la página de inicio de sesión si no está autenticado
        header("Location: ../../aplication/public/login.html");
        exit();
    }

    // Retorna los datos del usuario para uso en la página
    return $_SESSION['user'];
}

*/