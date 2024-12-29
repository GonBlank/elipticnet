<?php
// Cargar las variables de entorno

use function PHPSTORM_META\type;

require_once '../env.php';
include '../sesion/checkAuth.php';
include_once '../functions/generate_random_hash.php';
require_once '../email/email.php';
require_once '../email/templates/validate_transport_email.php';
$user = checkAuth();
$owner = $user['id'];


// Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "Method not allowed"]);
    exit;
}

// Verificar si se proporcionó el transportId en la URL (GET)
if (!isset($_GET['transportId'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Validation Error", "message" => "Transport id is required"]);
    exit;
}

$id = (int)$_GET['transportId'];


try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => $conn->connect_error]);
        exit;
    }

    /**/

    // Consulta SQL para obtener el owner y hash_date asociado al validation_hash
    $select_query = "SELECT type, transport_id FROM transports WHERE id = ? AND owner = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($select_query);
    if (!$stmt) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Query Error", "message" => $conn->error]);
        exit;
    }

    // Vincular los parámetros
    $stmt->bind_param("ii", $id, $owner);

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
        echo json_encode(["error" => true, "type" => "error", "title" => "Error", "message" => "Invalid id"]);
        exit;
    }


    // Se encontró exactamente un registro => valido el hash_date
    $row = $result->fetch_assoc();
    $type = $row['type'];
    $transport_id = $row['transport_id'];

    $hash_date = date('Y-m-d H:i:s');
    $validation_hash = generate_random_hash($conn, "transports", "validation_hash");

    $update_query = "UPDATE transports SET validation_hash = ?, hash_date = ? WHERE id = ? AND owner = ?";

    $update_stmt = $conn->prepare($update_query);
    if (!$update_stmt) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Update Error", "message" => $conn->error]);
        exit;
    }

    // Vincular los parámetros
    $update_stmt->bind_param("ssii", $validation_hash, $hash_date, $id, $owner);

    // Ejecutar la consulta
    if ($update_stmt->execute()) {
        if ($update_stmt->affected_rows == 0 || $update_stmt->affected_rows > 1) {
            //No se modifica ningun registro
            echo json_encode(["error" => true, "type" => "error", "message" => "Update Failed"]);
            exit;
        }

        // Cerrar el statement y la conexión
        $update_stmt->close();
        $conn->close();
        //Enviar mensaje al transporte
        resend_validation_code($type, $validation_hash, $transport_id);
        echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "validation link sent correctly."]);
        exit;
    } else {
        echo json_encode(["error" => true, "type" => "error", "title" => "Update Failed", "message" => $update_stmt->error]);
        exit;
    }
    /* */

    // Cerrar la consulta y la conexión
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Manejo de errores generales
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
}






function resend_validation_code($type, $validation_hash, $transport_id)
{

    switch ($type) {
        case 'email':
            // Enviar correo electrónico de validación
            $body = validate_transport_email_template($validation_hash);
            send_email($body, "Verification required", $transport_id);
            break;

        default:
            # code...
            break;
    }



    return;
}
