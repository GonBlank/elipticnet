<?php
require_once '../env.php';
require_once '../email/email.php';
require_once '../email/templates/validate_email.php';
include_once '../functions/generate_random_hash.php';


//Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "data no set"]);
    exit;
}

$user = json_decode(file_get_contents('php://input'), true);

if (!isset($user['username']) || !isset($user['email']) || !isset($user['password']) || !isset($user['timeZone'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "data no set"]);
    exit;
}

$name = $user['username'];
$email = $user['email'];
$raw_password = $user['password'];
$time_zone = $user['timeZone'];

// Validate password length
$password_length = strlen($raw_password);
if ($password_length < 8 || $password_length > 20) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid password length", "message" => "Password must be between 8 and 20 characters."]);
    exit;
}

// Validate name length
$name_length = strlen($name);
if ($name_length < 3 || $name_length > 15) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid username length", "message" => "Username must be between 3 and 15 characters."]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid email format", "message" => "Check the email format."]);
    exit;
}

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        error_log("[ERROR]" . basename(__FILE__) . ":" . $conn->connect_error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    $check_query = "SELECT COUNT(*) FROM users WHERE email = ?";


    $stmt = $conn->prepare($check_query);
    if (!$stmt) {
        error_log("[ERROR]" . basename(__FILE__) . ":" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo json_encode(["error" => true, "type" => "warning", "title" => "Duplicated email", "message" => "$email already exist."]);
        exit;
    }

    // Generate the password hash
    $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);
    $validation_hash = generate_random_hash($conn, "users", "validation_hash");
    $hash_date = date('Y-m-d H:i:s');
    $user_type = 0; //default free user

    // Insert new user
    $sql = "INSERT INTO users  (username, email, password, type) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("[ERROR]" . basename(__FILE__) . ":" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    $validation_hash = generate_random_hash($conn, "transports", "validation_hash");
    $hash_date = date('Y-m-d H:i:s');

    $stmt->bind_param("ssss", $name, $email, $hashed_password, $user_type);


    if (!$stmt->execute()) {
        error_log("[ERROR]" . basename(__FILE__) . ":" .  $e->getMessage());
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    // Obtener el ID del registro insertado
    $user_id = $conn->insert_id;

    $sql = "INSERT INTO transports (owner, type, alias, transport_id, validation_hash, hash_date) 
                                   VALUES ( ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("[ERROR]" . basename(__FILE__) . ":" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    $validation_hash = generate_random_hash($conn, "transports", "validation_hash");
    $hash_date = date('Y-m-d H:i:s');
    $type = "email";

    $stmt->bind_param("isssss", $user_id, $type, $name, $email, $validation_hash, $hash_date);


    if (!$stmt->execute()) {
        error_log("[ERROR]: Insert transport email user signup.php:" . $e->getMessage());
        echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Insert transport email."]);
        exit;
    }


    $sql = "INSERT INTO user_config (owner, time_zone) VALUES (?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("[ERROR]" . basename(__FILE__) . ":" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);

        exit;
    }

    $stmt->bind_param("is", $user_id, $time_zone);

    if (!$stmt->execute()) {
        error_log("[ERROR]: Insert user_config signup.php:" . $e->getMessage());
        echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Insert user_config."]);
        exit;
    }

    /*
    ╔═════════════════════════╗
    ║ Send email verification ║
    ╚═════════════════════════╝
    */
    $body = validate_email_template($name, $validation_hash);
    send_email($body, "Verify your email", $email);
    echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "Check your email to finish registration"]);
} catch (Exception $e) {
    // Manejo de errores
    error_log("[ERROR]" . basename(__FILE__) . ":" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
}