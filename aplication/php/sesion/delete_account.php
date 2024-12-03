<?php
// Cargar las variables de entorno
require_once '../env.php';
require_once '../email/email.php';
require_once '../email/templates/delete_account.php';
require_once 'close_sesion.php';
require_once 'checkAuth.php';
$user = checkAuth();
$owner = $user['id'];


try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => $conn->connect_error]);
        exit;
    }

    // Consulta SQL para eliminar el host por id y owner
    $delete_query = "DELETE FROM users WHERE id = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($delete_query);
    if (!$stmt) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Query Error", "message" => $conn->error]);
        exit;
    }

    // Vincular los parámetros
    $stmt->bind_param("i", $owner);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Comprobar si realmente se eliminó una fila
        if ($stmt->affected_rows == 1) {
            // Cerrar la consulta y la conexión
            $stmt->close();
            $conn->close();

            //Enviar correo
            $body=delete_account_email_template();
            send_email($body, "Deleted account", $user['email']);

            close_sesion();

            echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "Your account was deleted."]);
        } else if ($stmt->affected_rows == 0) {
            echo json_encode(["error" => true, "type" => "warning", "title" => "Not Found", "message" => "Your account could not be deleted, please contact support.", "link_text" => "🔧Support team", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        } else {
            //Si llega acá es porque se borro más de una cuenta
            //Hacer un log para detectar el que se borro
            echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "Your account was deleted."]);
        }
    } else {
        $stmt->close();
        $conn->close();
        echo json_encode(["error" => true, "type" => "error", "title" => "Execution Error", "message" => $stmt->error]);
    }
} catch (Exception $e) {
    // Manejo de errores generales
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
}
