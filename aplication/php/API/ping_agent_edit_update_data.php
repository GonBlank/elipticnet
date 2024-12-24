<?php
// Cargar las variables de entorno
require_once '../env.php';
include '../sesion/checkAuth.php';
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
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error:", "message" => $conn->connect_error]);
        exit;
    }

    // Actualizar el host en la base de datos
    $sql = "UPDATE host_data SET name = ?, description = ?, threshold = ?, threshold_exceeded = ?, transports = ? WHERE id = ? AND owner = ? ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $conn->error]);
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
        echo json_encode(["error" => true, "type" => "error", "title" => "Insertion Error", "message" => $stmt->error]);
        exit;
    }

    /*
    ╔══════════════╗
    ║ HOST CREADO  ║
    ╚══════════════╝
    */

    echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "Hostess created successfully"]);
    exit;
} catch (Exception $e) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
} finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}
