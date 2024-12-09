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
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => $conn->connect_error]);
        exit;
    }
    $time_range = '1 DAY'; // Cambiar entre '1 DAY', '1 MONTH', '1 YEAR'

    $sql = "SELECT 
        AVG(l.latency) AS average_latency,
        MIN(l.latency) AS minimum_latency,
        MAX(l.latency) AS maximum_latency
    FROM 
        latency l
    JOIN 
        host_data h 
    ON 
        l.host_id = h.id
    WHERE 
        l.host_id = ?
        AND h.owner = ?
        AND l.time >= NOW() - INTERVAL $time_range"; // El intervalo ya está definido aquí

    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $hostId, $owner);
    $stmt->execute();
    $result = $stmt->get_result();
    $statistics = $result->fetch_assoc();
    echo json_encode($statistics, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    // Manejo de errores
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
} finally {
    
    if (isset($stmt) && $stmt !== false) {
        $stmt->close(); // Aquí se lanza el error si $stmt ya estaba cerrado.
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}
