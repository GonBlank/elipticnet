<?php
require_once '../env.php';
require_once '../email/email.php';
require_once '../email/templates/restore_password.php';


// Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "Data not set"]);
    exit;
}


// Verificar si se proporcionó el email en la solicitud
if (empty($_POST['email'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Email error", "message" => "Email is not provided"]);
    exit;
}

$email = $_POST['email'];

// Validar formato del email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid email format", "message" => "Check the email format."]);
    exit;
}


try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => $conn->connect_error]);
        exit;
    }

    // Preparar consulta de actualización
    $update_query = "UPDATE users SET validation_hash = ?, hash_date = ? WHERE email = ?";

    $hash_date = date('Y-m-d H:i:s');
    $validation_hash = bin2hex(random_bytes(12));

    $update_stmt = $conn->prepare($update_query);
    if (!$update_stmt) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Update Error", "message" => $conn->error]);
        exit;
    }

    // Vincular los parámetros
    $update_stmt->bind_param("sss", $validation_hash, $hash_date, $email);

    // Ejecutar la consulta
    if ($update_stmt->execute()) {
        if ($update_stmt->affected_rows == 1) {
            //enviar email
            $body = restore_password_template($validation_hash);
            send_email($body, "Restore your password", $email);

            //Cerrar conexión
            $update_stmt->close();
            $conn->close();

            echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "If your email is registered, we will send you a link to recover your password."]);
            exit;
        } else if ($update_stmt->affected_rows == 0) {
            //No se modifica ningun registro
            echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "If your email is registered, we will send you a link to recover your password."]);
            exit;
        } else if ($update_stmt->affected_rows > 1) {
            //Se modificó más de un registro. Nunca se debería llegar acá
            echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Fatal error, please contact", "link_text" => "support.", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
            exit;
        }
    } else {
        echo json_encode(["error" => true, "type" => "error", "title" => "Update Failed", "message" => $update_stmt->error]);
        exit;
    }

    // Cerrar el statement y la conexión
    $update_stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Manejo de errores generales
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
    exit;
}
