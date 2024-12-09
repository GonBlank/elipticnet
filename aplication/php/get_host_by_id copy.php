<?php
// Cargar las variables de entorno
require_once 'env.php';
include '../php/sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];

try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Connection error: " . $conn->connect_error);
    }

    // Obtener los datos enviados
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        $hostId = $_GET['id'];
    } else {
        echo json_encode(["error" => true, "type" => "error", "title" => "Invalid Request", "message" => "Invalid request method or missing id"]);
        exit;
    }

    if ($hostId === null) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Missing Parameter", "message" => "Host ID is required"]);
        exit;
    }

    // Consulta SQL para obtener el host con el id y owner correspondiente
    $sql = "SELECT id, ip, name, description, state, last_check, last_down, last_up, latency, log, transports, extra 
            FROM host_data WHERE id = ? AND owner = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare statement error: " . $conn->error);
    }

    // Vincular parámetros
    $stmt->bind_param("ii", $hostId, $owner);  // Usamos 'ii' para indicar que son dos enteros
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Obtener el host
        $host = $result->fetch_assoc();

        // Decodificar los campos JSON, si existen
        if ($host['latency']) {
            $host['latency'] = json_decode($host['latency'], true); // Convertir a array
        }
        if ($host['log']) {
            $host['log'] = json_decode($host['log'], true); // Convertir a array
        }
        if ($host['transports']) {
            $host['transports'] = json_decode($host['transports'], true); // Convertir a array
        }
        if ($host['extra']) {
            $host['extra'] = json_decode($host['extra'], true); // Convertir a array
        }

        // Devolver el host en formato JSON
        echo json_encode($host, JSON_PRETTY_PRINT);
    } else {
        echo json_encode(["error" => true, "type" => "warning", "title" => "Not Found", "message" => "No host found."]);
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Manejar errores
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
}
?>
