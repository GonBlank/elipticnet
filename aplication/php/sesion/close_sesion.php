<?php

function close_session()
{
    // Iniciar la sesión si no está iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Limpiar todas las variables de sesión
    $_SESSION = [];

    // Eliminar la cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();

        // Forzar eliminación de la cookie
        setcookie(
            session_name(), // Nombre de la cookie de sesión
            '', // Valor vacío
            time() - 42000, // Tiempo en el pasado para eliminarla
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Destruir la sesión en el servidor
    session_destroy();
}
