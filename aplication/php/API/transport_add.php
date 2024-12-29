<?php
// Cargar las variables de entorno
require_once '../env.php';
include '../sesion/checkAuth.php';
include_once '../functions/generate_random_hash.php';

require_once '../email/email.php';
require_once '../email/templates/validate_transport_email.php';

$user = checkAuth();
$owner = $user['id'];


// Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "Method not allowed"]);
    exit;
}

// Obtener los datos JSON enviados a través del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['alias']) || !isset($data['transport_id']) || !isset($data['type'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Validation Error", "message" => "Incomplete data."]);
    exit;
}

$alias = $data['alias'];
$transport_id = $data['transport_id'];
$type = $data['type'];

//Validar el formato del transporte
if (!validateTransportFormat($type, $transport_id)) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid transport format", "message" => "Check the transport format."]);
    exit;
}


try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        error_log("[ERROR] transport_add:" . $conn->connect_error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error:", "message" => $conn->connect_error]);
        exit;
    }

    $check_query = "SELECT COUNT(*) FROM transports WHERE type = ? AND transport_id = ? AND owner = ?";


    $stmt = $conn->prepare($check_query);
    if (!$stmt) {
        error_log("[ERROR] transport_add:" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $conn->error]);
        exit;
    }

    $stmt->bind_param("sss", $type, $transport_id, $owner);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo json_encode(["error" => true, "type" => "warning", "title" => "Duplicate Error", "message" => "The transport already exists."]);
        exit;
    }

    $sql = "INSERT INTO transports (owner, type, alias, transport_id, validation_hash, hash_date) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("[ERROR] transport_add:" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $conn->error]);
        exit;
    }

    $validation_hash = generate_random_hash($conn, "transports", "validation_hash");
    $hash_date = date('Y-m-d H:i:s');

    $stmt->bind_param("isssss", $owner, $type, $alias, $transport_id, $validation_hash, $hash_date);


    if (!$stmt->execute()) {
        error_log("[ERROR] transport_add:" . $stmt->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Insertion Error", "message" => $stmt->error]);
        exit;
    }
    sendValidationLink($type, $validation_hash, $transport_id);

    echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "Check the added transport to validate it"]);
    exit;
} catch (Exception $e) {
    error_log("[ERROR] transport_add:" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
} finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}




function sendValidationLink($type, $validation_hash, $transport_id)
{
    switch ($type) {
        case 'email':
            // Enviar correo electrónico de validación
            $body = validate_transport_email_template($validation_hash);
            send_email($body, "Verification required", $transport_id);
            break;
        case 'telegram':
            return;
            break;

        default:
            # code...
            break;
    }

    return;
}

function validateTransportFormat($type, $transport_id)
{
    switch ($type) {
        case 'email':
            // Validate email format
            return filter_var($transport_id, FILTER_VALIDATE_EMAIL);
            break;

        case 'telegram':
            return filter_var($transport_id, FILTER_VALIDATE_INT);
            break;

        default:
            return false;
            break;
    }
}
