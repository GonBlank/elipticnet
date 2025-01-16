<?php
// Cargar las variables de entorno

#use function PHPSTORM_META\type;

require_once __DIR__ . '/../env.php';
include __DIR__ . '/../sesion/checkAuth.php';
include_once __DIR__ . '/../functions/generate_random_hash.php';
require_once __DIR__ . '/../email/email.php';
require_once __DIR__ . '/../email/templates/validate_transport_email.php';
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
        error_log("[ERROR] " . __FILE__ . ": " . $conn->connect_error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    $select_query = "SELECT type, transport_id, retries, hash_date FROM transports WHERE id = ? AND owner = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($select_query);
    if (!$stmt) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
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


    // Se encontró exactamente un registro =>

    $row = $result->fetch_assoc();


    /*Limitar cantidad de reenvio de link por tiempo*/

    $hash_date = $row['hash_date'];

    // Obtener la fecha actual
    $current_time = new DateTime();

    // Convertir hash_date a objeto DateTime
    $hash_time = new DateTime($hash_date);

    // Calcular la diferencia entre la fecha actual y hash_date
    $interval = $current_time->diff($hash_time);

    // Verificar si la diferencia es menor a 5 minutos
    if ($interval->i < 5 && $interval->d == 0 && $interval->h == 0) {
        echo json_encode(["error" => true, "type" => "warning", "title" => "Wait", "message" => "You must wait at least 5 minutes to resend the validation link"]);
        exit;
    }

    //Verificar que no se haya solicitado enviar más de 5 links
    $retries = ($row['retries'] ?? 0) + 1;

    if ($retries > 5) {
        echo json_encode(["error" => true, "type" => "warning", "title" => "Validation failed", "message" => "It looks like you've sent a lot of validation links. Please contact our ", "link_text" => "support team", "link" => APP_LINK . "/aplication/public/support.php"]);
        exit;
    }


    $type = $row['type'];
    $transport_id = $row['transport_id'];
    $hash_date = date('Y-m-d H:i:s');
    $validation_hash = generate_random_hash($conn, "transports", "validation_hash");
    $validation_sent = NULL; //este es un flag que se usa en casos especificos desde otros scripts. Lo usa el validador de telegram para saber si tiene que enviar el link de validacion
    $update_query = "UPDATE transports SET validation_hash = ?, hash_date = ?, retries = ?, validation_sent = ? WHERE id = ? AND owner = ?";

    $update_stmt = $conn->prepare($update_query);
    if (!$update_stmt) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    // Vincular los parámetros
    $update_stmt->bind_param("ssisii", $validation_hash, $hash_date, $retries, $validation_sent,  $id, $owner);

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
        resend_validation_code($user['username'], $type, $validation_hash, $transport_id);
        echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "validation link sent correctly."]);
        exit;
    } else {
        error_log("[ERROR] " . __FILE__ . ": " . $update_stmt->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }
    /* */

    // Cerrar la consulta y la conexión
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Manejo de errores generales
    error_log("[ERROR] " . __FILE__ . ": " . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
}finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}



function resend_validation_code($user_name, $type, $validation_hash, $transport_id)
{

    switch ($type) {
        case 'email':
            // Enviar correo electrónico de validación
            $body = validate_transport_email_template($user_name, $validation_hash);
            send_email($body, "Verification required", $transport_id);
            break;

        default:
            # code...
            break;
    }



    return;
}
