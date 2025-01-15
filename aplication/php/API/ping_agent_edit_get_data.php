<?php
// Cargar las variables de entorno
require_once '../env.php';
include '../sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];


// Validar entrada
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $hostId = $_GET['id'];
} else {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid Request", "message" => "Invalid request method or missing id"]);
    exit;
}

if (empty($hostId) || !is_numeric($hostId)) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid Parameter", "message" => "Host ID must be a valid numeric value"]);
    exit;
}

$hostId = (int)$hostId;

try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->connect_error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    // Consulta SQL para obtener el host con el id y owner correspondiente
    $sql = "SELECT ip, name, description, transports, threshold 
            FROM ping_agent_data WHERE id = ? AND owner = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    // Vincular parámetros
    $stmt->bind_param("ii", $hostId, $owner);  // Usamos 'ii' para indicar que son dos enteros
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode(["error" => true, "type" => "warning", "title" => "Not Found", "message" => "No host found."]);
        exit;
    }

    // Obtener el host
    $host = $result->fetch_assoc();

    // Decodificar los campos JSON, si existen
    if ($host['threshold']) {
        $host['threshold'] = json_decode($host['threshold'], true); // Convertir a array
    }

    // Devolver el host en formato JSON
    echo json_encode($host, JSON_PRETTY_PRINT);
    exit;
} catch (Exception $e) {
    // Manejar errores
    error_log("[ERROR] " . __FILE__ . ": " . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
    exit;
} finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}
