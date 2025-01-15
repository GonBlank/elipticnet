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
        error_log("[ERROR] " . __FILE__ . ": " . $conn->connect_error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
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
        error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
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
    error_log("[ERROR] " . __FILE__ . ": " . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
}finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}
?>
