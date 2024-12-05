<?php
// Cargar las variables de entorno
require_once '../env.php';
include '../sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];

// Validar método
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid Request", "message" => "Invalid request method or missing id"]);
    exit;
}


// Obtener los datos JSON enviados a través del POST
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
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error:", "message" => $conn->connect_error]);
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
        echo json_encode($data);
    } else {
        echo json_encode(["error" => true, "type" => "error", "title" => "Update Error", "message" => "Failed to update host."]);
    }

    $stmt->close();


    // Cerrar la conexión
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
}
