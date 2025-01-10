<?php
require_once '../env.php';
require_once 'generate_random_hash.php';



//Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "Method not allowed"]);
    exit;
}

$user = json_decode(file_get_contents('php://input'), true);

if (!isset($user['email']) || !isset($user['password'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "Data no set"]);
    exit;
}


$email = $user['email'];
$raw_password = $user['password'];
$remember_me = $user['remember_me'];



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

//Connection to the database
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $conn = null;
    error_log("[ERROR]: Connection to the database signup.php:" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Database connection failed.". $e->getMessage()]);
}

// Check the users table if the email exists
try {
    $stmt = $conn->prepare("SELECT id, password, username, email, enable, type FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $conn = null;
    error_log("[ERROR]:Check if the email already exists in the users table login.php:" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Database connection failed.". $e->getMessage()]);
    exit;
}

if (!$user) {
    // The email does not exist
    $conn = null;
    echo json_encode(["error" => true, "type" => "error", "title" => "Ups!", "message" => "That email/password combination does not match our records."]);
    exit;
}


// Verify credentials and establish session if valid
if (!password_verify($raw_password, $user['password'])) {
    // Invalid credentials
    $conn = null;
    echo json_encode(["error" => true, "type" => "error", "title" => "Ups!", "message" => "That email/password combination does not match our records."]);
    exit;
}

//Check if the email is verified
try {
    $stmt = $conn->prepare("SELECT valid FROM transports WHERE transport_id = :email AND owner = :owner");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':owner', $user['id']);
    $stmt->execute();
    $valid_email = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $conn = null;
    error_log("[ERROR]:Check if the email is validated login.php:" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Database connection failed.". $e->getMessage()]);
    exit;
}


if (!$valid_email['valid']) {
    // The email is not validated
    $conn = null;
    echo json_encode(["error" => true, "type" => "error", "title" => "Email not validated", "message" => "Please first validate your email."]);
    exit;
}

if (!$user['enable']) {
    // The user is not validated
    $conn = null;
    echo json_encode(["error" => true, "type" => "error", "title" => "Your user is not enable", "message" => "Your user has been disabled, please contact support."]);
    exit;
}





/*
╔═══════════════════════════╗
║ Successful authentication ║
╚═══════════════════════════╝
*/

//Set the remember_me cookie
if ($remember_me) {
    // Generate a random hash of 24 characters
    $cookie_hash = generate_random_hash($conn, "users", "cookie_hash");

    try {
        // Update the hash in the "users" table
        $sql_update_hash = "UPDATE users SET cookie_hash = :cookie_hash WHERE email = :email";
        $stmt_update_hash = $conn->prepare($sql_update_hash);
        $stmt_update_hash->bindParam(':cookie_hash', $cookie_hash);
        $stmt_update_hash->bindParam(':email', $user['email']);
        $stmt_update_hash->execute();
    } catch (PDOException $e) {
        $conn = null;
        error_log("[ERROR]:Update the hash in the users table resend_validation_code.php:" . $e->getMessage());
        echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Database connection failed.". $e->getMessage()]);
        exit;
    }

    setcookie('remember_me', $cookie_hash, time() + (30 * 24 * 3600), '/');
}


session_start();
session_regenerate_id(true); // Regenerate session ID to prevent session fixation


$_SESSION['user'] = [
    'id' => $user['id'],
    'username' => $user['username'],
    'email' => $user['email'],
    'type' => $user['type']
];

error_log("Authenticated user: " . print_r($_SESSION['user'], true));

// Redirect to home page
$conn = null;
//header('Location: ../../public/home.html');
$data['state'] = true;
echo json_encode($data);
exit;
