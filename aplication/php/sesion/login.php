<?php
require_once '../env.php';

// Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "Method not allowed"]);
    exit;
}

$user = json_decode(file_get_contents('php://input'), true);

if (!isset($user['email']) || !isset($user['password'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "Data not set"]);
    exit;
}

$email = $user['email'];
$raw_password = $user['password'];
$remember_me = isset($user['remember_me']) && $user['remember_me'];

// Validate password length
$password_length = strlen($raw_password);
if ($password_length < 8 || $password_length > 20) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid password length", "message" => "Password must be between 8 and 20 characters."]);
    exit;
}

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
        error_log("[ERROR]" . basename(__FILE__) . ":" . $conn->connect_error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    $sql = "SELECT id, password, username, email, enable, type FROM users WHERE email = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("[ERROR]" . basename(__FILE__) . ":" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_db = $result->fetch_assoc();

    if (!$user_db || !password_verify($raw_password, $user_db['password'])) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Ups!", "message" => "Invalid email/password combination."]);
        exit;
    }


    $sql = "SELECT valid FROM transports WHERE transport_id = ? AND owner = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("[ERROR]" . basename(__FILE__) . ":" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    $stmt->bind_param("si", $email, $user_db['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $valid_email =  $result->fetch_assoc();


    if (!$valid_email['valid'] || !$user_db['enable']) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Account issue", "message" => "Your account is not enabled or email not validated."]);
        exit;
    }

    // Set session duration based on "remember me"
    $lifetime = $remember_me ? (30 * 24 * 3600) : 3600; // 30 days or 1 hour
    // Configuración para ENTORNO DE DESARROLLO (HTTP)
    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path' => '/',
        'secure' => false, // No requiere HTTPS en desarrollo
        'httponly' => true, // Protege contra XSS
        'samesite' => 'Lax' // Mitiga CSRF en solicitudes de terceros
    ]);

    /*
// Configuración para PRODUCCIÓN (HTTPS)
session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => '/',
    'secure' => true, // Requiere HTTPS en producción
    'httponly' => true, // Protege contra XSS
    'samesite' => 'Strict' // Máxima protección contra CSRF
]);
*/

    session_start();
    session_regenerate_id(true); // Prevent session fixation

    $_SESSION['user'] = [
        'id' => $user_db['id'],
        'username' => $user_db['username'],
        'email' => $user_db['email'],
        'type' => $user_db['type']
    ];
    /*
// Optionally set a remember me cookie
if ($remember_me) {
    $cookie_hash = generate_random_hash($conn, "users", "cookie_hash");
    try {
        $stmt = $conn->prepare("UPDATE users SET cookie_hash = :cookie_hash WHERE id = :id");
        $stmt->execute(['cookie_hash' => $cookie_hash, 'id' => $user['id']]);
        setcookie('remember_me', $cookie_hash, time() + (30 * 24 * 3600), '/', '', true, true);
    } catch (PDOException $e) {
        error_log("[ERROR]:Update cookie hash login.php:" . $e->getMessage());
    }
}
*/
    //echo json_encode(['state' => true]);
    echo json_encode(["error" => false, "type" => "success", "title" => "Successful login", "message" => "success."]);

    exit;
} catch (Exception $e) {
    // Manejo de errores
    error_log("[ERROR]" . basename(__FILE__) . ":" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
} finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}