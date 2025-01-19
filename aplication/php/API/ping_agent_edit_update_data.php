<?php
// Cargar las variables de entorno
require_once __DIR__ . '/../env.php';
include __DIR__ . '/../sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];


// Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "Method not allowed"]);
    exit;
}

// Obtener los datos JSON enviados a través del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name']) || !isset($data['id'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Validation Error", "message" => "Incomplete data."]);
    exit;
}

try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->connect_error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    // Actualizar el host en la base de datos
    $sql = "UPDATE ping_agent_data SET name = ?, description = ?, threshold = ?, threshold_exceeded = ?, transports = ? WHERE id = ? AND owner = ? ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }


    // Convertir los valores JSON
    $log_json = json_encode([]);
    $transports_json = json_encode($data['transports']);
    $threshold = isset($data['threshold']) && is_numeric($data['threshold']) ? (int) $data['threshold'] : null;
    $threshold_exceeded = $threshold !== null ? 0 : null; // false si threshold tiene un valor


    // Bind parameters
    $stmt->bind_param("ssissii", $data['name'], $data['description'], $threshold, $threshold_exceeded, $transports_json, $data['id'], $owner);

    if (!$stmt->execute()) {
        error_log("[ERROR] " . __FILE__ . ": " . $stmt->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    /*
    ╔═══════════════════╗
    ║ HOST ACTUALIZADO  ║
    ╚═══════════════════╝
    */

    // Verificar cuántas filas fueron afectadas
    if ($stmt->affected_rows === 1) {
        echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "Host updated successfully"]);
    } elseif ($stmt->affected_rows === 0) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Error", "message" => "Failed update request"]);
    } else {
        // Si el número de filas afectadas es mayor a 1, lo que podría indicar un problema
        error_log("[ERROR]: ping_agent_edit_udate_data.php: Unexpectedly, multiple records were modified. The user ID who performed the update is: " . $owner);
    }
    exit;
} catch (Exception $e) {
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
