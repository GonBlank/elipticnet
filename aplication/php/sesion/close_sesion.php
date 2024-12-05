<?php
require_once '../env.php';

function close_session()
{
    // Asegurarse de que la sesión esté iniciada

    // Limpiar todas las variables de sesión
    $_SESSION = [];

    // Eliminar la cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
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

    // Destruir la sesión
    session_destroy();
}
