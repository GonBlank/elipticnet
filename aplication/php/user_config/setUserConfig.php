<?php
require_once __DIR__ . '/../env.php';
include __DIR__ . '/../sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];

/*Actualiza el timezone y el idioma acorde las preferencias del navegador
* La funcion principal es completar estos datos en la tabla de user_config
* para usuarios que se registraron con google y no se pudo obtener esta
* información al hacer el registro usando google
*/

//Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    error_log("[ERROR] " . __FILE__ . ": Invalid request method");
    exit;
}

$config = json_decode(file_get_contents('php://input'), true);

if (!isset($config['timeZone']) || !isset($config['language'])) {
    error_log("[ERROR] " . __FILE__ . ": Data no set");
    exit;
}

$time_zone = $config['timeZone'];
$language = $config['language'];


try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->connect_error);
        exit;
    }

    // SQL para actualizar los campos
    $sql = "UPDATE user_config SET time_zone = ?, language = ? WHERE owner = ?";

    $stmt = $conn->prepare($sql);

    // Pasar los valores al bind_param
    $stmt->bind_param("ssi", $time_zone, $language, $owner);

    // Ejecutar la consulta
    if (!$stmt->execute()) {
        // Capturar el error específico
        $error = $stmt->error;
        error_log("[ERROR] " . __FILE__ . ": Failed to update host user_data. Error: " . $error);
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['user'])) {
        $_SESSION['user']['time_zone'] = $time_zone;
        $_SESSION['user']['languageCode'] = $language;
    }
} catch (Exception $e) {
    // Manejo de errores
    error_log("[ERROR] " . __FILE__ . ": " . $e->getMessage());
} finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}
