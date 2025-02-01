<?php
// Cargar las variables de entorno
require_once __DIR__ . '/../../env.php';
include __DIR__ . '/../../sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];


// Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "Method not allowed"]);
    exit;
}

// Obtener los datos JSON enviados a través del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['url'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Validation Error", "message" => "Incomplete data."]);
    exit;
}

// Función para validar la url
function validate_url($url)
{
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        return true;  // La URL es válida
    } else {
        return false; // La URL no es válida
    }
}

// Validar la url
if (!validate_url($data['url'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Validation Error", "message" => "The URL address is not valid."]);
    exit;
}

if (!isset($data['alias']) || empty($data['alias'])) {
    $data['alias'] = $data['url'];
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

    // Verificar si ya existe un host con la misma IP y owner
    $check_query = "SELECT COUNT(*) FROM web_agent_data WHERE url = ? AND owner = ?";

    $stmt = $conn->prepare($check_query);
    if (!$stmt) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    $stmt->bind_param("ss", $data['url'], $owner);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo json_encode(["error" => true, "type" => "warning", "title" => "Duplicate Error", "message" => "The url already exists."]);
        exit;
    }

    // Insertar el host en la base de datos
    $sql = "INSERT INTO web_agent_data (owner, url, alias, request_timeout,  ssl_expiry, domain_expiry, check_sslError, transports) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    $transports_json = json_encode($data['transports']);


    // Bind parameters
    $stmt->bind_param("issiiiis", $owner, $data['url'], $data['alias'], $data['requestTimeout'], $data['sslExpiry'], $data['domainExpiry'], $data['checkSslError'], $transports_json);

    if (!$stmt->execute()) {
        error_log("[ERROR] " . __FILE__ . ": " . $stmt->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    /*
    ╔══════════════╗
    ║ HOST CREADO  ║
    ╚══════════════╝
    */

    echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "Agent created successfully"]);
    exit;
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
