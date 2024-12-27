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
        throw new Exception("Connection error: " . $conn->connect_error);
    }

    // Consulta SQL para obtener el host con el id y owner correspondiente
    $sql = "SELECT id, ip, name, description, state, last_check, last_down, last_up, log, threshold, threshold_exceeded
            FROM host_data WHERE id = ? AND owner = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare statement error: " . $conn->error);
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
    if ($host['log']) {
        $host['log'] = json_decode($host['log'], true); // Convertir a array
    }

    // Devolver el host en formato JSON
    echo json_encode($host, JSON_PRETTY_PRINT);
    exit;
} catch (Exception $e) {
    // Manejar errores
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
} finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}
