<?php
require_once 'env.php';
require_once 'generate_random_hash.php';
require_once 'email/email.php';
require_once 'email/templates/pre_release_template.php';

//Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "data no set"]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);


if (!isset($data['email'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "data no set"]);
    exit;
}

$email = $data['email'];
$language = $data['language'] ?? null;


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
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    $check_query = "SELECT COUNT(*) FROM pre_release WHERE email = ?";

    $stmt = $conn->prepare($check_query);
    if (!$stmt) {
        error_log("[ERROR] index:" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
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

    $hash_id = generate_random_hash($conn, "pre_release", "hash_id");

    $sql = "INSERT INTO pre_release  (email, language, hash_id) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("[ERROR] index:" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    $stmt->bind_param("sss", $email, $language, $hash_id);


    if (!$stmt->execute()) {
        error_log("[ERROR]: pre_release:" . $e->getMessage());
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    /*
    ╔════════════╗
    ║ Send email ║
    ╚════════════╝
    */

    if ($language == 'es') {
        $body = pre_release_template_spa($hash_id, $email);
        $subject = "Early Access confirmado: Prepárate para lo que viene";
    } else {
        $body = pre_release_template_eng($hash_id, $email);
        $subject = "Welcome to the Elipticnet Early Access List!";
    }

    send_email($body, $subject, $email);
    echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "Thank you for subscribing to the Elipticnet early access list!"]);
} catch (Exception $e) {
    error_log("[ERROR]: pre_release:" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
} finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}
