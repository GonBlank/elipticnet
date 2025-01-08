<?php
require_once '../env.php';
require_once '../email/email.php';
require_once '../email/templates/pre_release_template.php';

//Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "data no set"]);
    exit;
}

$user = json_decode(file_get_contents('php://input'), true);


if (!isset($user['email'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "data no set"]);
    exit;
}

$email = $user['email'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid email format", "message" => "Check the email format."]);
    exit;
}

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        error_log("[ERROR] index:" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later."]);
        exit;
    }

    $check_query = "SELECT COUNT(*) FROM pre_release WHERE email = ?";


    $stmt = $conn->prepare($check_query);
    if (!$stmt) {
        error_log("[ERROR] index:" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later."]);
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo json_encode(["error" => true, "type" => "warning", "title" => "Duplicated email", "message" => "$email already exist in the pre-release list."]);
        exit;
    }

    $sql = "INSERT INTO pre_release  (email) VALUES (?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("[ERROR] index:" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later."]);
        exit;
    }

    $stmt->bind_param("s", $email);


    if (!$stmt->execute()) {
        error_log("[ERROR]: pre_release:" . $e->getMessage());
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later."]);
        exit;
    }

    /*
    ╔════════════╗
    ║ Send email ║
    ╚════════════╝
    */
    $body = pre_release_template();
    send_email($body, "Welcome to the Elipticnet Early Access List!", $email);
    echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "Thank you for subscribing to the Elipticnet early access list!"]);
} catch (Exception $e) {
    error_log("[ERROR]: pre_release:" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later."]);
} finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}
