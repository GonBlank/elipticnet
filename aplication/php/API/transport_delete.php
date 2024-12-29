<?php
// Cargar las variables de entorno
require_once '../env.php';
include '../sesion/checkAuth.php';
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
        error_log("[ERROR] transport_delete:" . $conn->connect_error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => $conn->connect_error]);
        exit;
    }

    // Consulta SQL para eliminar el host por id y owner
    $delete_query = "DELETE FROM transports WHERE id = ? AND owner = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($delete_query);
    if (!$stmt) {
        error_log("[ERROR] transport_delete:" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Query Error", "message" => $conn->error]);
        exit;
    }

    // Vincular los parámetros
    $stmt->bind_param("is", $id, $owner);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Comprobar si realmente se eliminó una fila
        if ($stmt->affected_rows == 1) {
            cleanTransportsArray($conn, "host_data", $id, $owner);

            echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "Transport deleted successfully."]);
        } else if ($stmt->affected_rows == 0) {
            echo json_encode(["error" => true, "type" => "warning", "title" => "Not Found", "message" => "Transport not found."]);
        } else if ($stmt->affected_rows > 1) {
            error_log("[ERROR] transport_delete: Se elimino mas de un transporte");
            echo json_encode(["error" => true, "type" => "Error", "title" => "Error", "message" => "Transport deleted with errors."]);
        }
    } else {
        error_log("[ERROR] transport_delete:" . $stmt->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Execution Error", "message" => $stmt->error]);
    }

    // Cerrar la consulta y la conexión
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Manejo de errores generales
    error_log("[ERROR] transport_delete:" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
}


function cleanTransportsArray($conn, $table, $id, $ownerId)
{
    // Consulta SQL para eliminar un valor del array JSON en la columna 'transports'
    $query = "
        UPDATE $table
        SET transports = JSON_REMOVE(
            transports, 
            JSON_UNQUOTE(JSON_SEARCH(transports, 'one', ?))
        )
        WHERE JSON_SEARCH(transports, 'one', ?) IS NOT NULL
        AND owner = ?
    ";

    // Preparar la consulta
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("[ERROR] cleanTransportsArray: " . $conn->error);
        return false;
    }

    // Vincular parámetros
    $stmt->bind_param("sis", $id, $id, $ownerId);

    // Ejecutar la consulta
    if (!$stmt->execute()) {
        error_log("[ERROR] cleanTransportsArray Execution: " . $stmt->error);
        $stmt->close();
        return false;
    }

    $stmt->close();
    return true;
}
