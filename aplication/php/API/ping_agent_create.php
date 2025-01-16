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

if (!isset($data['name']) || !isset($data['ip'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Validation Error", "message" => "Incomplete data."]);
    exit;
}

// Función para validar la dirección IP
function validate_ip($ip)
{
    return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
}

// Validar la IP
if (!validate_ip($data['ip'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Validation Error", "message" => "The IP address is not valid."]);
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

    // Verificar si ya existe un host con la misma IP y owner
    $check_query = "SELECT COUNT(*) FROM ping_agent_data WHERE ip = ? AND owner = ?";

    $stmt = $conn->prepare($check_query);
    if (!$stmt) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    $stmt->bind_param("ss", $data['ip'], $owner);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo json_encode(["error" => true, "type" => "warning", "title" => "Duplicate Error", "message" => "The host already exists."]);
        exit;
    }

    // Insertar el host en la base de datos
    $sql = "INSERT INTO ping_agent_data (owner, ip, name, description, threshold, threshold_exceeded, log, transports) VALUES (?, ?, ?, ?, ?, ?, ?,?)";

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
    $stmt->bind_param("isssisss", $owner, $data['ip'], $data['name'], $data['description'], $threshold, $threshold_exceeded, $log_json, $transports_json);

    if (!$stmt->execute()) {
        error_log("[ERROR] " . __FILE__ . ": " . $stmt->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
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
