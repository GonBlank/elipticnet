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
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['hostId'])) {
        $hostId = $_GET['hostId'];
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $hostId = $data['hostId'] ?? null;
    } else {
        echo json_encode(["error" => true, "type" => "error", "title" => "Invalid Request", "message" => "Invalid request method or missing id"]);
        exit;
    }

    if ($hostId === null) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Missing Parameter", "message" => "host id is required"]);
        exit;
    }

    // Consulta SQL para obtener el host con el id y owner correspondiente
    $sql = "SELECT data FROM host_data WHERE id = ? AND JSON_EXTRACT(data, '$.owner') = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare statement error: " . $conn->error);
    }

    // Vincular parámetros
    $stmt->bind_param("is", $hostId, $owner);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Obtener el host en formato JSON
        $host = $result->fetch_assoc();
        echo json_encode(json_decode($host['data'], true), JSON_PRETTY_PRINT);
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
