<?php
require_once '../env.php';
include '../sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];


try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Connection error: " . $conn->connect_error);
    }

    // Validar método
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        echo json_encode(["error" => true, "type" => "error", "title" => "Invalid Request", "message" => "Method not allowed"]);
        exit;
    }

    // Consulta SQL para obtener todos los transportes del owner especificado
    $sql = "SELECT id, type, alias ,transport_id FROM transports WHERE owner = ? AND valid = true";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare statement error: " . $conn->error);
    }

    // Vincular parámetros y ejecutar la consulta
    $stmt->bind_param("i", $owner);
    $stmt->execute();
    $result = $stmt->get_result();

    // Crear un array para almacenar los transportes
    $transports = [];

    // Recorrer cada registro y agregarlo al array $transports
    while ($row = $result->fetch_assoc()) {
        $transports[] = [
            "id" => $row['id'],
            "type" => $row['type'],
            "transport_id" => $row['transport_id'],
            "alias" => $row['alias']
        ];
    }

    // Devolver el JSON con los transportes
    echo json_encode(["transports" => $transports], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    // Manejar errores
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
}finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}
?>
