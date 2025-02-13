<?php
// Cargar las variables de entorno
require_once __DIR__ . '/../env.php';
include __DIR__ . '/../sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];

// Validar método
if ($_SERVER['REQUEST_METHOD'] != 'PATCH') {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid Request", "message" => "Invalid request method or missing id"]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Data Error", "message" => "Incomplete data."]);
}

$username = $data['username'];

// Validate name length
$name_length = strlen($username);
if ($name_length < 3 || $name_length > 15) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid username length", "message" => "Username must be between 3 and 15 characters."]);
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

    // SQL para actualizar los campos
    $sql = "UPDATE users 
                SET username = ? 
                WHERE id = ?";

    $stmt = $conn->prepare($sql);

    // Pasar los valores al bind_param
    $stmt->bind_param(
        "si",
        $username,
        $owner
    );

    // Ejecutar la consulta
    if ($stmt->execute()) {
        $_SESSION['user']['username'] = $username;
        $data['state'] = true;
        echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "Updated username"]);
    } else {
        echo json_encode(["error" => true, "type" => "error", "title" => "Update Error", "message" => "Failed to update host."]);
    }

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
