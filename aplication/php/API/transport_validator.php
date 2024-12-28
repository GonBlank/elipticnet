<?php
require_once '../env.php';

// Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "Data not set"]);
    exit;
}


// Verificar si se proporcionó el hash en el cuerpo de la solicitud (POST)
if (!isset($_POST['validation_hash'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Validation Error", "message" => "Validation hash is required"]);
    exit;
}

// Obtener el validation_hash desde POST
$validation_hash = $_POST['validation_hash'];


try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => $conn->connect_error]);
        exit;
    }

    // Consulta SQL para obtener el owner y hash_date asociado al validation_hash
    $select_query = "SELECT owner, hash_date FROM transports WHERE validation_hash = ?";

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
        //More than one owner found for the provided validation hash
        echo json_encode(["error" => true, "type" => "error", "title" => "Validation hash error", "message" => "please contact support"]);
    } elseif ($result->num_rows === 1) {
        // Si hay exactamente un owner, validar el hash_date
        $row = $result->fetch_assoc();
        $owner = $row['owner'];
        $hash_date = $row['hash_date'];

        // Verificar si el hash_date es mayor a 1 hora
        $current_time = new DateTime();
        $hash_time = new DateTime($hash_date);
        $interval = $current_time->diff($hash_time);

        if ($interval->h > 1 || $interval->days > 0) {
            echo json_encode(["error" => true, "type" => "warning", "title" => "Validation Error", "message" => "hash is expired", "link_text" => "Resend the code", "link" => "/aplication/public/transports.html"]);
            exit;
        }

        // Consulta para actualizar valid a true y setear hash y hash_date a NULL
        $update_query = "UPDATE transports SET valid = TRUE, validation_hash = NULL, hash_date = NULL WHERE validation_hash = ?";
        $update_stmt = $conn->prepare($update_query);
        if (!$update_stmt) {
            echo json_encode(["error" => true, "type" => "error", "title" => "Update Error", "message" => $conn->error]);
            exit;
        }

        // Vincular el parámetro
        $update_stmt->bind_param("s", $validation_hash);

        // Ejecutar la consulta de actualización
        if ($update_stmt->execute()) {
            echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "you can close the page"]);
        } else {
            echo json_encode(["error" => true, "type" => "error", "title" => "Update Failed", "message" => $conn->error]);
        }

        $update_stmt->close();
    } else {
        // Si no se encontró ningún owner
        echo json_encode(["error" => true, "type" => "error", "title" => "Error", "message" => "invalid hash"]);
    }

    // Cerrar la consulta y la conexión
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Manejo de errores generales
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
}
