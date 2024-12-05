<?php
require_once '../env.php';
require_once '../email/email.php';
require_once '../email/templates/password_updated.php';
require_once '../sesion/close_sesion.php';
require_once '../sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];


// Validar el método de la solicitud
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "Invalid request method"]);
    exit;
}

// Obtener los datos JSON enviados a través del POST
$data = json_decode(file_get_contents('php://input'), true);


// Validar si se recibió el JSON y contiene las contraseñas
if (
    !isset($data['old_password'], $data['new_password'], $data['repeat_new_password']) ||
    empty($data['old_password']) ||
    empty($data['new_password']) ||
    empty($data['repeat_new_password'])
) {
    echo json_encode([
        "error" => true,
        "type" => "error",
        "title" => "Invalid Input",
        "message" => "All password fields must be provided and cannot be empty."
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


$old_password = $data['old_password'];


try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => $conn->connect_error]);
        exit;
    }

    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare statement error: " . $conn->error);
    }

    // Vincular parámetros
    $stmt->bind_param("i", $owner);  // Usamos 'i' para indicar que es un entero
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows != 1) {
        //trae mas de un password
        echo json_encode(["error" => true, "type" => "Error", "title" => "Info error", "message" => "Something went wrong."]);
    }

    $user_data = $result->fetch_assoc();

    // Verify credentials
    if (!password_verify($old_password, $user_data['password'])) {
        // Invalid credentials
        $conn = null;
        echo json_encode(["error" => true, "type" => "error", "title" => "Ups!", "message" => "Bad password."]);
        exit;
    }

    $raw_password = $data['new_password'];

    // Validate password length
    $password_length = strlen($raw_password);
    if ($password_length < 8 || $password_length > 20) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Invalid password length", "message" => "Password must be between 8 and 20 characters."]);
        exit;
    }


    // Generate the password hash
    $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);



    // Preparar consulta de actualización
    $update_query = "UPDATE users SET password = ? WHERE id = ? ";

    $update_stmt = $conn->prepare($update_query);
    if (!$update_stmt) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Update Error", "message" => $conn->error]);
        exit;
    }

    // Vincular los parámetros
    $update_stmt->bind_param("si", $hashed_password, $owner);

    // Ejecutar la consulta
    if ($update_stmt->execute()) {
        if ($update_stmt->affected_rows == 1) {
            //enviar email
            $body = password_updated_email_template($user['username']);
            send_email($body, "Password change detected", $user['email']);
            close_session();
            echo json_encode(["error" => false, "type" => "success", "title" => "Password updated", "message" => "Log in again to continue."]);
        } else {
            //No se modifica ningun registro
            echo json_encode(["error" => true, "type" => "error", "title" => "Update Failed", "message" => "Something went wrong."]);
        }
    } else {
        echo json_encode(["error" => true, "type" => "error", "title" => "Update Failed", "message" => $update_stmt->error]);
    }

    // Cerrar el statement y la conexión
    $update_stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Manejo de errores generales
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
}
