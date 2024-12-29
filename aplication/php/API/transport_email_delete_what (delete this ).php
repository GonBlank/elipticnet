<?php
// Cargar las variables de entorno
require_once '../env.php';
include '../sesion/checkAuth.php';
include_once '../functions/generate_random_hash.php';
$user = checkAuth();
$owner = $user['id'];


// Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "Method not allowed"]);
    exit;
}

// Obtener los datos JSON enviados a través del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['alias']) || !isset($data['email']) || !isset($data['type'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Validation Error", "message" => "Incomplete data."]);
    exit;
}

$alias = $data['alias'];
$email = $data['email'];
$type = $data['type'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid email format", "message" => "Check the email format."]);
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

    $check_query = "SELECT COUNT(*) FROM transports WHERE type = ? AND transport_id = ? AND owner = ?";


    $stmt = $conn->prepare($check_query);
    if (!$stmt) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $conn->error]);
        exit;
    }

    $stmt->bind_param("sss", $type, $email, $owner);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo json_encode(["error" => true, "type" => "warning", "title" => "Duplicate Error", "message" => "The email already exists in your transports."]);
        exit;
    }

    // Insertar el host en la base de datos
    $sql = "INSERT INTO transports (owner, type, alias, transport_id, validation_hash, hash_date) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $conn->error]);
        exit;
    }

    $validation_hash = generate_random_hash($conn, "transports", "validation_hash");
    $hash_date = date('Y-m-d H:i:s');


    // Bind parameters
    $stmt->bind_param("isssss", $owner, $type, $alias, $email, $validation_hash, $hash_date);

    if (!$stmt->execute()) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Insertion Error", "message" => $stmt->error]);
        exit;
    }

    echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "Check your email to validate the transport"]);
    exit;
} catch (Exception $e) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
} finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}
