<?php
require_once '../env.php';
require_once '../email/email.php';


//Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "data no set"]);
    exit;
}

$user = json_decode(file_get_contents('php://input'), true);


if (!isset($user['username']) || !isset($user['email']) || !isset($user['password'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "data no set"]);
    exit;
}

$name = $user['username'];
$email = $user['email'];
$raw_password = $user['password'];



// Validate password length
$password_length = strlen($raw_password);
if ($password_length < 8 || $password_length > 20) {
    //temp_message('Warn', 'Invalid password length', 'warn', '../html/login.html');
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid password length", "message" => "Password must be between 8 and 20 characters."]);
    exit;
}

// Validate name length
$name_length = strlen($name);
if ($name_length < 8 || $name_length > 20) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid username length", "message" => "Username must be between 8 and 20 characters."]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    //temp_message('Warn', 'Invalid email format', 'warn', '../html/login.html');
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid email format", "message" => "Check the email format."]);
    exit;
}

//connection to the database
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $conn = null;
    error_log("[ERROR]: Connection to the database signup.php:" . $e->getMessage());
    //temp_message('Fatal error', "Database connection failed", 'error', '../html/login.html');
    echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Database connection failed."]);
}

//check if the email already exists in the "users" table
try {
    $sql_check_email = "SELECT COUNT(*) FROM users WHERE email = :email";
    $stmt_check_email = $conn->prepare($sql_check_email);
    $stmt_check_email->bindParam(':email', $email);
    $stmt_check_email->execute();
    $email_exists = $stmt_check_email->fetchColumn();
} catch (PDOException $e) {
    $conn = null;
    error_log("[ERROR]:Check if the email already exists in the users table signup.php:" . $e->getMessage());
    //temp_message('Fatal error', "Database connection failed", 'error', '../html/login.html');
    echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Database connection failed."]);
    exit;
}

if ($email_exists) {
    $conn = null;
    //temp_message('Information', "The email $email already exist.", 'information', '../html/login.html', "To recover your account", "restore_password.html");
    echo json_encode(["error" => true, "type" => "error", "title" => "Duplicated email", "message" => "$email already exist."]);
    exit;
}


// Generate the password hash
$hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

// Generate a random 24-character hash to validate the email
$validation_hash = bin2hex(random_bytes(12));
$hash_date = date('Y-m-d H:i:s');
$user_type = 0; //default free user

// Insert new user
try {
    $sql_insert_user = "INSERT INTO users (username, email, password, type) VALUES (:name, :email, :password, :type)";
    $stmt_insert_user = $conn->prepare($sql_insert_user);
    $stmt_insert_user->bindParam(':name', $name);
    $stmt_insert_user->bindParam(':email', $email);
    $stmt_insert_user->bindParam(':password', $hashed_password);
    $stmt_insert_user->bindParam(':type', $user_type);
    $stmt_insert_user->execute();

    // Obtener el id del usuario recién insertado
    $user_id = $conn->lastInsertId();  // Esto devuelve el ID del último registro insertado
} catch (PDOException $e) {
    $conn = null;
    error_log("[ERROR]: Insert new user signup.php:" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Insert new user." . $e->getMessage()]);
    exit;
}

try {
    $type= 'email';
    // Insert transport email con el ID del usuario como owner
    $sql_insert_transport_email = "INSERT INTO transports (owner, type, transport_id, validation_hash, hash_date) 
                                   VALUES (:owner,:type, :email, :validation_hash, :hash_date)";
    $stmt_insert_transport_email = $conn->prepare($sql_insert_transport_email);
    $stmt_insert_transport_email->bindParam(':owner', $user_id);  // Usamos el id del usuario
    $stmt_insert_transport_email->bindParam(':type', $type);
    $stmt_insert_transport_email->bindParam(':email', $email);
    $stmt_insert_transport_email->bindParam(':validation_hash', $validation_hash);
    $stmt_insert_transport_email->bindParam(':hash_date', $hash_date);
    $stmt_insert_transport_email->execute();  // Ejecución de la sentencia preparada
} catch (PDOException $e) {
    $conn = null;
    error_log("[ERROR]: Insert transport email user signup.php:" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Insert transport email."]);
    exit;
}

// send email verification
$body = "Please verify your email: http://127.0.0.1/Loging%20template/html/login.html?hash=$validation_hash";
//send_email($body, "Verify your email", $email);
$conn = null;

$data['state'] = true;
echo json_encode($data);
//echo json_encode(["error" => true, "type" => "error", "title" => "salio ok", "message" => "test."]);
//temp_message('User created!', 'Check your email to finish the registration', 'success', '../html/login.html');
