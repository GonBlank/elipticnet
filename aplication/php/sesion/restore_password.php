<?php
require_once '../env.php';
require_once '../email/email.php';
require_once '../email/templates/password_updated.php';


// Validar el método de la solicitud
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "Invalid request method"]);
    exit;
}

// Obtener los datos JSON enviados a través del POST
$data = json_decode(file_get_contents('php://input'), true);


// Validar si se recibió el JSON y contiene las contraseñas
if (
    !isset($data['new_password'], $data['validation_hash'], $data['repeat_new_password']) ||
    empty($data['new_password']) ||
    empty($data['repeat_new_password'] || empty($data['validation_hash']))
) {
    echo json_encode([
        "error" => true,
        "type" => "error",
        "title" => "Invalid Input",
        "message" => "Incomplete data"
    ]);
    exit;
}

// Verificar que new_password y repeat_new_password coincidan
if ($data['new_password'] !== $data['repeat_new_password']) {
    echo json_encode([
        "error" => true,
        "type" => "error",
        "title" => "Password Mismatch",
        "message" => "New password and repeat password do not match."
    ]);
    exit;
}

$validation_hash = $data['validation_hash'];
$raw_password = $data['new_password'];

// Validate password length
$password_length = strlen($raw_password);
if ($password_length < 8 || $password_length > 20) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid password length", "message" => "Password must be between 8 and 20 characters."]);
    exit;
}

// Generate the password hash
$hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);


try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => $conn->connect_error]);
        exit;
    }


    // Consulta SQL para obtener el owner y hash_date asociado al validation_hash
    $select_query = "SELECT username, email, hash_date FROM users WHERE validation_hash = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($select_query);
    if (!$stmt) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Query Error", "message" => $conn->error]);
        exit;
    }

    // Vincular los parámetros
    $stmt->bind_param("s", $validation_hash);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado
    $result = $stmt->get_result();


    // Verificar cuántos registros fueron encontrados
    if ($result->num_rows > 1) {
        //Se encontró mas de un registro
        echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Fatal error, please contact", "link_text" => "support.", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    } elseif ($result->num_rows === 0) {
        // No se encontró ningún registro
        echo json_encode(["error" => true, "type" => "error", "title" => "Error", "message" => "Invalid validation hash"]);
        exit;
    }


    // Se encontró exactamente un registro => valido el hash_date
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $hash_date = $row['hash_date'];
    $email = $row['email'];

    // Verificar si el hash_date es mayor a 1 hora
    $current_time = new DateTime();
    $hash_time = new DateTime($hash_date);
    $interval = $current_time->diff($hash_time);

    if ($interval->h > 1 || $interval->days > 0) {
        echo json_encode(["error" => true, "type" => "warning", "title" => "Validation Error", "message" => "Validation hash expired", "link_text" => "Resend a new one", "link" => "login.php"]);
        exit;
    }

    $update_query = "UPDATE users SET password = ?, validation_hash = NULL, hash_date = NULL WHERE validation_hash = ?";
    $update_stmt = $conn->prepare($update_query);
    if (!$update_stmt) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Update Error", "message" => $conn->error]);
        exit;
    }

    // Vincular el parámetro
    $update_stmt->bind_param("ss", $hashed_password, $validation_hash);

    // Ejecutar la consulta de actualización
    if ($update_stmt->execute()) {

        // Cerrar el statement y la conexión
        $update_stmt->close();
        $conn->close();

        //enviar email
        $body = password_updated_email_template($username);
        send_email($body, "Password change detected", $email);
        echo json_encode(["error" => false, "type" => "success", "title" => "Password updated", "message" => "You can log in now."]);
        exit;
    } else {
        echo json_encode(["error" => true, "type" => "error", "title" => "Update Failed", "message" => $conn->error]);
        exit;
    }

    $update_stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Manejo de errores generales
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
    exit;
}