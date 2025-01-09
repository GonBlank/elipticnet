<?php
// Cargar las variables de entorno
require_once 'env.php';


//Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid request", "message" => "data no set"]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['hash_id']) || !isset($data['email'])) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Error", "message" => "data is missing"]);
    exit;
}

$hash_id = $data['hash_id'];
$email = $data['email'];


try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        error_log("[ERROR] unsuscribe:" . $conn->connect_error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    // Consulta SQL para eliminar el host por id y owner
    $delete_query = "DELETE FROM pre_release WHERE hash_id = ? AND email = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($delete_query);
    if (!$stmt) {
        error_log("[ERROR] unsuscribe:" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    // Vincular los parámetros
    $stmt->bind_param("ss", $hash_id, $email);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Comprobar si realmente se eliminó una fila
        if ($stmt->affected_rows == 1) {
            echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "You will no longer receive messages from the early access list."]);
        } else if ($stmt->affected_rows == 0) {
            echo json_encode(["error" => true, "type" => "error", "title" => "Not Found", "message" => "We couldn't find your email on the early access list. ", "link_text" => "Contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        } else if ($stmt->affected_rows > 1) {
            error_log("[ERROR] unsuscribe: Se elimino mas de un  cliente de la lista de early access");
            echo json_encode(["error" => false, "type" => "success", "title" => "Success", "message" => "You will no longer receive messages from the early access list."]);
        }
    } else {
        error_log("[ERROR] unsuscribe:" . $stmt->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
    }

    // Cerrar la consulta y la conexión
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Manejo de errores generales
    error_log("[ERROR] unsuscribe:" . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
}
