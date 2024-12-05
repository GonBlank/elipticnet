<?php
require_once '../env.php';
require_once 'generate_random_hash.php';

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

// Connection to the database
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("[ERROR]: Connection to the database login.php:" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Database connection failed." . $e->getMessage()]);
    exit;
}

// Check if the user exists
try {
    $stmt = $conn->prepare("SELECT id, password, username, email, enable, type FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("[ERROR]:Check if the email exists login.php:" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Database connection failed." . $e->getMessage()]);
    exit;
}

if (!$user || !password_verify($raw_password, $user['password'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Ups!", "message" => "Invalid email/password combination."]);
    exit;
}

// Verify email validation
try {
    $stmt = $conn->prepare("SELECT valid FROM transports WHERE transport_id = :email AND owner = :owner");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':owner', $user['id']);
    $stmt->execute();
    $valid_email = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("[ERROR]:Check email validation login.php:" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Database connection failed." . $e->getMessage()]);
    exit;
}

if (!$valid_email['valid'] || !$user['enable']) {
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
    'id' => $user['id'],
    'username' => $user['username'],
    'email' => $user['email'],
    'type' => $user['type']
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
