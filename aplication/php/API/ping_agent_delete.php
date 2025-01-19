<?php
// Cargar las variables de entorno
require_once __DIR__ . '/../env.php';
include __DIR__ . '/../sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];



// Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "Method not allowed"]);
    exit;
}


// Verificar si se proporcionó el hostId en la URL (GET)
if (!isset($_GET['hostId'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Validation Error", "message" => "Host ID is required"]);
    exit;
}

$id = (int)$_GET['hostId'];



try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->connect_error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    // Consulta SQL para eliminar el host por id y owner
    $delete_query = "DELETE FROM ping_agent_data WHERE id = ? AND owner = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($delete_query);
    if (!$stmt) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    // Vincular los parámetros
    $stmt->bind_param("is", $hostId, $owner);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Comprobar si realmente se eliminó una fila
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true, "message" => "Host deleted successfully."]);
        } else {
            echo json_encode(["error" => true, "type" => "warning", "title" => "Not Found", "message" => "Host not found."]);
        }
    } else {
        error_log("[ERROR] " . __FILE__ . ": " . $stmt->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    // Cerrar la consulta y la conexión
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Manejo de errores generales
    error_log("[ERROR] " . __FILE__ . ": " . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
} finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}
