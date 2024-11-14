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
        die("Connection error: " . $conn->connect_error);
    }

    // Obtener el hostId del parámetro GET
    $hostId = isset($_GET['hostId']) ? $_GET['hostId'] : null;

    // Validar el hostId
    if ($hostId === null) {
        echo json_encode(["error" => true, "message" => "Host ID is required."]);
        exit;
    }

    // Consulta SQL para obtener el parámetro latency del host correspondiente
    $sql = "SELECT JSON_EXTRACT(data, '$.latency') AS latency FROM host_data WHERE id = ? AND JSON_EXTRACT(data, '$.owner') = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $hostId, $owner);
    $stmt->execute();
    $stmt->bind_result($latency);
    $stmt->fetch();
    $stmt->close();

    // Devolver solo el array de latency
    if ($latency !== null) {
        echo $latency;
    } else {
        echo json_encode([]);
    }

    // Cerrar la conexión
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
