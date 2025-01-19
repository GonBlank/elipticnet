<?php

require_once __DIR__ . '/../env.php';

/*
╔════════════╗
║ Desarrollo ║
╚════════════╝
*/

function checkAuth()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['user'])) {
        // Redirige al login si no está autenticado
        header("Location: /aplication/public/login.php?error=checkAuth_auth_required");
        exit();
    }

    // Retorna los datos del usuario autenticado
    return $_SESSION['user'];
}
