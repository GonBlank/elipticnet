<?php
require_once '../env.php';
require_once '../email/email.php';
require_once '../email/templates/restore_password.php';
include_once '../functions/generate_random_hash.php';


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
        error_log("[ERROR]" . basename(__FILE__) . ":" . $conn->connect_error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    // Preparar consulta de actualización
    $update_query = "UPDATE users SET validation_hash = ?, hash_date = ? WHERE email = ?";

    $hash_date = date('Y-m-d H:i:s');
    $validation_hash = generate_random_hash($conn, "users", "validation_hash");

    $update_stmt = $conn->prepare($update_query);
    if (!$update_stmt) {
        error_log("[ERROR]" . basename(__FILE__) . ":" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    // Vincular los parámetros
    $update_stmt->bind_param("sss", $validation_hash, $hash_date, $email);

    // Ejecutar la consulta
    if ($update_stmt->execute()) {
        if ($update_stmt->affected_rows == 1) {
            $body = restore_password_template($validation_hash);
            send_email($body, "Restore your password", $email);

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
            error_log("[ERROR]" . basename(__FILE__) . ": Se modifico más de un registro");
            echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
            exit;
        }
    } else {
        error_log("[ERROR]" . basename(__FILE__) . ":" . $update_stmt->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    // Cerrar el statement y la conexión
    $update_stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Manejo de errores generales
    error_log("[ERROR]" . basename(__FILE__) . ":" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
    exit;
}