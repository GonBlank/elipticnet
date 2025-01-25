<?php
// Cargar las variables de entorno
require_once __DIR__ . '/../env.php';
include __DIR__ . '/../sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];


// Validar entrada
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "Method not allowed"]);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Validation Error", "message" => "Incomplete data."]);
    exit;
}

$ping_agent_id = (int)$_GET['id'];


try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->connect_error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    $sql = "SELECT 
                pal.icon, 
                pal.cause, 
                pal.message, 
                pal.time
            FROM 
                ping_agent_log pal
            INNER JOIN 
                ping_agent_data pad
            ON 
                pal.ping_agent_id = pad.id
            WHERE 
                pad.id = ? AND pad.owner = ?;";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    // Vincular parámetros
    $stmt->bind_param("ii", $ping_agent_id, $owner);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Si no hay resultados, devolver un mensaje vacío
        header('Content-Type: application/json');
        echo json_encode([]);
        exit;
    }


    // Obtener todos los registros
    $logs = [];
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }

    // Devolver los logs en formato JSON
    echo json_encode($logs, JSON_PRETTY_PRINT);

    exit;
} catch (Exception $e) {
    // Manejar errores
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
